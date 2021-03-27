<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 09/04/2020
 * Time: 16:07
 */

namespace QuinenLib\Utility;

use DateTime;

class Dates
{
    //http://userguide.icu-project.org/formatparse/datetime
    const FORMAT_JOURNOM = 'ccc';
    const FORMAT_JOURNOM_JOUR_MOIS_ANNEE = 'cccc d MMMM yyyy';
    const FORMAT_JOURNOM_JOUR_MOIS_ANNEE_HEURE_MINUTE = 'ccc d MMM yyyy H:mm';
    const FORMAT_MOIS_ANNEE = 'MMMM yyyy';

    /**
     * doc : https://framework.zend.com/manual/1.12/en/zend.date.constants.html#zend.date.constants.selfdefinedformats
     * @param string $from
     * @param string $to
     */
    public static function format($date,$to = 'EEEE d MMMM YYYY', $from = 'Y-m-d', $locale = null)
    {
        if (null === $locale) {
            $locale = \Locale::getDefault();
        }

        $date = DateTime::createFromFormat($from, $date);
        $dateFormatter = \IntlDateFormatter::create(
            $locale,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN,
            $to
        );

        $date = $dateFormatter->format($date->getTimestamp());
        return $date;
    }

}