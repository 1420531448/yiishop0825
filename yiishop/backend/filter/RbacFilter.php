<?php

namespace backend\filter;
use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{
    //>>在执行操作前验证
    public function beforeAction($action)
    {
        //>>没有权限   $action->uniqueId  权限名(路由)
        if(!\Yii::$app->user->can($action->uniqueId)){
            //>>如果用户没有登录,则引导用户登录
            if(\Yii::$app->user->isGuest){
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
            //>>没有权限
            throw new HttpException(403,'对不起,您没有权限');
        }
        return true;
    }
}