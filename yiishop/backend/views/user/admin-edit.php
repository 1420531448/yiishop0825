<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($row,'username')->textInput();
//echo $form->field($row,'oldPassword')->passwordInput();
echo $form->field($row,'role',['inline'=>1])->checkboxList($r);
echo $form->field($row,'email')->textInput();
echo $form->field($row,'status',['inline'=>1])->radioList([0=>'禁用',1=>'可用']);
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();