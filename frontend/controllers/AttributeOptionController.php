<?php

namespace frontend\controllers;

use common\controllers\AuthReqWebController;
use common\models\Attribute;
use common\models\Service;
use Yii;
use common\models\AttributeOption;
use common\models\AttributeOptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttributeOptionController implements the CRUD actions for AttributeOption model.
 */
class AttributeOptionController extends AuthReqWebController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return array_merge($behaviors, [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all AttributeOption models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttributeOptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AttributeOption model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AttributeOption model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $returnTo string
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($returnTo = null)
    {
        $model = new AttributeOption();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($returnTo) {
                return $this->redirect($returnTo);
            }

            return $this->redirect(['/attribute-option/view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'returnTo' => $returnTo
        ]);
    }

    /**
     * Updates an existing AttributeOption model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $returnTo = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'returnTo' => $returnTo
        ]);
    }

    /**
     * Deletes an existing AttributeOption model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AttributeOption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AttributeOption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AttributeOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
