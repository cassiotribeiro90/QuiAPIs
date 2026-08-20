<?php

namespace app\controllers\api\lojista;

use Yii;
use app\components\ApiResponse;
use app\models\api\lojista\LojistaUsuarioLoja;
use app\models\api\app\Pedido;
use app\models\api\lojista\PedidoStatusHistorico;
use app\controllers\api\lojista\LojistaControllerBase;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class LojistaPedidoController extends LojistaControllerBase
{
    public $enableCsrfValidation = false;
    public $strictParsing = true;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [];
        }

        return $behaviors;
    }

    /**
     * Obtém o ID da loja a partir do header X-Store-Id
     * e valida se o lojista tem acesso a ela.
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
     * Retorna a lista de pedidos ativos formatada
     */
    private function getPedidosAtivos($lojaId)
    {
        $statusOrdem = ['novo', 'preparando', 'pronto', 'saiu'];
        
        $pedidos = Pedido::find()
            ->where(['loja_id' => $lojaId])
            ->andWhere(['in', 'status', $statusOrdem])
            ->andWhere(['deletado_em' => null])
            ->orderBy([
                new \yii\db\Expression("FIELD(status, 'novo', 'preparando', 'pronto', 'saiu')"),
                'criado_em' => SORT_ASC
            ])
            ->all();

        $grupos = [];
        foreach ($statusOrdem as $status) {
            $grupos[$status] = [
                'status' => $status,
                'label' => $this->getStatusLabel($status),
                'total' => 0,
                'itens' => []
            ];
        }

        foreach ($pedidos as $pedido) {
            if (isset($grupos[$pedido->status])) {
                $item = $this->formatPedidoCompleto($pedido);
                $item['tempo_espera'] = $this->calcularTempoEspera($pedido->criado_em);
                $item['nao_lidas'] = $this->getMensagensNaoLidas($pedido->id);
                $grupos[$pedido->status]['itens'][] = $item;
                $grupos[$pedido->status]['total']++;
            }
        }

        return array_values($grupos);
    }

    /**
     * GET /api/lojista/lojista-pedido
     * 
     * Parâmetros:
     * - status: filtrar por status (novo, aguardando, confirmado, preparando, pronto, saiu, entregue, cancelado)
     * - data_inicio: YYYY-MM-DD
     * - data_fim: YYYY-MM-DD
     * - page: página (padrão: 1)
     * - per_page: itens por página (padrão: 20)
     */
    public function actionIndex()
    {
        try {
            $lojaId = $this->getLojaIdFromRequest();

            $query = Pedido::find()
                ->where(['loja_id' => $lojaId])
                ->andWhere(['deletado_em' => null]);

            // Filtros
            $status = Yii::$app->request->get('status');
            if (!empty($status)) {
                $query->andWhere(['status' => $status]);
            }

            $dataInicio = Yii::$app->request->get('data_inicio');
            if (!empty($dataInicio)) {
                $query->andWhere(['>=', 'DATE(criado_em)', $dataInicio]);
            }

            $dataFim = Yii::$app->request->get('data_fim');
            if (!empty($dataFim)) {
                $query->andWhere(['<=', 'DATE(criado_em)', $dataFim]);
            }

            // Paginação
            $page = (int) Yii::$app->request->get('page', 1);
            $perPage = (int) Yii::$app->request->get('per_page', 20);
            
            $countQuery = clone $query;
            $total = $countQuery->count();
            
            $pagination = new Pagination([
                'totalCount' => $total,
                'pageSize' => $perPage,
                'page' => $page - 1,
            ]);

            $pedidos = $query->orderBy(['criado_em' => SORT_DESC])
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

            $data = array_map(function($pedido) {
                $formatted = $this->formatPedidoCompleto($pedido);
                $formatted['nao_lidas'] = $this->getMensagensNaoLidas($pedido->id);
                return $formatted;
            }, $pedidos);

            return ApiResponse::success([
                'items' => $data,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage),
                ]
            ], 'Pedidos listados com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao listar pedidos: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao listar pedidos', 500);
        }
    }

    /**
     * GET /api/lojista/lojista-pedido/ativos
     * Retorna pedidos ativos agrupados por status com detalhes completos
     */
    public function actionAtivos()
    {
        try {
            $lojaId = $this->getLojaIdFromRequest();
            $grupos = $this->getPedidosAtivos($lojaId);

            return ApiResponse::success([
                'grupos' => $grupos
            ], 'Pedidos ativos agrupados listados com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            $statusOrdem = ['novo', 'preparando', 'pronto', 'saiu'];
            $gruposVazios = [];
            foreach ($statusOrdem as $status) {
                $gruposVazios[] = [
                    'status' => $status,
                    'label' => $this->getStatusLabel($status),
                    'total' => 0,
                    'itens' => []
                ];
            }
            return ApiResponse::success(['grupos' => $gruposVazios], 'Nenhum pedido ativo');
        } catch (\Exception $e) {
            Yii::error("Erro ao listar pedidos ativos: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao listar pedidos ativos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/lojista/lojista-pedido/view
     * Visualiza um pedido específico
     */
    public function actionView()
    {
        try {
            $id = Yii::$app->request->get('id');
            if (empty($id)) {
                return ApiResponse::error('ID do pedido é obrigatório', 400);
            }

            $lojaId = $this->getLojaIdFromRequest();

            $pedido = Pedido::find()
                ->where(['id' => $id, 'loja_id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            return ApiResponse::success(
                $this->formatPedidoCompleto($pedido),
                'Pedido encontrado com sucesso'
            );
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao ver pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao buscar pedido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/lojista/lojista-pedido/aceitar
     * Aceita um pedido - muda status para 'preparando'
     * Retorna a lista atualizada de pedidos ativos
     */
    public function actionAceitar()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return ApiResponse::error('ID do pedido é obrigatório', 400);
        }

        try {
            $lojaId = $this->getLojaIdFromRequest();
            $lojistaId = $this->getLojistaId();

            $pedido = Pedido::find()
                ->where(['id' => $id, 'loja_id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            if ($pedido->status !== 'novo') {
                return ApiResponse::error('Este pedido já foi processado. Status atual: ' . $pedido->status, 400);
            }

            $pedido->status = 'preparando';
            $pedido->data_confirmacao = date('Y-m-d H:i:s');
            $pedido->atualizado_em = date('Y-m-d H:i:s');

            if (!$pedido->save()) {
                return ApiResponse::error('Erro ao salvar pedido: ' . json_encode($pedido->errors), 500);
            }

            $this->salvarHistorico($pedido->id, $lojistaId, 'novo', 'preparando');

            // 🔥 RETORNA OS DADOS ATUALIZADOS
            $pedidosAtivos = $this->getPedidosAtivos($lojaId);

            return ApiResponse::success([
                'pedido_id' => $pedido->id,
                'status' => $pedido->status,
                'grupos' => $pedidosAtivos,
            ], 'Pedido aceito com sucesso!');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao aceitar pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao aceitar pedido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/lojista/lojista-pedido/recusar
     * Recusa um pedido - muda status para 'cancelado'
     * Retorna a lista atualizada de pedidos ativos
     */
    public function actionRecusar()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return ApiResponse::error('ID do pedido é obrigatório', 400);
        }

        $request = Yii::$app->request;
        $motivo = $request->getBodyParam('motivo');
        $motivoCodigo = $request->getBodyParam('motivo_codigo');

        try {
            $lojaId = $this->getLojaIdFromRequest();
            $lojistaId = $this->getLojistaId();

            $pedido = Pedido::find()
                ->where(['id' => $id, 'loja_id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            if ($pedido->status !== 'novo') {
                return ApiResponse::error('Este pedido já foi processado. Status atual: ' . $pedido->status, 400);
            }

            $pedido->status = 'cancelado';
            $pedido->data_cancelamento = date('Y-m-d H:i:s');
            $pedido->cancelado_por = 'loja';
            $pedido->cancelado_motivo = $motivo;
            $pedido->atualizado_em = date('Y-m-d H:i:s');

            if (!$pedido->save()) {
                return ApiResponse::error('Erro ao salvar pedido: ' . json_encode($pedido->errors), 500);
            }

            $this->salvarHistorico($pedido->id, $lojistaId, 'novo', 'cancelado', $motivo, $motivoCodigo);

            // 🔥 RETORNA OS DADOS ATUALIZADOS
            $pedidosAtivos = $this->getPedidosAtivos($lojaId);

            return ApiResponse::success([
                'pedido_id' => $pedido->id,
                'status' => $pedido->status,
                'grupos' => $pedidosAtivos,
            ], 'Pedido recusado com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao recusar pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao recusar pedido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/lojista/lojista-pedido/atualizar-status
     * Atualiza o status de um pedido
     * Retorna a lista atualizada de pedidos ativos
     */
    public function actionAtualizarStatus()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return ApiResponse::error('ID do pedido é obrigatório', 400);
        }

        $request = Yii::$app->request;
        $novoStatus = $request->getBodyParam('status');
        $motivo = $request->getBodyParam('motivo');

        if (empty($novoStatus)) {
            return ApiResponse::error('Status é obrigatório', 400);
        }

        $statusPermitidos = ['novo', 'aguardando', 'confirmado', 'preparando', 'pronto', 'saiu', 'entregue', 'cancelado'];
        if (!in_array($novoStatus, $statusPermitidos)) {
            return ApiResponse::error('Status inválido. Permitidos: ' . implode(', ', $statusPermitidos), 400);
        }

        try {
            $lojaId = $this->getLojaIdFromRequest();
            $lojistaId = $this->getLojistaId();

            $pedido = Pedido::find()
                ->where(['id' => $id, 'loja_id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            $statusAnterior = $pedido->status;
            $pedido->status = $novoStatus;
            $pedido->atualizado_em = date('Y-m-d H:i:s');

            switch ($novoStatus) {
                case 'confirmado':
                case 'preparando':
                    $pedido->data_confirmacao = date('Y-m-d H:i:s');
                    break;
                case 'pronto':
                    $pedido->data_preparo = date('Y-m-d H:i:s');
                    break;
                case 'saiu':
                    $pedido->data_saida = date('Y-m-d H:i:s');
                    break;
                case 'entregue':
                    $pedido->data_entrega = date('Y-m-d H:i:s');
                    break;
                case 'cancelado':
                    $pedido->data_cancelamento = date('Y-m-d H:i:s');
                    break;
            }

            if (!$pedido->save()) {
                return ApiResponse::error('Erro ao salvar pedido: ' . json_encode($pedido->errors), 500);
            }

            $this->salvarHistorico($pedido->id, $lojistaId, $statusAnterior, $novoStatus, $motivo);

            // 🔥 RETORNA OS DADOS ATUALIZADOS
            $pedidosAtivos = $this->getPedidosAtivos($lojaId);

            return ApiResponse::success([
                'pedido_id' => $pedido->id,
                'status' => $pedido->status,
                'status_anterior' => $statusAnterior,
                'grupos' => $pedidosAtivos,
            ], 'Status atualizado com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao atualizar status: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao atualizar status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/lojista/lojista-pedido/cancelar
     * Cancela um pedido
     * Retorna a lista atualizada de pedidos ativos
     */
    public function actionCancelar()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return ApiResponse::error('ID do pedido é obrigatório', 400);
        }

        $request = Yii::$app->request;
        $motivo = $request->getBodyParam('motivo');

        if (empty($motivo)) {
            return ApiResponse::error('Motivo do cancelamento é obrigatório', 400);
        }

        try {
            $lojaId = $this->getLojaIdFromRequest();
            $lojistaId = $this->getLojistaId();

            $pedido = Pedido::find()
                ->where(['id' => $id, 'loja_id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            $statusCancelaveis = ['novo', 'aguardando', 'confirmado', 'preparando', 'pronto'];
            if (!in_array($pedido->status, $statusCancelaveis)) {
                return ApiResponse::error('Este pedido não pode ser cancelado. Status atual: ' . $pedido->status, 400);
            }

            $statusAnterior = $pedido->status;
            $pedido->status = 'cancelado';
            $pedido->data_cancelamento = date('Y-m-d H:i:s');
            $pedido->cancelado_por = 'loja';
            $pedido->cancelado_motivo = $motivo;
            $pedido->atualizado_em = date('Y-m-d H:i:s');

            if (!$pedido->save()) {
                return ApiResponse::error('Erro ao salvar pedido: ' . json_encode($pedido->errors), 500);
            }

            $this->salvarHistorico($pedido->id, $lojistaId, $statusAnterior, 'cancelado', $motivo);

            // 🔥 RETORNA OS DADOS ATUALIZADOS
            $pedidosAtivos = $this->getPedidosAtivos($lojaId);

            return ApiResponse::success([
                'pedido_id' => $pedido->id,
                'status' => $pedido->status,
                'grupos' => $pedidosAtivos,
            ], 'Pedido cancelado com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao cancelar pedido: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao cancelar pedido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/lojista/lojista-pedido/status-count
     * Retorna contagem de pedidos por status
     */
    public function actionStatusCount()
    {
        try {
            $lojaId = $this->getLojaIdFromRequest();

            $statusLista = ['novo', 'aguardando', 'confirmado', 'preparando', 'pronto', 'saiu', 'entregue', 'cancelado'];
            $counts = [];

            foreach ($statusLista as $status) {
                $counts[$status] = Pedido::find()
                    ->where(['loja_id' => $lojaId])
                    ->andWhere(['status' => $status])
                    ->andWhere(['deletado_em' => null])
                    ->count();
            }

            return ApiResponse::success(['counts' => $counts], 'Contagem de status obtida com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao contar status: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao contar status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/lojista/lojista-pedido/historico
     * Retorna o histórico de status de um pedido
     */
    public function actionHistorico()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return ApiResponse::error('ID do pedido é obrigatório', 400);
        }

        try {
            $lojaId = $this->getLojaIdFromRequest();

            $pedido = Pedido::find()
                ->where(['id' => $id, 'loja_id' => $lojaId, 'deletado_em' => null])
                ->one();

            if (!$pedido) {
                return ApiResponse::error('Pedido não encontrado', 404);
            }

            $historico = PedidoStatusHistorico::find()
                ->where(['pedido_id' => $id])
                ->orderBy(['criado_em' => SORT_DESC])
                ->all();

            $data = array_map(function($item) {
                return [
                    'id' => $item->id,
                    'status_anterior' => $item->status_anterior,
                    'status_novo' => $item->status_novo,
                    'motivo' => $item->motivo,
                    'motivo_codigo' => $item->motivo_codigo,
                    'responsavel' => $item->getResponsavelNome(),
                    'criado_em' => $item->criado_em,
                ];
            }, $historico);

            return ApiResponse::success($data, 'Histórico obtido com sucesso');
            
        } catch (BadRequestHttpException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        } catch (NotFoundHttpException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            Yii::error("Erro ao buscar histórico: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao buscar histórico: ' . $e->getMessage(), 500);
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Salva histórico de status
     */
    private function salvarHistorico($pedidoId, $lojistaId, $statusAnterior, $statusNovo, $motivo = null, $motivoCodigo = null)
    {
        try {
            $historico = new PedidoStatusHistorico();
            $historico->pedido_id = $pedidoId;
            $historico->store_usuario_id = $lojistaId;
            $historico->status_anterior = $statusAnterior;
            $historico->status_novo = $statusNovo;
            $historico->motivo = $motivo;
            $historico->motivo_codigo = $motivoCodigo;
            $historico->ip_origem = Yii::$app->request->userIP;
            $historico->user_agent = Yii::$app->request->userAgent;
            $historico->save();
        } catch (\Exception $e) {
            Yii::error("Erro ao salvar histórico: " . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Retorna o label do status
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'novo' => 'Novos',
            'aguardando' => 'Aguardando',
            'confirmado' => 'Confirmados',
            'preparando' => 'Preparando',
            'pronto' => 'Prontos',
            'saiu' => 'Saiu',
            'entregue' => 'Entregues',
            'cancelado' => 'Cancelados',
        ];
        return $labels[$status] ?? $status;
    }

    /**
     * Formata pedido com todos os detalhes (incluindo itens do relacionamento)
     */
    private function formatPedidoCompleto($pedido)
    {
        $clienteNome = null;
        $clienteTelefone = null;
        if (isset($pedido->usuario) && $pedido->usuario) {
            $clienteNome = $pedido->usuario->nome ?? null;
            $clienteTelefone = $pedido->usuario->telefone ?? null;
        }
        if (empty($clienteNome) && isset($pedido->cliente_nome)) {
            $clienteNome = $pedido->cliente_nome;
        }

        // Buscar itens do relacionamento (tabela pedido_item)
        $itens = [];
        if (method_exists($pedido, 'getItens')) {
            foreach ($pedido->itens as $item) {
                $itens[] = [
                    'nome' => $item->produto_nome,
                    'quantidade' => (int)$item->quantidade,
                    'preco_unitario' => (float)$item->preco_unitario,
                    'total' => (float)$item->preco_total,
                ];
            }
        }

        // Normalizar endereço
        $endereco = null;
        if (isset($pedido->endereco_entrega)) {
            if (is_string($pedido->endereco_entrega)) {
                $decoded = json_decode($pedido->endereco_entrega, true);
                if (is_array($decoded)) {
                    $endereco = $decoded;
                } else {
                    $endereco = $pedido->endereco_entrega;
                }
            } else {
                $endereco = $pedido->endereco_entrega;
            }
        }

        return [
            'id' => (int)$pedido->id,
            'codigo' => $pedido->codigo ?? null,
            'cliente_nome' => $clienteNome,
            'cliente_telefone' => $clienteTelefone,
            'loja_id' => (int)$pedido->loja_id,
            'status' => $pedido->status,
            'total' => (float)$pedido->total,
            'subtotal' => (float)$pedido->subtotal,
            'taxa_entrega' => (float)$pedido->taxa_entrega,
            'desconto' => (float)$pedido->desconto,
            'forma_pagamento' => $pedido->forma_pagamento,
            'pagamento_status' => $pedido->pagamento_status ?? null,
            'troco_para' => $pedido->troco_para ? (float)$pedido->troco_para : null,
            'endereco_entrega' => $endereco,
            'itens' => $itens,
            'observacoes' => $pedido->observacoes,
            'distancia_km' => $pedido->distancia_km ? (float)$pedido->distancia_km : null,
            'tempo_espera_min' => $pedido->tempo_espera_min,
            'criado_em' => $pedido->criado_em,
            'data_confirmacao' => $pedido->data_confirmacao,
            'data_preparo' => $pedido->data_preparo,
            'data_saida' => $pedido->data_saida,
            'data_entrega' => $pedido->data_entrega,
        ];
    }

    /**
     * Calcula o tempo de espera em minutos
     */
    private function calcularTempoEspera($criadoEm)
    {
        if (empty($criadoEm)) {
            return 0;
        }
        $criado = strtotime($criadoEm);
        $agora = time();
        return floor(($agora - $criado) / 60);
    }

    /**
     * Conta mensagens não lidas do chat
     */
    private function getMensagensNaoLidas($pedidoId)
    {
        try {
            $count = (new \yii\db\Query())
                ->from('chat_mensagens')
                ->where([
                    'pedido_id' => $pedidoId,
                    'lida' => 0,
                    'enviado_por' => 'cliente'
                ])
                ->count();
            return (int)$count;
        } catch (\Exception $e) {
            return 0;
        }
    }
}