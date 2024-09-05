<?php


namespace App\Support\Utils;


use DateTime;
use Exception;

class DateUtils
{
    public static function castStringToDateTime(?string $date): DateTime
    {
        if (!$date) return self::defaultDate();
        try {
            return new DateTime($date);
        } catch (Exception) {
            return self::defaultDate();
        }
    }

    public static function defaultDate(): DateTime
    {
        return (new DateTime)->setTimestamp(0);
    }

    public static function formatDateIso8601(DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
