<?php

namespace backend\models;
use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    public function rules()
    {
        return [
            [
                ['name','intro','article_category_id','status'],'required'
            ]
        ];
    }
    public function attributeLabels()
    {
     return [
       'name'=>'文章标题',
       'intro'=>'简介',
       'article_category_id'=>'文章分类',
       'status'=>'状态'
     ];
    }
}