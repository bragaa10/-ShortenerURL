<?php

use yii\db\Migration;

/**
 * Class m260508_115144_init_schema
 */
class m260508_115144_init_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Table: user
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->string(20)->notNull()->defaultValue('user'),
            'profile_bio' => $this->text(),
            'profile_company' => $this->string(150),
            'profile_website' => $this->string(255),
            'last_login_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Table: campaign
        $this->createTable('{{%campaign}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'status' => $this->smallInteger()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-campaign-user_id', '{{%campaign}}', 'user_id', '{{%user}}', 'id', 'CASCADE');

        // Table: short_url
        $this->createTable('{{%short_url}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'campaign_id' => $this->integer(),
            'title' => $this->string(255),
            'original_url' => $this->text()->notNull(),
            'short_code' => $this->string(32)->notNull()->unique(),
            'qr_code_path' => $this->string(255),
            'status' => $this->smallInteger()->defaultValue(1),
            'expires_at' => $this->integer(),
            'password_protected' => $this->boolean()->defaultValue(false),
            'password_hash' => $this->string(255),
            'notes' => $this->text(),
            'tags' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-short_url-user_id', '{{%short_url}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-short_url-campaign_id', '{{%short_url}}', 'campaign_id', '{{%campaign}}', 'id', 'SET NULL');

        // Table: scan_log
        $this->createTable('{{%scan_log}}', [
            'id' => $this->primaryKey(),
            'short_url_id' => $this->integer()->notNull(),
            'accessed_at' => $this->integer()->notNull(),
            'ip_address' => $this->string(45),
            'user_agent' => $this->text(),
            'referer' => $this->text(),
            'source' => $this->string(50),
            'country' => $this->string(100),
            'city' => $this->string(100),
            'device_type' => $this->string(50),
            'os' => $this->string(100),
            'browser' => $this->string(100),
            'language' => $this->string(20),
            'utm_source' => $this->string(100),
            'utm_medium' => $this->string(100),
            'utm_campaign' => $this->string(100),
            'utm_term' => $this->string(100),
            'utm_content' => $this->string(100),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-scan_log-short_url_id', '{{%scan_log}}', 'short_url_id', '{{%short_url}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%scan_log}}');
        $this->dropTable('{{%short_url}}');
        $this->dropTable('{{%campaign}}');
        $this->dropTable('{{%user}}');
    }
}
