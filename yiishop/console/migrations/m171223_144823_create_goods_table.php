<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m171223_144823_create_goods_table extends Migration
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
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->notNull()->comment('商品名称'),
            'sn'=>$this->string(20)->notNull()->comment('货号'),
            'logo'=>$this->string(255)->defaultValue('图片缺失')->comment('logo图片'),
            'goods_category_id'=>$this->integer()->notNull()->comment('商品分类id'),
            'brand_id'=>$this->integer()->notNull()->comment('品牌分类'),
            'market_price'=>$this->decimal(10,2)->notNull()->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->notNull()->comment('商品价格'),
            'stock'=>$this->integer()->notNull()->defaultValue(0)->comment('库存'),
            'is_on_sale'=>$this->smallInteger(1)->notNull()->defaultValue(0)->comment('是否在售(1,在售;0,下架)'),
            'status'=>$this->smallInteger(1)->notNull()->comment('状态(1,正常;0,回收站)'),
            'sort'=>$this->integer()->comment('排序'),
            'create_time'=>$this->integer()->comment('添加时间'),
            'view_times'=>$this->integer()->comment('浏览次数')
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
