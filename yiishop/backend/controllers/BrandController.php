<?php

namespace backend\controllers;
use backend\models\Brand;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
class BrandController extends Controller{
    //>>防
    public $enableCsrfValidation = false;
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
            //$model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                //>>处理图片保存路径
               /* $file = '/upload/brand/'.uniqid().'.'.$model->imgFile->extension;
                if($model->imgFile->saveAs(\Yii::getAlias('@webroot').$file)){
                    $model->logo = $file;
                }*/
                $model->save();
                \Yii::$app->session->setFlash('success','添加品牌成功');
                return $this->redirect(['brand/index']);
            }
       }else{
           return $this->render('update',['model'=>$model]);
       }
    }
    //>>图片接收
    public function actionUpload(){
        //>>实例化图片对象
        $img = UploadedFile::getInstanceByName('file');
        //>>处理图片名
        $file = '/upload/brand/'.uniqid().'.'.$img->extension;
        if($img->saveAs(\Yii::getAlias('@webroot').$file)){
            //>>上传到七牛云对象存储
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey ="3PGolvtj_RXpv8tan9q0FROW76r4tUhuxqCPhOFy";
            $secretKey = "II5UA2Ve0YB1Zpa9obF2FE9v1RR4K4Ju9IVGP_jj";
            $bucket = "yiishop";
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').$file;
            // 上传到七牛后保存的文件名
            $key = $file;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                return Json::encode(false);
            } else {
                return Json::encode(['url'=>'http://p1aufkkh7.bkt.clouddn.com/'.$file]);
            }

        }
    }
    //>>品牌信息修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $row = Brand::find()->where(['id'=>$id])->one();
        if($request->isPost){
            $row->load($request->post());
            //$row->imgFile = UploadedFile::getInstance($row,'imgFile');
            if($row->validate()){
                /*$file='/upload/brand/'.uniqid().'.'.$row->imgFile->extension;
                if($row->imgFile->saveAs(\Yii::getAlias('@webroot').$file)){
                    $row->logo = $file;
                }*/
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
    //>>七牛云对象存储测试
    public function actionQiniu(){


    }
}