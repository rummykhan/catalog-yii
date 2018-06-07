<?php

namespace frontend\controllers;

use common\models\GlobalAvailabilityException;
use common\models\GlobalAvailabilityRule;
use RummyKhan\Collection\Arr;
use Yii;
use common\models\Provider;
use common\models\ProviderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProviderController implements the CRUD actions for Provider model.
 */
class ProviderController extends Controller
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
     * Lists all Provider models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProviderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Provider model.
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
     * Creates a new Provider model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Provider();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Provider model.
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
     * Deletes an existing Provider model.
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
     * Finds the Provider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Provider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Provider::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCalendar($provider_id)
    {
        $model = $this->findModel($provider_id);

        return $this->render('calendar/index', compact('model'));
    }

    public function actionAddCalendar($provider_id)
    {
        $model = $this->findModel($provider_id);

        return $this->render('calendar/add', compact('model'));
    }

    public function actionCalendarGlobalRules($provider_id)
    {
        $model = $this->findModel($provider_id);
        $data = Yii::$app->getRequest()->post();

        $rules = Arr::get($data, 'global-rules');

        if (empty($rules) || empty(json_decode($rules, true))) {
            return $this->redirect(['/provided/add-calendar', 'provider_id' => $model->id]);
        }

        $rulesJson = collect(json_decode($rules, true))->groupBy('type')->toArray();

        foreach ($rulesJson as $ruleType => $rules) {
            switch ($ruleType) {
                case 'Available':
                    GlobalAvailabilityRule::addRules($rules, $provider_id);
                    break;

                case 'Not Available':
                    GlobalAvailabilityException::addRules($rules, $provider_id);
                    break;
            }
        }

        return $this->redirect(['/provided/calendar', 'provider_id' => $provider_id]);
    }

    public function actionCalendarDateRule($provider_id)
    {
        $model = $this->findModel($provider_id);
        $providedServiceType = ProvidedRequestType::find()
            ->where(['provided_service_id' => $model->id])
            ->andWhere(['service_type_id' => $type])
            ->andWhere(['deleted' => false])
            ->one();

        if (!$providedServiceType) {
            throw new NotFoundHttpException();
        }

        /** @var ProvidedServiceArea $area */
        $area = $providedServiceType->getProvidedServiceAreas()->where(['id' => $area])->one();

        if (!$area) {
            throw new NotFoundHttpException();
        }

        $data = Yii::$app->getRequest()->post();

        $rules = Arr::get($data, 'date-rules');
        $date = Arr::get($data, 'date');

        if (empty($rules) || empty(json_decode($rules, true)) || empty($date)) {
            return $this->redirect(['/provided-service/set-availability', 'id' => $model->id, 'area' => $area->id, 'type' => $type]);
        }

        $rulesJson = collect(json_decode($rules, true))->groupBy('type')->toArray();

        foreach ($rulesJson as $ruleType => $rules) {
            switch ($ruleType) {
                case 'Available':
                    AvailabilityRule::addRules($area, $rules, $date);
                    break;

                case 'Not Available':
                    AvailabilityException::addRules($area, $rules, $date);
                    break;
            }
        }

        return $this->redirect(['/provided-service/set-availability', 'id' => $model->id, 'area' => $area->id, 'type' => $type]);
    }
}
