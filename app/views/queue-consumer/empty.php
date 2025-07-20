<?php
use yii\helpers\Html;
?>

    <div class="alert alert-info">
        <h2>Очередь пуста</h2>
        <p>В очереди "<?= Html::encode($queue) ?>" нет сообщений</p>
    </div>

<?= Html::a('Проверить снова', ['receive-from-queue', 'queue' => $queue], ['class' => 'btn btn-primary']) ?>