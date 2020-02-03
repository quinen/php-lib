<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/30/20
 * Time: 8:41 AM
 */

namespace QuinenLib\Files;


use Cake\Utility\Hash;
use QuinenLib\Utility\Strings;

class Afb
{
    const ENREGISTREMENT_NOUVEAU_SOLDE = '07';
    const MOUVEMENT_LIBELLE_LENGTH = 31;

    private static $enregistrementTypes = [
        '01' => "Ancien solde",
        '04' => 'Mouvement',
        self::ENREGISTREMENT_NOUVEAU_SOLDE => 'Nouveau solde'
    ];

    private static $operationsInterbancaires = [
        '01' => 'Chèques payés',
        '41' => 'Transferts vers/en provenance de l\'étranger',
        '91' => 'Opérations diverses'
    ];

    private static $fileMap = [
        '01' => [
            'codeEnregistrement' => 2,
            'codeBanque' => 5,
            'zoneReservee1c' => 4,
            'codeGuichet' => 5,
            'codeDevise' => 3,
            'nbDecimales' => 1,
            'zoneReservee1g' => 1,
            'numeroCompte' => 11,
            'zoneReservee1i' => 2,
            'date' => 6,
            'zoneReservee1k' => 50,
            'montant' => 14,
            'zoneReservee1m' => 16
        ],
        '04' => [
            'codeEnregistrement' => 2,
            'codeBanque' => 5,
            'codeOperationInterne' => 4,
            'codeGuichet' => 5,
            'codeDevise' => 3,
            'nbDecimales' => 1,
            'zoneReservee2g' => 1,
            'numeroCompte' => 11,
            'codeOperationInterbancaire' => 2,
            'date' => 6,
            'codeMotifRejet' => 2,
            'dateValeur' => 6,
            'libelle' => self::MOUVEMENT_LIBELLE_LENGTH,
            'zoneReservee2n' => 2,
            'numeroEcriture' => 7,
            'indiceExoneration' => 1,
            'indiceIndisponibilite' => 1,
            'montant' => 14,
            'zoneReference' => 16
        ],
        '07' => [
            'codeEnregistrement' => 2,
            'codeBanque' => 5,
            'zoneReservee3c' => 4,
            'codeGuichet' => 5,
            'codeDevise' => 3,
            'nbDecimales' => 1,
            'zoneReservee3g' => 1,
            'numeroCompte' => 11,
            'zoneReservee3i' => 2,
            'date' => 6,
            'zoneReservee3k' => 50,
            'montant' => 14,
            'zoneReservee3m' => 16
        ]
    ];

    public static function getMontant($string, $nbDecimal)
    {
        return self::getMontantCentimes($string) / (10 ** $nbDecimal);
    }

    public static function getMontantCentimes($string)
    {
        $data = self::getDataFromMontant($string);
        return ($data['montant'] * $data['multiplier']);
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

        $montant = mb_substr($string, 0, -1) . $lastCentime;

        return compact(['multiplier', 'lastCentime', 'montant']);
    }

    public static function getEnregistrementType($char2 = null)
    {
        if ($char2 === null) {
            return self::$enregistrementTypes;
        }

        return Hash::get(self::$enregistrementTypes, $char2, $char2);
    }

    public static function getOperationInterbancaire($char2 = null)
    {

        if ($char2 === null) {
            return self::$operationsInterbancaires;
        }

        return Hash::get(self::$operationsInterbancaires, $char2, $char2);

    }

    public static function fileToArray($fileString)
    {
        $fileLines = preg_split('/\r\n|\r|\n/', $fileString);

// lengths
        $lengths = self::$fileMap;

        $fileArray = collection($fileLines)->reduce(function ($reducer, $line) use ($lengths) {

            $codeEnregistrement = mb_substr($line, 0, 2);

            $lineSplitted = collection($lengths[$codeEnregistrement])->reduce(function ($reducer, $length, $index) use (
                $line
            ) {

                if (!Strings::startsWith($index, 'zoneReservee')) {
                    $reducer['columns'][$index] = mb_substr($line, $reducer['position'], $length);
                }

                $reducer['position'] += $length;
                return $reducer;
            }, ['columns' => [], 'position' => 0]);


            if ($lineSplitted) {
                $reducer['lines'][] = $lineSplitted['columns'];
            }

            return $reducer;
        }, ['lines' => []]);

        return $fileArray['lines'];
    }
}
