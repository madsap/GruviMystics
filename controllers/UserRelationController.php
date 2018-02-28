<?php

namespace app\controllers;

use Yii;
use app\models\UserRelation;
use app\models\User;
use app\models\search\UserRelation as UserRelationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\components\widgets\ReportUserAlert;
use app\models\Site;

/**
 * UserRelationController implements the CRUD actions for UserRelation model.
 */
class UserRelationController extends Controller
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
                            return (Yii::$app->user->identity->role == User::ROLE_READER || Yii::$app->user->identity->role == User::ROLE_ADMIN);
                        }
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all UserRelation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request->queryParams;
        
        if(!User::isAdmin()){
            $request['UserRelation']['senderId'] = Yii::$app->user->identity->id;
        }
        
        $searchModel = new UserRelationSearch();
        $dataProvider = $searchModel->search($request);
        
        $dataProvider->sort->attributes['messageText'] = [
            'asc' => ['md_message.message' => SORT_ASC],
            'desc' => ['md_message.message' => SORT_DESC],
            ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserRelation model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPartial()
    {
        $request = Yii::$app->request;
        $params = $request->get();
        //return Site::done_json(['debug' => $params]);
        
        switch ( $params['partial'] ) {
            case 'report_user':
                $html = ReportUserAlert::widget(['params'=>$params]);
                break;
            default:
                $html = '';
        }

        return Site::done_json(['html' => $html]);
        /*
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
         */
    }

    /**
     * Creates a new UserRelation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new UserRelation();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing UserRelation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing UserRelation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the UserRelation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserRelation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserRelation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
