<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 4/30/18
 * Time: 11:13 AM
 */

namespace common\forms;


use yii\base\Model;

class AddType extends Model
{
    public $provided_service_id;
    public $service_types;

    public function rules()
    {
        return [
            [['service_types'], 'each', 'rule' => ['integer']],
            [['provided_service_id'], 'integer'],
        ];
    }

    public function attach()
    {
        dd($this);
    }
}