<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/DataTables-1.10.15/media/css/jquery.dataTables.css');

$this->registerJsFile('@web/DataTables-1.10.15/media/js/jquery.dataTables.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
$js=<<<JS
$(document).ready( function () {
$('#table_id_example').DataTable(
         {
    language: {
    "sProcessing": "处理中...",
    "sLengthMenu": "显示 _MENU_ 项结果",
    "sZeroRecords": "没有匹配结果",
    "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
    "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
    "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
    "sInfoPostFix": "",
    "sSearch": "搜索:",
    "sUrl": "",
    "sEmptyTable": "表中数据为空",
    "sLoadingRecords": "载入中...",
    "sInfoThousands": ",",
    "oPaginate": {
        "sFirst": "首页",
        "sPrevious": "上页",
        "sNext": "下页",
        "sLast": "末页"
    },
    "oAria": {
        "sSortAscending": ": 以升序排列此列",
        "sSortDescending": ": 以降序排列此列"
    }
}

    }
    );
} );
JS;
$this->registerJs($js);
?>
<a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['login/logout'])?>">注销</a>
<table id="table_id_example" class="table table-bordered">
    <thead>
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
    </thead>
    <tbody>
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
                <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['user/admin-edit','id'=>$row->id])?>">修改</a><a class="btn btn-default" href="">密码重置</a><a class="btn btn-warning">删除</a></td>

            </tr>
        <?php endforeach;?>
    </tbody>
    <tr>
        <td colspan="9" style="text-align: center"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['user/add'])?>">添加</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['user/delete']);
$js_delete = <<<JS
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

$url_reset=\yii\helpers\Url::to(['user/reset-pwd']);
$js_resetPwd=<<<JS
 $('table').on('click','.btn-default',function() { 
     //console.debug(1);
        var id =$(this).closest('tr').attr('id');
        var tr = $(this).closest('tr');
       
        if(confirm('是否重置')){
            //alert(id);
            $.getJSON('{$url_reset}?id='+id,function(data) {
                if(data){
                    alert('重置成功');
                }else{
                    alert('重置失败');
                }
        });
      }
        
    })
JS;
$this->registerJs($js_delete);
$this->registerJs($js_resetPwd);