<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/6/18
 * Time: 12:32 PM
 */

namespace common\helpers;


class AvailabilityHelper
{
    const AVAILABLE = 'Available';
    const UN_AVAILABLE = 'Not Available';

    public static function toList()
    {
        return [
            static::AVAILABLE => static::AVAILABLE,
            static::UN_AVAILABLE => static::UN_AVAILABLE
        ];
    }
}