<?php

namespace frontend\controllers;

use common\forms\AddPricingAttribute;
use common\forms\AttachAttribute;
use common\helpers\MatrixHelper;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeMatrix;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeDepends;
use common\models\ServiceCity;
use RummyKhan\Collection\Arr;
use Yii;
use common\models\Service;
use common\models\ServiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\View;
use yii\web\ViewAction;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends Controller
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
     * Lists all Service models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Service model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Service model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Service();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $cities = Yii::$app->getRequest()->post('cities');

            $model->attachCities($cities);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $cities = Yii::$app->getRequest()->post('cities');

            // delete all cities from service
            ServiceCity::deleteAll(['service_id' => $model->id]);

            $model->attachCities($cities);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Service model.
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
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Attach Attributes to service
     * @param $id
     * @return mixed
     */
    public function actionAttachAttribute($id)
    {
        $service = $this->findModel($id);
        $model = new AttachAttribute();
        $model->service_id = $service->id;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {
            return $this->redirect(['/service/view', 'id' => $service->id]);
        }

        return $this->render('attach', [
            'service' => $service,
            'model' => $model
        ]);
    }

    /**
     * Set pricing attributes for a service
     *
     * @param $id
     * @return mixed
     */
    public function actionAddPricing($id)
    {
        $model = $this->findModel($id);
        $formModel = new AddPricingAttribute();
        $formModel->service_id = $model->id;

        foreach ($model->serviceLevelAttributes as $serviceLevelAttribute) {

            $pricingAttribute = PricingAttribute::find()
                ->where(['service_attribute_id' => $serviceLevelAttribute->id])
                ->one();

            if (!$pricingAttribute) {
                return $this->render('set-pricing-attributes', ['model' => $model, 'formModel' => $formModel]);
            }
        }

        $matrix = new MatrixHelper($model);

        return $this->render('view-price-matrix', [
            'model' => $model,
            'matrixHeaders' => $matrix->getMatrixHeaders(),
            'matrixRows' => $matrix->getMatrixRows(),
            'noImpactRows' => $matrix->getNoImpactRows()
        ]);
    }

    public function actionSetPricing($id)
    {
        $model = $this->findModel($id);

        $formModel = new AddPricingAttribute();
        $formModel->service_id = $model->id;

        return $this->render('set-pricing-attributes', ['model' => $model, 'formModel' => $formModel]);
    }

    public function actionViewPriceMatrix($id)
    {
        $model = $this->findModel($id);

        $matrix = new MatrixHelper($model);

        return $this->render('view-price-matrix', [
            'model' => $model,
            'matrixHeaders' => $matrix->getMatrixHeaders(),
            'matrixRows' => $matrix->getMatrixRows(),
            'noImpactRows' => $matrix->getNoImpactRows()
        ]);
    }

    public function actionConfirmPriceMatrix($id)
    {
        $model = $this->findModel($id);

        $matrix = new MatrixHelper($model);

        if (empty($matrix->getMatrixRows())) {
            Yii::$app->getSession()->addFlash('error', 'Pricing attributes not set');
            return $this->redirect(['/service/add-pricing', 'id' => $model->id]);
        }

        foreach ($matrix->getMatrixRows() as $matrixRow) {
            $matrix->saveMatrixRow($matrixRow);
        }

        Yii::$app->getSession()->addFlash('success', 'Pricing attributes matrix saved');

        return $this->redirect(['/service/add-pricing', 'id' => $model->id]);
    }

    public function actionAddPricingAttribute($id)
    {
        $model = $this->findModel($id);

        $formModel = new AddPricingAttribute();
        $formModel->service_id = $id;


        if ($formModel->load(Yii::$app->getRequest()->post()) && $formModel->addAttribute()) {
            Yii::$app->getSession()->addFlash('success', 'Added');
        } else {
            Yii::$app->getSession()->addFlash('error', 'Unable to add');
        }

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    public function actionAddAttributeDependency($id, $attribute_id = null, $depends_on_id = null)
    {
        $model = $this->findModel($id);

        $depends_on = ServiceAttribute::findOne($depends_on_id);

        if ($depends_on) {
            $options = $depends_on->getOptionsList();
        } else {
            $options = [];
        }


        return $this->render('add-attribute-depenedency', [
            'model' => $model,
            'options' => $options,
            'attribute_id' => $attribute_id,
            'depends_on_id' => $depends_on_id
        ]);
    }

    public function actionAttachAttributeDependency($id)
    {
        $model = $this->findModel($id);

        $data = Yii::$app->getRequest()->post();

        $attribute_id = Arr::get($data, 'attribute_id');
        $depends_on = Arr::get($data, 'depends_on_id');
        $service_attribute_options = Arr::get($data, 'service_attribute_option_id');

        if (empty($service_attribute_options)) {
            Yii::$app->getSession()->addFlash('error', 'Please select attribute option');
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        foreach ($service_attribute_options as $service_attribute_option) {
            $dependency = new ServiceAttributeDepends();
            $dependency->service_attribute_id = $attribute_id;
            $dependency->depends_on_id = $depends_on;
            $dependency->service_attribute_option_id = $service_attribute_option;
            $dependency->save();
        }

        return $this->redirect(['/service/add-attribute-dependency', 'id' => $model->id]);
    }
}
