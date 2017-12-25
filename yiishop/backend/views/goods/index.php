<form action="" method="get">
    货号:<input type="text" name="sn">
    商品名:<input type="text" name="name">
    最低价格<input type="text" name="price_low">
    最高价格<input type="text" name="price_high">
    <input type="submit" class="btn btn-info" value="搜索">
</form>
<table class="table table-bordered">
    <tr>
        <th>id</th>
        <th>货号</th>
        <th>商品名</th>
        <th>logo</th>
        <th>商品分类</th>
        <th>商品品牌</th>
        <th>库存</th>
        <th>售价</th>
        <th>是否上架</th>
        <th>操作</th>
    </tr>
    <?php foreach ($rows as $row):?>
    <tr id="<?=$row->id?>">
        <td><?=$row->id?></td>
        <td><?=$row->sn?></td>
        <td><?=$row->name?></td>
        <td><img width="40px" src="<?=$row->logo?>"></td>
        <td><?=$arrCategory[$row->goods_category_id]?></td>
        <td><?=$arrBrand[$row->brand_id]?></td>
        <td><?=$row->stock?></td>
        <td><?=$row->shop_price?></td>
        <td><?=$row->is_on_sale==1?'上架':'下架'?></td>
        <td><a class="btn btn-info"  href="<?=\yii\helpers\Url::to(['goods/gallery-display','id'=>$row->id])?>"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span>相册</a><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$row->id])?>">修改</a><a class="btn btn-warning">删除</a><a class="btn btn-default" href="<?=\yii\helpers\Url::to(['goods/view','id'=>$row->id])?>">预览</a></td>

    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="10" style="text-align: center"><a class="btn btn-info" href="<?=\yii\helpers\Url::to(['goods/add'])?>">添加商品</a></td>
    </tr>
</table>
<?=\yii\widgets\LinkPager::widget([
        'pagination'=>$pageTool
])?>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['goods/delete']);
$js = <<<JS
    $('table').on('click','.btn-warning',function() {
        var id = $(this).closest('tr').attr('id');
        var tr = $(this).closest('tr');
        if(confirm("确定删除?")){
             $.getJSON('{$url}?id='+id,function(data) {
                    if(data){
                        alert('删除成功');
                        tr.remove();
                    }else{
                        alert("删除失败");
                    }
             })
        }
            
    })
JS;
$this->registerJs($js);
