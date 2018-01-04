<?php

namespace frontend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $remember;
    public function rules()
    {
        return [
            [
                ['username','password'],'required'
            ],
            ['remember','default','value'=>null]
        ];
    }
    public function login(){
        $res = Member::find()->where(['username'=>$this->username])->one();
        if($res){
            //>>有该用户
            if(\Yii::$app->security->validatePassword($this->password,$res->password_hash)){
                //>>密码正确
                if($this->remember==1){
                    \Yii::$app->user->login($res,24*3600);
                    //echo 1;die;
                }else{
                    \Yii::$app->user->login($res);
                }
                return true;
            }else{
                echo '密码错误';
            }
        }else{
            echo '没有该用户';
        }
        sleep(1);
        return false;
    }
}