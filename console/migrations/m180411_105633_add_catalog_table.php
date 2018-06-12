<?php

use yii\db\Migration;

/**
 * Class m180411_105633_add_catalog_table
 */
class m180411_105633_add_catalog_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'image' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
            'active' => $this->boolean()->defaultValue(false),
            'order' => $this->integer(),
            'mobile_ui_style' => $this->integer(),
            'parent_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('category_lang', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'language' => $this->string(),
            'name' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
        ]);

        $this->addForeignKey('fk-parent-id-category-id', 'category', 'parent_id', 'category', 'id');
        $this->addForeignKey('fk-category_lang-category', 'category_lang', 'category_id', 'category', 'id');

        $this->createTable('service', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'image' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
            'active' => $this->boolean()->defaultValue(false),
            'order' => $this->integer(),
            'mobile_ui_style' => $this->integer(),
            'category_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('service_lang', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'language' => $this->string(),
            'name' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
        ]);

        $this->addForeignKey('fk-service-category', 'service', 'category_id', 'category', 'id');
        $this->addForeignKey('fk-service_lang_service', 'service_lang', 'service_id', 'service', 'id');


        $this->createTable('provider', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->unique(),
            'password' => $this->string(),
            'email' => $this->string()->unique(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('provided_service', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'provider_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk-provided_service-provider', 'provided_service', 'provider_id', 'provider', 'id');
        $this->addForeignKey('fk-provided_service-service', 'provided_service', 'service_id', 'service', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-parent-id-category-id', 'category');
        $this->dropForeignKey('fk-category_lang-category', 'category_lang');
        $this->dropForeignKey('fk-service-category', 'service');
        $this->dropForeignKey('fk-service_lang_service', 'service_lang');
        $this->dropForeignKey('fk-provided_service-provider', 'provided_service');
        $this->dropForeignKey('fk-provided_service-service', 'provided_service');

        $this->dropTable('category');
        $this->dropTable('category_lang');
        $this->dropTable('service');
        $this->dropTable('service_lang');
        $this->dropTable('provider');
        $this->dropTable('provided_service');
    }
}
