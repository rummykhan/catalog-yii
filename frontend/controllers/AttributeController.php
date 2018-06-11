d<?php

namespace frontend\controllers;

use common\controllers\AuthReqWebController;
use common\forms\AttachOption;
use common\forms\AttachValidation;
use common\models\Service;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeValidation;
use Yii;
use common\models\AttributeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AttributeController implements the CRUD actions for Attribute model.
 */
class AttributeController extends AuthReqWebController
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
                ],
            ],
        ]);
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

    public function actionDetachOptions($service_attribute_id, $option_id)
    {
        $attribute = ServiceAttribute::findOne($service_attribute_id);

        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $option = $attribute->getServiceAttributeOptions()->where(['id' => $option_id])->one();

        if (!$option) {
            throw new NotFoundHttpException();
        }

        dd($option);
    }

    public function actionDetachValidation($service_attribute_id, $validation_id)
    {
        $serviceAttribute = ServiceAttribute::findOne($service_attribute_id);

        if (!$serviceAttribute) {
            throw new NotFoundHttpException();
        }

        $validation = $serviceAttribute->getValidations()->where(['id' => $validation_id])->one();

        if (!$validation) {
            throw new NotFoundHttpException();
        }

        $serviceAttributeValidation = ServiceAttributeValidation::find()
            ->where(['service_attribute_id' => $service_attribute_id])
            ->andWhere(['validation_id' => $validation_id])
            ->one();

        $serviceAttributeValidation->delete();

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    public function actionAttachValidation($attribute_id, $service_id)
    {
        $service = Service::findOne($service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        $attribute = $service->getServiceAttributes()->where(['id' => $attribute_id])->one();
        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $model = new AttachValidation();
        $model->service_id = $service_id;
        $model->attribute_id = $attribute_id;


        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {

        }

        return $this->render('attach-validation', [
            'service' => $service,
            'attribute' => $attribute,
            'model' => $model
        ]);
    }

    public function actionGetOptions()
    {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        $service = Service::findOne(Yii::$app->getRequest()->post('service_id'));

        if (!$service) {
            return [];
        }

        /** @var ServiceAttribute $attribute */
        $attribute = $service->getServiceAttributes()->where(['deleted' => false])->andWhere(['id' => Yii::$app->getRequest()->post('attribute_id')])->one();

        if (!$service || !$attribute) {
            return [];
        }

        return collect($attribute->getServiceAttributeOptions()->where(['deleted' => false])->asArray()->all())
            ->pluck('name', 'id')->toArray();
    }
}
