<?php

namespace frontend\controllers;

use common\forms\AddCoverageArea;
use common\forms\AddType;
use common\helpers\MatrixHelper;
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
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

    public function actionAddCoverageArea($id, $type = null)
    {
        $providedService = $this->findModel($id);

        if (count($providedService->providedServiceTypes) === 0) {
            return $this->redirect(['/provided-service/add-type', 'id' => $id]);
        }
        // check service request types
        // if there is none redirect him to add the service request types
        // else show him the add coverage

        if (empty($type)) {
            $type = $providedService->getProvidedServiceTypes()->one()->id;
        }

        $model = new AddCoverageArea();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {
            Yii::$app->getSession()->addFlash('success', 'Coverage areas updated');
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        $model->service_type = $type;
        $model->provided_service_id = $id;

        return $this->render('add-coverage', [
            'providedService' => $providedService,
            'model' => $model,
            'type' => $type,
        ]);
    }

    public function actionAddPricing($id)
    {
        $model = $this->findModel($id);

        $matrix = new MatrixHelper($model->service);

        if (Yii::$app->getRequest()->isPost) {
            $matrixPrices = Yii::$app->getRequest()->post('matrix_price');
            $city = Yii::$app->getRequest()->post('city');

            if (empty($city)) {
                Yii::$app->getSession()->addFlash('error', 'City not selected');
                return $this->redirect(Yii::$app->getRequest()->getReferrer());
            }

            if (empty($matrixPrices)) {
                Yii::$app->getSession()->addFlash('error', 'Prices not set');
                return $this->redirect(Yii::$app->getRequest()->getReferrer());
            }

            $model->savePrices($matrixPrices, $city);
        }

        return $this->render('add-pricing', [
            'model' => $model,
            'service' => $model->service,
            'provider' => $model->provider,
            'matrixHeaders' => $matrix->getMatrixHeaders(),
            'matrixRows' => $matrix->getMatrixRows(),
            'noImpactRows' => $matrix->getNoImpactRows()
        ]);
    }


}
