<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\helpers\Inflector;
use app\models\api\app\Produto;
use app\models\api\lojista\LojistaUsuarioLoja;

/**
 * Controller para gestão do cardápio do lojista.
 * 
 * Rotas (prefixo /api/lojista/cardapio):
 * - GET    /                 -> listar produtos
 * - GET    /{id}             -> visualizar produto
 * - POST   /create           -> criar produto
 * - PUT    /update/{id}      -> atualizar produto (inclui estoque)
 * - DELETE /delete/{id}      -> remover produto (soft delete)
 * - PATCH  /toggle/{id}      -> alternar disponibilidade
 * - POST   /estoque/{id}     -> atualizar estoque
 * 
 * Todos os endpoints exigem autenticação via Bearer Token (userLojista)
 * e o header X-Store-Id com o ID da loja selecionada.
 */
class CardapioController extends LojistaControllerBase
{
    /**
     * Obtém o ID da loja a partir do header X-Store-Id e valida se o lojista tem acesso.
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

        // Se não veio no header, tenta no body (caso algum cliente prefira)
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
                'status'     => 1,
            ])
            ->exists();

        if (!$acesso) {
            throw new NotFoundHttpException('Loja não encontrada ou acesso negado');
        }

        return $lojaId;
    }

    /**
     * Lista todos os produtos da loja selecionada.
     */
    public function actionIndex()
    {
        $lojaId = $this->getLojaIdFromRequest();

        $produtos = Produto::find()
            ->where(['loja_id' => $lojaId])
            ->andWhere(['deletado_em' => null])
            ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC])
            ->all();

        $data = array_map([$this, 'formatProduto'], $produtos);

        return $this->asSuccess([
            'data'  => $data,
            'total' => count($data),
        ]);
    }

    /**
     * Visualiza um produto específico.
     */
    public function actionView($id)
    {
        $lojaId = $this->getLojaIdFromRequest();
        $produto = $this->findProduto($id, $lojaId);
        return $this->asSuccess([
            'data' => $this->formatProduto($produto),
        ]);
    }

    /**
     * Cria um novo produto.
     */
    public function actionCreate()
    {
        $lojaId = $this->getLojaIdFromRequest();
        $request = Yii::$app->request->post();

        $produto = new Produto();
        $produto->loja_id = $lojaId;
        $produto->attributes = $request;

        if (empty($produto->slug)) {
            $produto->slug = Inflector::slug($produto->nome);
        }

        if (empty($produto->tipo)) {
            $produto->tipo = Produto::TIPO_SIMPLES;
        }
        $produto->disponivel = isset($request['disponivel']) ? (int)$request['disponivel'] : 1;
        $produto->ativo      = isset($request['ativo'])      ? (int)$request['ativo']      : 1;
        $produto->destaque   = isset($request['destaque'])   ? (int)$request['destaque']   : 0;

        if (isset($request['estoque']) && $request['estoque'] !== '' && $request['estoque'] != -1) {
            $produto->estoque = (int)$request['estoque'];
        } else {
            $produto->estoque = null;
        }

        if ($produto->validate() && $produto->save()) {
            return $this->asSuccess([
                'data'    => $this->formatProduto($produto),
                'message' => 'Produto criado com sucesso',
            ]);
        }

        Yii::$app->response->statusCode = 422;
        return $this->asError($produto->getErrors());
    }

    /**
     * Atualiza um produto existente.
     */
    public function actionUpdate($id)
    {
        $lojaId = $this->getLojaIdFromRequest();
        $produto = $this->findProduto($id, $lojaId);
        $request = Yii::$app->request->getBodyParams();

        $produto->attributes = $request;

        if (empty($produto->slug)) {
            $produto->slug = Inflector::slug($produto->nome);
        }

        if (isset($request['disponivel'])) {
            $produto->disponivel = (int)$request['disponivel'];
        }
        if (isset($request['ativo'])) {
            $produto->ativo = (int)$request['ativo'];
        }
        if (isset($request['destaque'])) {
            $produto->destaque = (int)$request['destaque'];
        }

        if (array_key_exists('estoque', $request)) {
            if ($request['estoque'] === '' || $request['estoque'] === null || $request['estoque'] == -1) {
                $produto->estoque = null;
            } else {
                $produto->estoque = (int)$request['estoque'];
            }
        }

        if ($produto->validate() && $produto->save()) {
            return $this->asSuccess([
                'data'    => $this->formatProduto($produto),
                'message' => 'Produto atualizado com sucesso',
            ]);
        }

        Yii::$app->response->statusCode = 422;
        return $this->asError($produto->getErrors());
    }

    /**
     * Remove um produto (soft delete).
     */
    public function actionDelete($id)
    {
        $lojaId = $this->getLojaIdFromRequest();
        $produto = $this->findProduto($id, $lojaId);

        if ($produto->softDelete()) {
            return $this->asSuccess(['message' => 'Produto removido com sucesso']);
        }

        Yii::$app->response->statusCode = 500;
        return $this->asError('Erro ao remover produto');
    }

    /**
     * Alterna a disponibilidade do produto.
     */
    public function actionToggle($id)
    {
        $lojaId = $this->getLojaIdFromRequest();
        $produto = $this->findProduto($id, $lojaId);
        $request = Yii::$app->request->getBodyParams();

        if (!isset($request['disponivel']) || !in_array($request['disponivel'], [0, 1], true)) {
            throw new BadRequestHttpException('O campo "disponivel" é obrigatório e deve ser 0 ou 1.');
        }

        $produto->disponivel = (int)$request['disponivel'];

        if ($produto->save()) {
            return $this->asSuccess([
                'data'    => $this->formatProduto($produto),
                'message' => $produto->disponivel ? 'Produto ativado' : 'Produto desativado',
            ]);
        }

        Yii::$app->response->statusCode = 422;
        return $this->asError($produto->getErrors());
    }

    /**
     * Atualiza APENAS o estoque.
     */
    public function actionEstoque($id)
    {
        $lojaId = $this->getLojaIdFromRequest();
        $produto = $this->findProduto($id, $lojaId);
        $request = Yii::$app->request->getBodyParams();

        if (!array_key_exists('estoque', $request)) {
            throw new BadRequestHttpException('O campo "estoque" é obrigatório.');
        }

        if ($request['estoque'] === '' || $request['estoque'] === null || $request['estoque'] == -1) {
            $produto->estoque = null;
        } else {
            $estoque = (int)$request['estoque'];
            if ($estoque < 0) {
                throw new BadRequestHttpException('O estoque não pode ser negativo.');
            }
            $produto->estoque = $estoque;
        }

        if ($produto->save()) {
            return $this->asSuccess([
                'data'    => $this->formatProduto($produto),
                'message' => 'Estoque atualizado com sucesso',
            ]);
        }

        Yii::$app->response->statusCode = 422;
        return $this->asError($produto->getErrors());
    }

    // ============ MÉTODOS AUXILIARES ============

    /**
     * Busca um produto verificando se pertence à loja.
     */
    protected function findProduto($id, $lojaId)
    {
        $produto = Produto::find()
            ->where([
                'id'        => $id,
                'loja_id'   => $lojaId,
            ])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$produto) {
            throw new NotFoundHttpException('Produto não encontrado ou não pertence a esta loja.');
        }

        return $produto;
    }

    /**
     * Formata os dados do produto para resposta.
     */
    protected function formatProduto(Produto $produto)
    {
        $data = $produto->toArray();

        $subcategoria = $produto->subcategoria;
        $categoria = $subcategoria ? $subcategoria->categoria : null;

        $data['categoria_id']     = $categoria ? $categoria->id : null;
        $data['categoria_nome']   = $categoria ? $categoria->nome : null;
        $data['categoria_icone']  = $categoria ? $categoria->icone : null;
        $data['categoria_cor']    = $categoria ? $categoria->cor : null;
        $data['subcategoria_id']  = $subcategoria ? $subcategoria->id : null;
        $data['subcategoria_nome']= $subcategoria ? $subcategoria->nome : null;

        $data['preco_formatado']  = $produto->getPrecoFormatado();
        $data['preco_atual']      = $produto->getPrecoAtual();
        $data['disponivel_agora'] = $produto->isDisponivelAgora();
        $data['status_estoque']   = $produto->getStatusEstoque();

        if ($produto->temOpcoes()) {
            $data['opcoes'] = $produto->getOpcoesFormatadas();
        }

        return $data;
    }

    protected function asSuccess(array $data = [])
    {
        return array_merge([
            'success'   => true,
            'timestamp' => date('c'),
        ], $data);
    }

    protected function asError($errors, $statusCode = 400)
    {
        Yii::$app->response->statusCode = $statusCode;
        return [
            'success'   => false,
            'errors'    => $errors,
            'timestamp' => date('c'),
        ];
    }
}