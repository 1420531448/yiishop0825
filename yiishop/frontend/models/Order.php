<?php

namespace frontend\models;
use yii\db\ActiveRecord;

class Order extends ActiveRecord{
    public $logo;
    public static $delivery=[
        1=>['顺丰快递',25,'速度快,服务好,价格贵'],
        2=>['EMS',25,'速度慢,服务差,价格贵'],
        3=>['圆通快递',10,'速度一般,服务好,价格便宜']
    ];
    public static $payments=[
      1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
      2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
      3=>['邮局汇款','通过快钱平台收款 汇款后1-3个工作日到账']
    ];
    public function rules()
    {
        return [
            [
                ['member_id','name','province','city','area','address','tel','delivery_id','delivery_name','delivery_price','payment_id','payment_name','create_time'],'required'
            ],
            [
                ['total'],'default','value'=>null
            ]
        ];
    }
}