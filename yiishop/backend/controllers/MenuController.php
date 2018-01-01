<?php

namespace backend\controllers;
use backend\filter\RbacFilter;
use backend\models\Menu;
use yii\helpers\Json;
use yii\web\Controller;

class MenuController extends Controller{

    //>>菜单列表
    public function actionIndex(){
//        $count = Menu::find()->where(['=','p_id',0])->count();
//        var_dump($count);die;
        $rows = Menu::find()->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //>>菜单添加
    public function actionAdd(){
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        $p = [];
        foreach($permissions as $permission){
            $p[$permission->name]=$permission->description;
        }
        $model = new Menu();
        $menus = Menu::find()->where(['=','p_id',0])->asArray()->all();
        array_unshift($menus,['id'=>0,'name'=>'顶级分类','p_id'=>0]);
        foreach($menus as $menu){
            $m[$menu['id']] = $menu['name'];
        }
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['menu/index']);
            }
        }
       return  $this->render('add',['model'=>$model,'p'=>$p,'menu'=>$m]);
    }
    //>>菜单修改
    public function actionEdit($id){
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        $p = [];
        foreach($permissions as $permission){
            $p[$permission->name]=$permission->description;
        }
        $model = Menu::find()->where(['id'=>$id])->one();
        $menus = Menu::find()->where(['=','p_id',0])->asArray()->all();
        array_unshift($menus,['id'=>0,'name'=>'顶级分类','p_id'=>0]);
        foreach($menus as $menu){
            $m[$menu['id']] = $menu['name'];
        }
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['menu/index']);
            }
        }
        return  $this->render('add',['model'=>$model,'p'=>$p,'menu'=>$m]);
    }
    //>>菜单删除
    public function actionDelete($id){
        $row = Menu::find()->where(['id'=>$id])->one();
        $res = $row->delete();
        echo json_encode($res);
        \Yii::$app->session->setFlash('success','删除成功');


    }
    //>>权限管理
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}