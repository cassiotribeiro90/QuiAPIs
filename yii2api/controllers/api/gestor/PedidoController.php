<?php
// controllers/api/gestor/PedidoController.php

namespace app\controllers\api\gestor;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Pedido;
use app\models\api\app\PedidoItem;
use app\models\api\app\Loja;
use app\models\api\app\Usuario;
use app\controllers\api\gestor\ControllerBase;
use yii\web\UnauthorizedHttpException;
use yii\web\NotFoundHttpException;
use yii\caching\DbDependency;

class PedidoController extends ControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * GET /api/gestor/pedidos
     * Lista todos os pedidos com paginação, filtros e opções de filtro
     */
    public function actionIndex()
    {
        try {
            $this->getUserByToken();

            $request = Yii::$app->request;

            // 🔥 USAR ALIAS 'p' PARA PEDIDO PARA EVITAR AMBIGUIDADE
            $query = Pedido::find()
                ->alias('p')
                ->with(['loja', 'usuario', 'itens'])
                ->orderBy(['p.criado_em' => SORT_DESC]);

            // 🔥 Filtro de Período (TEMPO) - COM DOMINGO COMO INÍCIO DA SEMANA
            if ($request->get('periodo')) {
                $periodo = $request->get('periodo');
                switch ($periodo) {
                    case 'hoje':
                        $query->andWhere(['>=', 'p.criado_em', date('Y-m-d 00:00:00')]);
                        break;
                    case 'semana':
                        // 🔥 CORRIGIDO: Início da semana = DOMINGO PASSADO (00:00)
                        $startOfWeek = date('Y-m-d', strtotime('last sunday'));
                        $query->andWhere(['>=', 'p.criado_em', $startOfWeek . ' 00:00:00']);
                        break;
                    case 'mes':
                        $query->andWhere(['>=', 'p.criado_em', date('Y-m-01 00:00:00')]);
                        break;
                    case 'ano':
                        $query->andWhere(['>=', 'p.criado_em', date('Y-01-01 00:00:00')]);
                        break;
                    case 'todos':
                    default:
                        // não aplica filtro
                        break;
                }
            }

            // Filtros existentes
            if ($request->get('status')) {
                $statusList = explode(',', $request->get('status'));
                $statusList = array_map('trim', $statusList);
                $statusList = array_filter($statusList);
                if (!empty($statusList)) {
                    $query->andWhere(['in', 'p.status', $statusList]);
                }
            }

            if ($request->get('loja_id')) {
                $query->andWhere(['p.loja_id' => (int)$request->get('loja_id')]);
            }

            if ($request->get('cliente_id')) {
                $query->andWhere(['p.usuario_id' => (int)$request->get('cliente_id')]);
            }

            if ($request->get('data_inicio')) {
                $query->andWhere(['>=', 'p.criado_em', $request->get('data_inicio') . ' 00:00:00']);
            }

            if ($request->get('data_fim')) {
                $query->andWhere(['<=', 'p.criado_em', $request->get('data_fim') . ' 23:59:59']);
            }

            // 🔥 Busca por código, nome do cliente (via relacionamento) ou telefone
            if ($request->get('search')) {
                $search = $request->get('search');
                
                // 🔥 CORREÇÃO: usar alias 'u' para usuario e 'p' para pedido
                $query->innerJoin(['u' => 'app_usuario'], 'u.id = p.usuario_id');
                $query->andWhere([
                    'or',
                    ['like', 'p.codigo', $search],
                    ['like', 'u.nome', $search],
                    ['like', 'u.telefone', $search],
                    ['like', 'p.endereco_entrega', $search],
                ]);
            }

            // Paginação
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $pedidos = $query->offset($offset)->limit($perPage)->all();

            $data = array_map(function($pedido) {
                return $this->formatarPedido($pedido);
            }, $pedidos);

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
            ], 'Lista de pedidos recuperada com sucesso');

        } catch (UnauthorizedHttpException $e) {
            return ApiResponse::error($e->getMessage(), 401, 'unauthorized');
        } catch (\Exception $e) {
            Yii::error('[PedidoController] Erro: ' . $e->getMessage(), __METHOD__);
            return ApiResponse::error(
                $e->getMessage(),
                500,
                'internal_error'
            );
        }
    }

    /**
     * GET /api/gestor/pedidos/<id>
     * Visualiza um pedido específico com todos os detalhes (itens, cliente, loja)
     */
    public function actionView($id)
    {
        try {
            $this->getUserByToken();

            $pedido = Pedido::find()
                ->with(['loja', 'usuario', 'itens'])
                ->andWhere(['id' => (int)$id])
                ->one();

            if (!$pedido) {
                throw new NotFoundHttpException('Pedido não encontrado');
            }

            return ApiResponse::success(
                $this->formatarPedido($pedido, true),
                'Pedido recuperado com sucesso'
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
     * PUT /api/gestor/pedidos/update/<id>
     * Atualiza um pedido (status, observações, etc.)
     * Não permite criar novos pedidos, apenas editar os existentes.
     */
    public function actionUpdate($id)
    {
        try {
            $this->getUserByToken();

            $pedido = Pedido::findOne(['id' => (int)$id]);
            if (!$pedido) {
                throw new NotFoundHttpException('Pedido não encontrado');
            }

            $request = Yii::$app->request->post();

            // Campos permitidos para edição
            $camposPermitidos = ['status', 'observacoes', 'pagamento_status', 'troco_para', 'endereco_entrega'];

            $alterado = false;
            foreach ($camposPermitidos as $campo) {
                if (isset($request[$campo])) {
                    $pedido->$campo = $request[$campo];
                    $alterado = true;
                }
            }

            // Se enviou itens (substitui a lista atual) – opcional
            if (isset($request['itens']) && is_array($request['itens'])) {
                // Remover itens antigos
                PedidoItem::deleteAll(['pedido_id' => $pedido->id]);

                // Adicionar novos itens
                foreach ($request['itens'] as $itemData) {
                    $item = new PedidoItem();
                    $item->pedido_id = $pedido->id;
                    $item->produto_id = $itemData['produto_id'] ?? null;
                    $item->nome = $itemData['nome'];
                    $item->quantidade = $itemData['quantidade'] ?? 1;
                    $item->preco_unitario = $itemData['preco_unitario'] ?? 0;
                    $item->total = $itemData['total'] ?? ($item->quantidade * $item->preco_unitario);
                    $item->observacoes = $itemData['observacoes'] ?? null;
                    if (!$item->save()) {
                        return ApiResponse::error('Erro ao salvar item do pedido', 422, 'item_error', $item->errors);
                    }
                }

                // Recalcular total do pedido
                $novoTotal = PedidoItem::find()
                    ->where(['pedido_id' => $pedido->id])
                    ->sum('total') ?? 0;
                $pedido->total = $novoTotal;
                $alterado = true;
            }

            if (!$alterado) {
                return ApiResponse::error('Nenhum campo para atualizar', 400, 'no_changes');
            }

            // Salvar pedido
            if (!$pedido->save()) {
                return ApiResponse::error('Erro ao atualizar pedido', 422, 'update_failed', $pedido->errors);
            }

            // Recarregar com relacionamentos
            $pedido = Pedido::find()
                ->with(['loja', 'usuario', 'itens'])
                ->andWhere(['id' => $id])
                ->one();

            return ApiResponse::success(
                $this->formatarPedido($pedido, true),
                'Pedido atualizado com sucesso'
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
     * DELETE /api/gestor/pedidos/delete/<id>
     * Cancela um pedido (soft delete ou marca como cancelado)
     */
    public function actionDelete($id)
    {
        try {
            $usuarioLogado = $this->getUserByToken();

            $pedido = Pedido::findOne(['id' => (int)$id]);
            if (!$pedido) {
                throw new NotFoundHttpException('Pedido não encontrado');
            }

            // Impedir cancelamento de pedidos já finalizados
            if (in_array($pedido->status, ['entregue', 'cancelado'])) {
                return ApiResponse::error(
                    'Este pedido não pode ser cancelado (já está ' . $pedido->status . ')',
                    400,
                    'invalid_status'
                );
            }

            $pedido->status = 'cancelado';
            $pedido->observacoes = ($pedido->observacoes ? $pedido->observacoes . ' | ' : '') . 'Cancelado pelo gestor';

            if ($pedido->save(false)) {
                return ApiResponse::success(null, 'Pedido cancelado com sucesso');
            }

            return ApiResponse::error('Erro ao cancelar pedido', 500, 'delete_failed');

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
     * GET /api/gestor/pedidos/options
     * Retorna opções para selects (status, lojas, clientes)
     */
    public function actionOptions()
    {
        try {
            $this->getUserByToken();

            // Lojas ativas
            $lojas = Loja::find()
                ->select(['id', 'nome'])
                ->where(['deletado_em' => null])
                ->orderBy(['nome' => SORT_ASC])
                ->asArray()
                ->all();

            // Status disponíveis (definidos no model)
            $statusOptions = [
                ['value' => 'novo', 'label' => 'Novo'],
                ['value' => 'aguardando', 'label' => 'Aguardando'],
                ['value' => 'confirmado', 'label' => 'Confirmado'],
                ['value' => 'preparando', 'label' => 'Preparando'],
                ['value' => 'pronto', 'label' => 'Pronto'],
                ['value' => 'saiu', 'label' => 'Saiu para entrega'],
                ['value' => 'entregue', 'label' => 'Entregue'],
                ['value' => 'cancelado', 'label' => 'Cancelado'],
                ['value' => 'recusado', 'label' => 'Recusado'],
            ];

            // Clientes (apenas os que já fizeram pedidos)
            $clientes = Usuario::find()
                ->select(['u.id', 'u.nome'])
                ->alias('u')
                ->innerJoin('pedido p', 'p.usuario_id = u.id')
                ->where(['u.tipo' => 'cliente'])
                ->groupBy('u.id')
                ->orderBy(['u.nome' => SORT_ASC])
                ->asArray()
                ->all();

            return ApiResponse::success([
                'lojas' => $lojas,
                'status' => $statusOptions,
                'clientes' => $clientes,
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
     * Formata os dados de um pedido para resposta
     * 🔥 AGORA INCLUI `loja_imagem` (campo `logo` da tabela loja)
     */
    private function formatarPedido($pedido, $detalhado = false)
    {
        // Obtém nome e telefone do cliente via relacionamento
        $clienteNome = $pedido->usuario ? $pedido->usuario->nome : null;
        $clienteTelefone = $pedido->usuario ? $pedido->usuario->telefone : null;

        // 🔥 Obtém a logo da loja (campo `logo`)
        $lojaImagem = $pedido->loja ? $pedido->loja->logo : null;

        $dados = [
            'id' => $pedido->id,
            'codigo' => $pedido->codigo,
            'loja_id' => $pedido->loja_id,
            'loja_nome' => $pedido->loja ? $pedido->loja->nome : null,
            'loja_imagem' => $lojaImagem,
            'usuario_id' => $pedido->usuario_id,
            'cliente_nome' => $clienteNome,
            'cliente_telefone' => $clienteTelefone,
            'status' => $pedido->status,
            'status_label' => $this->getStatusLabel($pedido->status),
            'total' => (float)$pedido->total,
            'subtotal' => (float)$pedido->subtotal,
            'taxa_entrega' => (float)$pedido->taxa_entrega,
            'desconto' => (float)$pedido->desconto,
            'forma_pagamento' => $pedido->forma_pagamento,
            'pagamento_status' => $pedido->pagamento_status,
            'troco_para' => $pedido->troco_para ? (float)$pedido->troco_para : null,
            'endereco_entrega' => $pedido->endereco_entrega,
            'observacoes' => $pedido->observacoes,
            'criado_em' => $pedido->criado_em,
            'atualizado_em' => $pedido->atualizado_em,
            'tempo_espera_min' => $pedido->tempo_espera_min,
        ];

        if ($detalhado) {
            $itens = PedidoItem::find()
                ->where(['pedido_id' => $pedido->id])
                ->asArray()
                ->all();

            $dados['itens'] = $itens;
            $dados['loja'] = $pedido->loja ? [
                'id' => $pedido->loja->id,
                'nome' => $pedido->loja->nome,
                'telefone' => $pedido->loja->telefone,
                'imagem' => $lojaImagem,
            ] : null;
            $dados['cliente'] = $pedido->usuario ? [
                'id' => $pedido->usuario->id,
                'nome' => $pedido->usuario->nome,
                'telefone' => $pedido->usuario->telefone,
            ] : null;
        }

        return $dados;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'novo' => 'Novo',
            'aguardando' => 'Aguardando',
            'confirmado' => 'Confirmado',
            'preparando' => 'Preparando',
            'pronto' => 'Pronto',
            'saiu' => 'Saiu para entrega',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            'recusado' => 'Recusado',
        ];
        return $labels[$status] ?? $status;
    }

    // ==================== FILTER OPTIONS ====================

    /**
     * Gera as opções de filtro (com cache)
     */
    private function generateFilterOptions()
    {
        $cacheKey = 'pedidos_filter_options_v2';
        
        $dependency = new DbDependency([
            'sql' => 'SELECT MAX(atualizado_em) FROM pedido',
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
     * 🔥 AGORA INCLUI O FILTRO DE PERÍODO
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
        $statusCounts = Pedido::find()
            ->select(['status', 'COUNT(*) as total'])
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

        // ----- LOJAS COM PEDIDOS -----
        $lojas = Loja::find()
            ->select(['l.id', 'l.nome', 'COUNT(p.id) as total'])
            ->alias('l')
            ->innerJoin(['p' => 'pedido'], 'p.loja_id = l.id')
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
            'periodo' => $periodoOptions,
            'status' => $statusOptions,
            'loja_id' => $lojaOptions,
        ];
    }
}