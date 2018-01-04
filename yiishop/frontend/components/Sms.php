<?php

namespace frontend\components;
use frontend\models\SignatureHelper;
use yii\base\Component;

class Sms extends Component{
    public $ak;
    public $sk;
    public $sign;
    public $template;

    public function send($tel,$param){
        $params = [];
        $params['PhoneNumbers']=$tel;
        $params['TemplateParam'] =$param;
        $params['SignName'] = $this->sign;
        $params['TemplateCode'] = $this->template;
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->ak,
            $this->sk,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );
        return $content;
    }
}