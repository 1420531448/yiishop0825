<?php

namespace frontend\controllers;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\web\Controller;

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
    public function actionCheckUsername($username){
        if(Member::find()->where(['username'=>$username])->one()){
            echo 'false';
        }else{
            echo 'true';
        }
    }
    //>>登录界面
    public function actionLogin(){
        $request = \Yii::$app->request;
        $model = new LoginForm();

        if($request->isPost){
            $model->load($request->post(),'');
            if($model->validate()){
                if($model->login()){
                    //>>登录成功
                    $user = Member::find()->where(['username'=>$model->username])->one();
                    $user->last_login_time = time();
                    $user->last_login_ip=$_SERVER['REMOTE_ADDR'];
                    $user->save(false);
                    echo '登录成功';
                    sleep(1);
                    return $this->redirect(['site/index']);
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
}