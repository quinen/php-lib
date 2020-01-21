<?php

namespace QuinenLib\Cake\Model;

use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Trait UiTrait
 *
 * add label icon and gender option to a model
 *
 * @package AdminUi\Model\Table
 */
trait UiTrait
{
    protected $uiLabel;
    protected $uiIcon;
    protected $uiGender;
    protected $uiFieldsLabel = [];
    protected $uiFieldsFormat = [];

    /**
     * @return mixed
     */
    public function getUiLabel($isPlural = true)
    {
        if ($this->uiLabel === null) {
            $plural = Inflector::humanize(Inflector::underscore($this->getAlias()));
            $this->setUiLabel($plural);
        }
        return $this->uiLabel[(int)$isPlural];
    }

    /**
     * @param mixed $uiLabel
     */
    public function setUiLabel($uiLabel)
    {
        if (is_string($uiLabel)) {
            $arrayLabel = explode(' ', $uiLabel);
            $arrayLabel[0] = Inflector::singularize($arrayLabel[0]);
            $singular = implode(' ', $arrayLabel);
            $uiLabel = [$singular, $uiLabel];
        }

        $this->uiLabel = $uiLabel;
    }

    /**
     * @return mixed
     */
    public function getUiIcon()
    {
        if ($this->uiIcon === null) {
            $this->setUiIcon('question-sign');
        }
        return $this->uiIcon;
    }

    /**
     * @param mixed $uiIcon
     */
    public function setUiIcon($uiIcon)
    {
        $this->uiIcon = $uiIcon;
    }

    /**
     * @return mixed
     */
    public function getUiGender()
    {
        return $this->uiGender;
    }

    /**
     * @param mixed $uiGender
     */
    public function setUiGender($uiGender)
    {
        $this->uiGender = $uiGender;
    }

    /**
     * @return mixed
     */
    public function getUiFieldsLabel()
    {
        return $this->uiFieldsLabel;
    }

    /**
     * @param mixed $uiFieldsLabel
     */
    public function setUiFieldsLabel($uiFieldsLabel)
    {
        $default = $this->getUiFieldsLabelDefault();
        $this->uiFieldsLabel = $uiFieldsLabel + $this->uiFieldsLabel + $default;
        ksort($this->uiFieldsLabel);
    }

    /**
     * @return array|\Cake\Collection\CollectionTrait
     */
    public function getUiFieldsLabelDefault()
    {
        $columns = $this->getSchema()->columns();
        return array_combine($columns, $columns);
    }

    public function getUiFieldLabel($field)
    {
        if (($pos = strpos($field, '.')) && $pos !== false) {
            $associationProperty = substr($field, 0, $pos);
            $table = $this->associations()->getByProperty($associationProperty)->getTarget();
            $associationField = substr($field, $pos + 1);
            return $table->getUiFieldLabel($associationField);
        }

        if ($this->uiFieldsLabel === []) {
            $this->uiFieldsLabel = $this->getUiFieldsLabelDefault();
        }

        return Hash::get($this->uiFieldsLabel, $field);
    }

    /**
     * @return array
     */
    public function getUiFieldsFormat(): array
    {
        return $this->uiFieldsFormat;
    }

    /**
     * @param array $uiFieldsFormat
     */
    public function setUiFieldsFormat(array $uiFieldsFormat): void
    {
        $default = $this->getUiFieldsFormatDefault();
        $this->uiFieldsFormat = $uiFieldsFormat + $this->uiFieldsFormat + $default;
        ksort($this->uiFieldsFormat);
    }

    /**
     * @return array|\Cake\Collection\CollectionTrait
     */
    public function getUiFieldsFormatDefault()
    {
        $columns = $this->getSchema()->columns();
        return array_fill_keys($columns, true);
    }

    public function getUiFieldFormat($field)
    {

        if (($pos = strpos($field, '.')) && $pos !== false) {
            $associationProperty = substr($field, 0, $pos);

            $association = $this->associations()->getByProperty($associationProperty);
            if ($association === null) {
                return false;
            }
            $table = $association->getTarget();

            $associationField = substr($field, $pos + 1);
            return $table->getUiFieldFormat($associationField);
        }
        return Hash::get($this->uiFieldsFormat, $field);
    }


}