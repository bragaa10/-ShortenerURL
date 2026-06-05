<?php

use yii\db\Migration;

class m260514_133659_add_country_code_to_scan_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%scan_log}}', 'country_code', $this->string(5)->after('country'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%scan_log}}', 'country_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260514_133659_add_country_code_to_scan_log cannot be reverted.\n";

        return false;
    }
    */
}
