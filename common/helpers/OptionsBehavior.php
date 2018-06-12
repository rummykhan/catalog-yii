<?php

namespace common\helpers;

use yii\base\Behavior;
use yii\base\UnknownPropertyException;
use yii\db\ActiveRecord;

/**
 * Class OptionsBehavior
 * @package common\helpers
 */
class OptionsBehavior extends Behavior {

    /**
     *
     * @var array associative Array of options
     */
    public $options;

    /**
     *
     * @var string name of the attribute
     */
    public $attribute;

    /**
     *
     * @var string whether the attribute is used for multiple selection
     */
    public $multiple = false;

    /**
     * Get the list of
     * @param mixed $key
     * @return mixed
     */
    private function getOptionLabelByKey($key) {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * Get the current label of the models attribute
     * @return mixed
     */
    private function getOptionLabel() {
        $attr = $this->attribute;
        if ($this->multiple) {
            foreach ($this->owner->$attr as $key => $value) {
                $return[] = $this->getOptionLabelByKey($value);
            }
            return implode(",", $return);
        } else {
            return $this->getOptionLabelByKey($this->owner->$attr);
        }
    }

    /**
     * Get the array of options as defined
     * @return mixed
     */
    private function getOptions() {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function __get($name) {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if ($name == $this->attribute . '_label') {
                return $this->getOptionLabel();
            }
            // @codeCoverageIgnoreStart
            else if ($name == $this->attribute . '_list') {
                return $this->getOptions();
            }
            // @codeCoverageIgnoreStart
            else {
                throw $e;
            }
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true) {
        return $name == $this->attribute . '_list' || $name == $this->attribute . '_label';
    }

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    public function afterFind($event) {
        if ($this->multiple) {
            $attr = $this->attribute;
            $this->owner->$attr = explode(",", $this->owner->$attr);
        }
    }

}
