<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'captchaAction'=>'login/captcha',
    'template'=>'<div class="row">
                    <div class="col-xs-1">{image}</div>
                    <div class="col-xs-1">{input}</div>
</div>'
]);
echo \yii\helpers\Html::submitButton('登陆',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();