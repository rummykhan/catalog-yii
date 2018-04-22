<?php

use yii\db\Migration;

/**
 * Class m180411_112456_add_attribute_table
 */
class m180411_112456_add_attribute_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('attribute_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('attribute_input_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('attribute', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'type' => $this->integer(),
            'input_type' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createTable('validation', [
            'id' => $this->primaryKey(),
            'type' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('attribute_validation', [
            'id' => $this->primaryKey(),
            'attribute_id' => $this->integer(),
            'validation_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-av-a', 'attribute_validation', 'attribute_id', 'attribute', 'id');
        $this->addForeignKey('fk-av-v', 'attribute_validation', 'validation_id', 'validation', 'id');

        $this->addForeignKey('fk-a-at', 'attribute', 'type', 'attribute_type', 'id');
        $this->addForeignKey('fk-a-ait', 'attribute', 'input_type', 'attribute_input_type', 'id');


        $this->createTable('validation_option', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('attribute_validation_option', [
            'id' => $this->primaryKey(),
            'attribute_validation_id' => $this->integer(),
            'validation_option_id' => $this->integer(),
            'value' => $this->string()
        ]);

        $this->addForeignKey('fk-avo-avi', 'attribute_validation_option', 'attribute_validation_id', 'validation_option', 'id');
        $this->addForeignKey('fk-avo-vo', 'attribute_validation_option', 'validation_option_id', 'validation_option', 'id');

        $this->createTable('attribute_option', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('service_attribute', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'attribute_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-sa-s', 'service_attribute', 'service_id', 'service', 'id');
        $this->addForeignKey('fk-sa-a', 'service_attribute', 'attribute_id', 'attribute', 'id');

        $this->createTable('service_attribute_option', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'attribute_option_id' => $this->integer()
        ]);


        $this->addForeignKey('fk-sao-sa',
            'service_attribute_option',
            'service_attribute_id',
            'service_attribute',
            'id'
        );
        $this->addForeignKey('fk-sao-ao',
            'service_attribute_option',
            'attribute_option_id',
            'attribute_option',
            'id'
        );


        $this->createTable('price_type', [
            'id' => $this->primaryKey(),
            'type' => $this->string()
        ]);

        $this->createTable('pricing_attribute', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'price_type_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-pa-sa', 'pricing_attribute', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-pa-pt', 'pricing_attribute', 'price_type_id', 'price_type', 'id');

        $this->createTable('pricing_attribute_parent', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-pap-s', 'pricing_attribute_parent', 'service_id', 'service', 'id');

        $this->createTable('pricing_attribute_matrix', [
            'id' => $this->primaryKey(),
            'pricing_attribute_parent_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-pam-pap', 'pricing_attribute_matrix', 'pricing_attribute_parent_id', 'pricing_attribute_parent', 'id');
        $this->addForeignKey('fk-pam-sao', 'pricing_attribute_matrix', 'service_attribute_option_id', 'service_attribute_option', 'id');

        $this->createTable('provided_service_matrix_pricing', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'pricing_attribute_parent_id' => $this->integer(),
            'price' => $this->double(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psmp-ps', 'provided_service_matrix_pricing', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-psmp-pap', 'provided_service_matrix_pricing', 'pricing_attribute_parent_id', 'pricing_attribute_parent', 'id');

        $this->createTable('provided_service_base_pricing', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'pricing_attribute_id' => $this->integer(),
            'base_price' => $this->double(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psbp-ps', 'provided_service_base_pricing', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-psbp-pa', 'provided_service_base_pricing', 'pricing_attribute_id', 'pricing_attribute', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('fk-sao-ao', 'service_attribute_option');
        $this->dropForeignKey('fk-sao-sa', 'service_attribute_option');
        $this->dropForeignKey('fk-sa-a', 'service_attribute');
        $this->dropForeignKey('fk-sa-s', 'service_attribute');
        $this->dropForeignKey('fk-a-at', 'attribute');
        $this->dropForeignKey('fk-a-ait', 'attribute');
        $this->dropForeignKey('fk-av-a', 'attribute_validation');
        $this->dropForeignKey('fk-av-v', 'attribute_validation');
        $this->dropForeignKey('fk-avo-avi', 'attribute_validation_option');
        $this->dropForeignKey('fk-avo-vo', 'attribute_validation_option');
        $this->dropForeignKey('fk-pa-sa', 'pricing_attribute');
        $this->dropForeignKey('fk-pa-pt', 'pricing_attribute');
        $this->dropForeignKey('fk-pap-s', 'pricing_attribute_parent');
        $this->dropForeignKey('fk-pam-pap', 'pricing_attribute_matrix');
        $this->dropForeignKey('fk-pam-sao', 'pricing_attribute_matrix');
        $this->dropForeignKey('fk-psmp-ps', 'provided_service_matrix_pricing');
        $this->dropForeignKey('fk-psmp-pap', 'provided_service_matrix_pricing');
        $this->dropForeignKey('fk-psbp-ps', 'provided_service_base_pricing');
        $this->dropForeignKey('fk-psbp-pa', 'provided_service_base_pricing');


        $this->dropTable('service_attribute_option');
        $this->dropTable('service_attribute');
        $this->dropTable('attribute_option');
        $this->dropTable('attribute_type');
        $this->dropTable('attribute_input_type');
        $this->dropTable('attribute_validation');
        $this->dropTable('attribute_validation_option');
        $this->dropTable('validation_option');
        $this->dropTable('validation');
        $this->dropTable('attribute');
        $this->dropTable('price_type');
        $this->dropTable('pricing_attribute');
        $this->dropTable('pricing_attribute_parent');
        $this->dropTable('pricing_attribute_matrix');
        $this->dropTable('provided_service_matrix_pricing');
        $this->dropTable('provided_service_base_pricing');
    }
}
