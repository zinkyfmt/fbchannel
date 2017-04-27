<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PostForm extends Model
{
    public $pageId;
    public $postId;
    public $fromName;
    public $fromCategory;
    public $fromId;
    public $numLikes;
    public $numComments;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

}
