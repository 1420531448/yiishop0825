<?php
$form = \yii\bootstrap\ActiveForm::begin();
//echo $form->field($row,'username')->textInput();
echo $form->field($row,'oldPassword')->passwordInput();
echo $form->field($row,'password_hash')->passwordInput();
echo $form->field($row,'verify_password')->passwordInput();
/*echo $form->field($row,'email')->textInput();
echo $form->field($row,'status',['inline'=>1])->radioList([0=>'禁用',1=>'可用']);*/
echo \yii\helpers\Html::submitButton('添加',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();