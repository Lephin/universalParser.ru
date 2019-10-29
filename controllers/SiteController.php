<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\Session;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UploadForm;

use app\models\ParseExcel;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
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
     public function actionUpload()
    {

    }

    /**
     * {@inheritdoc}
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
     * @return string
     */
    public function actionIndex()
    {
        $model = new UploadForm(); // Загрузка файла
        
        $session = Yii::$app->session;
        $session->open();

        if (!isset($session['lineFile'])) {
            $session->destroy();
            $parseModel = new ParseExcel($model->lineFile); // Парсер файла, передаем путь
        } else {
            $parseModel = new ParseExcel($session['lineFile']); // Парсер файла, передаем путь
            $parseModel->getArray($name = null);                     
        }
      
        if (Yii::$app->request->isPost) {
            
            $request = Yii::$app->request;
            $post = $request->post();
            
            $parseModel->selected = $post['changeSheet'];
            $parseModel->getArray($post['changeSheet']); 
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if (isset($model->imageFile)) {
                if ($model->upload()) {

                    unset($session['lineFile']);
                    $session->destroy();

                    $session['lineFile'] = $model->lineFile; 

                    $parseModel = new ParseExcel($session['lineFile']); // Парсер файла, передаем путь
                    $parseModel->getArray($name = null); //Получаем чистый массив данных конкретного листа ( Обязательный параметр )                    

                    return $this->render('index', [
                        'model' => $model,
                        'test' => $parseModel,
                    ]);

                }
            }
                
        }

        return $this->render('index', [
            'model' => $model,
            'test' =>  $parseModel
                ]);
       
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
