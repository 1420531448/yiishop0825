<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'verify_password')->passwordInput();
echo $form->field($model,'role',['inline'=>1])->checkboxList($r);
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList([0=>'禁用',1=>'可用']);
echo \yii\helpers\Html::submitButton('添加',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();