<table class="table table-bordered">
    <tr>
        <th>名称</th>
        <th>角色/权限</th>
        <th>描述</th>
        <th>创建时间</th>
        <th>修改时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($roles as $role):?>
        <tr id="<?=$role->name?>">
            <td><?=$role->name?></td>
            <td><?=$role->type==1?'角色':'权限'?></td>
            <td><?=$role->description?></td>
            <td><?=date('Y-m-d H:i:s',$role->createdAt)?></td>
            <td><?=date('Y-m-d H:i:s',$role->updatedAt)?></td>
            <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['rbac/role-edit','name'=>$role->name])?>">修改</a><a class="btn btn-warning" >删除</a></td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td style="text-align: center" colspan="6"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['rbac/role-add'])?>">添加</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['rbac/role-delete']);
$js_delete = <<<JS
        $('table').on('click','.btn-warning',function() {
                var name = $(this).closest('tr').attr('id');
                var tr = $(this).closest('tr');
              
                if(confirm('是否删除?')){
                    $.getJSON('{$url}?name='+name,function(data) {
                            if(data){
                                tr.remove();
                                alert('删除成功');
                            }else{
                                alert('删除失败');
                            }
                    })
                }
        })
JS;
$this->registerJs($js_delete);