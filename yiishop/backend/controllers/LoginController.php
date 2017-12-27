<?php

namespace backend\controllers;
use backend\models\LoginForm;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\web\Controller;

class LoginController extends Controller{
    //>>登陆页面
    public function actionIndex(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());

            if($model->login()){
                \Yii::$app->session->setFlash('success','登陆成功');
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('index',['model'=>$model]);
    }
    //>>用户注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','注销成功');
        return $this->redirect(['login/index']);
    }
    //>>验证码
    public function actions()
    {
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'maxLength'=>4,
                'minLength'=>4,
                'padding'=>10,
            ]
        ];
    }
    //>>权限
    public function behaviors()
    {
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'actions'=>['index','captcha'],
                        'roles'=>['?','@'],
                    ],
                    [
                        'allow'=>true,
                        'actions'=>['logout'],
                        'roles'=>['@'],
                    ]
                ],

            ]
        ];
    }
}