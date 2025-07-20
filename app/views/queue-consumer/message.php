<?php
use yii\helpers\Html;
?>

    <h1>Сообщение из очереди: <?= Html::encode($queue) ?></h1>

    <div class="well">
        <pre><?= print_r($message, true) ?></pre>
    </div>

<?= Html::a('Получить следующее', ['receive-from-queue', 'queue' => $queue], ['class' => 'btn btn-primary']) ?>