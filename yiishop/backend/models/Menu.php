<?php

namespace backend\models;
use yii\db\ActiveRecord;

class Menu extends ActiveRecord{
    public function rules()
    {
        return [
            [['name','route','p_id','sort'],'required']
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'菜单名称',
            'route'=>'路由',
            'p_id'=>'上级菜单',
            'sort'=>'排序'
        ];
    }
}