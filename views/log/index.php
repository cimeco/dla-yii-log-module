<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \quoma\modules\log\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \quoma\modules\log\LogModule::t('Logs');
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
                'attribute' => 'user_id',
                'value' => function($model){
                    $user = \webvimark\modules\UserManagement\models\User::findOne($model->user_id);
                    if($user){
                        return $user->username;
                    }
                    return '';
                },
            ],
            [
                'attribute' => 'datetime',
                'format' => 'datetime',
                'filter' => false
            ],
            'attribute',
            [
                'class' => 'quoma\core\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
