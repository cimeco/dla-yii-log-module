<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \quoma\modules\log\models\Log */

$this->title = $model->log_id;
$this->params['breadcrumbs'][] = ['label' => \quoma\modules\log\LogModule::t('Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'log_id',
            'route',
            'user_id',
            'datetime:datetime',
            'model',
            'model_id',
            'data:ntext',
        ],
    ]) ?>

</div>
