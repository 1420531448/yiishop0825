<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'imgFile')->fileInput();
echo $form->field($model,'status',['inline'=>1])->radioList([0=>'隐藏',1=>'正常']);
echo '<button type="submit" class="btn btn-primary">添加</button>';
\yii\bootstrap\ActiveForm::end();