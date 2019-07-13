<?php
namespace app\common\lib\tencent;
use Qcloud\Sms\SmsSingleSender;

class Sms
{
    public static function smsSend($phoneNum, $code){
        // 短信应用SDK AppID
        $appid = config('sms.tx_appid'); // 1400开头

        // 短信应用SDK AppKey
        $appkey = config('sms.tx_appkey');

        // 短信模板ID，需要在短信应用中申请
        $templateId = config('sms.tx_templateId');

        // 签名
        $smsSign = config('sms.tx_smsSign');

        // 指定模板ID单发短信
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $params = [$code,5];
            $result = $ssender->sendWithParam("86", $phoneNum, $templateId,
                $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result);
            echo $result;
        } catch(\Exception $e) {
            echo var_dump($e);
        }
        echo "\n";
    }
}