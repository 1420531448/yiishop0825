<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member`.
 */
class m180102_093534_create_member_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('member', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(50)->notNull()->comment('用户名'),
            'auth_key'=>$this->string(32),
            'password_hash'=>$this->string(100)->notNull()->comment('密码(密文)'),
            'email'=>$this->string(100)->notNull()->comment('邮箱'),
            'tel'=>$this->char(11)->notNull()->comment('手机号码'),
            'last_login_time'=>$this->integer()->comment('最后登录时间'),
            'last_login_ip'=>$this->string(50)->comment('最后登录ip'),
            'status'=>$this->integer(1)->notNull()->comment('状态(1正常,0删除)'),
            'created_at'=>$this->integer()->notNull()->comment('添加时间'),
            'updated_at'=>$this->integer()->comment('修改时间')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('member');
    }
}
