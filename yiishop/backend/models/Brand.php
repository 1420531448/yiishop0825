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
              ['name','intro','imgFile','status'],'required'
          ],
            //>>字段单独规则
            ['imgFile','file','extensions'=>['jpg','gif','png'],'maxSize'=>4*1024*1024,'skipOnEmpty'=>false]
        ];
    }

    public function AttributeLabels(){
        return [
            'name'=>'品牌名',
            'intro'=>'简介',
            'imgFile'=>'logo上传',
            'status'=>'状态'
        ];
    }
}