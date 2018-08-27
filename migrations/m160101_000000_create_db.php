<?php

use yii\db\Schema;
use yii\db\Migration;

class m160101_000000_create_db extends Migration
{
    
    public function up()
    {
        $db = \quoma\core\helpers\DbHelper::getDbName('dblog');
        $this->execute("CREATE DATABASE `$db`");
    }

    public function down()
    {
        $this->execute("DROP DATABASE `$db`");
    }
}