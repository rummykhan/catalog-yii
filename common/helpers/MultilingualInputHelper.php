<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/9/18
 * Time: 2:44 PM
 */

namespace common\helpers;


use dosamigos\ckeditor\CKEditor;
use Exception;
use Yii;
use yii\base\Model;
use yii\bootstrap\Tabs;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class MultilingualInputHelper
{

    /**
     * @param $form ActiveForm
     * @param $model Model
     * @param $attribute
     * @param bool $disabled
     * @return mixed
     */
    public static function textInputs($form, $model, $attribute, $disabled = false)
    {
        $items = [];
        $items[] = [
            'label' => Yii::$app->params['defaultLanguageLabel'],
            'content' => $form->field($model, $attribute)->textInput(['maxlength' => true, 'disabled' => $disabled]),
            'active' => true,
            'linkOptions' => [
                'data-multilingual-tab' => 'multilingual-inputs-' . Yii::$app->params['defaultLanguage']
            ],
            'headerOptions' => [
                'class' => 'multilingual-inputs-' . Yii::$app->params['defaultLanguage'],
            ]
        ];
        foreach (Yii::$app->params['languages'] as $key => $lang) {
            if ($key != Yii::$app->params['defaultLanguage']) {
                $items[] = [
                    'label' => $lang,
                    'content' => $form->field($model, $attribute . '_' . $key)->textInput(['maxlength' => true])->label($model->getAttributeLabel($attribute) . " - " . $lang),
                    'linkOptions' => [
                        'data-multilingual-tab' => 'multilingual-inputs-' . $key,
                    ],
                    'headerOptions' => [
                        'class' => 'multilingual-inputs-' . $key
                    ]
                ];
            }
        }
        return Tabs::widget(['items' => $items, 'clientEvents' => [
            'shown.bs.tab' => "function (e) { "
                /* - */ . "console.log(e.target);"
                /* - */ . "$('.'+$(e.target).data('multilingual-tab')+':not(.active) > a').each("
                /* --- */ . "function(index, value){ $(value).tab('show')"
                /**/ . "}); "
                . "}"
        ],
        ]);
    }

    /**
     * @param $form ActiveForm
     * @param $model Model
     * @param $attribute
     * @return string
     */
    public static function textareaInputs($form, $model, $attribute)
    {
        $items = [];
        $items[] = [
            'label' => Yii::$app->params['defaultLanguageLabel'],
            'content' => $form->field($model, $attribute)->textarea(['maxlength' => true]),
            'active' => true,
            'linkOptions' => [
                'data-multilingual-tab' => 'multilingual-inputs-' . Yii::$app->params['defaultLanguage']
            ],
            'headerOptions' => [
                'class' => 'multilingual-inputs-' . Yii::$app->params['defaultLanguage'],
            ]
        ];
        foreach (Yii::$app->params['languages'] as $key => $lang) {
            if ($key != Yii::$app->params['defaultLanguage']) {
                $items[] = [
                    'label' => $lang,
                    'content' => $form->field($model, $attribute . '_' . $key)->textarea(['maxlength' => true])->label($model->getAttributeLabel($attribute) . " - " . $lang),
                    'linkOptions' => [
                        'data-multilingual-tab' => 'multilingual-inputs-' . $key,
                    ],
                    'headerOptions' => [
                        'class' => 'multilingual-inputs-' . $key,
                    ]
                ];
            }
        }
        return Tabs::widget(['items' => $items, 'clientEvents' => [
            'shown.bs.tab' => "function (e) { "
                /* - */ . "console.log(e.target);"
                /* - */ . "$('.'+$(e.target).data('multilingual-tab')+':not(.active) > a').each("
                /* --- */ . "function(index, value){ $(value).tab('show')"
                /**/ . "}); "
                . "}"
        ],
        ]);
    }

    /**
     * @param $form ActiveForm
     * @param $model Model
     * @param $attribute
     * @return string
     */
    public static function richTextareaInputs($form, $model, $attribute)
    {
        $items = [];
        $items[] = [
            'label' => Yii::$app->params['defaultLanguageLabel'],
            'content' => $form->field($model, $attribute)->widget(CKEditor::className(), [
                'options' => ['rows' => 10],
                'preset' => 'full',
                'clientOptions' => [
                    'filebrowserUploadUrl' => '/media/image-upload'
                ]
            ]),
            'active' => true,
            'linkOptions' => [
                'data-multilingual-tab' => 'multilingual-inputs-' . Yii::$app->params['defaultLanguage']
            ],
            'headerOptions' => [
                'class' => 'multilingual-inputs-' . Yii::$app->params['defaultLanguage'],
            ]
        ];
        foreach (Yii::$app->params['languages'] as $key => $lang) {
            if ($key != Yii::$app->params['defaultLanguage']) {
                $items[] = [
                    'label' => $lang,
                    'content' => $form->field($model, $attribute . '_' . $key)->widget(CKEditor::className(), [
                        'options' => ['rows' => 10],
                        'preset' => 'full',
                        'clientOptions' => [
                            'filebrowserUploadUrl' => '/media/image-upload'
                        ]
                    ]),
                    'linkOptions' => [
                        'data-multilingual-tab' => 'multilingual-inputs-' . $key,
                    ],
                    'headerOptions' => [
                        'class' => 'multilingual-inputs-' . $key,
                    ]
                ];
            }
        }

        return Tabs::widget(['items' => $items, 'clientEvents' => [
            'shown.bs.tab' => "function (e) { "
                /* - */ . "console.log(e.target);"
                /* - */ . "$('.'+$(e.target).data('multilingual-tab')+':not(.active) > a').each("
                /* --- */ . "function(index, value){ $(value).tab('show')"
                /**/ . "}); "
                . "}"
        ],
        ]);
    }

    /**
     * @param $form ActiveForm
     * @param $model Model
     * @param $attribute
     * @param bool $showLables
     * @return string
     */
    public static function textInputsRows($form, $model, $attribute, $showLables = true)
    {
        $items = '';
        if ($showLables) {
            $items .= $form->field($model, $attribute)->textInput(['maxlength' => true]);
        } else {
            $items .= $form->field($model, $attribute)->textInput(['maxlength' => true, 'placeholder' => $model->getAttributeLabel($attribute)])->label('');
        }
        foreach (Yii::$app->params['languages'] as $key => $lang) {
            if ($key != Yii::$app->params['defaultLanguage']) {
                if ($showLables) {
                    $items .= $form->field($model, $attribute . '_' . $key)->textInput(['maxlength' => true]);
                } else {
                    $items .= $form->field($model, $attribute . '_' . $key)->textInput(['maxlength' => true, 'placeholder' => $model->getAttributeLabel($attribute . '_' . $key)])->label('');
                }
            }
        }
        return $items;
    }

    /**
     * @param $attributename
     * @return array
     */
    public static function attributesList($attributename)
    {
        $list = [$attributename];
        foreach (Yii::$app->params['languages'] as $key => $lang) {
            if ($key != Yii::$app->params['defaultLanguage']) {
                $list[] = $attributename . '_' . $key;
            }
        }
        return $list;
    }

    /**
     * @param $callback
     */
    public static function forEachLanguageDo($callback)
    {
        foreach (Yii::$app->params['languages'] as $key => $lang) {
            if ($key != Yii::$app->params['defaultLanguage']) {
                $callback($key);
            }
        }
    }

    /**
     * @param $callback
     */
    public static function forAllLanguagesDo($callback)
    {
        foreach (array_merge(Yii::$app->params['languages'], [Yii::$app->params['defaultLanguage'] => Yii::$app->params['defaultLanguageLabel']]) as $key => $lang) {
            $callback($key);
        }
    }

    /**
     * @param $key
     * @return string
     */
    public static function getLangIfAvailable($key)
    {
        if (strtolower($key) == Yii::$app->params['defaultLanguage'] || array_key_exists(strtolower($key), Yii::$app->params['languages'])) {
            return strtolower($key);
        }
        return Yii::$app->params['defaultLanguage'];
    }

    /**
     * @return string
     */
    public static function langSwithcingDropdown()
    {
        ob_start();
        ?>
        <?php if (false) { ?>
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                <span class="lang-sm lang-lbl" lang="<?= strtoupper(Yii::$app->language) ?>"></span> <span
                        class="caret"></span>
            </button>
            <ul class="dropdown-menu" style="min-width: auto;">
                <?php foreach (array_merge(Yii::$app->params['languages'], [Yii::$app->params['defaultLanguage'] => Yii::$app->params['defaultLanguageLabel']]) as $key => $lang) { ?>
                    <?php if ($key == Yii::$app->language) continue ?>
                    <li>
                        <a href="<?= Yii::$app->getUrlManager()->createUrl(['/site/change-language', 'lang' => $key]) ?>">
                            <span class="lang-sm lang-lbl" lang="<?= strtoupper($key) ?>"></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
        <?php foreach (array_merge(Yii::$app->params['languages'], [Yii::$app->params['defaultLanguage'] => Yii::$app->params['defaultLanguageLabel']]) as $key => $lang) { ?>
        <?php if ($key == Yii::$app->language) continue ?>
        <?php
        $url = preg_replace('/\?.*|<\/style>.*|<\/scRipt>.*|-->.*/', '', Yii::$app->getRequest()->getUrl());
        $extra = explode('?', (Yii::$app->request->getUrl()));
        $currentLang = Yii::$app->language;
        if (strpos($url, "/$currentLang/") > -1 || $url == "/$currentLang") {
            $url = substr($url, strlen("/$key"));
        }
        if (strlen($url) == 0) {
            $url = "/";
        }
        $route = [
            $url,
            '__lang' => $key
        ];
        if ($key == Yii::$app->params['defaultLanguage']) {
            $route['__lang'] = '';
        }
        $q = isset($extra[1]) ? "?" . Html::encode($extra[1]) : "";
        ?>
        <a href="<?= \Yii::$app->getUrlManager()->createAbsoluteUrl($route) . $q ?>" class="btn btn-link pull-right-">
            <?= \Yii::$app->params['languagesLabels'][strtolower($key)] ?>
        </a>
        <!--<span class="pull-right vertical-seperator"></span>-->
    <?php } ?>
        <?php
        return ob_get_clean();
    }

    /**
     * @param ActiveRecord $model
     * @param $attribute_name
     * @throws Exception
     * @return string
     */
    public static function getLanguageSpecificAttribute($model, $attribute_name)
    {
        if (!$model->hasAttribute($attribute_name)) {
            throw new Exception("Model doesn't has attribute:{$attribute_name}");
        }

        $lang = Yii::$app->language;
        $lang_attribute = $attribute_name . '_' . $lang;

        if (!$model->hasAttribute($lang_attribute) || empty($model->{$lang_attribute})) {
            return $attribute_name;
        }

        return $lang_attribute;
    }

}