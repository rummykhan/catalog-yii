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
        $this->createTable('attribute', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'type' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createTable('attribute_option', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'attribute_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk-ao-a', 'attribute_option', 'attribute_id', 'attribute', 'id');

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



        $this->createTable('provided_service_attribute', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'service_attribute_id' => $this->integer(),
        ]);



        $this->addForeignKey(
            'fk-psa-ps',
            'provided_service_attribute',
            'provided_service_id',
            'provided_service',
            'id'
        );
        $this->addForeignKey(
            'fk-psa-sa',
            'provided_service_attribute',
            'service_attribute_id',
            'service_attribute',
            'id'
        );



        $this->createTable('provided_service_attribute_option', [
            'id' => $this->primaryKey(),
            'provided_service_attribute_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer()
        ]);



        $this->addForeignKey(
            'fk-psao-psa',
            'provided_service_attribute_option',
            'provided_service_attribute_id',
            'provided_service_attribute',
            'id'
        );

        $this->addForeignKey(
            'fk-psao-pso',
            'provided_service_attribute_option',
            'service_attribute_option_id',
            'service_attribute_option',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('attribute_option');
        $this->dropTable('attribute');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180411_112456_add_attribute_table cannot be reverted.\n";

        return false;
    }
    */
}
