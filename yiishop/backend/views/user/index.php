<a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['login/logout'])?>">注销</a>
<table class="table table-bordered">
    <tr>
        <td>id</td>
        <td>用户名</td>
        <td>email</td>
        <td>状态</td>
        <td>创建时间</td>
        <td>最后修改时间</td>
        <td>最后登录时间</td>
        <td>最后登录ip</td>
        <td>操作</td>
    </tr>
    <?php foreach ($rows as $row):?>
    <tr id="<?=$row->id?>">
        <td><?=$row->id?></td>
        <td><?=$row->username?></td>
        <td><?=$row->email?></td>
        <td><?=$row->status==1?'可用':'禁用'?></td>
        <td><?=date('Y-m-d H:i:s',$row->created_at)?></td>
        <td><?=date('Y-m-d H:i:s',$row->updated_at)?></td>
        <td><?=date('Y-m-d H:i:s',$row->last_login_time)?></td>
        <td><?=$row->last_login_ip?></td>
        <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['user/edit','id'=>$row->id])?>">修改</a><a class="btn btn-warning">删除</a></td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="9" style="text-align: center"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['user/add'])?>">添加</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['user/delete']);
$js = <<<JS
    $('table').on('click','.btn-warning',function() {
        
        var id =$(this).closest('tr').attr('id');
        var tr = $(this).closest('tr');
        if(confirm('是否删除')){
            $.getJSON('{$url}?id='+id,function(data) {
                if(data){
                    alert('删除成功');
                    tr.remove();
                }else{
                    alert('删除失败');
                }
        });
      }
        
    })
JS;
$this->registerJs($js);