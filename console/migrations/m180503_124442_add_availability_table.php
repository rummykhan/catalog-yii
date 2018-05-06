<?php

use yii\db\Migration;

/**
 * Class m180503_124442_add_availability_table
 */
class m180503_124442_add_availability_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('rule_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->createTable('rule_value_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->createTable('global_availability_rule', [
            'id' => $this->primaryKey(),
            'provided_service_area_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'rule_value' => $this->integer(),
            'rule_value_type_id' => $this->integer(),
            'rule_type_id' => $this->integer(),
            'day' => $this->string()
        ]);

        $this->addForeignKey('fk-gar-psa', 'global_availability_rule', 'provided_service_area_id', 'provided_service_area', 'id');
        $this->addForeignKey('fk-gar-rvt', 'global_availability_rule', 'rule_value_type_id', 'rule_value_type', 'id');
        $this->addForeignKey('fk-gar-rt', 'global_availability_rule', 'rule_type_id', 'rule_type', 'id');

        $this->createTable('availability_rule', [
            'id' => $this->primaryKey(),
            'provided_service_area_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'rule_value' => $this->integer(),
            'rule_value_type_id' => $this->integer(),
            'rule_type_id' => $this->integer(),
            'date' => $this->string()
        ]);

        $this->addForeignKey('fk-ar-psa', 'availability_rule', 'provided_service_area_id', 'provided_service_area', 'id');
        $this->addForeignKey('fk-ar-rvt', 'availability_rule', 'rule_value_type_id', 'rule_value_type', 'id');
        $this->addForeignKey('fk-ar-rt', 'availability_rule', 'rule_type_id', 'rule_type', 'id');

        $this->createTable('global_availability_exception', [
            'id' => $this->primaryKey(),
            'provided_service_area_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'day' => $this->string()
        ]);

        $this->addForeignKey('fk-gae-psa', 'global_availability_exception', 'provided_service_area_id', 'provided_service_area', 'id');

        $this->createTable('availability_exception', [
            'id' => $this->primaryKey(),
            'provided_service_area_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'date' => $this->string()
        ]);

        $this->addForeignKey('fk-ae-psa', 'availability_exception', 'provided_service_area_id', 'provided_service_area', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-ae-psa', 'availability_exception');
        $this->dropForeignKey('fk-gae-psa', 'global_availability_exception');
        $this->dropForeignKey('fk-ar-psa', 'availability_rule');
        $this->dropForeignKey('fk-ar-rvt', 'availability_rule');
        $this->dropForeignKey('fk-gar-psa', 'global_availability_rule');
        $this->dropForeignKey('fk-gar-rvt', 'global_availability_rule');
        $this->dropForeignKey('fk-ar-rt', 'availability_rule');
        $this->dropForeignKey('fk-gar-rt', 'global_availability_rule');


        $this->dropTable('availability_exception');
        $this->dropTable('global_availability_exception');
        $this->dropTable('availability_rule');
        $this->dropTable('global_availability_rule');
        $this->dropTable('rule_value_type');
        $this->dropTable('rule_type');
    }
}
