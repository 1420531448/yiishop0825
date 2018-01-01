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
<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>名称</th>
        <th>角色/权限</th>
        <th>描述</th>
        <th>创建时间</th>
        <th>修改时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $row):?>
        <tr id="<?=$row->name?>">
            <td><?=$row->name?></td>
            <td><?=$row->type==1?'角色':'权限'?></td>
            <td><?=$row->description?></td>
            <td><?=date('Y-m-d H:i:s',$row->createdAt)?></td>
            <td><?=date('Y-m-d H:i:s',$row->updatedAt)?></td>
            <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['rbac/permission-edit','name'=>$row->name])?>">修改</a><a class="btn btn-warning" >删除</a></td>
        </tr>
    <?php endforeach;?>

    </tbody>
    <tr>
        <td style="text-align: center" colspan="6"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['rbac/permission-add'])?>">添加</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 *
 */
$url = \yii\helpers\Url::to(['rbac/permission-delete']);
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

