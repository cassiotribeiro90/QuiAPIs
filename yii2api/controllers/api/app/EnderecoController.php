<?php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Endereco;
use app\models\api\app\Usuario;
use app\controllers\api\app\AppControllerBase;

class EnderecoController extends AppControllerBase
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'buscar-cep',
                'create',
                'index',
                'view',
                'update',
                'delete',
                'set-padrao',
            ];
        }
        
        return $behaviors;
    }

    private function getOrCreateUsuarioByDeviceId()
    {
        $request = Yii::$app->request;
        $deviceId = $request->post('device_id') ?? 
                    $request->getHeaders()->get('X-Device-Id');

        if (!$deviceId) {
            Yii::warning("Device ID não informado", __METHOD__);
            return null;
        }

        $usuario = Usuario::find()
            ->where(['device_id' => $deviceId])
            ->one();

        if (!$usuario) {
            try {
                $usuario = new Usuario();
                $usuario->device_id = $deviceId;
                $usuario->status = 'convidado';
                $usuario->nome = null;
                $usuario->email = null;
                $usuario->auth_key = Yii::$app->security->generateRandomString(32);
                $usuario->tipo = 'cliente';
                $usuario->pref_tema = 'auto';
                $usuario->senha_hash = null;
                $usuario->cpf = null;
                $usuario->telefone = null;
                $usuario->whatsapp = null;
                $usuario->data_nascimento = null;
                $usuario->avatar = null;
                $usuario->ultimo_login_em = date('Y-m-d H:i:s');
                $usuario->save(false);
                
                Yii::info("✅ Usuário convidado criado: ID {$usuario->id}, device_id: $deviceId", __METHOD__);
            } catch (\Exception $e) {
                Yii::error("❌ Erro ao criar usuário convidado: " . $e->getMessage(), __METHOD__);
                return null;
            }
        }

        return $usuario;
    }

    private function gerarTokenParaUsuario(Usuario $usuario)
    {
        $token = Yii::$app->security->generateRandomString(64);
        $usuario->access_token = $token;
        $usuario->access_token_expira_em = date('Y-m-d H:i:s', time() + 7200);
        $usuario->save(false);
        return $token;
    }

    public function actionIndex()
    {
        try {
            $usuario = $this->getOrCreateUsuarioByDeviceId();
            
            if (!$usuario) {
                return ApiResponse::error('Device ID não informado', 400);
            }

            $enderecos = Endereco::find()
                ->where(['usuario_id' => $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->andWhere(['ativo' => 1])
                ->orderBy(['padrao' => SORT_DESC, 'criado_em' => SORT_DESC])
                ->all();

            $data = array_map(function($endereco) {
                return $this->formatEndereco($endereco);
            }, $enderecos);

            return ApiResponse::success($data);

        } catch (\Exception $e) {
            Yii::error("Erro ao listar endereços: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao listar endereços: ' . $e->getMessage(), 500);
        }
    }

    public function actionView($id)
    {
        try {
            $usuario = $this->getOrCreateUsuarioByDeviceId();
            
            if (!$usuario) {
                return ApiResponse::error('Device ID não informado', 400);
            }

            $endereco = Endereco::find()
                ->where(['id' => $id, 'usuario_id' => $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->one();

            if (!$endereco) {
                return ApiResponse::error('Endereço não encontrado', 404);
            }

            return ApiResponse::success($this->formatEndereco($endereco));

        } catch (\Exception $e) {
            Yii::error("Erro ao buscar endereço: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao buscar endereço', 500);
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $debug = [];

        try {
            $deviceId = $request->post('device_id') ?? 
                        $request->getHeaders()->get('X-Device-Id');
            $debug['device_id_recebido'] = $deviceId;

            if (!$deviceId) {
                return ApiResponse::error('Device ID não informado', 400, $debug);
            }

            $usuario = $this->getOrCreateUsuarioByDeviceId();
            if (!$usuario) {
                return ApiResponse::error('Falha ao criar usuário convidado', 500, $debug);
            }
            $debug['usuario_id'] = $usuario->id;

            $token = $this->gerarTokenParaUsuario($usuario);
            $debug['token_gerado'] = true;
            $debug['dados_recebidos'] = $request->post();

            $endereco = new Endereco();
            $endereco->usuario_id = $usuario->id;
            $endereco->tipo = $request->post('tipo', Endereco::TIPO_ENTREGA);
            $endereco->apelido = $request->post('label');
            $endereco->cep = $request->post('cep');
            $endereco->logradouro = $request->post('logradouro');
            $endereco->numero = $request->post('numero');
            $endereco->complemento = $request->post('complemento');
            $endereco->bairro = $request->post('bairro');
            $endereco->cidade = $request->post('cidade');
            $endereco->uf = $request->post('uf');
            $endereco->latitude = $request->post('latitude');
            $endereco->longitude = $request->post('longitude');
            $endereco->referencia = $request->post('referencia');
            $endereco->destinatario = $request->post('destinatario');
            $endereco->telefone_contato = $request->post('telefone_contato');

            $debug['endereco_antes_salvar'] = $endereco->attributes;

            $endereco->padrao = 0;
            $endereco->ativo = 1;

            if (!$endereco->save()) {
                $errors = [];
                foreach ($endereco->errors as $field => $fieldErrors) {
                    $errors[$field] = implode(', ', $fieldErrors);
                }
                $debug['erros_validacao'] = $errors;
                return ApiResponse::error('Erro de validação: ' . json_encode($errors), 400, $debug);
            }

            Endereco::updateAll(
                ['padrao' => 0],
                ['and', ['usuario_id' => $usuario->id], ['<>', 'id', $endereco->id]]
            );

            $endereco->padrao = 1;
            $endereco->save(false);

            $debug['endereco_id'] = $endereco->id;
            $debug['endereco_salvo'] = true;

            return ApiResponse::success([
                'token' => $token,
                'usuario' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'status' => $usuario->status,
                ],
                'endereco' => $this->formatEndereco($endereco),
                'debug' => $debug,
            ], 'Endereço criado com sucesso', 201);

        } catch (\Exception $e) {
            $debug['excecao'] = $e->getMessage();
            $debug['arquivo'] = $e->getFile();
            $debug['linha'] = $e->getLine();
            return ApiResponse::error('Erro ao criar endereço: ' . $e->getMessage(), 500, $debug);
        }
    }

    public function actionUpdate($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $usuario = $this->getOrCreateUsuarioByDeviceId();
            
            if (!$usuario) {
                return ApiResponse::error('Device ID não informado', 400);
            }

            $endereco = Endereco::find()
                ->where(['id' => $id, 'usuario_id' => $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->one();

            if (!$endereco) {
                return ApiResponse::error('Endereço não encontrado', 404);
            }

            $request = Yii::$app->request;

            $endereco->tipo = $request->post('tipo', $endereco->tipo);
            $endereco->apelido = $request->post('label', $endereco->apelido);
            $endereco->cep = $request->post('cep', $endereco->cep);
            $endereco->logradouro = $request->post('logradouro', $endereco->logradouro);
            $endereco->numero = $request->post('numero', $endereco->numero);
            $endereco->complemento = $request->post('complemento', $endereco->complemento);
            $endereco->bairro = $request->post('bairro', $endereco->bairro);
            $endereco->cidade = $request->post('cidade', $endereco->cidade);
            $endereco->uf = $request->post('uf', $endereco->uf);
            $endereco->latitude = $request->post('latitude', $endereco->latitude);
            $endereco->longitude = $request->post('longitude', $endereco->longitude);
            $endereco->referencia = $request->post('referencia', $endereco->referencia);
            $endereco->destinatario = $request->post('destinatario', $endereco->destinatario);
            $endereco->telefone_contato = $request->post('telefone_contato', $endereco->telefone_contato);

            if (!$endereco->save()) {
                $errors = [];
                foreach ($endereco->errors as $field => $fieldErrors) {
                    $errors[$field] = implode(', ', $fieldErrors);
                }
                return ApiResponse::error('Erro ao atualizar endereço: ' . json_encode($errors), 400);
            }

            $transaction->commit();

            return ApiResponse::success(
                $this->formatEndereco($endereco),
                'Endereço atualizado com sucesso'
            );

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao atualizar endereço: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao atualizar endereço: ' . $e->getMessage(), 500);
        }
    }

    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $usuario = $this->getOrCreateUsuarioByDeviceId();
            
            if (!$usuario) {
                return ApiResponse::error('Device ID não informado', 400);
            }

            $endereco = Endereco::find()
                ->where(['id' => $id, 'usuario_id' => $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->one();

            if (!$endereco) {
                return ApiResponse::error('Endereço não encontrado', 404);
            }

            if ($endereco->padrao == 1) {
                $outroEndereco = Endereco::find()
                    ->where(['usuario_id' => $usuario->id])
                    ->andWhere(['deletado_em' => null])
                    ->andWhere(['!=', 'id', $id])
                    ->andWhere(['ativo' => 1])
                    ->orderBy(['criado_em' => SORT_ASC])
                    ->one();
                
                if ($outroEndereco) {
                    $outroEndereco->padrao = 1;
                    $outroEndereco->save(false);
                }
            }

            $endereco->softDelete();

            $transaction->commit();

            return ApiResponse::success(null, 'Endereço removido com sucesso');

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao remover endereço: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao remover endereço: ' . $e->getMessage(), 500);
        }
    }

    public function actionSetPadrao($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $usuario = $this->getOrCreateUsuarioByDeviceId();
            
            if (!$usuario) {
                return ApiResponse::error('Device ID não informado', 400);
            }

            $endereco = Endereco::find()
                ->where(['id' => $id, 'usuario_id' => $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->one();

            if (!$endereco) {
                return ApiResponse::error('Endereço não encontrado', 404);
            }

            if (!$endereco->setComoPadrao()) {
                return ApiResponse::error('Erro ao definir endereço como padrão', 400);
            }

            $endereco = Endereco::findOne($id);

            $transaction->commit();

            return ApiResponse::success(
                $this->formatEndereco($endereco),
                'Endereço definido como padrão'
            );

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Erro ao definir endereço como padrão: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao definir endereço como padrão: ' . $e->getMessage(), 500);
        }
    }

    public function actionBuscarCep()
    {
        try {
            $request = Yii::$app->request;
            $cep = $request->post('cep');
            
            if (empty($cep)) {
                return ApiResponse::error('CEP é obrigatório', 400);
            }

            $cep = preg_replace('/\D/', '', $cep);
            
            if (strlen($cep) != 8) {
                return ApiResponse::error('CEP inválido (8 dígitos)', 400);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$cep/json/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode != 200) {
                throw new \Exception('Erro ao buscar CEP');
            }

            $data = json_decode($response, true);

            if (isset($data['erro']) && $data['erro'] === true) {
                return ApiResponse::error('CEP não encontrado', 404);
            }

            return ApiResponse::success([
                'cep' => $data['cep'] ?? '',
                'logradouro' => $data['logradouro'] ?? '',
                'bairro' => $data['bairro'] ?? '',
                'cidade' => $data['localidade'] ?? '',
                'uf' => $data['uf'] ?? '',
            ]);

        } catch (\Exception $e) {
            Yii::error("Erro ao buscar CEP: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Erro ao buscar CEP: ' . $e->getMessage(), 500);
        }
    }

    private function formatEndereco($endereco)
    {
        return [
            'id' => (int)$endereco->id,
            'usuario_id' => (int)$endereco->usuario_id,
            'tipo' => $endereco->tipo ?? 'entrega',
            'label' => $endereco->apelido,
            'cep' => $endereco->cep,
            'logradouro' => $endereco->logradouro,
            'numero' => $endereco->numero,
            'complemento' => $endereco->complemento,
            'bairro' => $endereco->bairro,
            'cidade' => $endereco->cidade,
            'uf' => $endereco->uf,
            'latitude' => $endereco->latitude ? (float)$endereco->latitude : null,
            'longitude' => $endereco->longitude ? (float)$endereco->longitude : null,
            'referencia' => $endereco->referencia,
            'destinatario' => $endereco->destinatario,
            'telefone_contato' => $endereco->telefone_contato,
            'principal' => (bool)$endereco->padrao,
            'ativo' => (bool)$endereco->ativo,
            'endereco_completo' => $endereco->getEnderecoCompleto(),
            'endereco_resumido' => $endereco->getEnderecoResumido(),
            'criado_em' => $endereco->criado_em,
            'atualizado_em' => $endereco->atualizado_em,
        ];
    }
}