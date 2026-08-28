<?php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\models\api\app\Endereco;
use app\models\api\app\Usuario;
use app\controllers\api\app\AppControllerBase;
use GuzzleHttp\Client;

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

    /**
     * ✅ CORRIGIDO: Prioriza token autenticado, depois device_id (só convidado)
     */
    private function getOrCreateUsuarioByDeviceId()
    {
        $request = Yii::$app->request;
        $deviceId = $request->post('device_id') ??
                    $request->getHeaders()->get('X-Device-Id');

        // ✅ 1. SEMPRE prioriza o token autenticado
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader);
            $usuario = Usuario::find()
                ->where(['access_token' => $token])
                ->andWhere(['>', 'access_token_expira_em', date('Y-m-d H:i:s')])
                ->one();

            if ($usuario) {
                return $usuario;
            }
        }

        // ✅ 2. Se NÃO tem token válido, tenta device_id (apenas convidado)
        if ($deviceId) {
            $usuario = Usuario::find()
                ->where(['device_id' => $deviceId])
                ->andWhere(['status' => 'convidado'])
                ->andWhere(['deletado_em' => null])
                ->one();

            if ($usuario) {
                return $usuario;
            }
        }

        // ✅ 3. Cria novo convidado apenas se tiver device_id
        if ($deviceId) {
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
                return $usuario;
            } catch (\Exception $e) {
                Yii::error("❌ Erro ao criar usuário convidado: " . $e->getMessage(), __METHOD__);
                return null;
            }
        }

        Yii::warning("Device ID não informado", __METHOD__);
        return null;
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

        // 🔥 LOG DA REQUISIÇÃO COMPLETA (PAYLOAD)
        Yii::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", __METHOD__);
        Yii::info("📝 [CREATE] NOVO ENDEREÇO RECEBIDO", __METHOD__);
        Yii::info("📝 [CREATE] PAYLOAD: " . json_encode($request->post(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), __METHOD__);
        Yii::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", __METHOD__);

        try {
            // ✅ 1. Tenta obter usuário pelo token (autenticado)
            $authHeader = $request->getHeaders()->get('Authorization');
            $usuario = null;

            if ($authHeader) {
                $token = str_replace('Bearer ', '', $authHeader);
                $usuario = Usuario::find()
                    ->where(['access_token' => $token])
                    ->andWhere(['>', 'access_token_expira_em', date('Y-m-d H:i:s')])
                    ->one();
            }

            // ✅ 2. Se NÃO tem token válido, usa device_id (convidado)
            if (!$usuario) {
                $deviceId = $request->post('device_id') ??
                            $request->getHeaders()->get('X-Device-Id');

                if (!$deviceId) {
                    return ApiResponse::error('Device ID não informado', 400, $debug);
                }

                $usuario = $this->getOrCreateUsuarioByDeviceId();
            }

            if (!$usuario) {
                return ApiResponse::error('Falha ao identificar usuário', 500, $debug);
            }

            $debug['usuario_id'] = $usuario->id;
            $debug['usuario_status'] = $usuario->status;

            // ✅ Só gera token novo se for convidado
            $token = null;
            if ($usuario->status == 'convidado') {
                $token = $this->gerarTokenParaUsuario($usuario);
                $debug['token_gerado'] = true;
            }

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
            
            // 🔥 Mantém coordenadas do CEP (se vier do frontend)
            $latitude = $request->post('latitude');
            $longitude = $request->post('longitude');
            
            $endereco->latitude = $latitude ? (float)$latitude : null;
            $endereco->longitude = $longitude ? (float)$longitude : null;
            
            $endereco->referencia = $request->post('referencia');
            $endereco->destinatario = $request->post('destinatario');
            $endereco->telefone_contato = $request->post('telefone_contato');

            $endereco->padrao = 0;
            $endereco->ativo = 1;
            $endereco->deletado_em = null;

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
                ['and',
                    ['usuario_id' => $usuario->id],
                    ['<>', 'id', $endereco->id],
                    ['deletado_em' => null],
                ]
            );

            $endereco->padrao = 1;
            $endereco->save(false);

            // 🔥 Tenta enriquecer com o endereço completo (com número)
            // Se conseguir, substitui pela coordenada mais precisa
            // Se não, mantém a original (do CEP)
            $this->enriquecerCoordenadas($endereco);
            $endereco->save(false);

            $debug['endereco_id'] = $endereco->id;
            $debug['endereco_salvo'] = true;

            $responseData = [
                'usuario' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'status' => $usuario->status,
                ],
                'endereco' => $this->formatEndereco($endereco),
            ];

            if ($token) {
                $responseData['token'] = $token;
            }

            $responseData['debug'] = $debug;

            return ApiResponse::success($responseData, 'Endereço criado com sucesso', 201);
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
            
            // 🔥 Atualiza coordenadas se vier do frontend
            $latitude = $request->post('latitude');
            $longitude = $request->post('longitude');
            
            if ($latitude !== null && $longitude !== null) {
                $endereco->latitude = (float)$latitude;
                $endereco->longitude = (float)$longitude;
            }
            
            $endereco->referencia = $request->post('referencia', $endereco->referencia);
            $endereco->destinatario = $request->post('destinatario', $endereco->destinatario);
            $endereco->telefone_contato = $request->post('telefone_contato', $endereco->telefone_contato);

            $endereco->ativo = 1;
            $endereco->deletado_em = null;

            // 🔥 Tenta enriquecer com o endereço completo (com número)
            $this->enriquecerCoordenadas($endereco);

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

            Endereco::updateAll(
                ['padrao' => 0],
                ['and',
                    ['usuario_id' => $usuario->id],
                    ['deletado_em' => null],
                ]
            );

            $endereco->padrao = 1;
            $endereco->ativo = 1;
            $endereco->save(false);

            $transaction->commit();

            $enderecos = Endereco::find()
                ->where(['usuario_id' => $usuario->id])
                ->andWhere(['deletado_em' => null])
                ->andWhere(['ativo' => 1])
                ->all();

            $data = array_map(function($endereco) {
                return $this->formatEndereco($endereco);
            }, $enderecos);

            return ApiResponse::success($data, 'Endereço definido como padrão');
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

    /**
     * 🔥 Busca coordenadas via Nominatim (OpenStreetMap)
     * Com logs detalhados para depuração
     */
    private function buscarCoordenadasNominatim($enderecoCompleto)
    {
        try {
            $client = new Client(['timeout' => 15]);

            $queryParams = [
                'q' => $enderecoCompleto,
                'format' => 'json',
                'limit' => 1,
                'accept-language' => 'pt-BR',
                'addressdetails' => 1,
            ];

            $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query($queryParams);
            
            Yii::info("📡 [NOMINATIM] URL: $url", __METHOD__);

            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => $queryParams,
                'headers' => [
                    'User-Agent' => 'QuiPede/1.0 (contato@quipede.com.br)',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            Yii::info("📡 [NOMINATIM] Resposta: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), __METHOD__);

            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                $lat = (float)$data[0]['lat'];
                $lon = (float)$data[0]['lon'];
                
                if ($lat >= -34 && $lat <= 5 && $lon >= -74 && $lon <= -34) {
                    Yii::info("✅ [NOMINATIM] Coordenadas obtidas: $lat, $lon", __METHOD__);
                    return ['lat' => $lat, 'lng' => $lon];
                } else {
                    Yii::warning("⚠️ [NOMINATIM] Coordenadas fora do Brasil: $lat, $lon", __METHOD__);
                }
            } else {
                Yii::warning("⚠️ [NOMINATIM] Nenhum resultado encontrado para: $enderecoCompleto", __METHOD__);
            }
        } catch (\Exception $e) {
            Yii::error("❌ [NOMINATIM] Erro: " . $e->getMessage(), __METHOD__);
        }

        return null;
    }

    /**
     * 🔥 Enriquecimento com fallbacks
     * 1. Nominatim com endereço completo (número)
     * 2. Nominatim com CEP (fallback)
     * 3. Mantém coordenada original (se existir)
     * 4. Coordenada padrão (Belo Horizonte)
     */
    private function enriquecerCoordenadas(Endereco $endereco)
    {
        // 🔥 Constrói o endereço completo com número
        $partes = [];
        
        if (!empty($endereco->logradouro)) {
            $partes[] = trim($endereco->logradouro);
        }
        
        if (!empty($endereco->numero)) {
            $partes[] = trim($endereco->numero);
        }
        
        if (!empty($endereco->complemento)) {
            $partes[] = trim($endereco->complemento);
        }
        
        if (!empty($endereco->bairro)) {
            $partes[] = trim($endereco->bairro);
        }
        
        if (!empty($endereco->cidade)) {
            $partes[] = trim($endereco->cidade);
        }
        
        if (!empty($endereco->uf)) {
            $partes[] = trim($endereco->uf);
        }
        
        $partes[] = 'Brasil';
        
        $enderecoCompleto = implode(', ', array_filter($partes));

        // 🔥 Salva coordenada original (se existir)
        $latOriginal = $endereco->latitude;
        $lngOriginal = $endereco->longitude;

        Yii::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", __METHOD__);
        Yii::info("🔍 [COORD] INICIANDO ENRIQUECIMENTO", __METHOD__);
        Yii::info("🔍 [COORD] ID: " . ($endereco->id ?? 'NOVO'), __METHOD__);
        Yii::info("🔍 [COORD] Endereço: $enderecoCompleto", __METHOD__);
        Yii::info("🔍 [COORD] Coordenada original: lat=$latOriginal, lng=$lngOriginal", __METHOD__);
        Yii::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", __METHOD__);

        // 🔥 1. Tenta com endereço completo + número
        $coordenadas = $this->buscarCoordenadasNominatim($enderecoCompleto);
        
        if ($coordenadas) {
            $endereco->latitude = $coordenadas['lat'];
            $endereco->longitude = $coordenadas['lng'];
            Yii::info("✅ [COORD] Substituído por Nominatim (endereço completo): {$endereco->latitude}, {$endereco->longitude}", __METHOD__);
            return true;
        }

        // 🔥 2. Tenta com CEP (fallback)
        if (!empty($endereco->cep)) {
            $cep = preg_replace('/\D/', '', $endereco->cep);
            $enderecoCep = "CEP $cep, " . trim($endereco->cidade) . ", " . trim($endereco->uf) . ", Brasil";
            
            Yii::info("🔄 [COORD] Tentando com CEP: $enderecoCep", __METHOD__);
            
            $coordenadas = $this->buscarCoordenadasNominatim($enderecoCep);
            if ($coordenadas) {
                $endereco->latitude = $coordenadas['lat'];
                $endereco->longitude = $coordenadas['lng'];
                Yii::info("✅ [COORD] Substituído por CEP: {$endereco->latitude}, {$endereco->longitude}", __METHOD__);
                return true;
            }
        }

        // 🔥 3. Nenhuma coordenada melhor encontrada → mantém a original (se existir)
        if ($latOriginal !== null && $lngOriginal !== null) {
            $endereco->latitude = $latOriginal;
            $endereco->longitude = $lngOriginal;
            Yii::info("⚠️ [COORD] Nenhuma coordenada melhor encontrada. Mantendo original: $latOriginal, $lngOriginal", __METHOD__);
            return false;
        }

        // 🔥 4. Último fallback: coordenada padrão (Belo Horizonte)
        Yii::warning("⚠️ [COORD] Nenhuma coordenada disponível! Usando coordenada padrão", __METHOD__);
        $endereco->latitude = -19.8271886;
        $endereco->longitude = -43.9555711;
        
        return false;
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