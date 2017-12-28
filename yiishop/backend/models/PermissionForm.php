<?php

namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    //>>字段
    public $name;
    public $description;
    //>>场景
    const SCENARIO_ADD_PERMISSION ='add-permission';
    const SCENARIO_EDIT_PERMISSION ='edit-permission';

    //>>验证规则
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['name','addPermissionCheck','on'=>self::SCENARIO_ADD_PERMISSION],
            ['name','editPermissionCheck','on'=>self::SCENARIO_EDIT_PERMISSION],
        ];
    }
    //>>label
    public function attributeLabels()
    {
        return [
            'name'=>'路由',
            'description'=>'描述'
        ];
    }
    //>>验证添加场景权限是否重复
    public function addPermissionCheck(){
        $authManager = \Yii::$app->authManager;
        if($authManager->getPermission($this->name)){
            $this->addError('name','权限名已存在');
        }
    }
    //>>验证修改场景权限是否重复
    public function editPermissionCheck(){
        $request = \Yii::$app->request;
        $authManager = \Yii::$app->authManager;
        $name = $request->get('name');
        if($name != $this->name){
            if($authManager->getPermission($this->name)){
                $this->addError('name','权限已存在');
            }
        }
    }
}