<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\ChatMessage;
use app\components\UserFactory;

class SiteController extends Controller
{
    /**
     * @return array[]
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'chat'],
                'rules' => [
                    [
                        'actions' => ['logout', 'chat'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionChat()
    {
        $users = User::find()
            ->where(['<>', 'id', Yii::$app->user->id])
            ->all();

        return $this->render('chat', [
            'users' => $users,
            'currentUser' => Yii::$app->user->identity
        ]);
    }

    /**
     * @return ChatMessage[]|array|array[]|\yii\db\ActiveRecord[]|\yii\db\T[]
     */
    public function actionChatHistory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->request->get('userId');
        $currentUserId = Yii::$app->user->id;

        return ChatMessage::find()
            ->where(['user_id' => $currentUserId, 'recipient_id' => $userId])
            ->orWhere(['user_id' => $userId, 'recipient_id' => $currentUserId])
            ->orderBy('created_at ASC')
            ->asArray()
            ->all();
    }

    /**
     * @return string|Response
     * @throws \yii\db\Exception
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $user = UserFactory::createUser(
                $model->username,
                $model->email,
                $model->password
            );

            if ($user->save()) {
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Регистрация прошла успешно.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Ошибка регистрации.');
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}