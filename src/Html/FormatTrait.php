<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 16/09/20
 * Time: 17:49
 */

namespace QuinenLib\Html;

use QuinenLib\Utility\Dates;

trait FormatTrait
{
    public function formatDatetime(\DateTime $datetime = null)
    {
        if ($datetime === null) {
            return null;
        }
        $dateFormat = \IntlDateFormatter::create(
            \Locale::getDefault(),
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN, Dates::FORMAT_JOURNOM_JOUR_MOIS_ANNEE_HEURE_MINUTE
        );
        return $dateFormat->format($datetime->getTimestamp());
    }

    public function formatDate(\DateTime $datetime = null)
    {
        if ($datetime === null) {
            return null;
        }

        $dateFormat = \IntlDateFormatter::create(
            \Locale::getDefault(),
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN, Dates::FORMAT_JOURNOM_JOUR_MOIS_ANNEE
        );
        return $dateFormat->format($datetime->getTimestamp());
    }
}