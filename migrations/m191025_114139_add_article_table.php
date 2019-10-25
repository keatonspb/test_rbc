<?php

use yii\db\Migration;

/**
 * Class m191025_114139_add_article_table
 */
class m191025_114139_add_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable("articles", [
            "id" => $this->primaryKey()->unsigned(),
            "rbc_hash" => $this->char("255")->notNull(),
            "modified_at" => $this->timestamp(),
            "link" => $this->char("255")->notNull(),
            "title" => $this->char("255")->notNull(),
            "image_link" => $this->char("255")->notNull()->defaultValue(""),
            "content" => $this->text()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("articles");


        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191025_114139_add_article_table cannot be reverted.\n";

        return false;
    }
    */
}
