<?php
use yii\helpers\Html;

$this->title = 'Home';
?>
<div class="site-index">
    <h1>Добро пожаловать</h1>
    <div class="jumbotron">
        <?= Yii::$app->user->isGuest ? '' : Html::a('Войти в чат', ['site/chat'], ['class' => 'btn btn-lg btn-success']) ?>
    </div>
</div>