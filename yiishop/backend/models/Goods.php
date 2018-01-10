<?php

namespace backend\models;
use yii\db\ActiveRecord;

class Goods extends ActiveRecord{
    public function rules()
    {
        return [
            [
                ['name','brand_id','goods_category_id','logo','market_price','shop_price','stock','is_on_sale','status'],'required'
            ],
            [
                ['market_price','shop_price'],'number'
            ],
            [
                ['name','sn'],'string','max'=>20
            ],
            ['stock','integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'商品名',
            'brand_id'=>'品牌名',
            'goods_category_id'=>'商品分类',
            'logo'=>'LOGO',
            'market_price'=>'市场价格',
            'shop_price'=>'售价',
            'stock'=>'库存',
            'is_on_sale'=>'是否上架',
            'status'=>'状态',
        ];
    }



}