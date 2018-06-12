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
use common\models\ProvidedServiceType;
use common\models\ProvidedServiceTypeSearch;
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
        $model = $this->findModel($id);
        $searchModel = new ProvidedServiceTypeSearch();
        $searchModel->provided_service_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
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

    public function actionSetPricing($id, $hash = null)
    {
        /** @var ProvidedRequestType $providedServiceType */
        $providedServiceType = ProvidedServiceType::find()
            ->andWhere(['id' => $id])
            ->andWhere(['deleted' => false])
            ->one();

        if (!$providedServiceType) {
            throw new NotFoundHttpException();
        }

        $model = $providedServiceType->providedService;

        if (Yii::$app->getRequest()->isPost) {
            $data = Yii::$app->getRequest()->post();

            $model->savePrices($data, $providedServiceType, $hash);

            Yii::$app->getSession()->addFlash('success', 'Prices updated');

            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        $motherMatrix = new ServiceAttributeMatrix($model->service);

        return $this->render('set-pricing', [
            'model' => $model,
            'service' => $model->service,
            'provider' => $model->provider,
            'providedRequestType' => $providedServiceType,
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

    public function actionEditType($id)
    {
        $model = ProvidedServiceType::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('provided-service-type/edit', compact('model'));
    }

    public function actionAddType($id)
    {
        $providedService = $this->findModel($id);

        $model = new ProvidedServiceType();
        $model->provided_service_id = $providedService->id;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validateUnique() && $model->save()) {
            return $this->redirect(['/provided-service/view', 'id' => $providedService->id]);
        }


        return $this->render('provided-service-type/edit', compact('model'));
    }


}
