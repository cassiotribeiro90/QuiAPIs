<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\models\api\app\Loja;

/**
 * Controller para o lojista gerenciar sua própria loja.
 * 
 * Rotas (prefixo /api/lojista/loja):
 * - GET  /                 -> visualizar dados da loja
 * - PUT  /                 -> atualizar dados da loja (via body JSON)
 * - POST /                 -> alternativa para atualizar (se preferir)
 * 
 * Todos os endpoints exigem autenticação via Bearer Token (userLojista).
 */
class LojaController extends LojistaControllerBase
{
    /**
     * Visualiza os dados da loja do lojista autenticado.
     * 
     * @return array
     */
    public function actionIndex()
    {
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            Yii::$app->response->statusCode = 401;
            return $this->asError('Lojista não autenticado', 401);
        }

        $loja = $this->findLoja($lojistaId);
        return $this->asSuccess([
            'data' => $this->formatLoja($loja),
        ]);
    }

    /**
     * Atualiza os dados da loja (apenas campos permitidos).
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
        $lojistaId = $this->getLojistaId();
        if (!$lojistaId) {
            Yii::$app->response->statusCode = 401;
            return $this->asError('Lojista não autenticado', 401);
        }

        $loja = $this->findLoja($lojistaId);
        $request = Yii::$app->request->getBodyParams();

        // Campos permitidos para o lojista editar
        $allowedFields = ['nome', 'descricao', 'cor_tema', 'whatsapp', 'email', 'instagram'];
        $hasChanges = false;

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $request)) {
                // Se for string vazia, permitir null (para campos opcionais)
                $value = $request[$field];
                if ($value === '' && in_array($field, ['descricao', 'whatsapp', 'email', 'instagram', 'cor_tema'])) {
                    $loja->$field = null;
                } else {
                    $loja->$field = $value;
                }
                $hasChanges = true;
            }
        }

        if (!$hasChanges) {
            throw new BadRequestHttpException('Nenhum campo válido para atualizar.');
        }

        // Validação básica
        if (isset($request['email']) && $request['email'] !== '' && !filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException('E-mail inválido.');
        }

        if ($loja->save()) {
            return $this->asSuccess([
                'data' => $this->formatLoja($loja),
                'message' => 'Loja atualizada com sucesso!',
            ]);
        }

        Yii::$app->response->statusCode = 422;
        return $this->asError($loja->getErrors());
    }

    /**
     * Busca a loja do lojista pelo ID do lojista.
     * 
     * @param int $lojistaId
     * @return Loja
     * @throws NotFoundHttpException
     */
    protected function findLoja($lojistaId)
    {
        $loja = Loja::find()
            ->where(['id' => $lojistaId])
            ->andWhere(['deletado_em' => null])
            ->one();

        if (!$loja) {
            throw new NotFoundHttpException('Loja não encontrada para este lojista.');
        }

        return $loja;
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
            'cor_tema' => $loja->cor_tema,
            'whatsapp' => $loja->whatsapp,
            'email' => $loja->email,
            'instagram' => $loja->instagram,
            'logo' => $loja->logo,
            'capa' => $loja->capa,
            'categoria' => $loja->categoria,
            'status' => $loja->status,
            'verificado' => (bool)$loja->verificado,
            'destaque' => (bool)$loja->destaque,
            'nota_media' => (float)$loja->nota_media,
            'total_avaliacoes' => (int)$loja->total_avaliacoes,
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