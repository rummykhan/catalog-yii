<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/3/18
 * Time: 5:01 PM
 */

namespace console\controllers;


use common\models\RuleType;
use common\models\RuleValueType;
use yii\console\Controller;
use yii\helpers\Console;
use yii\rbac\Rule;

class AvailabilitySeederController extends Controller
{
    public function actionSeed()
    {
        $this->seedRuleValueType();
        $this->seedRuleType();
    }

    protected function seedRuleValueType()
    {
        $this->stdout('Seeding rule value type..', Console::FG_GREEN);
        $this->stdout("\n");
        if (RuleValueType::find()->count() > 0) {
            RuleValueType::deleteAll();
        }

        $types = [
            RuleValueType::TYPE_PERCENTAGE,
            RuleValueType::TYPE_FIXED
        ];
        foreach ($types as $type) {
            $attributeType = new RuleValueType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }

    protected function seedRuleType()
    {
        $this->stdout('Seeding rule type..', Console::FG_GREEN);
        $this->stdout("\n");
        if (RuleType::find()->count() > 0) {
            RuleType::deleteAll();
        }

        $types = [
            RuleType::TYPE_INCREASE,
            RuleType::TYPE_DECREASE
        ];
        foreach ($types as $type) {
            $attributeType = new RuleType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }
}