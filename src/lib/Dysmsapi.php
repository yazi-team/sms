<?php
include __DIR__ . '/dysmsapi/Config.php';
/**
 * 阿里大鱼新短信接口
 */
class Dysmsapi {
    private $config = [];
    public $error = '';
    /**
     * 阿里大鱼短信接口
     * @param array $config <ul>
     * <li>string $key AppKey </li>
     * <li>string $secret AppSecret </li>
     * <li>string $sign 短信签名 </li>
     * </ul>
     */
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    /**
     * 发送普通短信
     * @param string|array $mobile 
     * @param array $params
     * @return boolean
     */
    public function sendSms($mobile, $params) {
        $sms_id = $params['sms_id'];
        unset($params['sms_id']);
        if (empty($sms_id)) {
            $this->error = "参数有误：sms_id不能为空";
            return false;
        }
        if (empty($mobile)) {
            $this->error = "参数有误：手机号码不能为空";
            return false;
        }
        if (empty($params)) {
            $this->error = "参数有误：短信模板参数不能为空";
            return false;
        }
        if (empty($this->config['key']) || empty($this->config['secret']) || empty($this->config['sign'])) {
            $this->error = "参数有误：阿里大鱼配置信息有误";
            return false;
        }
        if (is_array($mobile)) {
            $mobile = implode(",", $mobile);
        }
        foreach ($params as $key => $value) {
            $params[$key] = (string) $value;
        }
        
        //此处需要替换成自己的AK信息
        $accessKeyId = trim($this->config['key']);
        $accessKeySecret = trim($this->config['secret']);
        //短信API产品名
        $product = "Dysmsapi";
        //短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";

        //初始化访问的acsCleint
        $profile = \DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient = new \DefaultAcsClient($profile);
        
        $request = new \SendSmsRequest;
        //必填-短信接收号码
        $request->setPhoneNumbers($mobile);
        //必填-短信签名
        $request->setSignName(trim($this->config['sign']));
        //必填-短信模板Code
        $request->setTemplateCode(trim($sms_id));
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setTemplateParam(json_encode($params));

        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        
        $result = $this->objectToArray($acsResponse, true);
        if (isset($result['Code']) && strtoupper($result['Code']) == 'OK') {
            return true;
        } else {
            $error = "";
            foreach ($result as $key => $value) {
                $error .= "{$key}:{$value};\r\n";
            }
            $this->error = $error;
            return false;
        }
    }
    
    //stdClass TO array
    private function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (!is_array($d)) {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }

    // XML转换成数组
    private function simplexml_obj2array($obj) {
        if (is_object($obj)) {
            $result = array();
            foreach ((array) $obj as $key => $item) {
                $result[$key] = $this->simplexml_obj2array($item);
            }
            return $result;
        }
        return $obj;
    }

}
