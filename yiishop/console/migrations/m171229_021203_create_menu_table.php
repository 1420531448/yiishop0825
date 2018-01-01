<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171229_021203_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(30)->notNull()->comment('菜单名称'),
            'route'=>$this->string(30)->notNull()->comment('路由'),
            'p_id'=>$this->integer()->notNull()->comment('上级菜单id'),
            'sort'=>$this->integer()->comment('排序')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
