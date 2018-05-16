<?php
/**
 * Created by PhpStorm.
 * User: rummykhan
 * Date: 5/13/18
 * Time: 12:38 PM
 */

namespace common\forms;


use yii\base\DynamicModel;
use yii\base\Model;

class ImportOptionsFromExcel extends Model
{
    public $file;
    public $service_id;
    public $attribute_id;

    public function rules()
    {
        return [
            [['service_id', 'attribute_id'], 'integer'],
            ['file', 'file', 'extensions' => ['xlsx', 'xls'], 'checkExtensionByMimeType' => false],
            [['service_id', 'attribute_id', 'file'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Excel file'
        ];
    }

    public function getValidRows()
    {
        if (!$this->validate()) {
            return [];
        }

        $validRows = [];

        /** @var array $importedRows */
        $importedRows = \moonland\phpexcel\Excel::import($this->file->tempName, []);

        foreach ($importedRows as $importedRow) {

            if (!$this->isValidRow($importedRow)) {
                continue;
            }

            $validRows[] = $importedRow;
        }

        return $validRows;
    }

    public function update()
    {

    }

    protected function isValidRow($importedRow)
    {
        $dynamicModel = DynamicModel::validateData($importedRow, [
            [['name', 'description', 'mobile-description'], 'required'],
            [['name', 'name-ar', 'description', 'description-ar', 'mobile-description', 'mobile-description-ar'], 'safe'],
            [['icon'], 'safe'],
        ]);

        return !$dynamicModel->hasErrors();
    }
}