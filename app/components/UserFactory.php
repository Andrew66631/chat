<?php

namespace app\components;

use app\models\User;

class UserFactory
{
    public static function createUser($username, $email, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password_hash = \Yii::$app->security->generatePasswordHash($password);

        return $user;
    }
}