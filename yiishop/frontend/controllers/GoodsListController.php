<?php

namespace frontend\controllers;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use yii\web\Controller;

class GoodsListController extends Controller{
    public function actionIndex($id){

       $rows =  Goods::find()->where(['goods_category_id'=>$id])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    public function actionGoodDisplay($id){
        $intro = GoodsIntro::find()->where(['goods_id'=>$id])->one();
        $gallerys = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        $row = Goods::find()->where(['id'=>$id])->one();
        $brand = Brand::find()->where(['id'=>$row->brand_id])->one();
        $row->brand_id = $brand->name;
        return $this->render('goods',['row'=>$row,'intro'=>$intro,'gallerys'=>$gallerys]);
    }
}