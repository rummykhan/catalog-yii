<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/2/18
 * Time: 11:56 AM
 */

namespace common\helpers;


use yii\base\Model;

class GlobalHelper
{
    /**
     * @param $model Model
     * @return string
     */
    public static function getModelName($model)
    {
        $class = get_class($model);

        return substr($class, strrpos($class, '\\') + 1);
    }
}