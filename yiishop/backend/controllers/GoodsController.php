<?php

namespace backend\controllers;
use backend\filter\RbacFilter;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use kucha\ueditor\UEditorAction;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;
use yii\gii\console\GenerateAction;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;

class GoodsController extends Controller{
    public $enableCsrfValidation=false;
    //>>商品列表展示
    public function actionIndex(){
        $request = \Yii::$app->request;
//        var_dump($request->get());die;
            $query = Goods::find();
            $sn = empty($request->get('sn'))?'':$request->get('sn');
            $name = empty($request->get('name'))?'':$request->get('name');
            $price_low = empty($request->get('price_low'))?'':$request->get('price_low');
            $price_high = empty($request->get('price_high'))?'':$request->get('price_high');
            if($sn){
                $query->Where(['like','sn',$sn]);
            }
            if($name){
                $query->andWhere(['like','name',$name]);
            }
            if($price_low){
                $query->andWhere(['>','shop_price',$price_low]);
            }
            if($price_high){
                $query->andWhere(['<','shop_price',$price_high]);
            }
             $total= $query->andWhere(['>=','status',0])->orderBy('sn asc')->count();
            $pageTool = new Pagination([
                'pageSize'=>4,
                'totalCount'=>$total
            ]);
           $rows = $query->andWhere(['>=','status',0])->orderBy('sn asc')->limit($pageTool->limit)->offset($pageTool->offset)->all();


        $brands = Brand::find()->all();
        $goodCategorys = GoodsCategory::find()->all();
        $arrBrand = [];
        $arrCategory = [];
        foreach($brands as $brand){
            $arrBrand[$brand->id]=$brand->name;
        }
        foreach($goodCategorys as $goodCategory){
            $arrCategory[$goodCategory->id]=$goodCategory->name;
        }
        return $this->render('index',['rows'=>$rows,'arrBrand'=>$arrBrand,'arrCategory'=>$arrCategory,'pageTool'=>$pageTool]);
    }
    //>>商品添加
    public function actionAdd(){
        //>>商品模型
        $model = new Goods();
        //>>商品品牌模型
        $brands = Brand::find()->all();
        $arrBrand = [];
        foreach($brands as $brand){
            $arrBrand[$brand->id] = $brand->name;
        }
        //>>商品详情模型
        $content = new GoodsIntro();
        $request = \Yii::$app->request;
        //>>表单提交
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate()){
                $time = date('Ymd',time());
                $one = GoodsDayCount::find()->where(['day'=>$time])->one();
                if($one){
                      $one->count = $one->count +1;
                      $one->save(false);
                      $model->sn = date('Ymd',time())*100000+$one->count;
                }else{
                    //>>商品每日添加数量模型
                    $count = new GoodsDayCount();
                    $count->day = date('Ymd',time());
                    $count->count = 1;
                    $count->save(false);
                    $model->sn = date('Ymd',time())*100000+1;
                }
                $model->create_time = time();
                $model->save(false);
                $content->goods_id = $model->id;
                $content->save(false);
                \Yii::$app->session->setFlash('success','添加商品信息成功');
                return $this->redirect(['goods/index']);
            }
        }else{
            return $this->render('update',['model'=>$model,'arrBrand'=>$arrBrand,'content'=>$content]);
        }
    }
    //>>商品信息图片接收
    public function actionUploader(){
        //>>实例化图片对象
        $img = UploadedFile::getInstanceByName('file');
        //>>处理图片名
        $file = '/upload/goods/'.uniqid().'.'.$img->extension;
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
    //>>商品信息修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $good = Goods::find()->where(['id'=>$id])->one();
        //>>商品品牌模型
        $brands = Brand::find()->all();
        $arrBrand = [];
        foreach($brands as $brand){
            $arrBrand[$brand->id] = $brand->name;
        }
        //>>商品详情
        $content = GoodsIntro::find()->where(['goods_id'=>$id])->one();
        if($request->isPost){
            $good->load($request->post());
            $content->load($request->post());
            if($good->validate()){
                $good->save();
                $content->save(false);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }
        }else{
            return $this->render('update',['model'=>$good,'arrBrand'=>$arrBrand,'content'=>$content]);
        }

    }
    //>>商品信息删除
    public function actionDelete($id){
        $row = Goods::find()->where(['id'=>$id])->one();
        $row->status = 0;
        $res = $row->save(false);
        if($res){
            return Json::encode(true);
        }

    }
    //>>相册展示
    public function actionGalleryDisplay($id){
        $model = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('gallery',['model'=>$model,'id'=>$id]);
    }
    //>>相册添加 Ajax
    public function actionGalleryAdd(){
        $request = \Yii::$app->request;
        $id = $request->post('id');
        if($request->isPost){
            $url = $_POST['url'];
                $model = new GoodsGallery();
                $model->goods_id = $id;
                $model->path = $url;
                $res = $model->save(false);
                return Json::encode($model->id);
        }
    }
    //>>相册删除 ajax
    public function actionGalleryDelete($id){
        $row = GoodsGallery::find()->where(['id'=>$id])->one();
        $res = $row->delete();
        return Json::encode($res);
    }
    //>>相册图片接收
    public function actionGalleryUploader(){
        //>>实例化图片对象
        $img = UploadedFile::getInstanceByName('file');
        //>>处理图片名
        $file = '/upload/gallery/'.uniqid().'.'.$img->extension;
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
    //>>内容展示
    public function actionView($id){
            $pictures = GoodsGallery::find()->where(['goods_id'=>$id])->all();
           $content =  GoodsIntro::find()->where(['goods_id'=>$id])->one();
           return $this->render('view',['content'=>$content,'pictures'=>$pictures]);
    }
    //>>插件
    public function actions()
    {
        return [
            //>>富文本编辑器
            'upload'=>[
                'class'=>UEditorAction::className(),
                /*      'config' => [
                          "imageUrlPrefix"  => "http://www.baidu.com",//图片访问路径前缀
                          "imagePathFormat" => "/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                          "imageRoot" => \Yii::getAlias("@webroot"),
                  ],*/
            ]
        ];
    }
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}