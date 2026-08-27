<?php
// controllers/api/gestor/ClientesController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Usuario;
use app\controllers\api\gestor\ControllerBase;
use yii\web\UnauthorizedHttpException;
use yii\web\NotFoundHttpException;
use yii\caching\DbDependency;

class ClientesController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/clientes
     * Lista todos os clientes com paginação, filtros e opções de filtro
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            // 🔥 USAR ALIAS 'u' PARA USUÁRIO PARA EVITAR AMBIGUIDADE
            $query = Usuario::find()
                ->alias('u')
                ->where(['u.deletado_em' => null]) // apenas clientes não deletados
                ->andWhere(['u.tipo' => 'cliente']) // apenas clientes (não admins)
                ->orderBy(['u.criado_em' => SORT_DESC]);

            // 🔥 Filtro de Período (TEMPO) - COM DOMINGO COMO INÍCIO DA SEMANA
            if ($request->get('periodo')) {
                $periodo = $request->get('periodo');
                switch ($periodo) {
                    case 'hoje':
                        $query->andWhere(['>=', 'u.criado_em', date('Y-m-d 00:00:00')]);
                        break;
                    case 'semana':
                        $startOfWeek = date('Y-m-d', strtotime('last sunday'));
                        $query->andWhere(['>=', 'u.criado_em', $startOfWeek . ' 00:00:00']);
                        break;
                    case 'mes':
                        $query->andWhere(['>=', 'u.criado_em', date('Y-m-01 00:00:00')]);
                        break;
                    case 'ano':
                        $query->andWhere(['>=', 'u.criado_em', date('Y-01-01 00:00:00')]);
                        break;
                    case 'todos':
                    default:
                        // não aplica filtro
                        break;
                }
            }

            // 🔥 Filtro por status
            if ($request->get('status')) {
                $statusList = explode(',', $request->get('status'));
                $statusList = array_map('trim', $statusList);
                $statusList = array_filter($statusList);
                if (!empty($statusList)) {
                    $query->andWhere(['in', 'u.status', $statusList]);
                }
            }

            // 🔥 Filtro por tipo (embora já esteja fixo em 'cliente', pode ser usado para admin futuramente)
            if ($request->get('tipo')) {
                $query->andWhere(['u.tipo' => $request->get('tipo')]);
            }

            // 🔥 Filtro por data de cadastro (intervalo)
            if ($request->get('data_inicio')) {
                $query->andWhere(['>=', 'u.criado_em', $request->get('data_inicio') . ' 00:00:00']);
            }
            if ($request->get('data_fim')) {
                $query->andWhere(['<=', 'u.criado_em', $request->get('data_fim') . ' 23:59:59']);
            }

            // 🔥 Busca por nome, email, CPF, telefone ou whatsapp
            if ($request->get('search')) {
                $search = $request->get('search');
                $query->andWhere([
                    'or',
                    ['like', 'u.nome', $search],
                    ['like', 'u.email', $search],
                    ['like', 'u.cpf', $search],
                    ['like', 'u.telefone', $search],
                    ['like', 'u.whatsapp', $search],
                ]);
            }

            // 🔥 Filtro por total de pedidos (clientes com pelo menos X pedidos)
            if ($request->get('min_pedidos') !== null) {
                $query->andWhere(['>=', 'u.total_pedidos', (int)$request->get('min_pedidos')]);
            }

            // 🔥 Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $clientes = $query->offset($offset)->limit($perPage)->all();

            $data = array_map(function($cliente) {
                return $this->formatarCliente($cliente);
            }, $clientes);

            // Filter options (com cache)
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
            ], 'Lista de clientes recuperada com sucesso');

        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            Yii::error('[ClientesController] Erro: ' . $e->getMessage(), __METHOD__);
            return ApiResponse::error(
                $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/clientes/<id>
     * Visualiza um cliente específico com todos os detalhes
     */
    public function actionView($id)
    {
        try {
            $this->getUserByToken();

            $cliente = Usuario::find()
                ->where(['id' => (int)$id, 'deletado_em' => null])
                ->andWhere(['tipo' => 'cliente'])
                ->one();

            if (!$cliente) {
                throw new NotFoundHttpException('Cliente não encontrado');
            }

            return ApiResponse::success(
                $this->formatarCliente($cliente, true),
                'Cliente recuperado com sucesso'
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
     * PUT /api/gestor/clientes/update/<id>
     * Atualiza um cliente (status, telefone, etc.)
     * Não permite criar novos clientes, apenas editar os existentes.
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();

            $cliente = Usuario::findOne([
                'id' => (int)$id,
                'deletado_em' => null,
                'tipo' => 'cliente'
            ]);
            if (!$cliente) {
                throw new NotFoundHttpException('Cliente não encontrado');
            }

            $request = Yii::$app->request->post();

            // Campos permitidos para edição
            $camposPermitidos = [
                'nome',
                'email',
                'telefone',
                'whatsapp',
                'cpf',
                'data_nascimento',
                'status',
                'tipo',
                'avatar',
                'pref_notificacoes_email',
                'pref_notificacoes_push',
                'pref_notificacoes_sms',
                'pref_tema',
                'pontos',
                'nivel',
            ];

            $alterado = false;
            foreach ($camposPermitidos as $campo) {
                if (array_key_exists($campo, $request)) {
                    $cliente->$campo = $request[$campo];
                    $alterado = true;
                }
            }

            // Se enviou senha, atualiza (com hash)
            if (!empty($request['senha'])) {
                $cliente->senha_hash = Yii::$app->security->generatePasswordHash($request['senha']);
                $alterado = true;
            }

            if (!$alterado) {
                return ApiResponse::error('Nenhum campo para atualizar', 400, 'no_changes');
            }

            // Salvar cliente
            if (!$cliente->save()) {
                return ApiResponse::error('Erro ao atualizar cliente', 422, 'update_failed', $cliente->errors);
            }

            // Recarregar
            $cliente = Usuario::findOne($id);

            return ApiResponse::success(
                $this->formatarCliente($cliente, true),
                'Cliente atualizado com sucesso'
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
     * GET /api/gestor/clientes/options
     * Retorna opções para selects (status, tipos, etc.)
     */
    public function actionOptions()
    {
        try {
            $this->getUserByToken();

            // Status disponíveis (definidos no model)
            $statusOptions = [
                ['value' => 'ativo', 'label' => 'Ativo'],
                ['value' => 'inativo', 'label' => 'Inativo'],
                ['value' => 'bloqueado', 'label' => 'Bloqueado'],
                ['value' => 'pendente', 'label' => 'Pendente'],
                ['value' => 'convidado', 'label' => 'Convidado'],
            ];

            // Tipos (embora fixo cliente, pode ser útil para filtros futuros)
            $tipoOptions = [
                ['value' => 'cliente', 'label' => 'Cliente'],
                ['value' => 'admin', 'label' => 'Administrador'],
            ];

            return ApiResponse::success([
                'status' => $statusOptions,
                'tipo' => $tipoOptions,
            ], 'Opções recuperadas com sucesso');

        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            return ApiResponse::error(
                $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Formata os dados de um cliente para resposta
     */
    private function formatarCliente($cliente, $detalhado = false)
    {
        $dados = [
            'id' => $cliente->id,
            'nome' => $cliente->nome,
            'email' => $cliente->email,
            'cpf' => $cliente->cpf,
            'telefone' => $cliente->telefone,
            'whatsapp' => $cliente->whatsapp,
            'data_nascimento' => $cliente->data_nascimento,
            'status' => $cliente->status,
            'status_label' => $this->getStatusLabel($cliente->status),
            'tipo' => $cliente->tipo,
            'avatar' => $cliente->avatar,
            'total_pedidos' => (int)$cliente->total_pedidos,
            'total_gasto' => (float)$cliente->total_gasto,
            'pontos' => (int)$cliente->pontos,
            'nivel' => (int)$cliente->nivel,
            'primeiro_pedido_em' => $cliente->primeiro_pedido_em,
            'ultimo_pedido_em' => $cliente->ultimo_pedido_em,
            'criado_em' => $cliente->criado_em,
            'atualizado_em' => $cliente->atualizado_em,
            'ultimo_login_em' => $cliente->ultimo_login_em,
            'ultimo_login_provider' => $cliente->ultimo_login_provider,
            'pref_notificacoes_email' => (bool)$cliente->pref_notificacoes_email,
            'pref_notificacoes_push' => (bool)$cliente->pref_notificacoes_push,
            'pref_notificacoes_sms' => (bool)$cliente->pref_notificacoes_sms,
            'pref_tema' => $cliente->pref_tema,
            'email_verificado' => (bool)$cliente->email_verificado,
            'telefone_verificado' => (bool)$cliente->telefone_verificado,
            'termos_aceitos' => (bool)$cliente->termos_aceitos,
            'login_count' => (int)$cliente->login_count,
        ];

        if ($detalhado) {
            // Adicionar campos extras para visão detalhada
            $dados['device_id'] = $cliente->device_id;
            $dados['device_token'] = $cliente->device_token;
            $dados['codigo_indicacao'] = $cliente->codigo_indicacao;
            $dados['indicacoes_count'] = (int)$cliente->indicacoes_count;
            $dados['indicado_por'] = $cliente->indicado_por;
            $dados['ultimo_login_ip'] = $cliente->ultimo_login_ip;
            $dados['refresh_token'] = $cliente->refresh_token; // cuidado com exposição
        }

        return $dados;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'bloqueado' => 'Bloqueado',
            'pendente' => 'Pendente',
            'convidado' => 'Convidado',
        ];
        return $labels[$status] ?? $status;
    }

    // ==================== FILTER OPTIONS ====================

    /**
     * Gera as opções de filtro (com cache)
     */
    private function generateFilterOptions()
    {
        $cacheKey = 'clientes_filter_options_v1';
        
        $dependency = new DbDependency([
            'sql' => 'SELECT MAX(atualizado_em) FROM app_usuario WHERE tipo = "cliente" AND deletado_em IS NULL',
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
     */
    private function buildFilterOptions()
    {
        // ----- PERÍODO (TEMPO) -----
        $periodoOptions = [
            ['value' => 'todos', 'label' => 'Todos'],
            ['value' => 'hoje', 'label' => 'Hoje'],
            ['value' => 'semana', 'label' => 'Esta Semana'],
            ['value' => 'mes', 'label' => 'Este Mês'],
            ['value' => 'ano', 'label' => 'Este Ano'],
        ];

        // ----- STATUS -----
        $statusCounts = Usuario::find()
            ->select(['status', 'COUNT(*) as total'])
            ->where(['tipo' => 'cliente', 'deletado_em' => null])
            ->groupBy('status')
            ->asArray()
            ->all();

        $statusOptions = [];
        foreach ($statusCounts as $item) {
            $statusOptions[] = [
                'value' => $item['status'],
                'label' => $this->getStatusLabel($item['status']),
                'count' => (int)$item['total'],
            ];
        }

        // ----- TIPO (embora fixo, mantido para consistência) -----
        $tipoCounts = Usuario::find()
            ->select(['tipo', 'COUNT(*) as total'])
            ->where(['deletado_em' => null])
            ->groupBy('tipo')
            ->asArray()
            ->all();

        $tipoOptions = [];
        foreach ($tipoCounts as $item) {
            $tipoOptions[] = [
                'value' => $item['tipo'],
                'label' => ucfirst($item['tipo']),
                'count' => (int)$item['total'],
            ];
        }

        return [
            'periodo' => $periodoOptions,
            'status' => $statusOptions,
            'tipo' => $tipoOptions,
        ];
    }
}