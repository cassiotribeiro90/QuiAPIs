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
     * Lista lojas disponíveis com paginação, filtros e ordenação.
     * A busca (search) considera nome, descrição e categoria,
     * com correspondência aproximada (Levenshtein) caso a busca exata não retorne resultados.
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $orderBy = $request->get('order_by', 'nota');
        $search = $request->get('search', '');

        // ===== QUERY BASE =====
        $query = Loja::find()
            ->where(['deletado_em' => null, 'status' => 'ativo']);

        // ===== FILTROS FIXOS =====
        if ($request->get('categoria')) {
            $query->andWhere(['categoria' => $request->get('categoria')]);
        }

        if ($request->get('cidade')) {
            $query->andWhere(['cidade' => $request->get('cidade')]);
        }

        // ===== BUSCA EXATA PRIMEIRO =====
        $usarBuscaAproximada = false;
        if (!empty($search) && trim($search) !== '') {
            $query->andWhere([
                'or',
                ['like', 'nome', $search],
                ['like', 'descricao', $search],
                ['like', 'categoria', $search],
            ]);
        }

        // ===== ORDENAÇÃO =====
        if ($orderBy === 'distancia' && $latitude && $longitude) {
            $distanceSql = DistanceCalculator::getDistanceSql($latitude, $longitude);
            $query->select(['loja.*', "$distanceSql as distancia"]);
            $query->orderBy(['distancia' => SORT_ASC]);
        } elseif ($orderBy === 'tempo_entrega') {
            $query->orderBy([
                '((tempo_entrega_min + tempo_entrega_max) / 2)' => SORT_ASC,
            ]);
        } elseif ($orderBy === 'taxa_entrega') {
            $query->orderBy(['taxa_entrega' => SORT_ASC]);
        } elseif ($orderBy === 'nota') {
            $query->orderBy([
                'nota_media' => SORT_DESC,
                'total_avaliacoes' => SORT_DESC,
            ]);
        } else {
            $query->orderBy([
                'destaque' => SORT_DESC,
                'nota_media' => SORT_DESC,
            ]);
        }

        // ===== PAGINAÇÃO =====
        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 20);
        $offset = ($page - 1) * $perPage;

        // Busca inicial (exata)
        $lojas = $query->all();

        // Se não encontrou, tenta busca aproximada (Levenshtein)
        if (empty($lojas) && !empty($search) && trim($search) !== '') {
            // Cria nova query sem filtro de busca
            $queryAprox = Loja::find()
                ->where(['deletado_em' => null, 'status' => 'ativo']);

            if ($request->get('categoria')) {
                $queryAprox->andWhere(['categoria' => $request->get('categoria')]);
            }
            if ($request->get('cidade')) {
                $queryAprox->andWhere(['cidade' => $request->get('cidade')]);
            }

            // Aplica mesma ordenação
            if ($orderBy === 'distancia' && $latitude && $longitude) {
                $distanceSql = DistanceCalculator::getDistanceSql($latitude, $longitude);
                $queryAprox->select(['loja.*', "$distanceSql as distancia"]);
                $queryAprox->orderBy(['distancia' => SORT_ASC]);
            } elseif ($orderBy === 'tempo_entrega') {
                $queryAprox->orderBy([
                    '((tempo_entrega_min + tempo_entrega_max) / 2)' => SORT_ASC,
                ]);
            } elseif ($orderBy === 'taxa_entrega') {
                $queryAprox->orderBy(['taxa_entrega' => SORT_ASC]);
            } elseif ($orderBy === 'nota') {
                $queryAprox->orderBy([
                    'nota_media' => SORT_DESC,
                    'total_avaliacoes' => SORT_DESC,
                ]);
            } else {
                $queryAprox->orderBy([
                    'destaque' => SORT_DESC,
                    'nota_media' => SORT_DESC,
                ]);
            }

            $todasLojas = $queryAprox->all();
            $termoNormalizado = $this->normalizarTexto($search);
            $maxDistance = max(1, intdiv(strlen($termoNormalizado), 4));

            $lojasAproximadas = [];
            foreach ($todasLojas as $loja) {
                $campos = [
                    $this->normalizarTexto($loja->nome),
                    $this->normalizarTexto($loja->descricao ?? ''),
                    $this->normalizarTexto($loja->categoria ?? ''),
                ];

                foreach ($campos as $campo) {
                    if (empty($campo)) continue;
                    if (levenshtein($campo, $termoNormalizado) <= $maxDistance) {
                        $lojasAproximadas[] = $loja;
                        break;
                    }
                }
            }

            $lojas = $lojasAproximadas;
        }

        // ===== TOTAL E PAGINAÇÃO =====
        $total = count($lojas);
        $lojasPaginadas = array_slice($lojas, $offset, $perPage);

        // ===== GERAR FILTER_OPTIONS =====
        $filterOptions = $this->generateFilterOptions();

        // ===== FORMATAR RESPOSTA =====
        $data = array_map(function ($loja) use ($orderBy, $latitude, $longitude) {
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
        }, $lojasPaginadas);

        return ApiResponse::success([
            'items' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int)ceil($total / $perPage),
            ],
            'filter_options' => $filterOptions,
        ]);
    }

    /**
     * GET /api/app/lojas/proximas
     * GET /api/app/loja/proximas
     *
     * Endpoint dedicado para lojas próximas por raio de distância
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

        $data = array_map(function ($loja) {
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
     * Normaliza texto para busca aproximada.
     */
    private function normalizarTexto($texto)
    {
        $comAcentos = ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç','ñ','ý','ÿ','Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Õ','Ö','Ú','Ù','Û','Ü','Ç','Ñ','Ý','Ÿ'];
        $semAcentos = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n','y','y','A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','C','N','Y','Y'];
        $texto = str_replace($comAcentos, $semAcentos, $texto);
        return strtolower($texto);
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