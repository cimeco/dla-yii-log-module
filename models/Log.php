<?php

namespace quoma\modules\log\models;

use Yii;
use quoma\modules\log\LogModule;

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
 * @property string $attribute
 * @property string $post
 * @property string $get
 *
 * @property LogData[] $logDatas
 */
class Log extends \quoma\core\db\ActiveRecord
{

    static $ignoreData = ['_csrf', 'r'];

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
            [['user_id', 'datetime', 'model_id'], 'integer'],
            [['route', 'attribute'], 'string', 'max' => 100],
            [['model'], 'string', 'max' => 45],
            [['old_value', 'new_value', 'post', 'get'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => LogModule::t('ID'),
            'route' => LogModule::t('Route'),
            'user_id' => LogModule::t('User'),
            'datetime' => LogModule::t('Datetime'),
            'class' => LogModule::t('Class'),
            'model_id' => LogModule::t('Model ID'),
            'old_value' => LogModule::t('Old Value'),
            'new_value' => LogModule::t('New Value'),
            'attribute' => LogModule::t('Attribute'),
            'post' => LogModule::t('Post'),
            'get' => LogModule::t('Get'),
            'model' => LogModule::t('Model'),
        ];
    }

    /**
     * @inheritdoc
     * Strong relations: LogDatas.
     */
    public function getDeletable()
    {
        if ($this->getLogData()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations()
    {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    public static function log($model = null, $model_id = null, $attributes = null, $old_values = null, $new_values = null, $data = null, $post = null, $get = null)
    {

        $log = new Log();
        $log->route = Yii::$app->requestedRoute ?? 'site/index';
        $log->user_id = Yii::$app->user->getId();
        $log->model = $model;
        $log->model_id = $model_id;

        $log->attribute = self::concatValues($attributes);
        $log->old_value = self::concatValues($old_values);
        $log->new_value = self::concatValues($new_values);

        if (empty($post)) {
            $post = self::prettyArray(Yii::$app->request->getBodyParams());
            if (Yii::$app->request->post()) {
                $post .= "\n\nDatos:\n" . self::prettyArray(Yii::$app->request->post());
            }
        }

        if (empty($get)) {
            $get = self::prettyArray(Yii::$app->request->getQueryParams());
            if (Yii::$app->request->get()) {
                $get .= "\n\nDatos:\n" . self::prettyArray(Yii::$app->request->get());
            }
        }

        $log->post = $post;
        $log->get = $get;

        $log->save();
        \Yii::trace($log->getErrors());
    }

    private static function prettyArray($data)
    {
        $pretty = "";

        if (is_array($data)) {
            foreach ($data as $k => $d) {
                if (!in_array($k, self::$ignoreData) && !empty($d)) {
                    $pretty .= "$k: ";
                    if (is_array($d)) {
                        $pretty .= "\n";
                    }
                    $pretty .= self::prettyArray($d);
                }
            }
        } else {
            $pretty .= "$data\n";
        }

        return $pretty;
    }

    private static function concatValues($array)
    {
        $concat_values = '';
        if (is_array($array)) {
            foreach ($array as $value) {
                $concat_values .= $value . "\n";
            }
        } else {
            $concat_values .= $array;
        }

        return $concat_values;
    }

    public function afterSave($insert, $changedAttributes)
    {
        Log::garbageCollector();
    }

    /**
     * Si se ejecuta en cada afterSave, siempre eliminara 1 solo item luego
     * de haber superado el limite especificado
     */
    public static function garbageCollector()
    {

        $limit = Yii::$app->params['garbageCollectorLimit'] ?? 1000000;
        $count = Log::find()->count();
        if ($count > $limit) {

            $qty = $count - $limit;

            Yii::$app->dblog->createCommand('DELETE FROM log ORDER BY log_id ASC LIMIT ' . $qty)
                    ->execute();
        }
    }

    public static function getLogClassNames()
    {
        $array = [];
        $data = Yii::$app->dblog->createCommand('SELECT DISTINCT route, model FROM ' . self::tableName())
                ->queryAll();

        foreach (Yii::$app->getModules() as $id => $module) {
            if (is_array($module)) {
                $class = new \ReflectionClass($module['class']);
                foreach ($data as $d) {
                    $basePath = dirname($class->getFileName()) . '/messages';
                    if (file_exists($basePath)) {
                        if (file_exists($basePath . '/' . Yii::$app->language . "/$id-log.php")) {
                            if(array_key_exists($d['route'], $array)){
                                $exist_not_translated = strpos($array[$d['route']], '/');
                                if($exist_not_translated){
                                    $array[$d['route']] = Yii::t($id . '-log', $d['route']);
                                }
                            } else {
                                $array[$d['route']] = Yii::t($id . '-log', $d['route']);
                            }
                        }
                    }
                }
            }
        }
        return $array;
    }

}
