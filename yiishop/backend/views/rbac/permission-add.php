<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'description')->textInput();
echo \yii\helpers\Html::submitButton('添加权限',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();