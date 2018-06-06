短信接口
===================
支持阿里云，Twilio

### 阿里云 示例
```
use yaplus\sms\Sms;

$config = [
    "key" => "AccessKeyId",
    "secret" => "AccessKeySecret",
    "sign" => "短信签名"
];
$sms_api = new Sms($config);
 
//单号码
$mobile = "1310000000";
//批量发送
$mobile = ["13123456789", "13012345678"];
           
$params = [
    "sms_id" => "短信模板id",
    "code" =>  "验证码",
    //多个模板参数
];

$res = $sms_api->sendSms($mobile, $params);
if (!$res) {
    echo $sms_api->getError();
}
```


### Twilio 示例

#### 安装 twilio sdk
```
composer require twilio/sdk
```

```
$config = [
    "account_sid" => "account_sid",
    "auth_token" => "auth_token",
    "from_phone" => "from_phone"
];
$sms_api = new Sms($config, "Twilio");
 
//只支持单号码
$mobile = "+861310000000";
           
$params = "短信内容";

$res = $sms_api->sendSms($mobile, $params);
if (!$res) {
    echo $sms_api->getError();
}
```

