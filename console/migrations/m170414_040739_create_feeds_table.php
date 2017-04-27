<?php

use yii\db\Migration;

/**
 * Handles the creation of table `feeds`.
 */
class m170414_040739_create_feeds_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('feeds', [
            'id' => $this->primaryKey(),
            'page_id' => $this->bigInteger()->notNull(),
            'post_id' => $this->string()->notNull(),
            'from_name' => $this->string(),
            'from_category' => $this->string(),
            'from_id' => $this->string(),
            'number_of_likes' => $this->integer(),
            'number_of_comments' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('feeds');
    }
}
