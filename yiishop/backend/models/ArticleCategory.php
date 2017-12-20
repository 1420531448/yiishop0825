<?php

namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
    public function rules()
    {
        return [
            [
                ['name','intro','status'],'required'
            ]
        ];
    }
    public function AttributeLabels(){
        return [
            'name'=>'分类名',
            'intro'=>'简介',
            'status'=>'状态'
        ];
    }
}