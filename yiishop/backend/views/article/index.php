<table class="table table-bordered">
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
    <tr>
        <td style="text-align: center" colspan="8"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['article/add'])?>">添加文章</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['article/delete']);
$js = <<<JS
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

$this->registerJs($js);