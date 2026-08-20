<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\web\BadRequestHttpException;
use app\models\api\gestor\Subcategoria;

/**
 * Controller para subcategorias (acesso do lojista)
 * 
 * Rotas:
 * - GET /api/lojista/subcategoria/por-categoria?id={categoriaId} -> listar subcategorias de uma categoria
 */
class SubcategoriaController extends LojistaControllerBase
{
    /**
     * Lista subcategorias por categoria
     */
    public function actionPorCategoria()
    {
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            Yii::$app->response->statusCode = 401;
            return $this->asError('Lojista não autenticado', 401);
        }

        $categoriaId = Yii::$app->request->get('id');
        if (!$categoriaId) {
            throw new BadRequestHttpException('Parâmetro "id" (categoria) é obrigatório.');
        }

        $subcategorias = Subcategoria::find()
            ->where(['categoria_id' => $categoriaId, 'ativo' => 1])
            ->orderBy(['nome' => SORT_ASC])
            ->all();

        $data = array_map(function($sub) {
            return [
                'id' => $sub->id,
                'nome' => $sub->nome,
                'categoria_id' => $sub->categoria_id,
            ];
        }, $subcategorias);

        return $this->asSuccess([
            'data' => $data,
            'total' => count($data),
        ]);
    }

    // ============ MÉTODOS AUXILIARES ============

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
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    protected function asError($message, $statusCode = 400)
    {
        Yii::$app->response->statusCode = $statusCode;
        return [
            'success' => false,
            'error' => $message,
            'timestamp' => date('c'),
        ];
    }
}