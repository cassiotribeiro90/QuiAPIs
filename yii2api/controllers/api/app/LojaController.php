<?php
// controllers/api/app/LojaController.php

namespace app\controllers\api\app;

use Yii;
use app\components\ApiResponse;
use app\components\DistanceCalculator;
use app\models\api\gestor\Loja;
use app\controllers\api\app\AppControllerBase;

class LojaController extends AppControllerBase
{
    /**
     * {@inheritdoc}
     * Libera acesso público para ações de listagem
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        if (isset($behaviors['authenticator'])) {
            $behaviors['authenticator']['except'] = [
                'index',
                'proximas',
            ];
        }
        
        return $behaviors;
    }
    
    /**
     * GET /api/app/lojas
     * GET /api/app/loja
     * 
     * Lista lojas disponíveis com paginação, filtros e ordenação
     * 
     * @return array
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $orderBy = $request->get('order_by', 'nota');
        
        // ===== QUERY BASE =====
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
        
        // ===== ORDENAÇÃO =====
        if ($orderBy === 'distancia' && $latitude && $longitude) {
            $distanceSql = DistanceCalculator::getDistanceSql($latitude, $longitude);
            $query->select(['loja.*', "$distanceSql as distancia"]);
            $query->orderBy(['distancia' => SORT_ASC]);
        } 
        elseif ($orderBy === 'tempo_entrega') {
            $query->orderBy([
                '((tempo_entrega_min + tempo_entrega_max) / 2)' => SORT_ASC
            ]);
        } 
        elseif ($orderBy === 'taxa_entrega') {
            $query->orderBy(['taxa_entrega' => SORT_ASC]);
        }
        elseif ($orderBy === 'nota') {
            $query->orderBy([
                'nota_media' => SORT_DESC,
                'total_avaliacoes' => SORT_DESC,
            ]);
        }
        else {
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
        
        // ===== GERAR FILTER_OPTIONS =====
        $filterOptions = $this->generateFilterOptions();
        
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
            ],
            'filter_options' => $filterOptions
        ]);
    }
    
    /**
     * GET /api/app/lojas/proximas
     * GET /api/app/loja/proximas
     * 
     * Endpoint dedicado para lojas próximas por raio de distância
     * 
     * @return array
     */
    public function actionProximas()
    {
        $request = Yii::$app->request;
        
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $raio = (float)$request->get('raio', 10);
        
        if (!$latitude || !$longitude) {
            return ApiResponse::error('Latitude e longitude são obrigatórios', 400);
        }
        
        $distanceSql = DistanceCalculator::getDistanceSql($latitude, $longitude);
        $radiusSql = DistanceCalculator::getRadiusSql($latitude, $longitude, $raio);
        
        $query = Loja::find()
            ->where(['deletado_em' => null, 'status' => 'ativo'])
            ->andWhere('latitude IS NOT NULL AND longitude IS NOT NULL')
            ->andWhere($radiusSql)
            ->select(['loja.*', "$distanceSql as distancia"])
            ->orderBy(['distancia' => SORT_ASC]);
        
        $limit = (int)$request->get('limit', 20);
        $lojas = $query->limit($limit)->all();
        
        $data = array_map(function($loja) {
            return [
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
     * Gera as opções de filtro (categorias com contagens)
     * 
     * @return array
     */
    private function generateFilterOptions()
    {
        $categoriasComLojas = (new \yii\db\Query())
            ->select(['categoria', 'COUNT(*) as total'])
            ->from('loja')
            ->where(['deletado_em' => null, 'status' => 'ativo'])
            ->andWhere(['is not', 'categoria', null])
            ->andWhere(['<>', 'categoria', ''])
            ->groupBy('categoria')
            ->having(['>', 'total', 0])
            ->orderBy(['total' => SORT_DESC])
            ->all();

        $categoriaOptions = [];
        foreach ($categoriasComLojas as $item) {
            $categoriaOptions[] = [
                'value' => $item['categoria'],
                'label' => $item['categoria'],
                'count' => (int)$item['total'],
            ];
        }

        return ['categorias' => $categoriaOptions];
    }
    
    /**
     * Formata distância em texto amigável
     * 
     * @param float $distancia
     * @return string
     */
    private function formatarDistancia($distancia)
    {
        if ($distancia < 1) {
            return round($distancia * 1000) . 'm';
        }
        return number_format($distancia, 1, ',', '.') . 'km';
    }
}