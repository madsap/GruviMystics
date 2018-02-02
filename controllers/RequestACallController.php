<?php

namespace app\controllers;

use Yii;
use app\models\RequestACall;
use app\models\Site;
use app\models\search\RequestACall as RequestACallSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;

/**
 * RequestACallController implements the CRUD actions for RequestACall model.
 */
class RequestACallController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticate' => [
                'class'  => '\app\filters\AuthenticateFilter',
                'except' => []
            ]
        ];
    }

    /**
     * Lists all RequestACall models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(!User::isAdmin())return $this->redirect(['/']);
        $searchModel = new RequestACallSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RequestACall model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if(!User::isAdmin())return $this->redirect(['/']);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RequestACall model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RequestACall();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
   public function actionCreateAjax(){
       
        $model = new RequestACall();

        $request = Yii::$app->request->post();
        $model->customerId = Yii::$app->user->identity->id;
        $model->readerId = !empty($request['readerId'])?$request['readerId']:0;
        $model->phone = !empty($request['phone'])?$request['phone']:'';
                
        if ($model->save()) {
            return Site::done_json([]);
        } else {
            $message = Site::get_error_summary($model->getErrors());
            return Site::done_json([], 'facebookError', $message);
        }
       
   }

    /**
     * Updates an existing RequestACall model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(!User::isAdmin())return $this->redirect(['/']);
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
     * Deletes an existing RequestACall model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(!User::isAdmin())return $this->redirect(['/']);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RequestACall model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RequestACall the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RequestACall::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
