<?php

namespace frontend\controllers;

use common\forms\AddCoverageArea;
use common\forms\AddType;
use common\helpers\MatrixHelper;
use common\models\ProvidedServiceArea;
use common\models\ProvidedServiceAreaSearch;
use common\models\ProvidedServiceType;
use common\models\Provider;
use common\models\ServiceType;
use Yii;
use common\models\ProvidedService;
use common\models\ProvidedServiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProvidedServiceController implements the CRUD actions for ProvidedService model.
 */
class ProvidedServiceController extends Controller
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

        //dd($providedService, $serviceTypes);

        /** @var ServiceType $serviceType */
        foreach (ServiceType::find()->all() as $serviceType) {

            // if service type is checked
            if (isset($serviceTypes[$serviceType->id])) {

                // if not attached..

                $providedServiceType = ProvidedServiceType::find()
                    ->where(['provided_service_id' => $id])
                    ->andWhere(['service_type_id' => $serviceType->id])
                    ->andWhere(['deleted' => false])
                    ->one();

                // attach it

                if ($providedServiceType) {
                    continue;
                }

                $providedServiceType = new ProvidedServiceType();
                $providedServiceType->provided_service_id = $providedService->id;
                $providedServiceType->service_type_id = $serviceType->id;
                $providedServiceType->save();
                continue;
            }


            // if service type is not checked..

            $providedServiceType = ProvidedServiceType::find()
                ->where(['provided_service_id' => $id])
                ->andWhere(['service_type_id' => $serviceType->id])
                ->andWhere(['deleted' => false])
                ->one();

            if (!$providedServiceType) {
                continue;
            }

            $providedServiceType->deleted = true;
            $providedServiceType->save();
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

        if (count($providedService->providedServiceTypes) === 0) {
            return $this->redirect(['/provided-service/add-type', 'id' => $id]);
        }
        // check service request types
        // if there is none redirect him to add the service request types
        // else show him the add coverage


        if (empty($type)) {
            $serviceType = $providedService->getProvidedServiceTypes()->one();
        } else {
            $serviceType = ServiceType::findOne($type);
        }

        $area = ProvidedServiceArea::find()->where(['id' => $area])->one();

        $model = new AddCoverageArea();

        $coveredAreas = [];

        if ($area) {
            $model->area_id = $area->id;
            $model->city_id = $area->city_id;
            $model->area_name = $area->name;

            $coveredAreas = $area->providedServiceCoverages;
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {
            Yii::$app->getSession()->addFlash('success', 'Coverage areas updated');
            return $this->redirect([
                '/provided-service/view-coverage-areas',
                'id' => $providedService->id,
                'type' => $serviceType->id,
                'area' =>  $model->provided_service_area->id
            ]);
        }

        $model->service_type = $serviceType->id;
        $model->provided_service_id = $id;

        return $this->render('add-coverage', [
            'providedService' => $providedService,
            'model' => $model,
            'serviceType' => $serviceType,
            'coveredAreas' => $coveredAreas
        ]);
    }

    public function actionSetPricing($id, $area, $type)
    {
        $model = $this->findModel($id);
        $providedServiceType = ProvidedServiceType::find()
            ->where(['provided_service_id' => $model->id])
            ->andWhere(['service_type_id' => $type])
            ->andWhere(['deleted' => false])
            ->one();

        if (!$providedServiceType) {
            throw new NotFoundHttpException();
        }

        $area = $providedServiceType->getProvidedServiceAreas()->where(['id' => $area])->one();

        if (!$area) {
            throw new NotFoundHttpException();
        }

        $matrix = new MatrixHelper($model->service);

        if (Yii::$app->getRequest()->isPost) {
            $matrixPrices = Yii::$app->getRequest()->post('matrix_price');
            $noImpactPrices = Yii::$app->getRequest()->post('no_impact_price');

            $model->saveMatrixPrices($matrixPrices, $area->id);
            $model->saveNoImpactPrices($noImpactPrices, $area->id);

            Yii::$app->getSession()->addFlash('success', 'Prices updated');
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        return $this->render('add-pricing', [
            'model' => $model,
            'service' => $model->service,
            'provider' => $model->provider,
            'area' => $area,
            'providedServiceType' => $providedServiceType,
            'matrixHeaders' => $matrix->getMatrixHeaders(),
            'matrixRows' => $matrix->getMatrixRows(),
            'noImpactRows' => $matrix->getNoImpactRows()
        ]);
    }


}
