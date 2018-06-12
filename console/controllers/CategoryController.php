<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/11/18
 * Time: 8:35 PM
 */

namespace console\controllers;


use common\models\Category;
use common\models\CategoryCity;
use common\models\City;
use common\models\Country;
use common\oldmodels\ServiceCategory;
use yii\console\Controller;
use yii\helpers\Console;

class CategoryController extends Controller
{
    public static $countryMap = [
        'AE' => 'United Arab Emirates',
        'SA' => 'Saudi Arabia',
    ];

    public static $cityMap = [
        'dubai' => 'Dubai',
        'abu-dhabi' => 'Abu Dhabi',
        'ras-al-khaimah' => 'Ras Al Khaimah',
        'fujairah' => 'Fujairah',
        'umm-al-quwain' => 'Umm Al Quwain',
        'sharjah' => 'Sharjah',
        'ajman' => 'Ajman',
        'riyadh' => 'Riyadh',
        'jeddah' => 'Jeddah',
        'dammam' => 'Dammam',
        'khobar' => 'Khobar',
        'al-ain' => 'Al Ain',
        'eastern-region' => 'Eastern Region',
    ];
    public $map = [];

    public function actionMigrate()
    {
        // Delete all categories..
        \Yii::$app->getDb()->createCommand('DELETE FROM category_lang WHERE id > :id', [':id' => 0])->execute();
        Category::deleteAll(['>', 'id', 0]);
        CategoryCity::deleteAll(['>', 'id', 0]);

        $this->seedCountries();
        $this->seedCities();

        $oldCategories = ServiceCategory::find();

        /** @var ServiceCategory $category */
        foreach ($oldCategories->each(20) as $oldCategory) {

            $category = $this->createCategory($oldCategory);

            $this->map["oldie-" . $oldCategory->id] = $category;

            $this->stdout(" -> Created " . $oldCategory->name);
            $this->stdout("\r\n");

        }


        $oldCategories = ServiceCategory::find();
        $this->stdout(" -> Setting parents.. ");
        $this->stdout("\r\n");

        /** @var ServiceCategory $oldCategory */
        foreach ($oldCategories->each(20) as $oldCategory) {

            $category = $this->map["oldie-" . $oldCategory->id];

            if (!empty($oldCategory->parent_id)) {
                $category->parent_id = $this->map["oldie-" . $oldCategory->parent_id]->id;
                $category->save();

                $this->stdout(" -> Parent Updated " . $oldCategory->name);
                $this->stdout("\r\n");
            }
        }
    }

    /**
     * @param ServiceCategory $oldCategory
     * @return Category
     */
    private function createCategory($oldCategory)
    {
        $category = new Category();
        $category->name = $oldCategory->name;
        $category->description = trim(htmlspecialchars_decode(strip_tags($oldCategory->description)));
        $category->mobile_description = trim(htmlspecialchars_decode(strip_tags($oldCategory->mobile_description)));
        $category->active = $oldCategory->enable;
        $category->save();

        $this->updateCities($oldCategory, $category);

        return $category;
    }

    /**
     * @param $oldCategory ServiceCategory
     * @param $category Category
     * @return mixed
     */
    private function updateCities($oldCategory, $category)
    {
        if (empty($oldCategory->available_cities)) {
            return true;
        }

        $cities = explode(',', $oldCategory->available_cities);

        if (empty($cities)) {
            return true;
        }

        foreach ($cities as $city) {
            $dbCity = City::find()->where(['name' => CategoryController::$cityMap[$city]])->one();

            if (!$dbCity) {
                dd($city);
                continue;
            }

            $categoryCity = new CategoryCity();
            $categoryCity->category_id = $category->id;
            $categoryCity->city_id = $dbCity->id;
            $categoryCity->save();
        }

        return true;
    }

    protected function seedCountries()
    {
        $this->stdout('Seeding countries', Console::FG_GREEN);
        $this->stdout("\n");

        if (City::find()->count() > 0) {
            City::deleteAll();
        }

        if (Country::find()->count() > 0) {
            Country::deleteAll();
        }

        $options = [
            'United Arab Emirates', 'Saudi Arabia'
        ];
        foreach ($options as $option) {
            $attributeOption = new Country();
            $attributeOption->name = $option;
            $attributeOption->save();
        }
    }

    protected function seedCities()
    {
        $this->stdout('Seeding cities', Console::FG_GREEN);
        $this->stdout("\n");
        if (City::find()->count() > 0) {
            City::deleteAll();
        }

        $countries = [
            'United Arab Emirates' => [
                'Al Ain', 'Dubai', 'Sharjah', 'Ajman', 'Fujairah', 'Abu Dhabi', 'Al ain', 'Ras Al Khaimah', 'Umm Al Quwain'
            ],
            'Saudi Arabia' => [
                'Riyadh', 'Jeddah', 'Dammam', 'Khobar', 'Eastern Region'
            ]
        ];
        foreach ($countries as $country => $cities) {
            $country = Country::find()
                ->where(['name' => $country])
                ->one();

            if (!$country) {
                continue;
            }

            foreach ($cities as $city) {
                $attributeOption = new City();
                $attributeOption->name = $city;
                $attributeOption->country_id = $country->id;
                $attributeOption->save();
            }
        }
    }
}