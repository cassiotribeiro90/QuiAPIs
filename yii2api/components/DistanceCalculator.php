<?php

namespace app\components;

use yii\base\Component;

class DistanceCalculator extends Component
{
    /**
     * Constantes de conversão
     * 1 grau de latitude ≈ 111.32 km
     */
    const KM_PER_DEGREE_LAT = 111.32;
    
    /**
     * Calcula a distância usando a fórmula Euclidiana (Plana)
     * Ideal para distâncias urbanas (< 50km)
     * Mais rápida e precisa o suficiente para entregas de comida
     */
    public static function euclidean($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return null;
        }
        
        // Cálculo do fator de correção para longitude baseado na latitude média
        $latMid = deg2rad(($lat1 + $lat2) / 2);
        $kmPerDegreeLon = self::KM_PER_DEGREE_LAT * cos($latMid);
        
        // Diferenças em km
        $deltaLat = ($lat2 - $lat1) * self::KM_PER_DEGREE_LAT;
        $deltaLon = ($lon2 - $lon1) * $kmPerDegreeLon;
        
        // Distância Euclidiana
        return sqrt($deltaLat * $deltaLat + $deltaLon * $deltaLon);
    }
    
    /**
     * Gera o SQL para ordenar por distância usando fórmula Euclidiana
     */
    public static function getDistanceSql($latitude, $longitude, $alias = 'loja')
    {
        if (!$latitude || !$longitude) {
            return null;
        }
        
        // 1 grau de latitude ≈ 111.32 km
        $kmPerDegreeLat = self::KM_PER_DEGREE_LAT;
        $kmPerDegreeLon = self::KM_PER_DEGREE_LAT * cos(deg2rad($latitude));
        
        return "
            ROUND(
                SQRT(
                    POW(({$alias}.latitude - $latitude) * $kmPerDegreeLat, 2) +
                    POW(({$alias}.longitude - $longitude) * $kmPerDegreeLon, 2)
                ), 2
            )
        ";
    }
    
    /**
     * Gera o SQL para filtrar por raio usando fórmula Euclidiana
     */
    public static function getRadiusSql($latitude, $longitude, $radiusKm, $alias = 'loja')
    {
        if (!$latitude || !$longitude) {
            return null;
        }
        
        $distanceSql = self::getDistanceSql($latitude, $longitude, $alias);
        
        return "$distanceSql <= $radiusKm";
    }
}