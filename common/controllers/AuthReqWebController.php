<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/12/18
 * Time: 12:03 AM
 */

namespace common\controllers;


use yii\filters\AccessControl;
use yii\web\Controller;

class AuthReqWebController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }
}