<?php

namespace backend\models;
use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Json;

class GoodsCategory extends ActiveRecord{
    //>>找儿子方法
    private function getChildren(&$categorys,$parent_id,$deep=0){
        //准备一个空数组,用于装找到的儿子
        static $children = [];
        //循环所有的分类数据,将每一条数据中的parent_id进行必须,
        //等于我传入的$parent_id,就是我们需要的儿子
        foreach ($categorys as $category){
            if($category['parent_id'] == $parent_id){
                //在每一个找到的儿子上保存一个字段表示缩进好的分类名称
                $category['name_txt'] = str_repeat("&emsp;",$deep*2).$category['name'];
                $children[] = $category;
                //继续找,$category下可能还有子节点
                $this->getChildren($categorys,$category['id'],$deep+1);
            }
        }
        //返回儿子
        return $children;
    }
    public function rules()
    {
        return [
            [
                ['name','parent_id','intro'],'required'
            ]
        ];
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