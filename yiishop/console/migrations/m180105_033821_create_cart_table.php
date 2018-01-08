<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m180105_033821_create_cart_table extends Migration
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
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->integer()->notNull()->comment('商品id'),
            'amount'=>$this->integer()->notNull()->comment('商品数量'),
            'member_id'=>$this->integer()->notNull()->comment('用户id'),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}
