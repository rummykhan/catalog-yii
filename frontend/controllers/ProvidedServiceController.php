<?php

namespace frontend\controllers;

use common\controllers\AuthReqWebController;
use common\forms\AddCoverageArea;
use common\forms\AddType;
use common\helpers\Matrix;
use common\helpers\MatrixHelper;
use common\helpers\ServiceAttributeMatrix;
use common\models\AvailabilityException;
use common\models\AvailabilityRule;
use common\models\GlobalAvailabilityException;
use common\models\GlobalAvailabilityRule;
use common\models\ProvidedServiceArea;
use common\models\ProvidedServiceAreaSearch;
use common\models\ProvidedRequestType;
use common\models\Provider;
use common\models\RequestType;
use RummyKhan\Collection\Arr;
use Yii;
use common\models\ProvidedService;
use common\models\ProvidedServiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProvidedServiceController implements the CRUD actions for ProvidedService model.
 */
class ProvidedServiceController extends AuthReqWebController
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
     * Lists all ProvidedService models.
     * @param $provider_id
     * @return mixed
     */
    public function actionIndex($provider_id)
    {
        $searchModel = new ProvidedServiceSearch();
        $searchModel->provider_id = $provider_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'provider' => Provider::findOne($provider_id)
        ]);
    }

    /**
     * Displays a single ProvidedService model.
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
     * Creates a new ProvidedService model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $provider_id
     * @return mixed
     */
    public function actionCreate($provider_id)
    {
        $model = new ProvidedService();
        $model->provider_id = $provider_id;

        if (Yii::$app->request->isPost && !empty(Yii::$app->getRequest()->post('services'))) {

            $model->provideServices(Yii::$app->getRequest()->post('services'));

            return $this->redirect(['/provided-service/index', 'provider_id' => $provider_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'provider' => Provider::findOne($provider_id)
        ]);
    }

    /**
     * Updates an existing ProvidedService model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $serviceTypes = Yii::$app->getRequest()->post('service_type');
        $providedService = $this->findModel($id);

        /** @var RequestType $serviceType */
        foreach ($providedService->service->getActiveRequestTypes() as $serviceType) {

            // if service type is checked
            if (isset($serviceTypes[$serviceType->id])) {

                // if not attached..

                $providedRequestType = ProvidedRequestType::find()
                    ->where(['provided_service_id' => $id])
                    ->andWhere(['service_request_type_id' => $serviceType->id])
                    ->andWhere(['deleted' => false])
                    ->one();

                if ($providedRequestType) {
                    continue;
                }

                // attach it

                $providedRequestType = new ProvidedRequestType();
                $providedRequestType->provided_service_id = $providedService->id;
                $providedRequestType->service_request_type_id = $serviceType->id;
                $providedRequestType->save();
                continue;
            }


            // if service type is not checked..

            $providedRequestType = ProvidedRequestType::find()
                ->where(['provided_service_id' => $id])
                ->andWhere(['service_request_type_id' => $serviceType->id])
                ->andWhere(['deleted' => false])
                ->one();

            if (!$providedRequestType) {
                continue;
            }

            $providedRequestType->deleted = true;
            $providedRequestType->save();
            // remove it.
        }

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * Deletes an existing ProvidedService model.
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
     * Finds the ProvidedService model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProvidedService the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProvidedService::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddType($id)
    {
        $providedService = $this->findModel($id);

        $model = new AddType();
        $model->provided_service_id = $id;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {

            return $this->redirect(['/provided-service/view', 'id' => $id]);
        }

        return $this->render('add-type', [
            'providedService' => $providedService,
            'model' => $model
        ]);
    }

    public function actionViewCoverageAreas($id)
    {
        $model = $this->findModel($id);

        $searchModel = new ProvidedServiceAreaSearch();
        $searchModel->provided_service_id = $model->id;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('view-coverage-area', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    public function actionAddCoverageArea($id, $type = null, $area = null)
    {
        $providedService = $this->findModel($id);

        if (count($providedService->providedRequestTypes) === 0) {
            return $this->redirect(['/provided-service/add-type', 'id' => $id]);
        }
        // check service request types
        // if there is none redirect him to add the service request types
        // else show him the add coverage


        if (empty($type)) {
            $providedRequestType = $providedService->getProvidedRequestTypes()->one();
        } else {
            $providedRequestType = ProvidedRequestType::findOne($type);
        }

        /** @var ProvidedServiceArea $providedServiceArea */
        $providedServiceArea = ProvidedServiceArea::find()->where(['id' => $area])->one();

        $model = new AddCoverageArea();

        $coveredAreas = [];

        if ($providedServiceArea) {
            $model->provided_service_area_id = $providedServiceArea->id;
        }

        if ($providedServiceArea && $providedServiceArea->serviceArea) {
            $model->city_id = $providedServiceArea->serviceArea->city_id;
            $model->area_name = $providedServiceArea->serviceArea->name;

            $coveredAreas = $providedServiceArea->serviceArea->serviceAreaCoverages;
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {
            Yii::$app->getSession()->addFlash('success', 'Coverage areas updated');
            return $this->redirect([
                '/provided-service/view-coverage-areas',
                'id' => $providedService->id,
                'type' => $providedRequestType->id,
                'area' => $model->provided_service_area->id
            ]);
        }

        $model->provided_request_type_id = $providedRequestType->id;
        $model->provided_service_id = $id;

        return $this->render('add-coverage', [
            'providedService' => $providedService,
            'model' => $model,
            'providedRequestType' => $providedRequestType,
            'coveredAreas' => $coveredAreas
        ]);
    }

    public function actionSetPricing($id, $area, $hash = null)
    {
        /** @var ProvidedRequestType $providedRequestType */
        $providedRequestType = ProvidedRequestType::find()
            ->andWhere(['id' => $id])
            ->andWhere(['deleted' => false])
            ->one();

        if (!$providedRequestType) {
            throw new NotFoundHttpException();
        }

        $model = $providedRequestType->providedService;

        if (count($providedRequestType->providedServiceAreas) === 0) {
            throw new NotFoundHttpException("No areas set");
        }

        /** @var ProvidedServiceArea $providedServiceArea */
        $providedServiceArea = $providedRequestType->getProvidedServiceAreas()->where(['id' => $area])->one();

        if (!$providedServiceArea     ) {
            throw new NotFoundHttpException();
        }

        if (Yii::$app->getRequest()->isPost) {
            $data = Yii::$app->getRequest()->post();

            $model->savePrices($data, $area, $hash);

            Yii::$app->getSession()->addFlash('success', 'Prices updated');

            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        $motherMatrix = new ServiceAttributeMatrix($model->service);

        return $this->render('set-pricing', [
            'model' => $model,
            'service' => $model->service,
            'provider' => $model->provider,
            'providedServiceArea' => $providedServiceArea,
            'providedRequestType' => $providedRequestType,
            'motherMatrix' => $motherMatrix
        ]);
    }

    public function actionSetDropdownPricing($id, $area, $type, $view = 3)
    {
        /** @var ProvidedRequestType $providedRequestType */
        $providedRequestType = ProvidedRequestType::find()
            ->andWhere(['id' => $id])
            ->andWhere(['deleted' => false])
            ->one();

        if (!$providedRequestType) {
            throw new NotFoundHttpException();
        }

        $model = $providedRequestType->providedService;

        /** @var ProvidedServiceArea $area */
        $providedServiceArea = $providedRequestType->getProvidedServiceAreas()->where(['id' => $area])->one();

        if (!$providedServiceArea) {
            throw new NotFoundHttpException();
        }

        $matrix = Yii::$app->getRequest()->post('attribute');
        $price = Yii::$app->getRequest()->post('price');

        $model->saveMatrixPrice($matrix, $price, $providedServiceArea->id);

        return $this->redirect(['/provided-service/set-pricing',
            'id' => $id, 'area' => $providedServiceArea->id, 'type' => $type, 'view' => $view
        ]);
    }

    public function actionSetAvailability($id, $area)
    {
        /** @var ProvidedRequestType $providedRequestType */
        $providedRequestType = ProvidedRequestType::find()
            ->andWhere(['id' => $id])
            ->andWhere(['deleted' => false])
            ->one();

        if (!$providedRequestType) {
            throw new NotFoundHttpException();
        }

        $model = $providedRequestType->providedService;

        /** @var ProvidedServiceArea $area */
        $area = $providedRequestType->getProvidedServiceAreas()->where(['id' => $area])->one();

        if (!$area) {
            throw new NotFoundHttpException();
        }

        $globalRules = $area->getGlobalRules();
        $localRules = $area->getLocalRules();

        return $this->render('set-availability', [
            'model' => $model,
            'providedRequestType' => $providedRequestType,
            'area' => $area,
            'service' => $model->service,
            'provider' => $model->provider,
            'type' => $providedRequestType,
            'globalRules' => $globalRules,
            'localRules' => $localRules,
        ]);
    }


}
