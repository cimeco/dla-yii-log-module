<?php

namespace quoma\modules\log\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $log_id
 * @property string $route
 * @property integer $user_id
 * @property integer $datetime
 * @property string $model
 * @property integer $model_id
 * @property string $data
 *
 * @property LogData[] $logDatas
 */
class Log extends \quoma\core\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dblog');
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['datetime'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'datetime'], 'integer'],
            [['route'], 'string', 'max' => 100],
            [['model'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => LogMoudle::t('Log ID'),
            'route' => LogMoudle::t('Route'),
            'user_id' => LogMoudle::t('User ID'),
            'datetime' => LogMoudle::t('Datetime'),
            'model' => LogMoudle::t('Model'),
        ];
    }    

    /**
     * @inheritdoc
     * Strong relations: LogDatas.
     */
    public function getDeletable()
    {
        if($this->getLogData()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }
    
    public static function log($model = null, $model_id = null, $data = null)
    {
        
        $log = new Log();
        $log->route = Yii::$app->requestedRoute ? Yii::$app->requestedRoute : 'site/index';
        $log->user_id = Yii::$app->user->getId();
        
        $log->model = $model;
        $log->model_id = $model_id;
        
        $log->data = $data;
        
        return $log->save();
        
    }
    
    public function afterSave($insert, $changedAttributes) {
        Log::garbageCollector();
    }
    
    /**
     * Si se ejecuta en cada afterSave, siempre eliminara 1 solo item luego
     * de haber superado el limite especificado
     */
    public static function garbageCollector()
    {
        
        $limit = Yii::$app->params['garbageCollectorLimit'];
        $count = Log::find()->count();
        if($count > $limit){
            
            $qty = $count - $limit;

            Yii::$app->dblog->createCommand('DELETE FROM log ORDER BY log_id ASC LIMIT '.$qty)
                ->execute();
            
        }
        
    }
    
}
