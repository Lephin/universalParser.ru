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
use app\models\ExcelForm;


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
        $request = Yii::$app->request;
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        
        
        $session = Yii::$app->session;
        
        $model = new UploadForm(); // Загрузка файла
        $parseModel = new ParseExcel($session->get('line'), null, $get,$session->get('numberList'), $post);
        
        if (Yii::$app->request->isGet) {
          if (isset($get['value'])) {
              return $parseModel->ajaxGet();
          }  
        }
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            
            if (isset($post['changeNameList'])) {
                $session->set('numberList', $post["changeNameList"]);
            }
            
            $parseModel = new ParseExcel($session->get('line'),null,$get,$session->get('numberList'),$post);
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile'); 
            
            if (isset($model->imageFile)) {
                if ($model->upload()) {        
                    $parseModel = new ParseExcel($session->get('line'),null,$get,$session->get('numberList'), $post );
  
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
    
    public function actionTest()
    {
        $excelForm = new ExcelForm(); //Полная подготовка Excel документа. Будет работать только после загрузки документа
        $uploadForm = new UploadForm(); //Загрузка документа на сервер
        
        if (Yii::$app->request->isPost) {
            
            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            
            if ($uploadForm->upload()) {
            
                $excelForm = new ExcelForm($uploadForm->lineFile);
                
                return $this->render('test',[
                    'uploadForm' => $uploadForm,
                    'excelForm' => $excelForm
                ]);
            }
        }

        return $this->render('test',[
            'uploadForm' => $uploadForm,
            'excelForm' => $excelForm
        ]);
    }
}
