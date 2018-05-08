<?php

namespace frontend\controllers;

use common\forms\AddPricingAttribute;
use common\forms\AttachAttribute;
use common\forms\UpdateAttribute;
use common\helpers\MatrixHelper;
use common\helpers\ServiceAttributeMatrix;
use common\models\FieldType;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\PricingAttributeMatrix;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeDepends;
use common\models\ServiceCity;
use frontend\helpers\FieldsConfigurationHelper;
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
        $service = $this->findModel($id);

        $model = new AttachAttribute();
        $model->service_id = $service->id;

        return $this->render('view', [
            'model' => $model,
            'service' => $service
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

    public function actionEditAttribute($id, $attribute_id = null)
    {
        $service = $this->findModel($id);
        /** @var ServiceAttribute $attribute */
        $attribute = $service->getServiceAttributes()->where(['id' => $attribute_id])->one();

        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $model = new UpdateAttribute();
        $model->attribute_id = $attribute->id;
        $model->service_id = $service->id;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->update()) {
            Yii::$app->getSession()->addFlash('success', 'Attribute configuration updated');
            return $this->redirect(['/service/edit-attribute', 'id' => $id, 'attribute_id' => $attribute_id]);
        }

        $model->attribute_name = $attribute->name;
        $model->price_type_id = count($attribute->pricingAttributes) > 0 ? Arr::first($attribute->pricingAttributes)->price_type_id : null;
        $model->input_type_id = $attribute->input_type_id;
        $model->user_input_type_id = $attribute->user_input_type_id;
        $model->attribute_options = collect($attribute->getServiceAttributeOptions()->asArray()->all())->pluck('id')->toArray();
        $model->attribute_validations = collect($attribute->getValidations()->asArray()->all())->pluck('id')->toArray();
        $model->field_type_id = $attribute->field_type_id;
        $model->field_type = FieldType::findOne($attribute->field_type_id)->name;

        if ($model->field_type === FieldType::TYPE_RANGE) {
            $model->min = $attribute->getMinimum() ? $attribute->getMinimum()->name : null;
            $model->max = $attribute->getMaximum() ? $attribute->getMaximum()->name : null;
        }


        return $this->render('update-attribute', [
            'model' => $model,
            'attribute' => $attribute,
            'service' => $service
        ]);
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
     * @param $view
     * @return mixed
     */
    public function actionAddPricing($id, $view = 1)
    {
        $model = $this->findModel($id);

        $motherMatrix = new ServiceAttributeMatrix($model);

        if (empty($view) || !in_array($view, range(1, 3))) {
            $view = 1;
        }


        return $this->render('view-price-matrix', [
            'model' => $model,
            'motherMatrix' => $motherMatrix,
            'view' => $view,
        ]);
    }

    public function actionConfirmPriceMatrix($id)
    {
        $model = $this->findModel($id);

        $motherMatrix = new ServiceAttributeMatrix($model);

        $motherMatrix->saveMatricesRows();

        Yii::$app->getSession()->addFlash('success', 'Pricing attributes matrix saved');

        return $this->redirect(['/service/add-pricing', 'id' => $model->id]);
    }

    public function actionAddAttributeDependency($id)
    {
        $model = $this->findModel($id);


        if (Yii::$app->getRequest()->isPost) {
            $data = Yii::$app->getRequest()->post();

            $attribute_id = Arr::get($data, 'depends_on_id');
            $depends_on_id = Arr::get($data, 'attribute_id');
            $option_id = Arr::get($data, 'option_id');

            $dependency = new ServiceAttributeDepends();
            $dependency->service_attribute_id = $attribute_id;
            $dependency->depends_on_id = $depends_on_id;
            $dependency->service_attribute_option_id = $option_id;
            $dependency->save();

            Yii::$app->getSession()->addFlash('success', 'Dependency saved');
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }


        return $this->render('add-attribute-depenedency', [
            'model' => $model
        ]);
    }

    public function actionRemoveDependency($id)
    {
        $dependency = ServiceAttributeDepends::findOne($id);

        if ($dependency) {
            $dependency->delete();
        }

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    public function actionSetPricingGroups($id)
    {
        $service = $this->findModel($id);
        $model = new AddPricingAttribute();
        $model->service_id = $id;

        if (
            Yii::$app->getRequest()->isPost &&
            $model->load(Yii::$app->getRequest()->post()) &&
            $model->addPricingGroup()
        ) {
            return $this->redirect(['/service/set-pricing-groups', 'id' => $service->id]);
        }

        return $this->render('set-pricing-groups', [
            'service' => $service,
            'model' => $model,
        ]);
    }

    public function actionRemovePricingAttribute($id, $group_id, $service_attribute_id)
    {
        $model = $this->findModel($id);

        /** @var PricingAttributeGroup $pricingGroup */
        $pricingGroup = $model->getPricingAttributeGroups()->where(['id' => $group_id])->one();

        if (!$pricingGroup) {
            throw new NotFoundHttpException();
        }

        /** @var PricingAttribute $pricingGroupAttribute */
        $pricingGroupAttribute = $pricingGroup->getPricingAttributes()
            ->where(['service_attribute_id' => $service_attribute_id])
            ->one();

        $pricingGroupAttribute->pricing_attribute_group_id = null;
        $pricingGroupAttribute->save();

        return $this->redirect(['/service/set-pricing-groups', 'id' => $id]);
    }
}
