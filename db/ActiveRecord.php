<?php

namespace app\modules\log\db;

use app\modules\log\models\Log;

class ActiveRecord extends \quoma\core\db\ActiveRecord
{
    
    public function init()
    {
        parent::init();
        $this->on( ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
            $this->createLog($event);
        });
        $this->on( self::EVENT_AFTER_UPDATE, function ($event) {
            $this->createLog($event);
        });
        $this->on( ActiveRecord::EVENT_AFTER_DELETE, function ($event) {
            $this->createLog($event);
        });
    }
    
    public function getDeletable(){
        
        return false;
        
    }
    
    public function createLog($event){
        $senderClass = get_class($event->sender);
        $keys = array_keys($this->attributes);
        foreach($keys as $key){
            if(array_key_exists($key, $this->attributes) && array_key_exists($key, $event->changedAttributes)){
                if($this->attributes[$key] != $event->changedAttributes[$key]){
                    Log::log($senderClass, $this->primaryKey, $key, $event->changedAttributes[$key], $this->attributes[$key]);
                }
            }
        }
    }
}
