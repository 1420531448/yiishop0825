<?php

namespace frontend\controllers;
use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\DetailAddress;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use frontend\models\SignatureHelper;
use yii\captcha\Captcha;
use yii\captcha\CaptchaAction;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Cookie;

class MemberController extends Controller{
    public $enableCsrfValidation = false;
    //>>注册
    public function actionRegist(){
        $request = \Yii::$app->request;
        $model = new Member();
        if($request->isPost){
            $model->load($request->post(),"");
            if($model->validate()){

                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->auth_key = uniqid();
                $model->created_at=time();
                $model->status = 1;
                $model->save(false);
                echo "注册成功";
                sleep(1);
                return $this->redirect(['member/login']);
            }
        }
        return $this->render('regist');
    }
    //>>验证用户名是否重复
    /**
     * @param $username 输入的用户名
     */
    public function actionCheckUsername($username){
        if(Member::find()->where(['username'=>$username])->one()){
            echo 'false';
        }else{
            echo 'true';
        }
    }
    //>>验证手机验证是否正确
    /**
     * @param $captcha 手机验证码
     * @param $tel 手机号
     * @return string
     */
    public function actionCheckTelCode($captcha,$tel){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $code = $redis->get('code_'.$tel);
        if($code == false){
            return 'false';
        }else{
           if($code == $captcha){
               return 'true';
           }else{
               return 'false';
           }
        }
    }
    //>>登录界面
    public function actionLogin()
    {
        $request = \Yii::$app->request;
        $model = new LoginForm();

        if ($request->isPost) {
            $model->load($request->post(), '');

            if ($model->validate()) {
                if ($model->login()) {
                    //>>登录成功
                    $user = Member::find()->where(['username' => $model->username])->one();
                    $user->last_login_time = time();
                    $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
                    $user->save(false);
                    //>>已登陆,将cookie信息存入数据表
                    $cookies = \Yii::$app->request->cookies;
                    //>>如果cookie里有数据
                    //var_dump($cookies->has('cart'));die;
                    if ($cookies->has('cart')) {
                        //echo 1;die;
                        $cart_info = unserialize($cookies->getValue('cart'));
                        $good_ids = array_keys($cart_info);
                        //>>获得cookie每个商品id
                        foreach ($good_ids as $good_id) {//1
                            $count=Cart::find()->where(['goods_id'=>$good_id])->andWhere(['member_id'=>\Yii::$app->user->identity->id])->one();
                            //>>数据表中该用户购物车有这个商品id就执行数量添加操作
                            if($count){
                                //>>有这个商品
                                $count->amount+=$cart_info[$good_id];
                                $count->save(false);
                            }else{
                                $cart = new Cart();
                                $cart->member_id = \Yii::$app->user->identity->id;
                                $cart->goods_id = $good_id;
                                $cart->amount = $cart_info[$good_id];
                                $cart->save(false);
                            }
                        }
                        $cookies = \Yii::$app->response->cookies;
                        $cookies->remove('cart');
                    }

                    echo '登录成功';
                    sleep(1);
                    return $this->redirect('http://www.yiishop.com');
                }
            }

        }
        return $this->render('login');
    }
    //>>注销
    public function actionLogout(){

        \Yii::$app->user->logout();
        return $this->redirect(['member/login']);
    }
    //>>用户订单地址显示
    /**
     * @param $id   用户id
     * @return string|\yii\web\Response
     */
    public function actionAddressDisplay($id){
        //>>实例化地址对象和详情地址对象
        $addresses = Address::find()->where(['member_id'=>$id])->all();
        $request = \Yii::$app->request;
        foreach ($addresses as &$address){
            $detail = DetailAddress::find()->where(['address_id'=>$address->id])->asArray()->one();
            $address['detail_address']=$detail['detail_address'];
        }
        //var_dump($address['detail_address']);die;
        if($request->isPost){
           $address = new Address();
           $detail = new DetailAddress();
           $address->load($request->post(),'');
           if(!isset($request->post()['status'])){
               $address->status = 0;
           }

           if($address->validate()){
               $address->member_id = $id;
                if($address->status ==1){
                    $addr = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->andWhere(['status'=>1])->one();
                    $addr->status = 0;
                   // var_dump($addr->status);die;
                   // echo $address->status;die;
                    $addr->save(false);
                }

               $address->save(false);
               $detail->address_id = $address->id;
               $detail->detail_address=$address->detail_address;
               $detail->save(false);
               return $this->redirect(['member/address-display','id'=>\Yii::$app->user->identity->id]);
           }
        }

        return $this->render('address-add',['addresses'=>$addresses]);
    }
    /**用户收货地址修改
     * @param $id   地址id
     * @return string|\yii\web\Response
     */
    public function actionAddressEdit($id){
        $addresses = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
        foreach ($addresses as &$address){
            $detail1 = DetailAddress::find()->where(['address_id'=>$address->id])->asArray()->one();
            $address['detail_address']=$detail1['detail_address'];
        }
        $request = \Yii::$app->request;
        $addr = Address::find()->where(['id'=>$id])->one();
        $detail = DetailAddress::find()->where(['address_id'=>$addr->id])->one();
        if($request->isPost){

            $addr->load($request->post(),'');

            if(!isset($request->post()['status'])){
                $addr->status=0;
            }
           // echo $addr->status;die;
            if($addr->validate()){
                $detail->detail_address = $addr->detail_address;

                if($addr->status ==1){
                   $address = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->andWhere(['status'=>1])->one();
                   $address->status =0;
                   $address->save(false);
                }
                $detail->save(false);

                $addr->save(false);
                return $this->redirect(['member/address-display','id'=>\Yii::$app->user->identity->id]);
            }
        }
        //var_dump($addr->status);die;
        return $this->render('address-edit',['addr'=>$addr,'detail'=>$detail,'addresses'=>$addresses]);
    }
    /**用户收货地址删除
     * @param $id 地址id
     * @return string
     */
    public function actionAddressDelete($id){
        $address = Address::find()->where(['id'=>$id])->one();
        $detail = DetailAddress::find()->where(['address_id'=>$address->id])->one();
        $res1 = $address->delete();
        $res2 = $detail->delete();
        if($res1 && $res2){
            return json_encode(true);
        }else{
            return json_encode(false);
        }

    }
    /**设置默认地址
     * @param $id 地址id
     * @return \yii\web\Response
     */
    public function actionAddrDefault($id){
       $addresses = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
       //>>状态全部清0
       foreach($addresses as $address){
           $address->status = 0;
           $address->save(false);
       }
       $addr = Address::find()->where(['id'=>$id])->one();
        $addr->status = 1;
        $addr->save(false);
        return $this->redirect(['member/address-display','id'=>\Yii::$app->user->identity->id]);
    }
    /**用户手机验证码发送
     * @param $tel 手机号
     * @return string
     */
    public function actionSendSms($tel)
    {
        //>>电话号码验证
        //>>使用正则
        if (preg_match("/^1[34578]{1}\d{9}$/", $tel)) {
            $code = rand(10000, 99999);
            $res = \Yii::$app->sms->send($tel, ['code' => $code]);
            if ($res->Code == 'OK') {
                //>>发送成功
                //>>将验证码存入redis以便验证
                $redis = new \Redis();
                $redis->connect('127.0.0.1');
                $redis->set('code_' . $tel, $code, 300);
                return 'true';
            } else {
                //>>发送失败
                return '手机号码格式错误或异常';
            }


            /*$params = array ();

            // *** 需用户填写部分 ***

            // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
            $accessKeyId = "LTAI6gNdbX23wBLy";
            $accessKeySecret = "RhScf4uraKyKk2sCDbXNsC1XBeRhUx";

            // fixme 必填: 短信接收号码
            $params["PhoneNumbers"] = "{$tel}";

            // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
            $params["SignName"] = "代氏商城";

            // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
            $params["TemplateCode"] = "SMS_120125274";

            // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
            $params['TemplateParam'] = Array (
                "code" => rand(1000,9999),
                //"product" => "阿里通信"
            );

            // fixme 可选: 设置发送短信流水号
            //$params['OutId'] = "12345";

            // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
            //$params['SmsUpExtendCode'] = "1234567";


            // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
            if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
                $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
            }

            // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
            $helper = new SignatureHelper();

            // 此处可能会抛出异常，注意catch
            $content = $helper->request(
                $accessKeyId,
                $accessKeySecret,
                "dysmsapi.aliyuncs.com",
                array_merge($params, array(
                    "RegionId" => "cn-hangzhou",
                    "Action" => "SendSms",
                    "Version" => "2017-05-25",
                ))
            );

            var_dump($content);*/
        }
    }
    //>>添加商品到购物车
    public function actionAddToCart($goods_id,$amount){

        if(\Yii::$app->user->isGuest){
        //>>未登陆 将购物车信息保存至cookie
            $cookies = \Yii::$app->request->cookies;
            //>>先读cookie.看商品是否存在
            if($cookies->has('cart')){
                //var_dump($cookies->getValue('cart'));die;
                $cart = unserialize($cookies->getValue('cart'));
            }else{
                $cart=[];
            }
                //>>判断商品存不存在,不存在就新增,存在就累加
            if(array_key_exists($goods_id,$cart)){
                $cart[$goods_id]+=$amount;
            }else{
                $cart[$goods_id]=$amount;
            }
            //>>写cookie
                $cookies = \Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name = 'cart';
                $cookie->value =serialize($cart);
                //var_dump($cookie);die;
                $cookies->add($cookie);
        }else{
            //>>如果用户已经将这个商品选入购物车
                $count=Cart::find()->where(['goods_id'=>$goods_id])->andWhere(['member_id'=>\Yii::$app->user->identity->id])->one();
                if($count){
                    $count->amount += $amount;
                    $count->save(false);
                }else{
                    //>>登陆后直接将购物车信息存入数据表
                    $cart = new Cart();
                    $cart->member_id=\Yii::$app->user->identity->id;
                    $cart->goods_id = $goods_id;
                    $cart->amount = $amount;
                    $cart->save(false);
                }

            }
        return $this->redirect(['member/cart']);
    }
    //>>在结算页面显示购物车的商品
    public function actionCart(){
        if(\Yii::$app->user->isGuest){
            //>>未登录->购物车信息从cookie获取
            $cookies = \Yii::$app->request->cookies;
            if($cookies->has('cart')){
               // var_dump($cookies->getValue('cart'));die;
                $count = unserialize($cookies->getValue('cart'));
                $ids =array_keys($count);
                $goods = Goods::find()->where(['in','id',$ids])->all();
            }else{
                return $this->render('cart-error');
            }


        }else{
            //>>登陆后根据登陆用户id从数据库查表获取购物车信息
            $carts = Cart::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
            $good_ids = [];
            //>>获取所有商品id
            $count=[];
            foreach($carts as $cart){
                $good_ids[]=$cart->goods_id;
                $count[$cart->goods_id]=$cart->amount;
            }
            //>>获取所有商品信息
            $goods = Goods::find()->where(['in','id',$good_ids])->all();
        }
        return $this->render('cart',['goods'=>$goods,'count'=>$count]);

    }
    /**购物车商品删除
     * @param $id 商品id
     */
    public function actionCartDelete($id){
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            if($cookies->has('cart')){
                //>>获取cookie   cart的值
               $arr = $cookies->getValue('cart');
               $arr = unserialize($arr);
                foreach($arr as $g_id=>$count){
                    if($g_id==$id){
                       unset($arr[$id]);
                    }
                }
                $cookies = \Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name='cart';
                $cookie->value =serialize($arr);
                $cookies->add($cookie);
                return json_encode(true);
            }else{
                return json_encode(false);
            }
        }else{
            $good = Cart::find()->where(['goods_id'=>$id])->andWhere(['member_id'=>\Yii::$app->user->identity->id])->one();
            $res = $good->delete();
        }
        if($res){
            return json_encode(true);
        }else{
            return json_encode(false);
        }
    }
    //>>购物车商品数量修改
    public function actionCartEdit(){
        $g_id = \Yii::$app->request->post('g_id');
        $count=\Yii::$app->request->post('count');
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            if($cookies->has('cart')){
                   $arr = unserialize($cookies->getValue('cart'));
                   foreach($arr as $id=>$c){
                       if($id == $g_id){
                           $arr[$g_id]=$count;
                       }
                   }
                   $cookies = \Yii::$app->response->cookies;
                   $cookie = new Cookie();
                   $cookie->name = 'cart';
                   $cookie->value = serialize($arr);
                   $cookies->add($cookie);
                   return json_encode(true);
            }
        }else{
            $cart = Cart::find()->where(['goods_id'=>$g_id])->andWhere(['member_id'=>\Yii::$app->user->identity->id])->one();
            $cart->amount = $count;
            $cart->save(false);
            return json_encode(true);
        }
    }
    //>>用户订单
    public function actionOrder($id){
        if(Cart::find()->where(['member_id'=>$id])->one()){
            //>>用户地址信息
            $addresses =  Address::find()->where(['member_id'=>$id])->all();

            //>>用户地址详细信息
            foreach ($addresses as &$address){
                $detail_addr = DetailAddress::find()->where(['address_id'=>$address->id])->one();
                $address->detail_address = $detail_addr->detail_address;
            }
            //>>用户购物车信息
            $carts = Cart::find()->where(['member_id'=>$id])->all();
            $total = 0;
            $total_price=0;
            foreach($carts as &$cart){
                //>>购物车每个商品信息
                $row = Goods::find()->where(['id'=>$cart->goods_id])->one();
                $cart->goods=$row;
                $total+=$cart->amount;
                $total_price+=$cart->amount*$row->shop_price;
            }
            //var_dump($total,$total_price);die;
            return $this->render('order',['addresses'=>$addresses,'carts'=>$carts,'total'=>$total,'total_price'=>$total_price]);
        }else{
            return $this->render('cart-error');
        }

    }
    //>>提交订单
    public function actionSubmitOrder(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }else{
            $request =\Yii::$app->request;
            if(Cart::find()->where(['member_id'=>\Yii::$app->user->id])->one()){
                //>>获取地址id 和 派送方式id ,付款方式
                if($request->isPost){
                    $addr_id = $request->post('address_id');
                    $deli_id = $request->post('delivery_id');
                    $pay_id = $request->post('payment_id');
                    $model = new Order();
                    $model->member_id=\Yii::$app->user->id;
                    //>>地址对象
                    $addr = Address::find()->where(['id'=>$addr_id])->one();
                    $model->name = $addr->name;
                    $model->province = $addr->cmbProvince;
                    $model->city = $addr->cmbCity;
                    $model->area = $addr->cmbArea;
                    $model->tel = $addr->tel;
                    //>>详细地址
                    $detail = DetailAddress::find()->where(['address_id'=>$addr_id])->one();
                    $model->address = $detail->detail_address;
                    //>>派送方式
                    $model->delivery_id = $deli_id;
                    $model->delivery_name =Order::$delivery[$deli_id][0];
                    $model->delivery_price =Order::$delivery[$deli_id][1];
                    //>>付款方式
                    $model->payment_id = $pay_id;
                    $model->payment_name = Order::$payments[$pay_id][0];
                    $model->create_time = time();
                    //>>开启事务
                    $transaction = \Yii::$app->db->beginTransaction();
                    try{
                        $model->save(false);
                        //>>订单商品
                        $carts = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
                        $total_order=0;
                        foreach($carts as $cart){
                            $model2 = new OrderGoods();
                            $model2->order_id = $model->id;

                            $model2->goods_id = $cart->goods_id;
                            $good = Goods::find()->where(['id'=>$cart->goods_id])->one();
                            if($good->stock>=$cart->amount){
                                //>>扣减库存
                                $good->stock-=$cart->amount;
                                $good->save(false);
                                //>>订单商品信息
                                $model2->goods_name = $good->name;
                                $model2->logo = $good->logo;
                                $model2->price = $good->shop_price;
                                $model2->amount = $cart->amount;
                                $model2->total = $good->shop_price*$cart->amount;
                                $model2->save(false);
                                $total_order+=$model2->total;
                            }else{
                                //>>抛出异常
                                throw new Exception('商品库存不足,求修改购物车商品数量');
                            }
                        }
                        //>>订单总计
                        $model->total = $total_order+$model->delivery_price;
                        $model->save(false);
                        //>>清除购物车
                        $rows = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
                        foreach ($rows as $row){
                            $row->delete();
                        }
                        //>>提交事务
                        $transaction->commit();
                    }catch (Exception $e){
                        $transaction->rollBack();
                        echo '商品库存不足';
                        sleep(1);
                        return $this->redirect(['member/order','id'=>\Yii::$app->user->id]);
                    }

                    return $this->redirect(['member/switch']);

                }
            }else{
                return $this->render('cart-error');
            }

        }
    }
    //>>提交订单后中专页面展示
    public function actionSwitch(){
        return $this->render('order-switch');
    }
    //>>已下订单详情
    public function actionDisplayOrder(){
        //>>已登录用户所有订单
        $orders = Order::find()->where(['member_id'=>\Yii::$app->user->id])->all();
        foreach($orders as $order){
            //>>查出每条订单的所有商品信息
            $goods = OrderGoods::find()->where(['order_id'=>$order->id])->all();
            foreach ($goods as $good){
                $order->logo=$good->logo;
            }
        }
        return $this->render('order-display',['orders'=>$orders]);
    }
    public function actions()
    {
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'maxLength'=>4,
                'minLength'=>4,
            ],
        ];
    }
}