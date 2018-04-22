<?php

namespace frontend\controllers;

use common\forms\AttachOption;
use common\models\Service;
use Yii;
use common\models\Attribute;
use common\models\AttributeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttributeController implements the CRUD actions for Attribute model.
 */
class AttributeController extends Controller
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
     * Lists all Attribute models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttributeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Attribute model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new Attribute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $returnTo string
     * @return mixed
     */
    public function actionCreate($returnTo = null)
    {
        $model = new Attribute();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($returnTo) {
                return $this->redirect($returnTo);
            }

            return $this->redirect(['/attribute/view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Attribute model.
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
     * Deletes an existing Attribute model.
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
     * Finds the Attribute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Attribute::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Attach options to attribute
     *
     * @param $attribute_id integer
     * @param $service_id integer
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionAttachOptions($attribute_id, $service_id)
    {
        $attribute = Attribute::findOne($attribute_id);
        $service = Service::findOne($service_id);

        if (!$attribute || !$service) {
            throw new NotFoundHttpException();
        }

        $model = new AttachOption();
        $model->attribute_id = $attribute_id;
        $model->service_id = $service_id;


        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {
            return $this->redirect(['/service/view', 'id' => $service_id]);
        }

        return $this->render('attach', [
            'model' => $model,
            'service' => $service,
            'attribute' => $attribute
        ]);
    }

    /**
     * Detach options from attribute
     *
     * @param $service_attribute_id
     * @param $option_id
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionDetachOptions($service_attribute_id, $option_id)
    {

    }
}
