<?php

namespace backend\controllers;
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
        $request = \Yii::$app->request;
        if($request->isPost){
            if($model->load($request->post()) && $model->validate()){
                if(GoodsCategory::find()->where(['id'=>$model->parent_id])->one()){
                    $model->appendTo(GoodsCategory::find()->where(['id'=>$model->parent_id])->one());
                }else{
                    $model->makeRoot();
                }
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods-category/index']);
            }
        }else{
            return $this->render('update',['model'=>$model]);
        }
    }
    //>>商品分类修改
    public function actionEdit($id){
        $row = GoodsCategory::find()->where(['id'=>$id])->one();
        $request = \Yii::$app->request;
        if($request->isPost){
            if($row->load($request->post()) && $row->validate()){
                $row->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods-category/index']);
            }
        }else{
            return $this->render('update',['model'=>$row]);
        }
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
}