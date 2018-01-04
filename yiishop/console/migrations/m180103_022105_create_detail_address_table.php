<?php

use yii\db\Migration;

/**
 * Handles the creation of table `detail_address`.
 */
class m180103_022105_create_detail_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('detail_address', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->notNull()->comment('用户id'),
            'detail_address'=>$this->string(180)->notNull()->comment('详细地址')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('detail_address');
    }
}
