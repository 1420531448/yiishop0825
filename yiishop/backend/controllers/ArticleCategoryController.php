<?php

namespace backend\controllers;
use backend\filter\RbacFilter;
use backend\models\ArticleCategory;
use yii\web\Controller;

class ArticleCategoryController extends Controller{
    //>>文章分类列表
    public function actionIndex(){
        $rows = ArticleCategory::find()->where(['>=','status',0])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //>>文章添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new ArticleCategory();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加文章分类成功');
                return $this->redirect(['article_category/index']);
            }
        }else{
            return $this->render('update',['model'=>$model]);
        }
    }
    //>>文章分类修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model = ArticleCategory::find()->where(['id'=>$id])->one();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article-category/index']);
            }
        }else{
            return $this->render('update',['model'=>$model]);
        }
    }
    //>>文章分类删除(状态改为-1)
    public function actionDelete($id){
        $row = ArticleCategory::find()->where(['id'=>$id])->one();
        $row->status = -1;
        $bool = $row->save(false);
        echo $bool;
    }
    //>>权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}