<?php

namespace backend\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface {
    public $oldPassword;
    public $verify_password;
    public $role;
    //添加用户
    const SCENARIO_ADD_USER = 'ADD_USER';
    //管理修改用户
    const SCENARIO_EDIT_USER = 'EDIT_USER';
    //用户个人修改
    const SCENARIO_EDIT_OWN = 'EDIT_USER_OWN';
    public function rules()
    {
        return [
            [
                ['username','password_hash','email','status'],'required'
            ],
            ['verify_password', 'compare', 'compareAttribute'=>'password_hash','message'=>'密码和确认密码必须相同'],
            ['email', 'email'],
            ['oldPassword','default','value'=>null],
            ['role','default','value'=>null,'on'=>[self::SCENARIO_EDIT_USER,self::SCENARIO_ADD_USER]],
            [['verify_password','password_hash'],'required','on'=>[self::SCENARIO_ADD_USER,self::SCENARIO_EDIT_OWN]]
        ];
    }
    public function attributeLabels()
    {
        return [
          'username'=>'用户名',
          'password_hash'=>'新密码',
          'oldPassword'=>'旧密码',
          'verify_password'=>'确认新密码',
          'email'=>'邮箱',
          'status'=>'状态',
            'role'=>'角色'
        ];
    }

    public function isSelf(){
        //查出当前用户信息
        $user = User::find()->where(['username'=>$this->username])->one();
//        var_dump($this->oldPassword);die;
        if($this->oldPassword){
            if(\Yii::$app->security->validatePassword($this->oldPassword,$user->password_hash)){
                return true;
            }else{
                $this->addError('oldPassword','原密码错误');
            }
        }else{
            $this->addError('oldPassword','原密码不为空');
        }
        return false;
    }
    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
       return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey()=== $authKey;
    }
    public function getMenus(){
        $menuItems = [];
        $menus = Menu::find()->where(['p_id'=>0])->all();

        //>>找顶级分类
        foreach($menus as $menu){
            $items = Menu::find()->where(['p_id'=>$menu->id])->all();
            $i=[];

                //>>找子类
                foreach ($items as $item){
                    if(\Yii::$app->user->can($item->route)){
                    // ['label'=>'用户列表','url'=>['user/index']],
                    $i[]=['label'=>$item->name,'url'=>[$item->route]];
                }

            }
            if($i){
                $menuItems[]=['label'=>$menu->name,'items'=>$i];
            }


        }
        return $menuItems;

      /*  $menuItems = [
            [
                'label'=>'用户管理',
                'items'=>[
                    ['label'=>'用户列表','url'=>['user/index']],
                    ['label'=>'修改密码','url'=>['user/edit-own','id'=>Yii::$app->user->id]]
                ]
            ],*/
    }
}