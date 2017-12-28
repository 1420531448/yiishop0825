<?php

namespace backend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $code;
    public $username;
    public $password_hash;
    public $remember;
    public function rules()
    {
        return [
            [['username','password_hash'],'required'],
            ['code','captcha','captchaAction'=>'login/captcha'],
            ['remember','default','value'=>null]
        ];

    }
    public function attributeLabels()
    {
        return [
            'remember'=>'记住我',
            'username'=>'用户名',
            'password_hash'=>'密码',
            'code'=>'验证码'
        ];
    }
    //>>用户登陆验证方法
    public function login(){
        //
        $user = User::find()->where(['username'=>$this->username])->andWhere(['>','status',0])->one();
//        var_dump($user);die;
        if($user){
            //>>验证密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$user->password_hash)){
                //>>密码正确
                $user->last_login_time = time();
                $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
                $user->save(false);
                if($this->remember==1){
                    \Yii::$app->user->login($user,7*24*3600);
                }else{
                    \Yii::$app->user->login($user);
                }

                return true;
            }else{
                $this->addError('password_hash','密码错误');
            }

        }else{
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}