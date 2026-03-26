<?php
// controllers/api/app/LojaController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\models\api\gestor\Loja;
use app\controllers\api\app\AppControllerBase;

class LojaController extends AppControllerBase
{
    /**
     * GET /api/app/lojas
     * Lista lojas disponíveis, podendo ordenar por distância
     * 
     * Parâmetros:
     * - latitude: float (opcional, necessário para ordenar por distância)
     * - longitude: float (opcional, necessário para ordenar por distância)
     * - order_by: string (distancia, nota, tempo_entrega, padrão = nota)
     * - categoria: string (filtro)
     * - cidade: string (filtro)
     * - search: string (busca)
     * - page: int
     * - per_page: int
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $orderBy = $request->get('order_by', 'nota');
        
        // Query base - apenas lojas ativas
        $query = Loja::find()
            ->where(['deletado_em' => null, 'status' => 'ativo']);
        
        // ===== FILTROS =====
        if ($request->get('categoria')) {
            $query->andWhere(['categoria' => $request->get('categoria')]);
        }
        
        if ($request->get('cidade')) {
            $query->andWhere(['cidade' => $request->get('cidade')]);
        }
        
        if ($request->get('search')) {
            $search = $request->get('search');
            $query->andWhere([
                'or',
                ['like', 'nome', $search],
                ['like', 'cidade', $search],
            ]);
        }
        
        // ===== ORDENAÇÃO POR DISTÂNCIA =====
        if ($orderBy === 'distancia' && $latitude && $longitude) {
            // Fórmula de Haversine para calcular distância em km
            $haversine = "
                (6371 * acos(
                    cos(radians($latitude)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians($longitude)) + 
                    sin(radians($latitude)) * 
                    sin(radians(latitude))
                ))
            ";
            
            // Adiciona o cálculo de distância como campo virtual
            $query->select([
                'loja.*',
                "ROUND($haversine, 2) as distancia"
            ]);
            
            // Ordena por distância
            $query->orderBy(['distancia' => SORT_ASC]);
        } 
        // ===== OUTRAS ORDENAÇÕES =====
        elseif ($orderBy === 'tempo_entrega') {
            // Ordena pelo tempo médio de entrega
            $query->orderBy([
                '((tempo_entrega_min + tempo_entrega_max) / 2)' => SORT_ASC
            ]);
        } 
        else {
            // Padrão: ordenar por nota e destaque
            $query->orderBy([
                'destaque' => SORT_DESC,
                'nota_media' => SORT_DESC,
            ]);
        }
        
        // ===== PAGINAÇÃO =====
        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 20);
        $offset = ($page - 1) * $perPage;
        
        $total = $query->count();
        $lojas = $query->offset($offset)->limit($perPage)->all();
        
        // ===== FORMATAR RESPOSTA =====
        $data = array_map(function($loja) use ($orderBy, $latitude, $longitude) {
            $item = [
                'id' => $loja->id,
                'nome' => $loja->nome,
                'categoria' => $loja->categoria,
                'logo' => $loja->logo,
                'capa' => $loja->capa,
                'cidade' => $loja->cidade,
                'uf' => $loja->uf,
                'nota_media' => (float)$loja->nota_media,
                'total_avaliacoes' => (int)$loja->total_avaliacoes,
                'tempo_entrega_min' => (int)$loja->tempo_entrega_min,
                'tempo_entrega_max' => (int)$loja->tempo_entrega_max,
                'taxa_entrega' => (float)$loja->taxa_entrega,
                'pedido_minimo' => (float)$loja->pedido_minimo,
                'destaque' => (bool)$loja->destaque,
                'verificado' => (bool)$loja->verificado,
            ];
            
            // Se ordenou por distância, inclui a distância calculada
            if ($orderBy === 'distancia' && $latitude && $longitude && isset($loja->distancia)) {
                $item['distancia'] = (float)$loja->distancia;
                $item['distancia_texto'] = $this->formatarDistancia($loja->distancia);
            }
            
            return $item;
        }, $lojas);
        
        return ApiResponse::success([
            'items' => $data,
            'pagination' => [
                'total' => (int)$total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }
    
    /**
     * GET /api/app/lojas/proximas
     * Endpoint dedicado para lojas próximas (mais simples)
     */
    public function actionProximas()
    {
        $request = Yii::$app->request;
        
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $raio = (float)$request->get('raio', 10); // raio padrão 10km
        
        if (!$latitude || !$longitude) {
            return ApiResponse::error('Latitude e longitude são obrigatórios', 400);
        }
        
        // Fórmula de Haversine
        $haversine = "
            (6371 * acos(
                cos(radians($latitude)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(latitude))
            ))
        ";
        
        $query = Loja::find()
            ->where(['deletado_em' => null, 'status' => 'ativo'])
            ->andWhere("latitude IS NOT NULL AND longitude IS NOT NULL")
            ->andWhere("$haversine <= $raio")
            ->select([
                'loja.*',
                "ROUND($haversine, 2) as distancia"
            ])
            ->orderBy(['distancia' => SORT_ASC]);
        
        $limit = (int)$request->get('limit', 20);
        $lojas = $query->limit($limit)->all();
        
        $data = array_map(function($loja) {
            return [
                'id' => $loja->id,
                'nome' => $loja->nome,
                'categoria' => $loja->categoria,
                'logo' => $loja->logo,
                'cidade' => $loja->cidade,
                'uf' => $loja->uf,
                'nota_media' => (float)$loja->nota_media,
                'tempo_entrega_min' => (int)$loja->tempo_entrega_min,
                'tempo_entrega_max' => (int)$loja->tempo_entrega_max,
                'taxa_entrega' => (float)$loja->taxa_entrega,
                'pedido_minimo' => (float)$loja->pedido_minimo,
                'distancia' => (float)$loja->distancia,
                'distancia_texto' => $this->formatarDistancia($loja->distancia),
            ];
        }, $lojas);
        
        return ApiResponse::success([
            'items' => $data,
            'total' => count($data),
        ]);
    }
    
    /**
     * Formata distância em texto amigável
     */
    private function formatarDistancia($distancia)
    {
        if ($distancia < 1) {
            return round($distancia * 1000) . 'm';
        }
        return number_format($distancia, 1, ',', '.') . 'km';
    }
}