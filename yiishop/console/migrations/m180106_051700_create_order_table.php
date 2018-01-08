<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m180106_051700_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->notNull()->comment('用户id'),
            'name'=>$this->string(50)->notNull()->comment('收货人'),
            'province'=>$this->string(20)->notNull()->comment('省'),
            'city'=>$this->string(20)->notNull()->comment('市'),
            'area'=>$this->string(20)->notNull()->comment('县'),
            'address'=>$this->string(255)->notNull()->comment('详细地址'),
            'tel'=>$this->char(11)->notNull()->comment('电话号码'),
            'delivery_id'=>$this->integer()->notNull()->comment('配送方式id'),
            'delivery_name'=>$this->string()->notNull()->comment('配送方式名称'),
            'delivery_price'=>$this->decimal(9,2)->notNull()->comment('配送方式价格'),
            'payment_id'=>$this->integer()->comment('支付方式id'),
            'payment_name'=>$this->string()->comment('支付方式名称'),
            'total'=>$this->decimal(9,2)->notNull()->comment('订单总金额'),
            'status'=>$this->smallInteger(2)->defaultValue(1)->comment('订单状态'),
            'trade_no'=>$this->string()->comment('第三方交易支付号'),
            'create_time'=>$this->integer()->notNull()->comment('创建时间')
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}
