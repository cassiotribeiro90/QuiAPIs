<?php
// controllers/api/gestor/StoreUsuarioController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\lojista\LojistaUsuario;
use app\models\api\lojista\LojistaUsuarioLoja;
use app\models\api\app\Loja;
use app\controllers\api\gestor\ControllerBase;
use yii\web\UnauthorizedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\caching\DbDependency;

class StoreUsuarioController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/store-usuarios
     * Lista lojistas com filtros e paginação
     */
    public function actionIndex()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $request = Yii::$app->request;
            
            $query = LojistaUsuario::find()
                ->orderBy(['criado_em' => SORT_DESC]);
            
            // Filtros
            if ($request->get('loja_id')) {
                $lojaId = (int)$request->get('loja_id');
                $query->joinWith('lojasRelacionadas sul')
                      ->andWhere(['sul.loja_id' => $lojaId]);
            }
            
            if ($request->get('funcao')) {
                $query->andWhere(['funcao' => $request->get('funcao')]);
            }
            
            if ($request->get('status') !== null) {
                $query->andWhere(['status' => (int)$request->get('status')]);
            }
            
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'nome', $search],
                    ['like', 'email', $search],
                    ['like', 'cpf_cnpj', $search],
                ]);
            }
            
            // Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;
            
            $total = $query->count();
            $lojistas = $query->offset($offset)->limit($perPage)->all();
            
            $data = array_map(function($lojista) {
                return $this->formatarLojista($lojista);
            }, $lojistas);
            
            // 🔥 FILTER OPTIONS (NOVO)
            $filterOptions = $this->generateFilterOptions();
            
            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ],
                'filter_options' => $filterOptions,
            ], 'Lista de lojistas recuperada com sucesso');
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404, 'not_found');
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/store-usuarios/<id>
     */
    public function actionView($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojista = LojistaUsuario::findOne(['id' => (int)$id]);
            
            if (!$lojista) {
                throw new NotFoundHttpException('Lojista não encontrado');
            }
            
            return ApiResponse::success(
                $this->formatarLojista($lojista),
                'Lojista recuperado com sucesso'
            );
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404, 'not_found');
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    /**
     * POST /api/gestor/store-usuarios/create
     */
    public function actionCreate()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            $request = Yii::$app->request->post();
            
            // Validação
            $erros = [];
            if (empty($request['nome'])) $erros['nome'] = 'Nome é obrigatório';
            if (empty($request['email'])) $erros['email'] = 'E-mail é obrigatório';
            if (empty($request['funcao'])) $erros['funcao'] = 'Função é obrigatória';
            if (empty($request['senha'])) $erros['senha'] = 'Senha é obrigatória';
            
            if (!empty($erros)) {
                return ApiResponse::error('Validação falhou', 422, 'validation_failed', $erros);
            }
            
            if (LojistaUsuario::findOne(['email' => $request['email']])) {
                return ApiResponse::error('E-mail já cadastrado', 422, 'duplicate_email', ['email' => 'E-mail duplicado']);
            }
            
            $lojista = new LojistaUsuario();
            $lojista->nome = $request['nome'];
            $lojista->email = $request['email'];
            $lojista->telefone = $request['telefone'] ?? null;
            $lojista->cpf_cnpj = $request['cpf_cnpj'] ?? null;
            $lojista->funcao = $request['funcao'];
            $lojista->status = $request['status'] ?? LojistaUsuario::STATUS_ATIVO;
            $lojista->generateAuthKey();
            $lojista->setPassword($request['senha']);
            
            if (!$lojista->save()) {
                return ApiResponse::error('Erro ao criar lojista', 500, 'save_error', $lojista->errors);
            }
            
            if (!empty($request['loja_ids'])) {
                $lojista->assignLojas($request['loja_ids']);
            }
            
            return ApiResponse::success(
                $this->formatarLojista($lojista),
                'Lojista criado com sucesso'
            );
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500, 'internal_error');
        }
    }

    /**
     * PUT /api/gestor/store-usuarios/update/<id>
     */
    public function actionUpdate($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojista = LojistaUsuario::findOne(['id' => (int)$id]);
            if (!$lojista) {
                throw new NotFoundHttpException('Lojista não encontrado');
            }
            
            $request = Yii::$app->request->post();
            
            if (!empty($request['nome'])) $lojista->nome = $request['nome'];
            if (!empty($request['telefone'])) $lojista->telefone = $request['telefone'];
            if (!empty($request['cpf_cnpj'])) $lojista->cpf_cnpj = $request['cpf_cnpj'];
            if (!empty($request['funcao'])) $lojista->funcao = $request['funcao'];
            if (isset($request['status'])) $lojista->status = (int)$request['status'];
            
            if (!empty($request['senha'])) {
                $lojista->setPassword($request['senha']);
                $lojista->generateAuthKey();
            }
            
            if (!$lojista->save()) {
                return ApiResponse::error('Erro ao atualizar lojista', 500, 'save_error', $lojista->errors);
            }
            
            if (isset($request['loja_ids'])) {
                $lojista->assignLojas($request['loja_ids']);
            }
            
            return ApiResponse::success(
                $this->formatarLojista($lojista),
                'Lojista atualizado com sucesso'
            );
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404, 'not_found');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500, 'internal_error');
        }
    }

    /**
     * DELETE /api/gestor/store-usuarios/delete/<id>
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojista = LojistaUsuario::findOne(['id' => (int)$id]);
            if (!$lojista) {
                throw new NotFoundHttpException('Lojista não encontrado');
            }
            
            if ($lojista->softDelete()) {
                return ApiResponse::success(null, 'Lojista removido com sucesso');
            }
            
            return ApiResponse::error('Erro ao remover lojista', 500, 'delete_failed');
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404, 'not_found');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500, 'internal_error');
        }
    }

    /**
     * GET /api/gestor/store-usuarios/options
     * Lista lojas para combobox
     */
    public function actionOptions()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojas = Loja::find()
                ->select(['id', 'nome'])
                ->where(['deletado_em' => null])
                ->orderBy(['nome' => SORT_ASC])
                ->asArray()
                ->all();
            
            return ApiResponse::success($lojas, 'Lista de lojas');
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500, 'internal_error');
        }
    }

    /**
     * GET /api/gestor/store-usuarios/lojas-options
     * Lista lojas para seleção múltipla
     */
    public function actionLojasOptions()
    {
        try {
            $usuarioLogado = $this->getUserByToken();
            
            $lojas = Loja::find()
                ->select(['id', 'nome'])
                ->where(['deletado_em' => null])
                ->orderBy(['nome' => SORT_ASC])
                ->asArray()
                ->all();
            
            return ApiResponse::success($lojas, 'Lista de lojas disponíveis');
            
        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500, 'internal_error');
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function formatarLojista(LojistaUsuario $lojista)
    {
        $lojasRelacionadas = LojistaUsuarioLoja::find()
            ->where(['usuario_id' => $lojista->id])
            ->with('loja')
            ->asArray()
            ->all();
        
        $lojas = array_map(function($rel) {
            return [
                'id' => $rel['loja']['id'],
                'nome' => $rel['loja']['nome'],
            ];
        }, $lojasRelacionadas);
        
        return [
            'id' => $lojista->id,
            'nome' => $lojista->nome,
            'email' => $lojista->email,
            'telefone' => $lojista->telefone,
            'cpf_cnpj' => $lojista->cpf_cnpj,
            'funcao' => $lojista->funcao,
            'status' => $lojista->status,
            'ultimo_login_em' => $lojista->ultimo_login_em,
            'ultimo_login_ip' => $lojista->ultimo_login_ip,
            'criado_em' => $lojista->criado_em,
            'atualizado_em' => $lojista->atualizado_em,
            'loja_ids' => array_column($lojas, 'id'),
            'lojas' => $lojas,
        ];
    }

    // ==================== FILTER OPTIONS ====================

    /**
     * Gera as opções de filtro (com cache)
     */
    private function generateFilterOptions()
    {
        $cacheKey = 'store_usuarios_filter_options_v2';
        
        $dependency = new DbDependency([
            'sql' => 'SELECT MAX(atualizado_em) FROM store_usuario WHERE deletado_em IS NULL',
        ]);

        return Yii::$app->cache->getOrSet(
            $cacheKey,
            function () {
                return $this->buildFilterOptions();
            },
            3600,
            $dependency
        );
    }

    /**
     * Constrói as opções de filtro (sem cache)
     * USANDO O MODEL Diretamente para evitar erros de nome de tabela
     */
    private function buildFilterOptions()
    {
        // ----- FUNÇÃO (funcao) -----
        $funcoes = LojistaUsuario::find()
            ->select(['funcao', 'COUNT(*) as total'])
            ->where(['deletado_em' => null])
            ->andWhere(['is not', 'funcao', null])
            ->andWhere(['<>', 'funcao', ''])
            ->groupBy('funcao')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();

        $funcaoOptions = [];
        foreach ($funcoes as $item) {
            $funcaoOptions[] = [
                'value' => $item['funcao'],
                'label' => ucfirst($item['funcao']),
                'count' => (int)$item['total'],
            ];
        }

        // ----- STATUS -----
        $statusCounts = LojistaUsuario::find()
            ->select(['status', 'COUNT(*) as total'])
            ->where(['deletado_em' => null])
            ->groupBy('status')
            ->asArray()
            ->all();

        $statusLabels = [
            1 => 'Ativo',
            0 => 'Inativo',
        ];

        $statusOptions = [];
        foreach ($statusCounts as $item) {
            $statusOptions[] = [
                'value' => (string)$item['status'],
                'label' => $statusLabels[$item['status']] ?? 'Desconhecido',
                'count' => (int)$item['total'],
            ];
        }

        // ----- LOJAS ASSOCIADAS -----
        $lojas = Loja::find()
            ->select(['l.id', 'l.nome', 'COUNT(sul.usuario_id) as total'])
            ->alias('l')
            ->leftJoin(['sul' => 'store_usuario_loja'], 'sul.loja_id = l.id')
            ->where(['l.deletado_em' => null])
            ->groupBy('l.id')
            ->having(['>', 'total', 0])
            ->orderBy(['l.nome' => SORT_ASC])
            ->asArray()
            ->all();

        $lojaOptions = [];
        foreach ($lojas as $item) {
            $lojaOptions[] = [
                'value' => (string)$item['id'],
                'label' => $item['nome'],
                'count' => (int)$item['total'],
            ];
        }

        return [
            'funcao' => $funcaoOptions,
            'status' => $statusOptions,
            'loja_id' => $lojaOptions,
        ];
    }
}