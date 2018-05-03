<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/3/18
 * Time: 5:01 PM
 */

namespace console\controllers;


use common\models\RuleValueType;
use yii\console\Controller;
use yii\helpers\Console;
use yii\rbac\Rule;

class AvailabilitySeederController extends Controller
{
    public function actionSeed()
    {
        $this->stdout("\n");
        $this->stdout('Running seeders start..', Console::FG_GREEN);
        $this->stdout("\n");
        $this->stdout("\n");

        $this->seedRuleValueType();

        $this->stdout("\n");
        $this->stdout('Running seeders complete..', Console::FG_GREEN);
        $this->stdout("\n");
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
            RuleValueType::TYPE_PREFIX
        ];
        foreach ($types as $type) {
            $attributeType = new RuleValueType();
            $attributeType->name = $type;
            $attributeType->save();
        }
    }
}