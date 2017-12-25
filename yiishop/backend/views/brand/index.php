<table class="table table-bordered">
    <tr>
        <td>id</td>
        <td>品牌名</td>
        <td>logo</td>
        <td>简介</td>
        <td>排序</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach($rows as $row):?>
    <tr id="<?=$row->id?>">
        <td><?=$row->id?></td>
        <td><?=$row->name?></td>
        <td><img src="<?=$row->logo?>" alt=""></td>
        <td><?=$row->intro?></td>
        <td><?=$row->sort?></td>
        <td><?=$row->status==0?'隐藏':''?><?=$row->status==1?'正常':''?></td>
        <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$row->id])?>">修改</a><a class="btn btn-warning" href="">删除</a></td>

    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="7"></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['brand/delete']);
$js = <<<JS
        $('table').on('click','.btn-warning',function() {
            var id = $(this).closest('tr').attr('id');
            var tr = $(this).closest('tr');
            if(confirm("是否删除")){
                 $.getJSON('{$url}?id='+id,function(data) {
                    if(data){
                      tr.remove();
                      alert('删除成功');
                    }else{
                        alert("删除失败");
                    }
            });
            }
           
        }) 
JS;
$this->registerJs($js);