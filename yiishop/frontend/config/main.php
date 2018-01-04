<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'layout'=>'header',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'sms'=>[
            'class'=>\frontend\components\Sms::className(),
            'ak'=>'LTAI6gNdbX23wBLy',
            'sk'=>'RhScf4uraKyKk2sCDbXNsC1XBeRhUx',
            'sign'=>'代氏商城',
            'template'=>'SMS_120125274'
        ],
        'user' => [
            'identityClass' => 'frontend\models\member',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,//url美化
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
