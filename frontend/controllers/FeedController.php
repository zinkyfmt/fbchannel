<?php
namespace frontend\controllers;

use common\models\Feed;
use common\models\LikeDetail;
use common\models\Post;
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
use Facebook\FacebookRequest;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ArrayDataProvider;

/**
 * Site controller
 */
class FeedController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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
        if (Yii::$app->request->post()){
            //request is post
            $data = Yii::$app->request->post();
            $config = [
                'app_id' => $data['app_id'],
                'app_secret' => $data['app_secret'],
                'default_access_token' => $data['app_id'].'|'.$data['app_secret']
            ];
            $fb_id = $data['page_id'];

            $model_feed_csv = $model_like_csv = [];
            $graph_array= $this->getDataFromGraphApi($config, $fb_id);
//            echo json_encode($graph_array);die;
            foreach ($graph_array as $item){
                $array_temp = [];
                $model = new Feed();
                $model->post_id = $item['id'];
                $model->page_id = $fb_id;
                $model->from_id = isset($item['from']['id']) ? $item['from']['id'] : '';
                $model->from_category = isset($item['from']['category']) ? $item['from']['category'] : '';
                $model->from_name = isset($item['from']['name']) ? $item['from']['name'] : '';
                $model->number_of_likes = isset($item['likes']) ? count($item['likes']) : 0;
                $model->number_of_comments = isset($item['comments']) ? count($item['comments']) : 0;
                $model->save();
                $model_like_existed = new LikeDetail();
                $model_like_existed->deleteAll(['post_id' => $item['id']]);
                if(isset($item['likes'])){
                    foreach ($item['likes'] as $like){
                        $array_temp_like = [];
                        $model_like = new LikeDetail();
                        $model_like->page_id = $fb_id;
                        $model_like->post_id = $item['id'];
                        $model_like->individual_category = isset($like['category']) ? $like['category'] : '';
                        $model_like->individual_id = isset($like['id']) ? $like['id'] : '';
                        $model_like->individual_name = isset($like['name']) ? $like['name'] : '';
                        $model_like->save();

                        $array_temp_like['page_id'] = $fb_id;
                        $array_temp_like['post_id'] = $item['id'];
                        $array_temp_like['individual_name'] = $model_like->individual_name;
                        $array_temp_like['individual_category'] = $model_like->individual_category;
                        $array_temp_like['individual_id'] = $model_like->individual_id;
                        $model_like_csv[] = $array_temp_like;
                    }
                }

                $array_temp_feed['page_id'] = $fb_id;
                $array_temp_feed['post_id'] = $item['id'];
                $array_temp_feed['from_name'] = $model->from_name;
                $array_temp_feed['from_category'] = $model->from_category;
                $array_temp_feed['from_id'] = $model->from_id;
                $array_temp_feed['page_owner'] =  $model->from_id == $fb_id ? 1 : 0;
                $array_temp_feed['to_name'] = isset($item['to'][0]['name']) ? $item['to'][0]['name'] : '';
                $array_temp_feed['to_category'] = isset($item['to'][0]['category']) ? $item['to'][0]['category'] : '';
                $array_temp_feed['to_id'] = isset($item['to'][0]['id']) ? $item['to'][0]['id'] : '';
                $array_temp_feed['message'] = isset($item['message']) ? $item['message'] : '';
                $array_temp_feed['message_tags'] = isset($item['message_tags']) ? 1 : 0;
                $array_temp_feed['picture'] = isset($item['picture']) ? $item['picture'] : '';
                $array_temp_feed['link'] = isset($item['link']) ? $item['link'] : '';
                $array_temp_feed['description'] = isset($item['description']) ? $item['description'] : '';
                $array_temp_feed['name'] = isset($item['name']) ? $item['name'] : '';
                $array_temp_feed['caption'] = isset($item['caption']) ? $item['caption'] : '';
                $array_temp_feed['source'] = isset($item['description']) ? $item['description'] : '';
                $array_temp_feed['icon'] = isset($item['picture']) ? $item['picture'] : '';
                $array_temp_feed['privacy_description'] = isset($item['privacy']['description']) ? $item['privacy']['description'] : '';
                $array_temp_feed['privacy_value'] = isset($item['privacy']['value']) ? $item['privacy']['value'] : '';
                $array_temp_feed['type'] = isset($item['type']) ? $item['type'] : '';
                $array_temp_feed['story'] = isset($item['story']) ? $item['story'] : '';
                $array_temp_feed['story_tags'] = isset($item['story_tags']) ? 1 : 0;
                $array_temp_feed['number_of_comments'] = $model->number_of_comments;
                $array_temp_feed['number_of_likes'] = $model->number_of_likes;
                $array_temp_feed['with_tags'] = isset($item['with_tags']) ? 1 : 0;
                $array_temp_feed['object_id'] = isset($item['object_id']) ? $item['object_id'] : '';
                $array_temp_feed['created_time'] = isset($item['created_time']) ? $item['created_time']->format('Y-m-d H:i:s') : '';
                $array_temp_feed['updated_time'] = isset($item['updated_time']) ? $item['updated_time']->format('Y-m-d H:i:s') : '';
                $array_temp_feed['data_aquired_time'] = date('Y-m-d H:i:s');
                $model_feed_csv[] = $array_temp_feed;
               // $f[] = $this->getDataFromPost($config,'1405743516413745');
            }
            $columns_feed = [
                [
                    'attribute' => 'page_id',
                ],
                [
                    'attribute' => 'post_id',
                ],
                [
                    'attribute' => 'from_name',
                ],
                [
                    'attribute' => 'from_category',
                ],
                [
                    'attribute' => 'from_id',
                ],
                [
                    'attribute' => 'page_owner',
                ],
                [
                    'attribute' => 'to_name',
                ],
                [
                    'attribute' => 'to_category',
                ],
                [
                    'attribute' => 'to_id',
                ],
                [
                    'attribute' => 'message',
                ],
                [
                    'attribute' => 'message_tags',
                ],
                [
                    'attribute' => 'picture',
                ],
                [
                    'attribute' => 'link',
                ],
                [
                    'attribute' => 'description',
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'caption',
                ],
                [
                    'attribute' => 'source',
                ],
                [
                    'attribute' => 'icon',
                ],
                [
                    'attribute' => 'privacy_description',
                ],
                [
                    'attribute' => 'privacy_value',
                ],
                [
                    'attribute' => 'type',
                ],
                [
                    'attribute' => 'story',
                ],
                [
                    'attribute' => 'story_tags',
                ],
                [
                    'attribute' => 'number_of_comments',
                ],
                [
                    'attribute' => 'number_of_likes',
                ],
                [
                    'attribute' => 'with_tags',
                ],
                [
                    'attribute' => 'object_id',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'update_time',
                ],
                [
                    'attribute' => 'data_aquired_time',
                ],
            ];
            $columns_like = [
                [
                    'attribute' => 'page_id',
                ],
                [
                    'attribute' => 'post_id',
                ],
                [
                    'attribute' => 'individual_name',
                ],
                [
                    'attribute' => 'individual_category',
                ],
                [
                    'attribute' => 'individual_id',
                ],
            ];
            $feed_file_name = 'Feeds.csv';
            $like_file_name = 'Likes.csv';
            $this->exportCSV($feed_file_name, $model_feed_csv,$columns_feed);
            $this->exportCSV($like_file_name, $model_like_csv,$columns_like);
            $this->downloadFile([$feed_file_name,$like_file_name]);
        }

        return $this->render('index');
    }
    public function getDataFromGraphApi($config, $page_id){
        $fb = new \Facebook\Facebook($config);
        $request = $fb->request('GET', $page_id.'/feed?fields=from,message,likes,to,message_tags,picture,link,name,caption,description,source,properties,icon,actions,privacy,type,place,story,story_tags,with_tags,comments,object_id,application,created_time,updated_time');
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
    /**
     * Get Post data
     *
     */
    public function getDataFromPost($config,$post_id){
        $fb = new \Facebook\Facebook($config);
        $request = $fb->request('GET',  '/me/photos');
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
        return $response->getGraphNode()->asArray();
    }
    /**
     * Export data to csv file.
     *
     */
    public function exportCSV($filename, $modelArray = false, $columns = false){
        $exporter = new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' =>  $modelArray
            ]),
            'columns' => $columns
        ]);
        $exporter->export()->saveAs($filename);
    }
    /**
     * Download file.
     *
     * @return mixed
     */
    public function downloadFile($file_array)
    {
        $zipname = 'post_feed.zip';
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);
        foreach ($file_array as $file) {
            $zip->addFile($file);
        }
        $zip->close();
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$zipname");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        // read the file from disk
        readfile($zipname);
        return true;
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
