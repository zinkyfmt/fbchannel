<?php

use yii\db\Migration;

/**
 * Handles the creation of table `like_details`.
 */
class m170414_062026_create_like_details_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('like_details', [
            'id' => $this->primaryKey(),
            'page_id' => $this->bigInteger(),
            'post_id' => $this->string()->unsigned(),
            'individual_name' => $this->string(),
            'individual_category' => $this->string(),
            'individual_id' => $this->string(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp()
        ]);
        // creates index for column `category_id`
        $this->createIndex(
            'idx-post-category_id',
            'like_details',
            'post_id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('like_details');
    }
}
