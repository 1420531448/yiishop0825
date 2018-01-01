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
<table id="table_id_example" class="table table-bordered">
    <thead>
        <tr>
            <th>id</th>
            <th>文章标题</th>
            <th>文章简介</th>
            <th>文章分类</th>
            <th>排序</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $row):?>
        <tr id="<?=$row->id?>">
            <td><?=$row->id?></td>
            <td><?=$row->name?></td>
            <td><?=$row->intro?></td>
            <td><?=$val[$row->article_category_id]?></td>
            <td><?=$row->sort?></td>
            <td><?=$row->status==0?'隐藏':''?><?=$row->status==1?'正常':''?></td>
            <td><?=date('Y-m-d H:i:s',$row->create_time)?></td>
            <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['article/edit','id'=>$row->id])?>">修改</a><a class="btn btn-warning">删除</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
    <tr>
        <td style="text-align: center" colspan="8"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['article/add'])?>">添加文章</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['article/delete']);
$js_delete = <<<JS
    $('tr').on('click','.btn-warning',function() {
        var id = $(this).closest('tr').attr('id');
        if(confirm('是否删除文章信息')){
            $(this).closest('tr').remove();
             $.getJSON('$url?id='+id,function(data) {
                if(data){
                    alert('删除成功');
                }else{
                    alert('删除失败');
                }
        })
        }
       
    })
JS;
$this->registerJs($js_delete);