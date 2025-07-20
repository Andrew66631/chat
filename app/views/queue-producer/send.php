<?php
use yii\helpers\Html;
?>

    <h1>Сообщение отправлено в очередь</h1>
    <p><?= Html::encode($message) ?></p>

<?= Html::a('Отправить новое сообщение', ['send-custom'], ['class' => 'btn btn-primary']) ?>