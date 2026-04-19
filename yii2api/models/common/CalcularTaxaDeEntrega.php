<?php
namespace app\models\common;

class CalcularTaxaDeEntrega
{
    public static function calcular($distanciaKm)
    {
        if ($distanciaKm <= 5) {
            return 10.00; // Taxa fixa para até 5 km
        } elseif ($distanciaKm <= 10) {
            return 15.00; // Taxa fixa para entre 5 e 10 km
        } else {
            return 20.00; // Taxa fixa para acima de 10 km
        }
    }
}