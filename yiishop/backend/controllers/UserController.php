<?php

namespace backend\controllers;
use backend\models\User;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;

class UserController extends Controller{
    //>>管理员列表展示
    public function actionIndex(){
        $rows = User::find()->where(['>','status',0])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //>>管理员添加列表
    public function actionAdd(){
        $model = new User();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                if( $model->verify_password == $model->password_hash ){
                    //将密码设为加盐加密
                    $password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                    $model->password_hash = $password_hash;
                }
                $model->auth_key=uniqid();
                $model->created_at=time();
                $model->updated_at=time();
                $model->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //>>管理员修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $row = User::find()->where(['id'=>$id])->one();

        $row->password_hash = '';
        if($request->isPost){
            $row->load($request->post());

                if($row->isSelf()){
                //>>是本人修改密码
                if($row->validate()){
                    if( $row->verify_password == $row->password_hash ){
                        //将密码设为加盐加密
                        $password_hash = \Yii::$app->security->generatePasswordHash($row->password_hash);
                        $row->password_hash = $password_hash;
                    }
                    $row->updated_at = time();
                    $row->save(false);
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['user/index']);
                }
            }

        }
        return $this->render('edit',['row'=>$row]);
    }
    //>>管理员删除(禁用)
    public function actionDelete($id){
        $row = User::find()->where(['id'=>$id])->one();
        $row->status = 0;
        $res = $row->save(false);
        if($res){
            return Json::encode(true);
        }else{
            return Json::encode(false);
        }
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
                        'actions'=>['index'],
                        'roles'=>['?','@']
                    ],
                    [
                        'allow'=>true,
                        'actions'=>['add','edit','delete'],
                        'roles'=>['@'],
                    ]
                ],
            ],
        ];
    }
}