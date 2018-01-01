<table class="table table-bordered">
    <tr>
        <th>id</th>
        <th>分类名</th>
        <th>深度</th>
        <th>简介</th>
        <th>父id</th>
        <th>操作</th>
    </tr>
    <?php foreach($rows as $row):?>
    <tr id="<?=$row->id?>">
        <td><?=$row->id?></td>
        <td><?=$row->name?></td>
        <td><?=str_repeat('....',$row->depth).$row->name?></td>
        <td><?=$row->intro?></td>
        <td><?=$row->parent_id?></td>
        <td><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['goods-category/edit','id'=>$row->id])?>">修改</a><a class="btn btn-warning">删除</a></td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="6" style="text-align: center"><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['goods-category/add'])?>">添加商品分类</a></td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url=\yii\helpers\Url::to(['goods-category/delete']);
$js = <<<JS
    $('td').on('click','.btn-warning',function() {
        var id = $(this).closest('tr').attr('id');
        var td = $(this);
        if(confirm('是否删除商品分类')){
            var res = $.getJSON('{$url}?id='+id,function(data) {
                      if(data){
                          td.closest('tr').remove();
                          alert('删除成功');
                      }else{
                          alert("删除失败,该分类下有子分类")
                      }
            })
           
        }
    })
JS;
$this->registerJs($js);