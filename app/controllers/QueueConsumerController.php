<?php

namespace app\controllers;

use app\services\QueueConsumerService;
use yii\web\Controller;
use yii\di\Container;

class QueueConsumerController extends Controller
{
    private $consumerService;
    public $layout = '/queue';

    public function __construct($id, $module, QueueConsumerService $consumerService, $config = [])
    {
        $this->consumerService = $consumerService;
        parent::__construct($id, $module, $config);
    }

    public function actionReceive()
    {
        try {
            $message = $this->consumerService->getMessage();

            if ($message === null) {
                return $this->asJson(['status' => 'empty']);
            }

            return $this->asJson([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->asJson(['error' => $e->getMessage()]);
        }
    }

    public function actionReceiveFromQueue()
    {
        $queue = \Yii::$app->request->get('queue', 'default_queue');

        try {
            $message = $this->consumerService->getMessage($queue);

            if ($message === null) {
                return $this->render('empty', ['queue' => $queue]);
            }

            return $this->render('message', [
                'message' => $message,
                'queue' => $queue
            ]);
        } catch (\Exception $e) {
            return $this->render('error', ['error' => $e->getMessage()]);
        }
    }
}