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
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->type==1?'角色':'权限'?></td>
            <td><?=$role->description?></td>
            <td><?=date('Y-m-d H:i:s',$role->createdAt)?></td>
            <td><?=date('Y-m-d H:i:s',$role->updatedAt)?></td>
            <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['rbac/role-edit','name'=>$role->name])?>">修改</a><a class="btn btn-warning" href="<?=\yii\helpers\Url::to(['rbac/role-delete','name'=>$role->name])?>">删除</a></td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td style="text-align: center" colspan="6"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['rbac/role-add'])?>">添加</a></td>
    </tr>
</table>