<?php

namespace core;

class AES
{
    /**
     * AES加密、解密类
     * 用法：
     * <pre>
     * // 实例化类
     * // 参数$_bit：格式，支持256、192、128，默认为128字节的
     * // 参数$_type：加密/解密方式，支持cfb、cbc、ecb、ofb默认为cbc
     * // 参数$_key：密钥，默认为 _Mikkle_AES_Key_ 密钥支持16、24、32字节
     * // 参数$_iv：偏移量 16字节
     * // 加密
     * $encodeString = $tcaes->encode($string);
     * // 解密
     * $decodeString = $tcaes->decode($encodeString);
     * </pre>
     */

    private $_bit        = '128';
    private $_type       = 'cbc';
    private $_key        = '#PanApi@AES_Key_'; // 密钥 必须16位 24位
    private $_use_base64 = true;
    private $_iv         = '1234567890123456'; //16位初始向量

    /**
     * @param string $_key 密钥
     * @param int $_bit 默认使用128字节
     * @param string $_type 加密解密方式
     * @param boolean $_use_base64 默认使用base64二次加密
     */
    public function __construct($_key = '', $_bit = 128, $_type = 'cbc', $_use_base64 = true, $_iv = '')
    {
        $this->_bit = $_bit ?: $this->_bit;
        // 加密方法
        $this->_type = "aes-{$this->_bit}-{$_type}";

        // 密钥
        if (!empty($_key)) {
            $this->_key = $_key;
        }
        if ($_iv) {
            $this->_iv = $_iv;
        }
        // 是否使用base64
        $this->_use_base64 = $_use_base64;
    }

    /**
     * aes加密
     * @param $decrypted
     * @return string
     */
    public function encrypt($decrypted)
    {
        if ('ecb' === $this->_type) {
            $encrypted = openssl_encrypt($decrypted, $this->_type, $this->_key, OPENSSL_RAW_DATA);
        } else {
            $encrypted = openssl_encrypt($decrypted, $this->_type, $this->_key, OPENSSL_RAW_DATA, $this->_iv);
        }
        if ($this->_use_base64) {
            $encrypted = base64_encode($encrypted);
        }
        return $encrypted;
    }

    /**
     * aes解密
     * @param $encrypted
     * @return mixed
     */
    public function decrypt($encrypted)
    {
        if ($this->_use_base64) {
            $encrypted = base64_decode($encrypted);
        }
        if ('ecb' === $this->_type) {
            $decrypted = openssl_decrypt($encrypted, $this->_type, $this->_key, OPENSSL_RAW_DATA);
        } else {
            $decrypted = openssl_decrypt($encrypted, $this->_type, $this->_key, OPENSSL_RAW_DATA, $this->_iv);
        }
        return $decrypted;
    }
}