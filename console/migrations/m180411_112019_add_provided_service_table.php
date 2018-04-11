<?php

use yii\db\Migration;

/**
 * Class m180411_112019_add_provided_service_table
 */
class m180411_112019_add_provided_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('provided_service', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'provider_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk-ps-p', 'provided_service', 'provider_id', 'provider', 'id');
        $this->addForeignKey('fk-ps-s', 'provided_service', 'service_id', 'service', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('provided_service');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180411_112019_add_provided_service_table cannot be reverted.\n";

        return false;
    }
    */
}
