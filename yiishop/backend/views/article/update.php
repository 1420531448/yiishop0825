<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'article_category_id')->dropDownList($id);
echo $form->field($model,'status',['inline'=>1])->radioList([0=>'隐藏',1=>'正常']);
echo $form->field($detail,'content')->widget(\common\widgets\ueditor\Ueditor::className());
echo "<button class='btn btn-primary' type='submit'>添加</button>";

\yii\bootstrap\ActiveForm::end();