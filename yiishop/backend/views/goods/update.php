<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form ->field($model,'brand_id')->dropDownList($arrBrand);
echo $form ->field($model,'goods_category_id')->hiddenInput();
//=========================zTree==================================
//加载ztree的的JS和CSS文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
echo <<<html
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
html;
$id = empty($model->goods_category_id)?0:$model->goods_category_id;
$nodes = \backend\models\GoodsCategory::getNodes();
$js = <<<JS
 var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event,treeId,treeNode) {
		            //获取节点id  赋值给输入框
		            $('#goods-goods_category_id').val(treeNode.id)
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes ={$nodes};
      
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //>>展开所有节点
            zTreeObj.expandAll(true);
            //>>节点选中回显
                //>>获取该节点
             
             var node = zTreeObj.getNodeByParam('id',$id,null);
             zTreeObj.selectNode(node);
JS;
$this->registerJs($js);
//==================================================================
echo $form->field($model,'logo')->hiddenInput();
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
    <!--用来存放item-->
    <div id="fileList" class="uploader-list">
    <img id="img" src="$model->logo" alt="">
</div>
    <div id="filePicker">选择图片</div>
</div>
HTML;
$url = \yii\helpers\Url::to(['goods/uploader']);

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
       
      
      $('#img').attr('src',response.url);
    $('#goods-logo').val(response.url); 
    
     
});
JS;
$this->registerJs($js);
//===================================================================
echo $form->field($model,'market_price')->textInput();
echo $form->field($model,'shop_price')->textInput();
echo $form->field($model,'stock')->textInput();
echo $form->field($model,'is_on_sale',['inline'=>1])->radioList([0=>'下架',1=>'上架']);
echo $form->field($model,'status',['inline'=>1])->radioList([0=>'回收站',1=>'正常']);
echo $form->field($content,'content')->widget(\kucha\ueditor\UEditor::className());
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();
