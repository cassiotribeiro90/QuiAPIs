<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\models\api\app\Loja;
use app\models\api\lojista\LojistaUsuarioLoja;

/**
 * Controller para o lojista gerenciar sua própria loja.
 * 
 * Rotas (prefixo /api/lojista/loja):
 * - GET  /                 -> visualizar dados da loja atual
 * - GET  /{id}             -> visualizar dados de uma loja específica (com permissão)
 * - GET  /minhas-lojas     -> listar todas as lojas do lojista
 * - PUT  /update           -> atualizar dados da loja atual (via body JSON)
 * 
 * Todos os endpoints exigem autenticação via Bearer Token (userLojista)
 * e o header X-Store-Id com o ID da loja selecionada.
 */
class LojaController extends LojistaControllerBase
{
    /**
     * Obtém o ID da loja a partir do header X-Store-Id
     * e valida se o lojista tem acesso a ela.
     * 
     * @return int ID da loja
     * @throws BadRequestHttpException se o header não for enviado
     * @throws NotFoundHttpException se o lojista não tiver acesso à loja
     */
    private function getLojaIdFromRequest()
    {
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            throw new BadRequestHttpException('Lojista não autenticado');
        }

        $request = Yii::$app->request;
        $lojaId = $request->getHeaders()->get('X-Store-Id');

        // Fallback: tenta no body/query
        if (empty($lojaId)) {
            $lojaId = $request->getBodyParam('store_id') ?: $request->get('store_id');
        }

        if (empty($lojaId)) {
            throw new BadRequestHttpException('O header X-Store-Id é obrigatório');
        }

        $lojaId = (int)$lojaId;

        // Verifica se o lojista tem acesso a essa loja
        $acesso = LojistaUsuarioLoja::find()
            ->where([
                'usuario_id' => $lojistaId,
                'loja_id'    => $lojaId,
                'status'     => LojistaUsuarioLoja::STATUS_ATIVO,
            ])
            ->exists();

        if (!$acesso) {
            throw new NotFoundHttpException('Loja não encontrada ou acesso negado');
        }

        return $lojaId;
    }

    /**
     * GET /api/lojista/loja
     * Visualiza os dados da loja atual (baseado no header X-Store-Id)
     * 
     * @return array
     */
    public function actionIndex()
    {
        try {
            $lojaId = $this->getLojaIdFromRequest();

            $loja = Loja::find()
                ->where(['id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$loja) {
                return $this->asError('Loja não encontrada', 404);
            }

            return $this->asSuccess([
                'data' => $this->formatLoja($loja),
            ]);

        } catch (BadRequestHttpException $e) {
            return $this->asError($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return $this->asError($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao buscar dados da loja: " . $e->getMessage(), __METHOD__);
            return $this->asError('Erro ao buscar dados da loja', 500);
        }
    }

    /**
     * GET /api/lojista/loja/{id}
     * Visualiza os dados de uma loja específica (com validação de permissão)
     * 
     * @param int $id
     * @return array
     */
    public function actionView($id)
    {
        try {
            $lojistaId = $this->getLojistaId();
            if (!$lojistaId) {
                return $this->asError('Lojista não autenticado', 401);
            }

            // Verifica se o lojista tem acesso à loja
            $acesso = LojistaUsuarioLoja::find()
                ->where([
                    'usuario_id' => $lojistaId,
                    'loja_id'    => $id,
                    'status'     => LojistaUsuarioLoja::STATUS_ATIVO,
                ])
                ->exists();

            if (!$acesso) {
                return $this->asError('Você não tem acesso a esta loja', 403);
            }

            $loja = Loja::find()
                ->where(['id' => $id, 'deletado_em' => null])
                ->one();

            if (!$loja) {
                return $this->asError('Loja não encontrada', 404);
            }

            return $this->asSuccess([
                'data' => $this->formatLoja($loja),
            ]);

        } catch (\Exception $e) {
            Yii::error("Erro ao buscar dados da loja: " . $e->getMessage(), __METHOD__);
            return $this->asError('Erro ao buscar dados da loja', 500);
        }
    }

    /**
     * GET /api/lojista/loja/minhas-lojas
     * Lista todas as lojas do lojista autenticado
     * 
     * @return array
     */
    public function actionMinhasLojas()
    {
        try {
            $lojistaId = $this->getLojistaId();
            if (!$lojistaId) {
                return $this->asError('Lojista não autenticado', 401);
            }

            $lojas = Loja::find()
                ->innerJoin('store_usuario_loja sul', 'sul.loja_id = loja.id')
                ->where(['sul.usuario_id' => $lojistaId, 'sul.status' => LojistaUsuarioLoja::STATUS_ATIVO])
                ->andWhere(['loja.deletado_em' => null])
                ->all();

            $data = array_map([$this, 'formatLoja'], $lojas);

            return $this->asSuccess([
                'lojas' => $data,
                'total' => count($data),
            ]);

        } catch (\Exception $e) {
            Yii::error("Erro ao listar lojas: " . $e->getMessage(), __METHOD__);
            return $this->asError('Erro ao listar lojas', 500);
        }
    }

    /**
     * PUT /api/lojista/loja/update
     * Atualiza os dados da loja atual (baseado no header X-Store-Id)
     * 
     * Body esperado (JSON):
     * {
     *   "nome": "Nova Loja",
     *   "descricao": "Descrição atualizada",
     *   "cor_tema": "#FF6B6B",
     *   "whatsapp": "(11) 99999-9999",
     *   "email": "contato@loja.com",
     *   "instagram": "@lojaoficial"
     * }
     * 
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionUpdate()
    {
        try {
            $lojaId = $this->getLojaIdFromRequest();

            $loja = Loja::find()
                ->where(['id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$loja) {
                return $this->asError('Loja não encontrada', 404);
            }

            $request = Yii::$app->request->getBodyParams();

            // Campos permitidos para o lojista editar
            $allowedFields = [
                'nome', 'descricao', 'cor_tema', 'whatsapp', 
                'email', 'instagram', 'telefone', 'logo', 'capa'
            ];
            $hasChanges = false;

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $request)) {
                    $value = $request[$field];
                    // Se for string vazia, permitir null (para campos opcionais)
                    if ($value === '' && in_array($field, ['descricao', 'whatsapp', 'email', 'instagram', 'cor_tema', 'logo', 'capa'])) {
                        $loja->$field = null;
                    } else {
                        $loja->$field = $value;
                    }
                    $hasChanges = true;
                }
            }

            if (!$hasChanges) {
                return $this->asError('Nenhum campo válido para atualizar.', 400);
            }

            // Validação básica
            if (isset($request['email']) && $request['email'] !== '' && !filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->asError('E-mail inválido.', 400);
            }

            $loja->atualizado_em = date('Y-m-d H:i:s');

            if ($loja->save()) {
                return $this->asSuccess([
                    'data' => $this->formatLoja($loja),
                    'message' => 'Loja atualizada com sucesso!',
                ]);
            }

            return $this->asError($loja->getErrors(), 422);

        } catch (BadRequestHttpException $e) {
            return $this->asError($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return $this->asError($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao atualizar loja: " . $e->getMessage(), __METHOD__);
            return $this->asError('Erro ao atualizar loja', 500);
        }
    }

    /**
     * Formata os dados da loja para a resposta.
     * 
     * @param Loja $loja
     * @return array
     */
    protected function formatLoja($loja)
    {
        return [
            'id' => $loja->id,
            'nome' => $loja->nome,
            'descricao' => $loja->descricao,
            'slug' => $loja->slug,
            'categoria' => $loja->categoria,
            'logo' => $loja->logo,
            'capa' => $loja->capa,
            'cor_tema' => $loja->cor_tema,
            'whatsapp' => $loja->whatsapp,
            'email' => $loja->email,
            'instagram' => $loja->instagram,
            'telefone' => $loja->telefone,
            'status' => $loja->status,
            'verificado' => (bool)$loja->verificado,
            'destaque' => (bool)$loja->destaque,
            'nota_media' => (float)$loja->nota_media,
            'total_avaliacoes' => (int)$loja->total_avaliacoes,
            'cep' => $loja->cep,
            'logradouro' => $loja->logradouro,
            'numero' => $loja->numero,
            'complemento' => $loja->complemento,
            'bairro' => $loja->bairro,
            'cidade' => $loja->cidade,
            'uf' => $loja->uf,
            'latitude' => $loja->latitude ? (float)$loja->latitude : null,
            'longitude' => $loja->longitude ? (float)$loja->longitude : null,
            'taxa_entrega' => (float)$loja->taxa_entrega,
            'pedido_minimo' => (float)$loja->pedido_minimo,
            'tempo_entrega_min' => (int)$loja->tempo_entrega_min,
            'tempo_entrega_max' => (int)$loja->tempo_entrega_max,
            'fluxo_status' => $loja->fluxo_status,
            'configuracoes' => $loja->configuracoes,
            'criado_em' => $loja->criado_em,
            'atualizado_em' => $loja->atualizado_em,
        ];
    }

    /**
     * Resposta padronizada de sucesso.
     * 
     * @param array $data
     * @return array
     */
    protected function asSuccess(array $data = [])
    {
        return array_merge([
            'success' => true,
            'timestamp' => date('c'),
        ], $data);
    }

    /**
     * Resposta padronizada de erro.
     * 
     * @param mixed $errors
     * @param int $statusCode
     * @return array
     */
    protected function asError($errors, $statusCode = 400)
    {
        Yii::$app->response->statusCode = $statusCode;
        return [
            'success' => false,
            'errors' => $errors,
            'timestamp' => date('c'),
        ];
    }
}