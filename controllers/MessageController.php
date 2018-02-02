<?php

namespace app\controllers;

use Yii;
use app\models\Message;
use app\models\Site;
use app\models\User;
use app\models\search\Message as MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
						'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->role == User::ROLE_ADMIN);
                        }
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionListAjax()
    {
        if(empty($_REQUEST['readerId']))return Site::done_json([], "error", "readerId is empty");
        
        $readerId = $_REQUEST['readerId'];
        $maxMessageId = !empty($_REQUEST['maxMessageId'])?$_REQUEST['maxMessageId']:"0";
        
        $reader = ($readerId != Yii::$app->user->identity->id)?User::findIdentity($readerId):Yii::$app->user->identity;
        
        if(empty($reader->role) || $reader->role != User::ROLE_READER)return Site::done_json([], "error", "readerId is wrong");    
        
        $ret = [];
        $ret['chat'] = $reader->renderChat(0, Yii::$app->user->identity->id, $maxMessageId);
        return Site::done_json($ret);
    }
    

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAjax()
    {
        $model = new Message();
        
        $request = Yii::$app->request->post();
        $model->customerId = Yii::$app->user->identity->id;
        $model->readerId = !empty($request['readerId'])?$request['readerId']:0;
        $model->message = !empty($request['message'])?$request['message']:'';
        
        if ($model->save()) {
            return Site::done_json([]);
        } else {
            $message = Site::get_error_summary($model->getErrors());
            return Site::done_json([], 'error', $message);
        }
    }
    

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteAjax()
    {
        if(empty($_REQUEST['id']))return Site::done_json([], 'error', "Bad Request (#400): Missing required parameters: id");
        
        $message = $this->findModel($_REQUEST['id']);
        if(empty($message) || !$message->editable(Yii::$app->user->identity->id)){
            return Site::done_json([], 'error', "Forbidden");
        }
        
        $message->setStatus(Message::STATUS_DELETED);
        
        return Site::done_json([]);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
