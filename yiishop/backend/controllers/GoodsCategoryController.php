<?php

namespace backend\controllers;
use backend\filter\RbacFilter;
use backend\models\GoodsCategory;
use yii\helpers\Json;
use yii\web\Controller;

class GoodsCategoryController extends Controller{
    //>>商品分类列表
    public function actionIndex(){
        //orderBy('parent_id asc')->orderBy('id desc')->
        $model = GoodsCategory::find()->orderBy('tree asc,lft asc')->all();
        //$rows = $model->getChildren();
//        var_dump($model);die;
        return $this->render('index',['rows'=>$model]);
    }
    //>>商品分类添加
    public function actionAdd(){
        $model = new GoodsCategory();
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $request = \Yii::$app->request;
        if($request->isPost){
//            var_dump($model);die;
            if($model->load($request->post()) && $model->validate()){
                if(GoodsCategory::find()->where(['id'=>$model->parent_id])->one()){
                    //>>追加一个子孙节点
                    $model->appendTo(GoodsCategory::find()->where(['id'=>$model->parent_id])->one());
                }else{
                    //>>生成一个新节点
                    $model->makeRoot();
                }
                $redis->del('goods_Category');
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods-category/index']);
            }
        }else{
            return $this->render('update',['model'=>$model]);
        }
    }
    //>>商品分类修改
    public function actionEdit($id){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $row = GoodsCategory::find()->where(['id'=>$id])->one();
        $parent_id = $row->parent_id;
        $request = \Yii::$app->request;

        if($request->isPost){
            $row->load($request->post());
            if($row->validate()){
                if($row->parent_id){
                    //>>创建子节点
                    $row->appendTo(GoodsCategory::find()->where(['id'=>$row->parent_id])->one());
                }else{
                    //>>创建根节点
                    if($parent_id == 0){
                        $row->save();
                    }else{
                        $row->makeRoot();
                    }
                }
                $redis->del('goods_Category');
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods-category/index']);
            }
        }
        return $this->render('update',['model'=>$row]);

    }
    //>>商品分类删除
    public function actionDelete($id){

        if(GoodsCategory::find()->where(['parent_id'=>$id])->one()){
            echo Json::encode(false);
        }else{
            //>>删除本节点
            GoodsCategory::deleteAll(['id'=>$id]);
            echo Json::encode(true);
        }
    }
    //>>zTree测试
    public function actionTest(){
        return $this->renderPartial('test');
    }
    //>>权限管理
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}