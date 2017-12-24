<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_category`.
 */
class m171222_013850_create_goods_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_category', [
            'id' => $this->primaryKey(),
            'tree'=>$this->integer()->unsigned()->notNull()->comment('树id'),
            'lft'=>$this->integer()->unsigned()->notNull()->comment('左值'),
            'rgt'=>$this->integer()->unsigned()->notNull()->comment('右值'),
            'depth'=>$this->integer()->comment('层级'),
            'name'=>$this->string(50)->notNull()->comment('商品分类名'),
            'parent_id'=>$this->integer()->comment('上级分类id'),
            'intro'=>$this->text()->comment('简介')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_category');
    }
}
