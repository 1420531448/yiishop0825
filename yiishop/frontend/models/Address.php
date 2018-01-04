<?php

namespace frontend\models;
use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public $detail_address;
    public function rules()
    {
        return [
            [['name','cmbProvince','cmbCity','cmbArea','tel','detail_address'],'required'],
            ['status','default','value'=>null],
        ];
    }
}