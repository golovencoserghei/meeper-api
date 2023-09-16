<?php

namespace App\Enums;

use Carbon\Carbon;

/**
 * @todo move to helper or service
 */
class WeekDaysEnum
{
    public const MON = 1;
    public const TUE = 2;
    public const WED = 3;
    public const THU = 4;
    public const FRI = 5;
    public const SAT = 6;
    public const SUN = 7;

    public const WEEK_DAYS = [
        self::MON => 'Понедельник',
        self::TUE => 'Вторник',
        self::WED => 'Среда',
        self::THU => 'Четверг',
        self::FRI => 'Пятница',
        self::SAT => 'Субота',
        self::SUN => 'Воскресенье',
    ];

    public static function getWeekDay(int $day): string
    {
        return self::WEEK_DAYS[$day];
    }

    public static function getWeekDayDate(int $day): string
    {
        return match($day) {
            self::MON => (new Carbon('last Monday'))->format('d.m'),
            self::TUE => (new Carbon('last Tuesday'))->format('d.m'),
            self::WED => (new Carbon('last Wednesday'))->format('d.m'),
            self::THU => (new Carbon('last Thursday'))->format('d.m'),
            self::FRI => (new Carbon('last Friday'))->format('d.m'),
            self::SAT => (new Carbon('last Saturday'))->format('d.m'),
            self::SUN => (new Carbon('last Sunday'))->format('d.m'),
        };
    }
}
