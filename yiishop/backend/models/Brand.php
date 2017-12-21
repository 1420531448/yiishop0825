<?php

namespace backend\models;
use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    public $imgFile;
    public function rules()
    {
        return [
            //>>必填字段
          [
              ['name','intro','logo','status'],'required'
          ],
            //>>字段单独规则

        ];
    }

    public function AttributeLabels(){
        return [
            'name'=>'品牌名',
            'intro'=>'简介',
            'logo'=>'logo上传',
            'status'=>'状态'
        ];
    }
}