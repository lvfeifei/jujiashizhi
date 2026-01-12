<?php

/**
 * Created by PhpStorm.
 * User:
 * Date:
 */

namespace Think;

use think\PKCS7Encoder;

class Xiaocx
{
    private $appid;
    private $sessionKey;
    public static $block_size = 16;
    public $key;
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;

    /**
     * 构造函数
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     * @param $appid string 小程序的appid
     */
    public function setConfig($appid, $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }

    function Prpcrypt($k)
    {
        $this->key = $k;
    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($encryptedData, $iv, &$data)
    {

        if (strlen($this->sessionKey) != 24) {
            return Xiaocx::$IllegalAesKey;
        }
        $aesKey = base64_decode($this->sessionKey);


        if (strlen($iv) != 24) {
            return Xiaocx::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj=json_decode($result);
        if ($dataObj == NULL) {
            return Xiaocx::$IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $this->appid) {
            return Xiaocx::$IllegalBuffer;
        }
        $data = $dataObj;
        return Xiaocx::$OK;
    }


    /**
     * 对需要加密的明文进行填充补位
     * @param $text 需要进行填充补位操作的明文
     * @return 补齐明文字符串
     */
    function encode($text)
    {
        $block_size = Xiaocx::$block_size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = Xiaocx::$block_size - ($text_length % Xiaocx::$block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = Xiaocx::block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param decrypted 解密后的明文
     * @return 删除填充补位后的明文
     */
    function decode($text)
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}
