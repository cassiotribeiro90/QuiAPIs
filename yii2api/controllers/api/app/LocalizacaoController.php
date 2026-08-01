<?php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\controllers\api\app\AppControllerBase;
use GuzzleHttp\Client;

/**
 * Controller para geocodificação e busca de endereços.
 * Todos os endpoints são públicos (não exigem autenticação).
 */
class LocalizacaoController extends AppControllerBase
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     * Remove autenticação de todas as ações.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'geocodificar',
                'buscar-endereco',
                'confirmar-endereco',
            ];
        }
        
        return $behaviors;
    }

    /**
     * GET /api/app/localizacao/geocodificar
     * Converte coordenadas (lat/lon) em endereço completo.
     * 
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function actionGeocodificar($latitude = null, $longitude = null)
    {
        $request = Yii::$app->request;
        $latitude = $latitude ?: $request->get('latitude');
        $longitude = $longitude ?: $request->get('longitude');
        
        if (!$latitude || !$longitude) {
            return ApiResponse::error('Latitude e longitude são obrigatórios', 400);
        }
        
        try {
            $client = new Client(['timeout' => 5]);
            $response = $client->get('https://nominatim.openstreetmap.org/reverse', [
                'query' => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'accept-language' => 'pt-BR',
                ],
                'headers' => [
                    'User-Agent' => 'QuiPede/1.0 (contato@quipede.com.br)',
                ],
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (empty($data) || isset($data['error'])) {
                return ApiResponse::error('Não foi possível encontrar um endereço para esta localização', 404);
            }
            
            $endereco = $this->formatarEnderecoNominatim($data);
            
            return ApiResponse::success($endereco, 'Endereço encontrado com sucesso');
            
        } catch (\Exception $e) {
            Yii::error("Erro na geocodificação: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Serviço de geocodificação indisponível', 503);
        }
    }

    /**
     * GET /api/app/localizacao/buscar-endereco
     * Busca endereços por texto, com prioridade para resultados próximos à localização do usuário.
     * Retorna apenas logradouro, bairro, cidade, UF e coordenadas, sem número (usuário digitará depois).
     * 
     * @param string $q        Texto de busca (mínimo 3 caracteres)
     * @param float  $lat      Latitude do usuário (opcional, para priorizar proximidade)
     * @param float  $lng      Longitude do usuário (opcional)
     * @param int    $limit    Limite de resultados (padrão 10)
     * @return array
     */
    public function actionBuscarEndereco($q = null)
    {
        $request = Yii::$app->request;
        $q = $q ?: $request->get('q');
        $latitude = $request->get('lat');
        $longitude = $request->get('lng');
        $limit = (int) $request->get('limit', 10);
        
        if (empty($q) || strlen(trim($q)) < 3) {
            return ApiResponse::error('Termo de busca deve ter pelo menos 3 caracteres', 400);
        }
        
        try {
            $client = new Client(['timeout' => 8]);
            
            // Construir parâmetros da consulta
            $queryParams = [
                'q' => $q . ', Brasil',
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => $limit,
                'accept-language' => 'pt-BR',
                'countrycodes' => 'br',
            ];
            
            // Se temos coordenadas do usuário, adicionar viewbox para priorizar resultados próximos
            if ($latitude && $longitude) {
                $offset = 0.3; // ~33km
                $queryParams['viewbox'] = ($longitude - $offset) . ',' . ($latitude - $offset) . ',' . 
                                          ($longitude + $offset) . ',' . ($latitude + $offset);
                $queryParams['bounded'] = 0;
            } else {
                // Fallback: centro do Brasil com offset grande
                $latitudeBrasil = -15.7801;
                $longitudeBrasil = -47.9292;
                $offsetBrasil = 30.0;
                $queryParams['viewbox'] = ($longitudeBrasil - $offsetBrasil) . ',' . 
                                          ($latitudeBrasil - $offsetBrasil) . ',' . 
                                          ($longitudeBrasil + $offsetBrasil) . ',' . 
                                          ($latitudeBrasil + $offsetBrasil);
                $queryParams['bounded'] = 0;
            }
            
            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => $queryParams,
                'headers' => [
                    'User-Agent' => 'QuiPede/1.0 (contato@quipede.com.br)',
                ],
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if (empty($data)) {
                return ApiResponse::success(['items' => []], 'Nenhum endereço encontrado');
            }
            
            $items = array_map([$this, 'formatarEnderecoNominatim'], $data);
            
            // Se temos coordenadas do usuário, ordenar por distância
            if ($latitude && $longitude) {
                usort($items, function($a, $b) use ($latitude, $longitude) {
                    $distA = $this->calcularDistancia(
                        $latitude, $longitude,
                        $a['latitude'], $a['longitude']
                    );
                    $distB = $this->calcularDistancia(
                        $latitude, $longitude,
                        $b['latitude'], $b['longitude']
                    );
                    return $distA <=> $distB;
                });
                
                // Adicionar distância formatada a cada item
                foreach ($items as &$item) {
                    if ($item['latitude'] && $item['longitude']) {
                        $distancia = $this->calcularDistancia(
                            $latitude, $longitude,
                            $item['latitude'], $item['longitude']
                        );
                        $item['distancia_km'] = round($distancia, 2);
                        $item['distancia_texto'] = $this->formatarDistancia($distancia);
                    }
                }
            }
            
            return ApiResponse::success([
                'items' => $items,
                'total' => count($items),
                'com_geolocalizacao' => ($latitude && $longitude),
            ], 'Endereços encontrados');
            
        } catch (\Exception $e) {
            Yii::error("Erro na busca de endereço: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Serviço de busca de endereço indisponível', 503);
        }
    }

    /**
     * POST /api/app/localizacao/confirmar-endereco
     * Geocodifica um endereço completo (com número) e retorna as coordenadas precisas.
     * 
     * Espera JSON com: logradouro, numero, bairro, cidade, uf, cep (opcional)
     * Retorna o endereço enriquecido com latitude e longitude.
     */
    public function actionConfirmarEndereco()
    {
        $request = Yii::$app->request;
        $logradouro = trim($request->post('logradouro'));
        $numero = trim($request->post('numero'));
        $bairro = trim($request->post('bairro'));
        $cidade = trim($request->post('cidade'));
        $uf = trim($request->post('uf'));
        $cep = trim($request->post('cep'));

        if (empty($logradouro) || empty($numero) || empty($cidade) || empty($uf)) {
            return ApiResponse::error('Logradouro, número, cidade e UF são obrigatórios', 400);
        }

        try {
            $client = new Client(['timeout' => 8]);

            // Construir parâmetros estruturados (melhor que 'q' genérico)
            $queryParams = [
                'street' => $logradouro . ', ' . $numero,
                'city' => $cidade,
                'state' => $uf,
                'country' => 'Brasil',
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
                'accept-language' => 'pt-BR',
            ];

            if (!empty($bairro)) {
                $queryParams['county'] = $bairro;
            }
            if (!empty($cep)) {
                $queryParams['postalcode'] = preg_replace('/\D/', '', $cep);
            }

            Yii::info("Confirmar endereço - Query params: " . json_encode($queryParams), __METHOD__);

            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => $queryParams,
                'headers' => [
                    'User-Agent' => 'QuiPede/1.0 (contato@quipede.com.br)',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            Yii::info("Resposta Nominatim: " . json_encode($data), __METHOD__);

            if (empty($data)) {
                // Fallback: tentar com 'q' como segunda tentativa
                $queryFallback = "$logradouro, $numero, $bairro, $cidade, $uf, Brasil";
                if (!empty($cep)) {
                    $queryFallback .= ", CEP $cep";
                }

                $response2 = $client->get('https://nominatim.openstreetmap.org/search', [
                    'query' => ['q' => $queryFallback, 'format' => 'json', 'limit' => 1, 'accept-language' => 'pt-BR'],
                    'headers' => ['User-Agent' => 'QuiPede/1.0 (contato@quipede.com.br)'],
                ]);
                $data = json_decode($response2->getBody(), true);
            }

            if (empty($data)) {
                return ApiResponse::success([
                    'logradouro' => $logradouro,
                    'numero' => $numero,
                    'bairro' => $bairro,
                    'cidade' => $cidade,
                    'uf' => $uf,
                    'cep' => $cep,
                    'latitude' => null,
                    'longitude' => null,
                ], 'Endereço confirmado (sem coordenadas precisas)');
            }

            $melhor = $data[0];
            $endereco = [
                'logradouro' => $logradouro,
                'numero' => $numero,
                'bairro' => $bairro,
                'cidade' => $cidade,
                'uf' => $uf,
                'cep' => $cep,
                'latitude' => isset($melhor['lat']) ? (float)$melhor['lat'] : null,
                'longitude' => isset($melhor['lon']) ? (float)$melhor['lon'] : null,
            ];

            return ApiResponse::success($endereco, 'Coordenadas obtidas com sucesso');

        } catch (\Exception $e) {
            Yii::error("Erro ao confirmar endereço: " . $e->getMessage(), __METHOD__);
            return ApiResponse::error('Serviço de geocodificação indisponível', 503);
        }
    }

    /**
     * Formata a resposta do Nominatim para o padrão do app.
     * ATENÇÃO: O campo 'numero' NÃO é preenchido automaticamente na busca de endereços,
     * pois o usuário seleciona apenas a rua e digita o número depois.
     */
    private function formatarEnderecoNominatim($data)
    {
        $address = $data['address'] ?? [];
        
        // Extrai CEP (pode vir em 'postcode')
        $cep = $address['postcode'] ?? '';
        $cep = preg_replace('/\D/', '', $cep);
        
        // Logradouro
        $logradouro = $address['road'] ?? $address['street'] ?? $address['pedestrian'] ?? '';
        
        // Bairro
        $bairro = $address['suburb'] ?? $address['neighbourhood'] ?? $address['city_district'] ?? '';
        
        // Cidade
        $cidade = $address['city'] ?? $address['town'] ?? $address['municipality'] ?? '';
        
        // UF
        $uf = $address['state'] ?? '';
        
        // Monta descrição amigável (sem número)
        $descricaoParts = [];
        if (!empty($logradouro)) {
            $descricaoParts[] = $logradouro;
        }
        if (!empty($bairro)) {
            $descricaoParts[] = $bairro;
        }
        if (!empty($cidade)) {
            $descricaoParts[] = $cidade;
        }
        if (!empty($uf)) {
            $descricaoParts[] = $uf;
        }
        $descricao = implode(', ', $descricaoParts);
        if (empty($descricao)) {
            $descricao = $data['display_name'] ?? 'Endereço';
        }
        
        return [
            'id' => $data['place_id'] ?? null,
            'descricao' => $descricao,
            'cep' => $cep ?: null,
            'logradouro' => $logradouro,
            'numero' => null, // Sempre null – usuário informará depois
            'bairro' => $bairro,
            'cidade' => $cidade,
            'uf' => $uf,
            'latitude' => isset($data['lat']) ? (float)$data['lat'] : null,
            'longitude' => isset($data['lon']) ? (float)$data['lon'] : null,
        ];
    }

    /**
     * Calcula a distância entre dois pontos geográficos (em km).
     * Fórmula de Haversine.
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }
        
        $earthRadius = 6371; // km
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Formata distância em texto amigável.
     */
    private function formatarDistancia($distancia)
    {
        if ($distancia < 1) {
            return round($distancia * 1000) . 'm';
        }
        return number_format($distancia, 1, ',', '.') . 'km';
    }
}