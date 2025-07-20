<?php
use yii\helpers\Html;
?>

    <div class="alert alert-danger">
        <h2>Ошибка при отправке сообщения</h2>
        <p><?= nl2br(Html::encode($error)) ?></p>
    </div>

<?= Html::a('Попробовать снова', ['send-custom'], ['class' => 'btn btn-primary']) ?>