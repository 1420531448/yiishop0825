<?php

namespace backend\controllers;
use backend\models\Brand;
use yii\web\Controller;
use yii\web\UploadedFile;

class BrandController extends Controller{
    //>>品牌列表展示
    public function actionIndex(){
        $rows = Brand::find()->where(['>=','status','0'])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //>>品牌添加
    public function actionAdd(){
       $model = new Brand();
       $request = \Yii::$app->request;
       if($request->isPost){
            $model->load($request->post());
            //>>将接受的图片信息转换为对象
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                //>>处理图片保存路径
                $file = '/upload/brand/'.uniqid().'.'.$model->imgFile->extension;
                if($model->imgFile->saveAs(\Yii::getAlias('@webroot').$file)){
                    $model->logo = $file;
                }
                $model->save();
                \Yii::$app->session->setFlash('success','添加品牌成功');
                return $this->redirect(['brand/index']);
            }
       }else{
           return $this->render('update',['model'=>$model]);
       }
    }
    //>>品牌信息修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $row = Brand::find()->where(['id'=>$id])->one();
        if($request->isPost){
            $row->load($request->post());
            $row->imgFile = UploadedFile::getInstance($row,'imgFile');
            if($row->validate()){
                $file='/upload/brand/'.uniqid().'.'.$row->imgFile->extension;
                if($row->imgFile->saveAs(\Yii::getAlias('@webroot').$file)){
                    $row->logo = $file;
                }
                $row->save();
                \Yii::$app->session->setFlash('success','修改品牌成功');
                return $this->redirect(['brand/index']);
            }
        }else{
            return $this->render('update',['model'=>$row]);
        }
    }
    //>>品牌删除(状态改为-1)
    public function actionDelete(){
        $id = $_GET['id'];
        $row = Brand::find()->where(['id'=>$id])->one();
        $row->status = -1;
//        var_dump($row);die;
        $bool = $row->save(false);
        echo $bool;
       /* \Yii::$app->session->setFlash('success','删除品牌成功');
        return $this->redirect(['brand/index']);*/
    }
}