<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/9/18
 * Time: 2:21 PM
 */

namespace console\controllers;


use common\models\Category;
use common\models\Service;
use Faker\Factory;
use Faker\Generator;
use yii\console\Controller;
use yii\helpers\Console;

class CatalogSeederController extends Controller
{
    /**
     * @var Generator
     */
    protected $faker;

    public function init()
    {
        $this->faker = Factory::create();
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function actionSeed()
    {
        $this->stdout("\n");
        $this->stdout('Seeding categories..', Console::FG_GREEN);
        $this->stdout("\n");

        if (Category::find()->count() > 0) {
            Category::deleteAll([]);
        }

        $graph = [
            [

                'name' => 'Home',
                'description' => $this->faker->text(500),
                'sub' => [
                    [
                        'name' => 'Cleaning',
                        'description' => $this->faker->text(500),
                        'mobile_description' => $this->faker->text(500),
                        'services' => [
                            [
                                'name' => 'House Cleaning',
                                'description' => $this->faker->text(500),
                                'mobile_description' => $this->faker->text(500),
                            ]
                        ]
                    ],
                    [
                        'name' => 'Moving',
                        'description' => $this->faker->text(500),
                        'mobile_description' => $this->faker->text(500),
                        'services' => [
                            [
                                'name' => 'House Moving',
                                'description' => $this->faker->text(500),
                                'mobile_description' => $this->faker->text(500),
                            ]
                        ]
                    ],
                ],
            ],
            [

                'name' => 'Electronics',
                'description' => $this->faker->text(500),
                'sub' => [
                    [
                        'name' => 'Mobile',
                        'description' => $this->faker->text(500),
                        'mobile_description' => $this->faker->text(500),
                        'services' => [
                            [
                                'name' => 'IPhone X Repair',
                                'description' => $this->faker->text(500),
                                'mobile_description' => $this->faker->text(500),
                            ]
                        ]
                    ]
                ]
            ],
        ];

        foreach ($graph as $item) {
            $this->saveCategory($item);
        }
    }


    protected function saveCategory($item, $parent = null)
    {
        $category = new Category();
        $category->name = $item['name'];
        $category->description = $item['description'];
        $category->active = true;

        if (!empty($parent)) {
            $category->parent_id = $parent->id;
        }

        $category->save();

        if (isset($item['sub'])) {
            foreach ($item['sub'] as $sub_categories) {
                $this->saveCategory($sub_categories, $category);
            }
        }

        if (isset($item['services'])) {
            foreach ($item['services'] as $service) {
                $this->saveService($service, $category);
            }
        }
    }

    protected function saveService($item, $category)
    {
        $service = new Service();
        $service->name = $item['name'];
        $service->description = $item['description'];
        $service->mobile_description = $item['mobile_description'];
        $service->active = true;
        $service->category_id = $category->id;
        $service->save();
    }
}