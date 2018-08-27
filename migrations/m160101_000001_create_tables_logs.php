<?php

use yii\db\Schema;
use yii\db\Migration;

class m160101_000001_create_tables_logs extends Migration
{
    
    public function init()
    {
        $this->db = 'dblog';
        parent::init();
    }
    
    public function up()
    {
        $this->createTable('log', [
            'log_id' => $this->primaryKey(),
            'route' => $this->string(255),
            'user_id' => $this->integer(),
            'datetime' => $this->integer(),
            'model' => $this->string(255),
            'model_id' => $this->integer(),
            'data' => $this->text()
        ]);
        
    }

    public function down()
    {
        $this->dropTable('log');
    }
}