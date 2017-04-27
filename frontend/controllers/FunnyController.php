<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class FunnyController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $appId = '1626747924005321';
        $appSecret = 'db5ef8e6f89798decdb1847af0efc2f1';
        $pageId = '510763732421747';
        $config = [
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_access_token' => $appId.'|'.$appSecret
        ];

        $model_feed_csv = $model_like_csv = [];
        $graph_array= $this->getDataFromGraphApi($config, $pageId);
        return $this->render('index',array('graph_array' => $graph_array));
    }

    public function getDataFromGraphApi($config, $page_id){
        $fb = new \Facebook\Facebook($config);
        //$request = $fb->request('GET', $page_id.'/feed?fields=from,message,likes,to,message_tags,picture,link,name,caption,description,source,properties,icon,actions,privacy,type,place,story,story_tags,with_tags,comments,object_id,application,created_time,updated_time');
        $request = $fb->request('GET', $page_id.'/videos?fields=embed_html,content_category,embeddable,description');
        try {
            $response = $fb->getClient()->sendRequest($request);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        return $response->getGraphEdge()->asArray();
    }
}
