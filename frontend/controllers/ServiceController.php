<?php

namespace frontend\controllers;

use common\forms\AddPricingAttribute;
use common\forms\AddServiceViewAttribute;
use common\forms\AttachAttribute;
use common\forms\AttachOptions;
use common\forms\ImportOptionsFromExcel;
use common\forms\UpdateAttribute;
use common\helpers\MatrixHelper;
use common\helpers\ServiceAttributeMatrix;
use common\models\FieldType;
use common\models\PriceType;
use common\models\PricingAttribute;
use common\models\PricingAttributeGroup;
use common\models\PricingAttributeMatrix;
use common\models\ServiceAttribute;
use common\models\ServiceAttributeOption;
use common\models\ServiceCity;
use common\models\ServiceView;
use common\models\ServiceViewAttribute;
use frontend\helpers\FieldsConfigurationHelper;
use RummyKhan\Collection\Arr;
use Yii;
use common\models\Service;
use common\models\ServiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
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

        $model->name = $attribute->name;
        $model->description = $attribute->description;
        $model->mobile_description = $attribute->mobile_description;

        $model->name_ar = $attribute->name_ar;
        $model->description_ar = $attribute->description_ar;
        $model->mobile_description_ar = $attribute->mobile_description_ar;

        $model->price_type_id = count($attribute->pricingAttributes) > 0 ? Arr::first($attribute->pricingAttributes)->price_type_id : null;
        $model->input_type_id = $attribute->input_type_id;
        $model->user_input_type_id = $attribute->user_input_type_id;
        $model->attribute_validations = collect($attribute->getValidations()->asArray()->all())->pluck('id')->toArray();
        $model->field_type_id = $attribute->field_type_id;
        $model->field_type = FieldType::findOne($attribute->field_type_id)->name;


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
     * @param $service_id int
     * @param $attribute_id int
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAttachOptions($service_id, $attribute_id)
    {
        $service = $this->findModel($service_id);

        /** @var ServiceAttribute $attribute */
        $attribute = ServiceAttribute::findOne($attribute_id);

        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $model = new AttachOptions();
        $model->service_id = $service->id;
        $model->service_attribute_id = $attribute->id;


        if ($model->load(Yii::$app->getRequest()->post()) && $model->attach()) {
            Yii::$app->getSession()->addFlash('success', 'Attribute options updated.');
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }

        if ($attribute->fieldType->name === FieldType::TYPE_RANGE) {
            $model->min = $attribute->getMinimum() ? $attribute->getMinimum()->name : null;
            $model->max = $attribute->getMaximum() ? $attribute->getMaximum()->name : null;
        }


        return $this->render('attach-options', [
            'service' => $service,
            'model' => $model,
            'attribute' => $attribute
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

        $motherMatrix = new ServiceAttributeMatrix($model);

        if (empty($view) || !in_array($view, range(1, 3))) {
            $view = 1;
        }

        return $this->render('view-price-matrix', [
            'model' => $model,
            'motherMatrix' => $motherMatrix,
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


            Yii::$app->getSession()->addFlash('success', 'Dependency saved');
            return $this->redirect(Yii::$app->getRequest()->getReferrer());
        }


        return $this->render('add-attribute-depenedency', [
            'model' => $model
        ]);
    }

    public function actionRemoveDependency($id)
    {


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

    public function actionSetViewGroups($id)
    {
        $service = $this->findModel($id);
        $model = new AddServiceViewAttribute();
        $model->service_id = $id;

        if (
            Yii::$app->getRequest()->isPost &&
            $model->load(Yii::$app->getRequest()->post()) &&
            $model->addViewGroup()
        ) {
            return $this->redirect(['/service/set-view-groups', 'id' => $service->id]);
        }

        return $this->render('set-view-groups', [
            'service' => $service,
            'model' => $model,
        ]);
    }

    public function actionRemoveViewAttribute($id, $view_id, $service_attribute_id)
    {
        $model = $this->findModel($id);

        /** @var ServiceView $serviceView */
        $serviceView = $model->getServiceViews()->where(['id' => $view_id])->one();

        if (!$serviceView) {
            throw new NotFoundHttpException();
        }

        /** @var ServiceViewAttribute $serviceViewAttribute */
        $serviceViewAttribute = ServiceViewAttribute::find()
            ->where(['service_attribute_id' => $service_attribute_id])
            ->andWhere(['service_view_id' => $view_id])
            ->one();

        if (!$serviceViewAttribute) {
            throw new NotFoundHttpException();
        }

        // since it's one to one association
        // so it's safe to assume we can delete this.
        $serviceViewAttribute->delete();

        return $this->redirect(['/service/set-view-groups', 'id' => $id]);
    }

    /**
     * @param $service_id
     * @param $attribute_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteAttribute($service_id, $attribute_id)
    {
        /** @var Service $service */
        $service = Service::findOne($service_id);

        /** @var ServiceAttribute $serviceAttribute */
        $serviceAttribute = $service->getServiceAttributes()->where(['id' => $attribute_id])->one();

        if (!$serviceAttribute) {
            throw new NotFoundHttpException();
        }

        $serviceAttribute->deleted = true;
        $serviceAttribute->save();

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * @param $service_id
     * @param $attribute_id
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionImportExcel($service_id, $attribute_id)
    {
        $service = Service::findOne($service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        /** @var ServiceAttribute $attribute */
        $attribute = $service->getServiceAttributes()->where(['id' => $attribute_id])->one();

        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $model = new ImportOptionsFromExcel();
        $model->service_id = $service_id;
        $model->attribute_id = $attribute_id;

        $rows = [];
        if ($model->load(Yii::$app->getRequest()->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');

            $rows = $model->getValidRows();

            if (!empty($rows)) {
                return $this->render('import-option-rows', compact('service', 'attribute', 'rows'));
            }
        }

        return $this->render('import-options', compact('service', 'attribute', 'model'));
    }

    public function actionImportOptions($service_id, $attribute_id)
    {
        $service = Service::findOne($service_id);
        if (!$service) {
            throw new NotFoundHttpException();
        }

        /** @var ServiceAttribute $attribute */
        $attribute = $service->getServiceAttributes()->where(['id' => $attribute_id])->one();

        if (!$attribute) {
            throw new NotFoundHttpException();
        }

        $rows = Yii::$app->getRequest()->post('rows');

        foreach ($rows as $row) {

            $option = $attribute->getServiceAttributeOptions()
                ->where(['name' => Arr::get($row, 'name')])
                ->andWhere(['deleted' => false])
                ->one();

            if (!$option) {
                $option = new ServiceAttributeOption();
                $option->service_attribute_id = $attribute_id;
            }

            $option->name = Arr::get($row, 'name');
            $option->name_ar = Arr::get($row, 'name_ar');
            $option->description = Arr::get($row, 'description');
            $option->description_ar = Arr::get($row, 'description_ar');
            $option->mobile_description = Arr::get($row, 'mobile_description');
            $option->mobile_description_ar = Arr::get($row, 'mobile_description_ar');
            $option->save();

            if ($option->hasErrors()) {
                dd($option->getErrors());
            }
        }

        return $this->redirect(['/service/view', 'id' => $service_id]);
    }
}
