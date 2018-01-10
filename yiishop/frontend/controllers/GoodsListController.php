<?php

namespace frontend\controllers;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use yii\data\Pagination;
use yii\web\Controller;

class GoodsListController extends Controller
{
    //>>根据点击商品分类展示该分类和子孙分类下的商品信息
    public function actionDisplay($id)
    {
        //>>查该分类
        $cate = GoodsCategory::find()->where(['id' => $id])->one();

        if ($cate->depth == 2) {
            //>>三级分类
            $v = [$cate->id];
        } else {
            //>>1级分类
            //>>为2级分类
            $v = [];
            //>>所有子孙分类
            $categorys = $cate->children()->andWhere(['depth' => 2])->all();
            foreach ($categorys as $category) {
                $v[] = $category->id;
            }
        }
        $count = Goods::find()->where(['in', 'goods_category_id', $v])->count();
        $pagination = new Pagination([
            'pageSize' => 4,
            'totalCount' => $count,
        ]);
        $rows = Goods::find()->where(['in', 'goods_category_id', $v])->limit($pagination->limit)->offset($pagination->offset)->all();

        return $this->render('display-index', ['rows' => $rows, 'pagination' => $pagination]);


    }

    //>>商品搜索
    public function actionSearch($search)
    {
            $count = Goods::find()->where(['like','name',$search])->count();
            $pagination = new Pagination([
                'pageSize' => 4,
                'totalCount' => $count,
            ]);
            $rows = Goods::find()->where(['like', 'name', $search])->limit($pagination->limit)->offset($pagination->offset)->all();
            return $this->render('display-index', ['rows' => $rows, 'pagination' => $pagination]);
    }
    //>>商品点击数(高并发下)
    public function actionHit($id){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        if($redis->get('times_'.$id)==false){
            $redis->set('times_'.$id,1);
            $res = $redis->get('times_'.$id);
        }else{
            $res = $redis->incr('times_'.$id);
        }


        return json_encode($res);
    }

}