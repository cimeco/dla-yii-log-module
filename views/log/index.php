<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \quoma\modules\log\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\log\LogModule::t('Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'log_id',
            'route',
            [
                'attribute' => 'username',
                'value' => function($model){
                    $user = \webvimark\modules\UserManagement\models\User::findOne($model->user_id);
                    return $user->username;
                },
            ],
            [
                'attribute' => 'datetime',
                'format' => 'datetime',
                'filter' => false
            ],
            'old_value',
            'new_value',
            [
                'class' => 'quoma\core\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
