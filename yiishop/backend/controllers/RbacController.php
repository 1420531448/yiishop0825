<?php

namespace backend\controllers;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\Controller;

class RbacController extends Controller{
    //RBAC
    //>>所有需要操作rbac数据表的地方,都是调用authManager组件的方法来实现,不需要直接操作数据表
    //>>权限的增删改查
    //>>权限列表
    public function actionPermissionIndex(){
        $authManager = \Yii::$app->authManager;
        //>>获取所有被添加权限的路由
        $rows = $authManager->getPermissions();
//        var_dump($rows);die;
        return $this->render('permission-index',['rows'=>$rows]);
    }
    //权限添加
    public function actionPermissionAdd(){
        $authManager = \Yii::$app->authManager;
        $request = \Yii::$app->request;
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD_PERMISSION;
        //>>1.添加权限
        //>>1.1创建权限
        $permission = new Permission();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //验证权限是否重复
                    //>>1.2保存到数据表
                    $permission->name = $model->name;
                    $permission->description = $model->description;
                    $authManager->add($permission);
                    \Yii::$app->session->setFlash('success','路由设置成功');
                    $this->redirect(['rbac/permission-index']);


            }
        }
        return $this->render('permission-add',['model'=>$model]);
    }
    //>>权限修改
    public function actionPermissionEdit($name){
        $authManager = \Yii::$app->authManager;
        $request = \Yii::$app->request;
        $permission = $authManager->getPermission($name);
        $model = new PermissionForm();
        //>>场景
        $model->scenario = PermissionForm::SCENARIO_EDIT_PERMISSION;
        $model->name =  $permission->name;
        $model->description = $permission->description;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                    $permission->name = $model->name;
                    $permission->description = $model->description;
                    $authManager->update($name,$permission);
                    \Yii::$app->session->setFlash('success','路由设置成功');
                    $this->redirect(['rbac/permission-index']);

            }
        }
        return $this->render('permission-add',['model'=>$model]);
    }
    //>>权限删除
    public function actionPermissionDelete($name){
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        $authManage->remove($permission);
        \Yii::$app->session->setFlash('success','路由删除成功');
        $this->redirect(['rbac/permission-index']);
    }
    
    //>>角色的增删改查
    //>>角色列表
    public function actionRoleIndex(){
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();
        return $this->render('role-index',['roles'=>$roles]);
    }
    //>>角色添加
    public function actionRoleAdd(){
        $authManager = \Yii::$app->authManager;
        $model = new RoleForm();
        $requset = \Yii::$app->request;
        $model->scenario=RoleForm::SCENARIO_ADD_ROLE;
        $role = new Role();
        //>>获取所有权限
        $permissions = $authManager->getPermissions();
        $val=[];
        foreach($permissions as $permission){
            $val[$permission->name]=$permission->description;
        }
//        var_dump($val);die;
        if($requset->isPost){
            $model->load($requset->post());
//            var_dump($model->permission);die;
            if($model->validate()){
                $role->name=$model->name;
                $role->description = $model->description;
                //>>1.1先保存角色
                $authManager->add($role);
                $permissions = $model->permission;
                //>>遍历权限
                foreach ($permissions as $permission){
                    $permission1 = $authManager->getPermission($permission);
                    //>>1.2再保存权限
                    $authManager->addChild($role,$permission1);
                }

                \Yii::$app->session->setFlash('success','路由设置成功');
                $this->redirect(['rbac/role-index']);
            }

        }
        return $this->render('role-add',['model'=>$model,'permission'=>$val]);
    }
    //>>角色修改
    public function actionRoleEdit($name){
        $authManager = \Yii::$app->authManager;
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_EDIT_ROLE;
        //>>查询这个角色
        $role = $authManager->getRole($name);
        $model->name = $role->name;
        $model->description = $role->description;
        //>>查询这个角色权限
        $role_permission = $authManager->getChildren($role->name);
        //>>查询所有权限
        $ps = $authManager->getPermissions();
        $permissions = [];
        foreach ($ps as $p){
            $permissions[$p->name] =$p->description ;
        }
//       var_dump($permissions);die;
        $val=[];
        foreach($role_permission as $v2){
            $val[]=$v2->name;
        }
        $model->permission = $val;
//        var_dump($val);die;
        $request = \Yii::$app->request;
        //>>修改提交
        if($request->isPost){
            $model->load($request->post());

            if($model->validate()){

                //>>验证角色名是否重复
                $role->name = $model->name;
                $role->description =$model->description;
                $authManager->update($name,$role);
                //>>删除原权限
                foreach($role_permission as $role_p){
                    $authManager->removeChild($role,$role_p);
                }
                //>>添加新权限
                $new_P = $model->permission;
                foreach($new_P as $new){
                    $p = $authManager->getPermission($new);
                    $authManager->addChild($role,$p);
                }

                $authManager->update($role->name,$role);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['rbac/role-index']);
            }
        }
        return $this->render('role-add',['model'=>$model,'permission'=>$permissions]);

    }
    //>>角色删除
    public function actionRoleDelete($name){
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);
        $auth->remove($role);
        \Yii::$app->session->setFlash('success','角色删除成功');
        $this->redirect(['rbac/role-index']);
    }
    //>>权限和角色关联
    //>>
    public function actionTable(){
        return $this->render('test');
    }
}