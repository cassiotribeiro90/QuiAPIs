<?php

namespace app\controllers\api\lojista;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\ConflictHttpException;
use app\models\api\lojista\LojistaUsuario;

class AuthLojistaController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Formato JSON para todas as respostas
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // CORS para API
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    /**
     * Cria um novo usuário lojista
     * 
     * POST /auth-lojista/create
     */
    public function actionCreate()
    {    
        $request = Yii::$app->request;
        $data = $request->bodyParams;

        // Validações básicas
        if (empty($data['nome'])) {
            throw new BadRequestHttpException('Nome é obrigatório');
        }
        if (empty($data['email'])) {
            throw new BadRequestHttpException('E-mail é obrigatório');
        }
        if (empty($data['password'])) {
            throw new BadRequestHttpException('Senha é obrigatória');
        }

        // Verificar se e-mail já existe
        if (LojistaUsuario::find()->where(['email' => $data['email']])->exists()) {
            throw new ConflictHttpException('E-mail já cadastrado');
        }

        // Criar usuário
        $usuario = new LojistaUsuario();
        $usuario->nome = $data['nome'];
        $usuario->email = $data['email'];
        $usuario->cpf_cnpj = $data['cpf_cnpj'];
        $usuario->setSenha($data['password']);
        $usuario->generateAuthKey();
        $usuario->generateTokenAcesso();
        
        // Campos opcionais
        if (!empty($data['telefone'])) {
            $usuario->telefone = $data['telefone'];
        }
        
        if (!empty($data['cpf_cnpj'])) {
            $usuario->cpf_cnpj = $data['cpf_cnpj'];
        }

        if ($usuario->save()) {
            return [
                'success' => true,
                'message' => 'Lojista criado com sucesso',
                'data' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'access_token' => $usuario->access_token,
                ]
            ];
        } else {
            Yii::error('Erro ao criar lojista: ' . json_encode($usuario->errors));
            
            return [
                'success' => false,
                'message' => 'Erro ao criar lojista',
                'errors' => $usuario->errors
            ];
        }
    }
}