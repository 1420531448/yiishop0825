<?php

namespace backend\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface {
    public $oldPassword;
    public $verify_password;
    public $code;
    public function rules()
    {
        return [
            [
                ['username','password_hash','verify_password','email','status','code'],'required'
            ],
            ['verify_password', 'compare', 'compareAttribute'=>'password_hash','message'=>'密码和确认密码必须相同'],
            ['email', 'email'],
            ['code','captcha','captchaAction'=>'login/captcha']
        ];
    }
    public function attributeLabels()
    {
        return [
          'username'=>'用户名',
          'password_hash'=>'密码',
          'verify_password'=>'确认密码',
          'code'=>'验证码',
          'email'=>'邮箱',
          'status'=>'状态',
        ];
    }
    //>>用户登陆验证方法
    public function login(){
        $user = User::find()->where(['username'=>$this->username])->one();
        if($user){
            //>>验证密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$user->password_hash)){
                //>>密码正确
                $user->last_login_time = time();
                $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
                $user->save(false);
                \Yii::$app->user->login($user);
                return true;
            }else{
                $this->addError('password_hash','密码错误');
            }

        }else{
            $this->addError('username','用户名不存在');
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
        // TODO: Implement getAuthKey() method.
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
        // TODO: Implement validateAuthKey() method.
    }
}