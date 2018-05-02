<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/2/18
 * Time: 2:44 PM
 */

namespace common\queries;


use yii\db\ActiveQuery;

class NotDeletedQuery extends ActiveQuery
{
    public function notDeleted()
    {
        $this->andOnCondition(['deleted' => false]);
    }
}