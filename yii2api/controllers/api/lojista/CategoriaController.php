<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\api\gestor\Categoria;

/**
 * Controller para categorias (acesso do lojista)
 * 
 * Rotas:
 * - GET /api/lojista/categorias -> listar categorias ativas
 * - GET /api/lojista/categorias/options -> opções para selects
 */
class CategoriaController extends LojistaControllerBase
{
    /**
     * Lista todas as categorias ativas, ordenadas por nome
     */
    public function actionIndex()
    {
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            Yii::$app->response->statusCode = 401;
            return $this->asError('Lojista não autenticado', 401);
        }

        $categorias = Categoria::find()
            ->where(['ativo' => 1])
            ->orderBy(['nome' => SORT_ASC])
            ->all();

        $data = array_map(function($categoria) {
            return [
                'id' => $categoria->id,
                'nome' => $categoria->nome,
                'icone' => $categoria->icone,
                'cor' => $categoria->cor,
                'slug' => $categoria->slug,
            ];
        }, $categorias);

        return $this->asSuccess([
            'data' => $data,
            'total' => count($data),
        ]);
    }

    /**
     * Retorna opções simplificadas para selects (frontend)
     */
    public function actionOptions()
    {
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            Yii::$app->response->statusCode = 401;
            return $this->asError('Lojista não autenticado', 401);
        }

        $categorias = Categoria::find()
            ->select(['id', 'nome', 'icone', 'cor'])
            ->where(['ativo' => 1])
            ->orderBy(['nome' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->asSuccess([
            'data' => $categorias,
            'total' => count($categorias),
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