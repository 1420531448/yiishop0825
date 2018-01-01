<?php

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'parent_id')->hiddenInput();
//=======================zTree============================
/**
 * @var $this \yii\web\View
 */
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
$id = empty($model->id)?0:$model->id;
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
		            $('#goodscategory-parent_id').val(treeNode.id)
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
//========================================================
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();