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

        $this->createTable('calendar', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'provider_id' => $this->integer(),
            'deleted' => $this->boolean()->defaultValue(false)
        ]);

        $this->addForeignKey('fk-cal-p', 'calendar', 'provider_id', 'provider', 'id');

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

        $this->createTable('field_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string()
        ]);

        $this->createTable('service_attribute', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'name' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
            'icon' => $this->string(),
            'input_type_id' => $this->integer(),
            'user_input_type_id' => $this->integer(),
            'field_type_id' => $this->integer(),
            'deleted' => $this->boolean()->defaultValue(false),
            'order' => $this->integer(),
        ]);

        $this->addForeignKey('fk-sa-s', 'service_attribute', 'service_id', 'service', 'id');
        $this->addForeignKey('fk-sa-it', 'service_attribute', 'input_type_id', 'input_type', 'id');
        $this->addForeignKey('fk-sa-uit', 'service_attribute', 'user_input_type_id', 'user_input_type', 'id');
        $this->addForeignKey('fk-sa-ft', 'service_attribute', 'field_type_id', 'field_type', 'id');

        $this->createTable('service_attribute_lang', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'language' => $this->string(),
            'name' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string()
        ]);

        $this->addForeignKey('fk-sal-sa', 'service_attribute_lang', 'service_attribute_id', 'service_attribute', 'id');

        $this->createTable('service_view', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'name' => $this->string(),
        ]);

        $this->addForeignKey('fk-svv-s', 'service_view', 'service_id', 'service', 'id');

        $this->createTable('service_view_attribute', [
            'id' => $this->primaryKey(),
            'service_view_id' => $this->integer(),
            'service_attribute_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-svva-s', 'service_view_attribute', 'service_view_id', 'service_view', 'id');
        $this->addForeignKey('fk-svva-sa', 'service_view_attribute', 'service_attribute_id', 'service_attribute', 'id');

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
            'name' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
            'icon' => $this->string(),
            'order' => $this->integer(),
            'deleted' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey('fk-sao-sa', 'service_attribute_option', 'service_attribute_id', 'service_attribute', 'id');

        $this->createTable('service_attribute_option_lang', [
            'id' => $this->primaryKey(),
            'service_attribute_option_id' => $this->integer(),
            'language' => $this->string(),
            'name' => $this->string(),
            'description' => $this->string(),
            'mobile_description' => $this->string(),
        ]);

        $this->addForeignKey('fk-saop-sao', 'service_attribute_option_lang', 'service_attribute_option_id', 'service_attribute_option', 'id');


        $this->createTable('price_type', [
            'id' => $this->primaryKey(),
            'type' => $this->string(),
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
            'pricing_attribute_group_id' => $this->integer(),
            'service_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-pa-sa', 'pricing_attribute', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-pa-pt', 'pricing_attribute', 'price_type_id', 'price_type', 'id');
        $this->addForeignKey('fk-pa-pag', 'pricing_attribute', 'pricing_attribute_group_id', 'pricing_attribute_group', 'id');
        $this->addForeignKey('fk-pa-s', 'pricing_attribute', 'service_id', 'service', 'id');

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

        $this->createTable('request_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string()
        ]);

        $this->createTable('service_request_type', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(),
            'request_type_id' => $this->integer(),
            'deleted' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey('fk-srt-s', 'service_request_type', 'service_id', 'service', 'id');
        $this->addForeignKey('fk-srt-rt', 'service_request_type', 'request_type_id', 'request_type', 'id');

        $this->createTable('provided_request_type', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'service_request_type_id' => $this->integer(),
            'deleted' => $this->boolean()->defaultValue(false)
        ]);

        $this->addForeignKey('fk-prt-ps', 'provided_request_type', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-prt-srt', 'provided_request_type', 'service_request_type_id', 'service_request_type', 'id');

        $this->createTable('service_area', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'city_id' => $this->integer(),
            'provider_id' => $this->integer(),
            'deleted' => $this->boolean()->defaultValue(false)
        ]);

        $this->addForeignKey('fk-sa-c', 'service_area', 'city_id', 'city', 'id');
        $this->addForeignKey('fk-sa-p', 'service_area', 'provider_id', 'provider', 'id');

        $this->createTable('provided_service_type', [
            'id' => $this->primaryKey(),
            'provided_service_id' => $this->integer(),
            'calendar_id' => $this->integer(),
            'service_area_id' => $this->integer(),
            'service_request_type_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-pst-ps', 'provided_service_type', 'provided_service_id', 'provided_service', 'id');
        $this->addForeignKey('fk-pst-cal', 'provided_service_type', 'calendar_id', 'calendar', 'id');
        $this->addForeignKey('fk-pst-sa', 'provided_service_type', 'service_area_id', 'service_area', 'id');
        $this->addForeignKey('fk-pst-srt', 'provided_service_type', 'service_request_type_id', 'service_request_type', 'id');

        $this->createTable('provided_service_composite_pricing', [
            'id' => $this->primaryKey(),
            'pricing_attribute_parent_id' => $this->integer(),
            'price' => $this->double(),
            'provided_service_type_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psmp-pap', 'provided_service_composite_pricing', 'pricing_attribute_parent_id', 'pricing_attribute_parent', 'id');
        $this->addForeignKey('fk-psmp-psa', 'provided_service_composite_pricing', 'provided_service_type_id', 'provided_service_type', 'id');

        $this->createTable('provided_service_independent_pricing', [
            'id' => $this->primaryKey(),
            'pricing_attribute_id' => $this->integer(),
            'base_price' => $this->double(),
            'provided_service_type_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psbp-pa', 'provided_service_independent_pricing', 'pricing_attribute_id', 'pricing_attribute', 'id');
        $this->addForeignKey('fk-psbp-psa', 'provided_service_independent_pricing', 'provided_service_type_id', 'provided_service_type', 'id');
        $this->addForeignKey('fk-psbp-sao', 'provided_service_independent_pricing', 'service_attribute_option_id', 'service_attribute_option', 'id');

        $this->createTable('provided_service_no_impact_pricing', [
            'id' => $this->primaryKey(),
            'pricing_attribute_id' => $this->integer(),
            'provided_service_type_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk-psnip-pa', 'provided_service_no_impact_pricing', 'pricing_attribute_id', 'pricing_attribute', 'id');
        $this->addForeignKey('fk-psnip-psa', 'provided_service_no_impact_pricing', 'provided_service_type_id', 'provided_service_type', 'id');
        $this->addForeignKey('fk-psnip-sao', 'provided_service_no_impact_pricing', 'service_attribute_option_id', 'service_attribute_option', 'id');

        $this->createTable('service_composite_attribute_parent', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-scap-s', 'service_composite_attribute_parent', 'service_id', 'service', 'id');

        $this->createTable('service_composite_attribute', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer(),
            'service_composite_attribute_parent_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-scca-sa', 'service_composite_attribute', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-scca-sao', 'service_composite_attribute', 'service_attribute_option_id', 'service_attribute_option', 'id');
        $this->addForeignKey('fk-scca-scap', 'service_composite_attribute', 'service_composite_attribute_parent_id', 'service_composite_attribute_parent', 'id');

        $this->createTable('service_composite_attribute_child', [
            'id' => $this->primaryKey(),
            'service_attribute_id' => $this->integer(),
            'service_attribute_option_id' => $this->integer(),
            'service_composite_attribute_parent_id' => $this->integer()
        ]);

        $this->addForeignKey('fk-sccac-sa', 'service_composite_attribute_child', 'service_attribute_id', 'service_attribute', 'id');
        $this->addForeignKey('fk-sccac-sao', 'service_composite_attribute_child', 'service_attribute_option_id', 'service_attribute_option', 'id');
        $this->addForeignKey('fk-sccac-scap', 'service_composite_attribute_child', 'service_composite_attribute_parent_id', 'service_composite_attribute_parent', 'id');

        $this->createTable('service_city', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer(),
            'service_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-sc-c', 'service_city', 'city_id', 'city', 'id');
        $this->addForeignKey('fk-sc-a', 'service_city', 'service_id', 'service', 'id');

        $this->createTable('category_city', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer(),
            'category_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-cc-c', 'category_city', 'city_id', 'city', 'id');
        $this->addForeignKey('fk-cc-ca', 'category_city', 'category_id', 'category', 'id');


        $this->createTable('service_area_coverage', [
            'id' => $this->primaryKey(),
            'service_area_id' => $this->integer(),
            'lat' => $this->string(),
            'lng' => $this->string(),
            'radius' => $this->decimal(),
        ]);

        $this->addForeignKey('fk-sac-sa', 'service_area_coverage', 'service_area_id', 'service_area', 'id');


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
            'calendar_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'rule_value' => $this->integer(),
            'rule_value_type_id' => $this->integer(),
            'rule_type_id' => $this->integer(),
            'day' => $this->string()
        ]);

        $this->addForeignKey('fk-gar-psa', 'global_availability_rule', 'calendar_id', 'calendar', 'id');
        $this->addForeignKey('fk-gar-rvt', 'global_availability_rule', 'rule_value_type_id', 'rule_value_type', 'id');
        $this->addForeignKey('fk-gar-rt', 'global_availability_rule', 'rule_type_id', 'rule_type', 'id');

        $this->createTable('availability_rule', [
            'id' => $this->primaryKey(),
            'calendar_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'rule_value' => $this->integer(),
            'rule_value_type_id' => $this->integer(),
            'rule_type_id' => $this->integer(),
            'date' => $this->string()
        ]);

        $this->addForeignKey('fk-ar-psa', 'availability_rule', 'calendar_id', 'calendar', 'id');
        $this->addForeignKey('fk-ar-rvt', 'availability_rule', 'rule_value_type_id', 'rule_value_type', 'id');
        $this->addForeignKey('fk-ar-rt', 'availability_rule', 'rule_type_id', 'rule_type', 'id');

        $this->createTable('global_availability_exception', [
            'id' => $this->primaryKey(),
            'calendar_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'day' => $this->string()
        ]);

        $this->addForeignKey('fk-gae-psa', 'global_availability_exception', 'calendar_id', 'calendar', 'id');

        $this->createTable('availability_exception', [
            'id' => $this->primaryKey(),
            'calendar_id' => $this->integer(),
            'start_time' => $this->integer(),
            'end_time' => $this->integer(),
            'date' => $this->string()
        ]);

        $this->addForeignKey('fk-ae-psa', 'availability_exception', 'calendar_id', 'calendar', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-avo-avi', 'attribute_validation_option');
        $this->dropForeignKey('fk-avo-vo', 'attribute_validation_option');
        $this->dropForeignKey('fk-sa-s', 'service_attribute');
        $this->dropForeignKey('fk-sa-it', 'service_attribute');
        $this->dropForeignKey('fk-sav-sa', 'service_attribute_validation');
        $this->dropForeignKey('fk-sav-v', 'service_attribute_validation');
        $this->dropForeignKey('fk-sao-sa', 'service_attribute_option');
        $this->dropForeignKey('fk-pa-sa', 'pricing_attribute');
        $this->dropForeignKey('fk-pa-pt', 'pricing_attribute');
        $this->dropForeignKey('fk-pa-pag', 'pricing_attribute');
        $this->dropForeignKey('fk-pa-s', 'pricing_attribute');
        $this->dropForeignKey('fk-pap-s', 'pricing_attribute_parent');
        $this->dropForeignKey('fk-pam-pap', 'pricing_attribute_matrix');
        $this->dropForeignKey('fk-pam-sao', 'pricing_attribute_matrix');
        $this->dropForeignKey('fk-psmp-pap', 'provided_service_composite_pricing');
        $this->dropForeignKey('fk-psbp-pa', 'provided_service_independent_pricing');
        $this->dropForeignKey('fk-sa-uit', 'service_attribute');
        $this->dropForeignKey('fk-c-c', 'city');
        $this->dropForeignKey('fk-sc-c', 'service_city');
        $this->dropForeignKey('fk-sc-a', 'service_city');
        $this->dropForeignKey('fk-prt-ps', 'provided_request_type');
        $this->dropForeignKey('fk-prt-srt', 'provided_request_type');
        $this->dropForeignKey('fk-sac-sa', 'service_area_coverage');
        $this->dropForeignKey('fk-psbp-psa', 'provided_service_independent_pricing');
        $this->dropForeignKey('fk-psmp-psa', 'provided_service_composite_pricing');
        $this->dropForeignKey('fk-sa-ft', 'service_attribute');
        $this->dropForeignKey('fk-psbp-sao', 'provided_service_independent_pricing');
        $this->dropForeignKey('fk-psnip-pa', 'provided_service_no_impact_pricing');
        $this->dropForeignKey('fk-psnip-psa', 'provided_service_no_impact_pricing');
        $this->dropForeignKey('fk-psnip-sao', 'provided_service_no_impact_pricing');
        $this->dropForeignKey('fk-svv-s', 'service_view');
        $this->dropForeignKey('fk-svva-s', 'service_view_attribute');
        $this->dropForeignKey('fk-svva-sa', 'service_view_attribute');
        $this->dropForeignKey('fk-sal-sa', 'service_attribute_lang');
        $this->dropForeignKey('fk-saop-sao', 'service_attribute_option_lang');
        $this->dropForeignKey('fk-scap-s', 'service_composite_attribute_parent');
        $this->dropForeignKey('fk-scca-sa', 'service_composite_attribute');
        $this->dropForeignKey('fk-scca-sao', 'service_composite_attribute');
        $this->dropForeignKey('fk-scca-scap', 'service_composite_attribute');
        $this->dropForeignKey('fk-sccac-sa', 'service_composite_attribute_child');
        $this->dropForeignKey('fk-sccac-sao', 'service_composite_attribute_child');
        $this->dropForeignKey('fk-sccac-scap', 'service_composite_attribute_child');
        $this->dropForeignKey('fk-sa-c', 'service_area');
        $this->dropForeignKey('fk-sa-p', 'service_area');
        $this->dropForeignKey('fk-srt-s', 'service_request_type');
        $this->dropForeignKey('fk-srt-rt', 'service_request_type');
        $this->dropForeignKey('fk-pst-ps', 'provided_service_type');
        $this->dropForeignKey('fk-pst-cal', 'provided_service_type');
        $this->dropForeignKey('fk-pst-sa', 'provided_service_type');
        $this->dropForeignKey('fk-pst-srt', 'provided_service_type');

        $this->dropForeignKey('fk-cal-p', 'calendar');
        $this->dropForeignKey('fk-ae-psa', 'availability_exception');
        $this->dropForeignKey('fk-gae-psa', 'global_availability_exception');
        $this->dropForeignKey('fk-ar-psa', 'availability_rule');
        $this->dropForeignKey('fk-ar-rvt', 'availability_rule');
        $this->dropForeignKey('fk-gar-psa', 'global_availability_rule');
        $this->dropForeignKey('fk-gar-rvt', 'global_availability_rule');
        $this->dropForeignKey('fk-ar-rt', 'availability_rule');
        $this->dropForeignKey('fk-gar-rt', 'global_availability_rule');
        $this->dropForeignKey('fk-cc-c', 'category_city');
        $this->dropForeignKey('fk-cc-ca', 'category_city');

        $this->dropTable('service_attribute_option');
        $this->dropTable('service_attribute');
        $this->dropTable('input_type');
        $this->dropTable('user_input_type');
        $this->dropTable('service_attribute_validation');
        $this->dropTable('attribute_validation_option');
        $this->dropTable('validation_option');
        $this->dropTable('validation');
        $this->dropTable('price_type');
        $this->dropTable('pricing_attribute_group');
        $this->dropTable('pricing_attribute');
        $this->dropTable('pricing_attribute_parent');
        $this->dropTable('pricing_attribute_matrix');
        $this->dropTable('provided_service_composite_pricing');
        $this->dropTable('provided_service_independent_pricing');
        $this->dropTable('provided_service_no_impact_pricing');
        $this->dropTable('country');
        $this->dropTable('city');
        $this->dropTable('service_city');
        $this->dropTable('request_type');
        $this->dropTable('service_request_type');
        $this->dropTable('provided_request_type');
        $this->dropTable('service_area');
        $this->dropTable('service_area_coverage');
        $this->dropTable('field_type');
        $this->dropTable('service_view');
        $this->dropTable('service_view_attribute');
        $this->dropTable('service_attribute_lang');
        $this->dropTable('service_attribute_option_lang');
        $this->dropTable('provided_service_type');
        $this->dropTable('service_composite_attribute_parent');
        $this->dropTable('service_composite_attribute');
        $this->dropTable('service_composite_attribute_child');
        $this->dropTable('category_city');

        $this->dropTable('calendar');
        $this->dropTable('availability_exception');
        $this->dropTable('global_availability_exception');
        $this->dropTable('availability_rule');
        $this->dropTable('global_availability_rule');
        $this->dropTable('rule_value_type');
        $this->dropTable('rule_type');


    }
}
