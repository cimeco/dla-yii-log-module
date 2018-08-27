<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use quoma\modules\log\LogModule;

/* @var $this yii\web\View */
/* @var $model \quoma\modules\log\models\Log */

$user = \webvimark\modules\UserManagement\models\User::findOne($model->user_id);
$username = '';
if($user){
    $username = $user->username;
}

$this->title = Yii::$app->formatter->asDatetime($model->datetime);
$this->params['breadcrumbs'][] = ['label' => LogModule::t('Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <h1><?= Html::encode($this->title) ?> <small><?= $username ?></small></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'log_id',
            'route',
            [
                'attribute' => 'user_id',
                'value' => $username
            ],
            'datetime:datetime',
            'model',
            'model_id',
            'attribute:raw',
            'old_value:raw',
            'new_value:raw',
        ],
    ]) ?>

</div>
