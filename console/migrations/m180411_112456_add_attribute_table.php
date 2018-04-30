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
        $this->createTable('country', [
            'id' => $this->primaryKey(),
            'name' => $this->string()
        ]);

        $this->createTable('city', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'country_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-c-c', 'city', 'country_id', 'country', 'id');

        $this->createTable('input_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('user_input_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->createTable('attribute', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createTable('validation', [
            'id' => $this->primaryKey(),
            'type' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

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
            'attribute_id' => $this->integer(),
            'input_type_id' => $this->integer(),
            'user_input_type_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-sa-s', 'service_attribute', 'service_id', 'service', 'id');
        $this->addForeignKey('fk-sa-a', 'service_attribute', 'attribute_id', 'attribute', 'id');
        $this->addForeignKey('fk-sa-it', 'service_attribute', 'input_type_id', 'input_type', 'id');
        $this->addForeignKey('fk-sa-uit', 'service_attribute', 'user_input_type_id', 'user_input_type', 'id');

        $this->createTable('service_attribute_validation', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'validation_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-sav-sa', 'service_attribute_validation', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-sav-v', 'service_attribute_validation', 'validation_id', 'validation', 'id');

        $this->createTable('service_attribute_option', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'attribute_option_id' => $this->integer()
        ]);


        $this->addForeignKey('fk-sao-sa', 'service_attribute_option', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-sao-ao', 'service_attribute_option', 'attribute_option_id', 'attribute_option', 'id');


        $this->createTable('price_type', [
            'id' => $this->primaryKey(),
            'type' => $this->string()
        ]);

        $this->createTable('pricing_attribute_group', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'service_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-pag-s', 'pricing_attribute_group', 'service_id', 'service', 'id');

        $this->createTable('pricing_attribute', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'price_type_id' => $this->integer(),
            'pricing_attribute_group_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-pa-sa', 'pricing_attribute', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-pa-pt', 'pricing_attribute', 'price_type_id', 'price_type', 'id');
        $this->addForeignKey('fk-pa-pag', 'pricing_attribute', 'pricing_attribute_group_id', 'pricing_attribute_group', 'id');

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

        $this->createTable('service_type', [
            'id' => $this->primaryKey(),
            'type' => $this->string()
        ]);

        $this->createTable('provided_service_type', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'service_type_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-pst-ps', 'provided_service_type', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-pst-st', 'provided_service_type', 'service_type_id', 'service_type', 'id');

        $this->createTable('provided_service_area', [
            'id' => $this->primaryKey(),
            'provided_service_type_id' => $this->integer(),
            'name' => $this->string(),
            'city_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-psa-pst', 'provided_service_area', 'provided_service_type_id', 'provided_service_type', 'id');
        $this->addForeignKey('fk-psa-c', 'provided_service_area', 'city_id', 'city', 'id');

        $this->createTable('provided_service_matrix_pricing', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'pricing_attribute_parent_id' => $this->integer(),
            'price' => $this->double(),
            'provided_service_area_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psmp-ps', 'provided_service_matrix_pricing', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-psmp-pap', 'provided_service_matrix_pricing', 'pricing_attribute_parent_id', 'pricing_attribute_parent', 'id');
        $this->addForeignKey('fk-psmp-psa', 'provided_service_matrix_pricing', 'provided_service_area_id', 'provided_service_area', 'id');

        $this->createTable('provided_service_base_pricing', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'pricing_attribute_id' => $this->integer(),
            'base_price' => $this->double(),
            'provided_service_area_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psbp-ps', 'provided_service_base_pricing', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-psbp-pa', 'provided_service_base_pricing', 'pricing_attribute_id', 'pricing_attribute', 'id');
        $this->addForeignKey('fk-psbp-psa', 'provided_service_base_pricing', 'provided_service_area_id', 'provided_service_area', 'id');

        $this->createTable('service_attribute_depends', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'depends_on_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-sad-sa', 'service_attribute_depends', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-sad-sado', 'service_attribute_depends', 'depends_on_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-sad-sao', 'service_attribute_depends', 'service_attribute_option_id', 'service_attribute_option', 'id');

        $this->createTable('service_city', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer(),
            'service_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-sc-c', 'service_city', 'city_id', 'city', 'id');
        $this->addForeignKey('fk-sc-a', 'service_city', 'service_id', 'service', 'id');


        $this->createTable('provided_service_coverage', [
            'id' => $this->primaryKey(),
            'provided_service_area_id' => $this->integer(),
            'lat' => $this->string(),
            'lng' => $this->string()
        ]);

        $this->addForeignKey('fk-psc-psa', 'provided_service_coverage', 'provided_service_area_id', 'provided_service_area', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-avo-avi', 'attribute_validation_option');
        $this->dropForeignKey('fk-avo-vo', 'attribute_validation_option');
        $this->dropForeignKey('fk-sa-s', 'service_attribute');
        $this->dropForeignKey('fk-sa-a', 'service_attribute');
        $this->dropForeignKey('fk-sa-it', 'service_attribute');
        $this->dropForeignKey('fk-sav-sa', 'service_attribute_validation');
        $this->dropForeignKey('fk-sav-v', 'service_attribute_validation');
        $this->dropForeignKey('fk-sao-sa', 'service_attribute_option');
        $this->dropForeignKey('fk-sao-ao', 'service_attribute_option');
        $this->dropForeignKey('fk-pa-sa', 'pricing_attribute');
        $this->dropForeignKey('fk-pa-pt', 'pricing_attribute');
        $this->dropForeignKey('fk-pa-pag', 'pricing_attribute');
        $this->dropForeignKey('fk-pap-s', 'pricing_attribute_parent');
        $this->dropForeignKey('fk-pam-pap', 'pricing_attribute_matrix');
        $this->dropForeignKey('fk-pam-sao', 'pricing_attribute_matrix');
        $this->dropForeignKey('fk-psmp-ps', 'provided_service_matrix_pricing');
        $this->dropForeignKey('fk-psmp-pap', 'provided_service_matrix_pricing');
        $this->dropForeignKey('fk-psbp-ps', 'provided_service_base_pricing');
        $this->dropForeignKey('fk-psbp-pa', 'provided_service_base_pricing');
        $this->dropForeignKey('fk-sa-uit', 'service_attribute');
        $this->dropForeignKey('fk-sad-sa', 'service_attribute_depends');
        $this->dropForeignKey('fk-sad-sado', 'service_attribute_depends');
        $this->dropForeignKey('fk-sad-sao', 'service_attribute_depends');
        $this->dropForeignKey('fk-c-c', 'city');
        $this->dropForeignKey('fk-sc-c', 'service_city');
        $this->dropForeignKey('fk-sc-a', 'service_city');
        $this->dropForeignKey('fk-pst-ps', 'provided_service_type');
        $this->dropForeignKey('fk-pst-st', 'provided_service_type');
        $this->dropForeignKey('fk-psc-psa', 'provided_service_coverage');
        $this->dropForeignKey('fk-psbp-psa', 'provided_service_base_pricing');
        $this->dropForeignKey('fk-psmp-psa', 'provided_service_matrix_pricing');
        $this->dropForeignKey('fk-psa-pst', 'provided_service_area');
        $this->dropForeignKey('fk-psa-c', 'provided_service_area');


        $this->dropTable('service_attribute_option');
        $this->dropTable('service_attribute');
        $this->dropTable('attribute_option');
        $this->dropTable('input_type');
        $this->dropTable('user_input_type');
        $this->dropTable('service_attribute_validation');
        $this->dropTable('attribute_validation_option');
        $this->dropTable('validation_option');
        $this->dropTable('validation');
        $this->dropTable('attribute');
        $this->dropTable('price_type');
        $this->dropTable('pricing_attribute_group');
        $this->dropTable('pricing_attribute');
        $this->dropTable('pricing_attribute_parent');
        $this->dropTable('pricing_attribute_matrix');
        $this->dropTable('provided_service_matrix_pricing');
        $this->dropTable('provided_service_base_pricing');
        $this->dropTable('service_attribute_depends');
        $this->dropTable('country');
        $this->dropTable('city');
        $this->dropTable('service_city');
        $this->dropTable('provided_service_area');
        $this->dropTable('service_type');
        $this->dropTable('provided_service_type');
        $this->dropTable('provided_service_coverage');
    }
}
