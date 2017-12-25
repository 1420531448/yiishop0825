<?php

namespace backend\models;
use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Json;

class GoodsCategory extends ActiveRecord{

    public function rules()
    {
        return [
            [
                ['name','parent_id','intro'],'required'
            ],
            ['parent_id','validatePid'],

        ];
    }
    //>>自定义验证规则
    public function validatePid(){
        $parent= GoodsCategory::find()->where(['id'=>$this->parent_id])->one();

        if(!is_object($parent)){
            return 0;
        }else{
            if($parent->isChildOf($this)){
                $this->addError('parent_id','不能修改为子孙节点的子节点');
            }
        }

    }
    public function attributeLabels()
    {
        return [
          'name'=>'商品分类名',
          'parent_id'=>'上级分类Id',
            'intro'=>'商品简介'
        ];
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                 'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }

    //>>获取所有分类数据
    public static function getNodes(){
        $res=self::find()->select(['id','name','parent_id'])->asArray()->all();
        array_unshift($res,['id'=>0,'name'=>'【顶级分类】','parent_id'=>0]);
        return Json::encode($res);
    }

}