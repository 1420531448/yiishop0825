<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180103_020849_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('收货人'),
            'cmbProvince'=>$this->string(15)->notNull()->comment('省'),
            'cmbCity'=>$this->string(30)->notNull()->comment('市'),
            'cmbArea'=>$this->string(30)->notNull()->comment('区,县'),
            'tel'=>$this->integer()->notNull()->comment('电话号码'),
            'status'=>$this->smallInteger(1)->notNull()->comment('状态:1为默认地址,0为其他地址'),
            'member_id'=>$this->integer()->notNull()->comment('用户Id')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
