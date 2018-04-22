<?php

namespace frontend\controllers;

use common\forms\AttachAttribute;
use common\models\PricingAttribute;
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

        if($model->load(Yii::$app->getRequest()->post()) && $model->attach()){
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

        // if this service already has all attribute marked in the pricing_attribute table

        // Show user the page where he can mark attributes as composite or individual
        // else
        // Show user the page where he can see the pricing matrix

        foreach ($model->serviceLevelAttributes as $serviceLevelAttribute){

            $pricingAttribute = PricingAttribute::find()
                ->where(['service_attribute_id' => $serviceLevelAttribute->id])
                ->one();

            if(!$pricingAttribute){
                return $this->render('set-pricing-attributes', ['model' => $model]);
            }

        }


        dd('all exists',$model);
    }
}
