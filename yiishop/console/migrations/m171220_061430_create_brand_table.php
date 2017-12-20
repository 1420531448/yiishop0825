<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m171220_061430_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('商品品牌名'),
            'intro'=>$this->text()->notNull()->comment('品牌简介'),
            'logo'=>$this->string(255)->notNull()->defaultValue("图片缺失")->comment("图片"),
            'sort'=>$this->integer(11)->comment("排序"),
            'status'=>$this->smallInteger(2)->notNull()->comment("状态(-1删除,0隐藏,1正常)")
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
