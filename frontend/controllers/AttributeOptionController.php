<?php

namespace frontend\controllers;

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
class AttributeOptionController extends Controller
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
        ];
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
     * @param $attribute_id integer
     * @param $service_id integer
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($attribute_id, $service_id = null)
    {
        $attribute = Attribute::findOne($attribute_id);
        $service = Service::findOne($service_id);

        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $model = new AttributeOption();
        $model->attribute_id = $attribute_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($service) {
                return $this->redirect(['/attribute/view', 'id' => $attribute->id, 'service_id' => $service_id]);
            } else {
                return $this->redirect(['/attribute/view', 'id' => $attribute->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'attribute' => $attribute,
            'service' => $service
        ]);
    }

    /**
     * Updates an existing AttributeOption model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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
