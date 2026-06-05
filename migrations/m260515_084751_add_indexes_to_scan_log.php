<?php

use yii\db\Migration;

class m260515_084751_add_indexes_to_scan_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-scan_log-country', '{{%scan_log}}', 'country');
        $this->createIndex('idx-scan_log-device_type', '{{%scan_log}}', 'device_type');
        $this->createIndex('idx-scan_log-browser', '{{%scan_log}}', 'browser');
        $this->createIndex('idx-scan_log-os', '{{%scan_log}}', 'os');
        $this->createIndex('idx-scan_log-accessed_at', '{{%scan_log}}', 'accessed_at');
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-scan_log-country', '{{%scan_log}}');
        $this->dropIndex('idx-scan_log-device_type', '{{%scan_log}}');
        $this->dropIndex('idx-scan_log-browser', '{{%scan_log}}');
        $this->dropIndex('idx-scan_log-os', '{{%scan_log}}');
        $this->dropIndex('idx-scan_log-accessed_at', '{{%scan_log}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260515_084751_add_indexes_to_scan_log cannot be reverted.\n";

        return false;
    }
    */
}
