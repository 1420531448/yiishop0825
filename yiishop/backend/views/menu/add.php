<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'p_id')->dropDownList($menu);
echo $form->field($model,'route')->dropDownList($p);
echo $form->field($model,'sort')->textInput();
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();