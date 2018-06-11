<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 6/12/18
 * Time: 12:20 AM
 */

namespace console\controllers;


use common\models\User;
use yii\console\Controller;

class UserController extends Controller
{
    public function actionCreateUser($email, $username, $password)
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->stdout("-> Invalid email");
            exit;
        }

        if (empty($username)) {
            $this->stdout('-> Invalid username');
            exit;
        }

        if (empty($password)) {
            $password = '12345';
        }

        $user = User::find()->where(['username' => $username])->one();
        if ($user) {
            $this->stdout('Already a user with same username');
            exit;
        }

        $user = User::find()->where(['email' => $email])->one();
        if ($user) {
            $this->stdout('Already a user with same email');
            exit;
        }

        $user = new User();
        $user->email = $email;
        $user->username = $username;
        $user->password = $password;
        $user->auth_key = \Yii::$app->getSecurity()->generateRandomString();
        $user->save();

        $this->stdout('User created successfully!');
    }
}