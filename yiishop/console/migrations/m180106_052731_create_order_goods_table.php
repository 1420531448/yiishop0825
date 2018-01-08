<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_goods`.
 */
class m180106_052731_create_order_goods_table extends Migration
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
        $this->createTable('order_goods', [
            'id' => $this->primaryKey(),
            'order_id'=>$this->integer()->notNull()->comment('订单id'),
            'goods_id'=>$this->integer()->notNull()->comment('商品id'),
            'goods_name'=>$this->string(255)->notNull()->comment('商品名称'),
            'logo'=>$this->string(255)->notNull()->comment('商品图片'),
            'price'=>$this->decimal(9,2)->notNull()->comment('价格'),
            'amount'=>$this->integer()->notNull()->comment('数量'),
            'total'=>$this->decimal(9,2)->notNull()->comment('小计')
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order_goods');
    }
}
