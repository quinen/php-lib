<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/30/20
 * Time: 8:41 AM
 */

namespace QuinenLib\Files;


class Afb
{
    public static function getMontant($string, $nbDecimal)
    {
        $data = self::getDataFromMontant($string);
        return ($data['montant'] * $data['multiplier']) / (10 ** $nbDecimal);
    }

    /*
     * prends une string de 14c en entrée pour renvoyé le float equivalent en postif/negatif
     *
     * */

    public static function getDataFromMontant($string)
    {
        $lastChar = mb_substr($string, -1);
        $lastCharInt = ord($lastChar);
        $multiplier = 0;
        $lastCentime = '?';
        if (65 <= $lastCharInt && $lastCharInt <= 82) {
            if ($lastCharInt < 74) {
                $multiplier = 1;
                $lastCentime = $lastCharInt - 65;
            } else {
                $multiplier = -1;
                $lastCentime = $lastCharInt - 73;
            }
        } else {
            if ($lastCharInt == 123) {
                $multiplier = 1;
                $lastCentime = 0;
            } else {
                if ($lastCharInt == 125) {
                    $multiplier = -1;
                    $lastCentime = 0;
                }
            }
        }

        $montant = mb_substr($string, 0, mb_strlen($string) - 1) . $lastCentime;

        return compact(['multiplier', 'lastCentime', 'montant']);
    }
}