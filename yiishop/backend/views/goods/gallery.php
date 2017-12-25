<?php
//========================webUpload==================================
/**
 * @var $this \yii\web\View
 */
//>>注册插件的css文件和js文件
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    //>>将js文件在JQuery文件之后加载
    'depends'=>\yii\web\YiiAsset::className()
]);
echo
<<<HTML
<!--dom结构部分-->
<div id="uploader-demo">
  
    <div id="filePicker">选择图片</div>
</div>
HTML;
$url = \yii\helpers\Url::to(['goods/gallery-uploader']);
$url_recieve = \yii\helpers\Url::to(['goods/gallery-add']);
$js =
    <<<JS
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    //swf文件路径
     swf: '@web/webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/gif,image/jpeg,image/png,image/jpg,image/bmp'
    }
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on( 'uploadSuccess', function( file,response ) {
    // $( '#'+file.id ).addClass('upload-state-done');
    //console.debug(response.url);
    
        var url = response.url;
     //$('#goods-logo').val(response.url); 
    $.post('{$url_recieve}',{'url':url,'id':$id},function(data) {
            if(data){
                console.debug(data);
                $('.table').append($("<tr id='"+data+"'>"+
                "<td><img src='"+url+"'><\/td>"+
                "<td><a class='btn btn-warning'>删除<\/a><\/td> <\/tr>')"));
            }else{
                alert("添加失败");
            }
    });
     
   
     //$('<tr><td><img src=''></td></tr>').appendTo('#menu'); 
});
JS;
$this->registerJs($js);
//===================================================================


?>
<table class="table">
    <tr id="menu">
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach($model as $row):?>
    <tr id="<?=$row->id?>">
        <td><img src="<?=$row->path?>" alt=""></td>
        <td><a class="btn btn-warning" href="">删除</a></td>
    </tr>
    <?php endforeach;?>
</table>
<?php
$url_delete = \yii\helpers\Url::to(['goods/gallery-delete']);
$js_delete = <<<JS
    $('table').on('click','.btn-warning',function() {
        var id = $(this).closest('tr').attr('id');
        var tr = $(this).closest('tr');
       
       if(confirm('是否删除')){
            $.getJSON('{$url_delete}?id='+id,function(data) {
                    if(data){
                        console.debug(data);
                        alert('删除成功');
                        tr.remove();
                    }else{
                        alert('删除失败');
                    }
            })
        }
    });
JS;
$js_delete = $this->registerJs($js_delete);
