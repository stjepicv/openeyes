<?php


class OphInLabResults_Type extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphInLabResults_Type static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophinlabresults_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, result_element_id, ', 'required'),
            array('min_range', 'minRangeValidation'),
            array('max_range', 'maxRangeValidation'),
            array('normal_min', 'normalMinValueValidation'),
            array('normal_max', 'normalMaxValueValidation'),
            array('type, result_element_id, field_type_id, default_units, custom_warning_message, min_range, max_range,
            normal_min, normal_max, show_on_whiteboard ', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'result_element_type' => array(self::BELONGS_TO, 'ElementType', 'result_element_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'fieldType' => [self::BELONGS_TO, 'OphInLabResults_Field_Type', 'field_type_id'],
            'resultOptions' => [self::HAS_MANY, 'OphInLabResults_Type_Options', 'type']
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type' => 'Type',
            'result_element_type' => 'Result Type',
        );
    }

    public function normalMinValueValidation($attribute, $params)
    {
        if ($this->$attribute) {
            if ($this->normal_max && $this->$attribute > $this->normal_max) {
                $this->addError(
                    $attribute, $attribute . ' has to be lower than the normal max value'
                );
            }
            if ($this->min_range && $this->$attribute < $this->min_range) {
                $this->addError(
                    $attribute, $attribute . ' has to be higher than the range min'
                );
            }
        }
    }

    public function normalMaxValueValidation($attribute, $params)
    {
        if ($this->$attribute) {
            if ($this->normal_min && $this->$attribute > $this->normal_min) {
                $this->addError(
                    $attribute, $attribute . ' has to be higher than the normal min value'
                );
            }
            if ($this->max_range && $this->$attribute < $this->max_range) {
                $this->addError(
                    $attribute, $attribute . ' has to be lower than the range max'
                );
            }
        }
    }

    public function minRangeValidation($attribute, $params)
    {
        if ($this->$attribute && $this->max_range) {
            if ($this->$attribute > $this->max_range) {
                $this->addError(
                    $attribute, $attribute . ' has to be lower than max range'
                );
            }
        }
    }

    public function maxRangeValidation($attribute, $params)
    {
        if ($this->$attribute && $this->min_range) {
            if ($this->$attribute < $this->min_range) {
                $this->addError(
                    $attribute, $attribute . ' has to be higher than min range'
                );
            }
        }
    }
}
