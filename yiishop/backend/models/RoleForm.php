<?php

namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permission;
    //>>环境常量
    const SCENARIO_ADD_ROLE ='add_role' ;
    const SCENARIO_EDIT_ROLE ='edit_role' ;
    //>>验证规则
    public function rules()
    {
        return [
            [
                ['name','description','permission'],'required'
            ],
            [
                'name','addRoleCheck','on'=>self::SCENARIO_ADD_ROLE
            ],
            [
                'name','editRoleCheck','on'=>self::SCENARIO_EDIT_ROLE
            ]
        ];
    }
    //>>label
    public function attributeLabels()
    {
        return [
         'name'=>'角色名',
         'description'=>'角色描述',
         'permission'=>'权限'
        ];
    }
    //>>自定义规则
    public function addRoleCheck(){
        $authManager = \Yii::$app->authManager;
        if($authManager->getRole($this->name)){
            $this->addError('name','该角色名已存在');
        }
    }
    public function editRoleCheck(){
        $name = \Yii::$app->request->get('name');
        $authManager = \Yii::$app->authManager;
        if($this->name != $name){
            if($authManager->getRole($this->name)){
                $this->addError('name','该角色名已存在');
            }
        }
    }
}