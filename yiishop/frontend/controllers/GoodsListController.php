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

    public function actionDisplay($id = '')
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

    //>>单个商品信息展示
    public function actionGoodDisplay($id)
    {
        /*$redis = new \Redis();
        $redis->connect('127.0.0.1');*/
        $intro = GoodsIntro::find()->where(['goods_id' => $id])->one();
        $gallerys = GoodsGallery::find()->where(['goods_id' => $id])->all();
        $row = Goods::find()->where(['id' => $id])->one();
        $brand = Brand::find()->where(['id' => $row->brand_id])->asArray()->one();
        $row->brand_id = $brand['name'];
        $row->view_times = $row->view_times + 1;
        $row->save(false);
        return $this->render('goods', ['row' => $row, 'intro' => $intro, 'gallerys' => $gallerys]);
    }

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

}