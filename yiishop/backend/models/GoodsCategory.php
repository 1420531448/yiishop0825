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
    //>>前台分类展示
    public static function CategoryShow(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $html = $redis->get('goods_Category');
//        $redis->del('goods_Category');die;
        if(!$html){
            $tops = GoodsCategory::find()->where(['parent_id'=>0])->all();
            foreach($tops as $k1=>$top){
                $html.='<div class="cat '.($k1==0?'item1':'').'">';
                $html.='<h3><a href="'.\yii\helpers\Url::to(['goods-list/display','id'=>$top->id]).'">'.$top->name.'</a><b></b></h3>';
                $seconds = GoodsCategory::find()->where(['parent_id'=>$top->id])->all();
                $two[$top->id]= $seconds;
                $html.='   <div class="cat_detail">';
                foreach ($two[$top->id] as $k2=>$second){
                    $html.=   '<dl '.($k2==0?'class="dl_1st"':'').'>';
                    $html.=' <dt><a href="'.\yii\helpers\Url::to(['goods-list/display','id'=>$second->id]).'">'.$second->name.'</a></dt>';
                    $thirds =GoodsCategory::find()->where(['parent_id'=>$second->id])->all();
                    $three[$second->id]=$thirds;
                    foreach($three[$second->id] as $third){
                        $html.='<dd>';
                        $html.='<a href="'.\yii\helpers\Url::to(['goods-list/display','id'=>$third->id]).'">'.$third->name.'</a>';
                        $html.='</dd>';
                    }
                    $html.=' </dl>';
                }
                $html.='</div>';

                $html.='</div>';
            }
            $redis->set('goods_Category',$html,24*3600);
        }


        return $html;
    }

}