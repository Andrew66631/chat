<?php

namespace app\controllers;

use app\services\QueueProducerService;
use yii\web\Controller;
use yii\di\Container;

class QueueProducerController extends Controller
{
    private $producerService;
    public $layout = '/queue';

    public function __construct($id, $module, QueueProducerService $producerService, $config = [])
    {
        $this->producerService = $producerService;
        parent::__construct($id, $module, $config);
    }

    public function actionSend()
    {
        try {
            $this->producerService->sendMessage('Default message from controller');
            return $this->asJson(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->asJson(['error' => $e->getMessage()]);
        }
    }

    public function actionSendCustom()
    {
        $message = \Yii::$app->request->get('message', 'Тестовое сообщение');
//var_dump($message);die();
        try {
            $this->producerService->sendMessage($message);
            return $this->render('send', ['message' => $message]);
        } catch (\Exception $e) {
            return $this->render('error', ['error' => $e->getMessage()]);
        }
    }
}