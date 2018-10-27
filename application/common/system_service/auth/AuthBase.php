<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/26
 */

namespace app\common\system_service\auth;


abstract class AuthBase
{
    const KEY_USER_ID = 'user_id';//用户id字段名称
    const KEY_USER_NAME = 'user_name'; //用户名字段名称
    protected $storeName = '';
    protected $type = '';
    protected $useInfo = [];

    function __construct($storeName, $type)
    {
        $this->storeName = $storeName;
        $this->type = $type;
    }

    /**
     * 设置token 移动端token包含有效期,而PC有效期在cookie过期时间
     * @param $userInfo 用户信息
     * @param int $lifetime 生命周期
     * @return string
     */
    abstract function createToken($userInfo, $lifetime = 0);

    /**
     * 读取已经登录的用户信息
     * @return array
     */
    public function get()
    {
        static $inited = false;
        if (!$inited) {
            $token = $this->getToken();
            $this->useInfo = self::decode($token);
            $inited = true;
        }
        return $this->useInfo;
    }

    /**
     * 删除登录cookie
     * @return mixed
     */
    public function delete(){
        return \cookie($this->storeName, null);
    }

    /**
     * 移动端是从get读取,PC则是cookie
     * @return string
     */
    abstract function getToken();

    /**
     * 解密,判断类型,类型不对返回不一致
     */
    protected function decode($token)
    {
        $key = config('app.secret_key');
        $token = self::authcode($token, $key, 'DECODE');
        $segs = explode("\t", $token);
        $rt = [];
        if (3 == count($segs) && $segs[2] == $this->type) {
            $rt[self::KEY_USER_ID] = $segs[0];
            $rt[self::KEY_USER_NAME] = $segs[1];
        }
        return $rt;
    }

    /**
     * 本函数来自成熟Discuz论坛加解密方案
     * @return string
     */
    private static function authcode($string, $key, $operation = 'DECODE', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
            substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? self::urlsafe_b64decode(substr($string, $ckey_length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0) .
            substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = [];
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', self::urlsafe_b64encode($result));
        }
    }

    /**
     * 加密
     * @param $userInfo
     * @param int $lifetime
     * @return string
     */
    protected function encode($userInfo, $lifetime = 0)
    {
        $user_id = $userInfo[self::KEY_USER_ID] ?? 0;
        $user_name = $userInfo[self::KEY_USER_NAME] ?? '';
        $text = $user_id . "\t" . $user_name . "\t" . $this->type;
        $key = config('app.secret_key');
        return self::authcode($text, $key, 'ENCODEE', $lifetime);
    }

    /**
     * URL base64解码
     */
    private static function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * URL base64编码
     */
    private static function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}