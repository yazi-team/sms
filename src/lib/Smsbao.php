<?php

/**
 * 短信宝
 */
class Smsbao {
    private $config = [];
    public $error = '';
    const API_URL = "http://api.smsbao.com/sms";
    const GJ_API_URL = "http://api.smsbao.com/wsms";

    /**
     * 接口网短信接口
     * @param array $config <ul>
     * <li>string $account account </li>
     * <li>string $password password </li>
     * </ul>
     */
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    /**
     * 发送国际短信
     * @param string $mobile 
     * @param array $code
     * @return boolean
     */
    public function sendGjSms($mobile, $code) {
        if (empty($mobile)) {
            $this->error = "参数有误：手机号码不能为空";
            return false;
        }
        if (trim($code) == "") {
            $this->error = "参数有误：短信验证码不能为空";
            return false;
        }
        if (empty($this->config['account']) || empty($this->config['password'])) {
            $this->error = "参数有误：配置信息有误";
            return false;
        }
        if (is_array($mobile)) {
            $this->error = "接口网不支持批量发送短信";
            return false;
        }
        $post_data = "u={$this->config['account']}&p=". md5($this->config['password']) ."&m={$mobile}&c=".rawurlencode("【爱乐淘】您的验证码是：{$code}。如需帮助请联系客服。");
        try {
            $message = $this->post($post_data, self::GJ_API_URL);
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
        if ($message === '0') {
            return true;
        } else {
            $this->error = $this->get_message($message);
            return false;
        }
    }
    
    /**
     * 发送普通短信
     * @param string $mobile 
     * @param array $code
     * @return boolean
     */
    public function sendSms($mobile, $code) {
        if (empty($mobile)) {
            $this->error = "参数有误：手机号码不能为空";
            return false;
        }
        if (trim($code) == "") {
            $this->error = "参数有误：短信验证码不能为空";
            return false;
        }
        if (empty($this->config['account']) || empty($this->config['password'])) {
            $this->error = "参数有误：配置信息有误";
            return false;
        }
        if (is_array($mobile)) {
            $this->error = "接口网不支持批量发送短信";
            return false;
        }
        $post_data = "u={$this->config['account']}&p=". md5($this->config['password']) ."&m={$mobile}&c=".rawurlencode("【爱乐淘】您的验证码是：{$code}。如需帮助请联系客服。");
        try {
            $message = $this->post($post_data, self::API_URL);
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
        if ($message === '0') {
            return true;
        } else {
            $this->error = $this->get_message($message);
            return false;
        }
    }
    
    private function post($params, $url) {
        $code = file_get_contents($url . "?{$params}");
        return $code;
    }

    
    private function get_message( $code ) {
        $msg = [
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        ];
        
        return isset($msg[$code]) ? $msg[$code] : "短信发送失败";
    }
}