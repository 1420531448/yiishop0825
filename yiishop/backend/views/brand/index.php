<table class="table table-bordered">
    <tr>
        <th>id</th>
        <th>品牌名</th>
        <th>品牌简介</th>
        <th>logo</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($rows as $row):?>
    <tr id="<?=$row->id?>">
        <td><?=$row->id?></td>
        <td><?=$row->name?></td>
        <td><?=$row->intro?></td>
        <td ><img width="50px" src="<?=$row->logo?>" alt="图片缺失"></td>
        <td><?=$row->sort?></td>
        <td><?=$row->status==1?'正常':''?><?=$row->status==0?'隐藏':''?><?=$row->status==-1?'删除':''?></td>
        <td><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$row->id])?>">修改</a><a class="btn btn-warning" >删除</a></td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="7" style="text-align: center"><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['brand/add'])?>">添加</a></td>
    </tr>
</table>
<?php
/**
 *
 *@var $this \yii\web\View
 */

$url = \yii\helpers\Url::to(['brand/delete']);
$js =
    <<<JS
    $('tr').on('click','.btn-warning',function() {
        var id = $('.btn-warning').closest('tr').attr('id');
        
        if( confirm('是否删除')){
             $(this).closest('tr').remove();
             $.getJSON('$url?id='+id,function(data) {
                if(data){
                    alert('删除成功');
                }else{
                    alert('删除失败');
                }
        })
      }   
    });
JS;

$this->registerJs($js);
