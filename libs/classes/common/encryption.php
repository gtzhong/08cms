<?php
/**
 * �ӽ��ܴ�����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class _08_Encryption
{
    /**
     * Ҫ���ܵ��ַ���
     */
    protected $_string = '';

    protected $_key = '';

    protected $_key_length = 0;
    
    const SPLIT_STRING = '1|';

    /**
     * ���췽��
     *
     * @param string $string ��Ҫ���д�����ַ���
     */
    public function __construct( $string )
    {
        $ckpre = (string) cls_env::getBaseIncConfigs('ckpre');
        $this->_string = (string) $string;
        $lic_str = (string) cls_env::GetLicense();
        
        $this->_key = $lic_str . $ckpre;
        $this->_key_length = strlen($this->_key);
    }

    /**
     * ��ȡ������Կ������Ϣ
     *
     * @return array ���ع�����Ϣ
     * @since  1.0
     */
    public function getKeyInfo()
    {
        return array('key' => $this->_key, 'length' => $this->_key_length);
    }

    /**
     * ��������
     *
     * @param  string Ҫ���ܵ��ַ���
     * @return string �����Ѿ����ܵ��ַ���
     *
     * @since  1.0
     */
    public static function password( $password )
    {
        return md5( md5( $password ) );
    }

    /**
     * �����ַ������÷������ܺ��ǿ����
     * libmcrypt > 2.4.x
     *
     * @return string �����Ѿ����ܵ��ַ���
     *
     * @since  1.0
     */
    public function enCryption()
    {
        // �Զ�������㷨
        $param = base64_encode(rawurlencode($this->_string));
        $keyLength = mt_rand(1, $this->_key_length - 1);
        $param = ($keyLength . self::SPLIT_STRING . substr($this->_key, 0, $keyLength) . $param);
        $key2Length = mt_rand(1, $this->_key_length - 1);
		$param .= (substr($this->_key, 0, $key2Length) . self::SPLIT_STRING . $key2Length);
        $param = authcode($param, 'ENCODE');

        // ���ü����㷨
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$plaintext_utf8 = utf8_encode($param);
		$param = @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_key, $plaintext_utf8, MCRYPT_MODE_CBC, $iv);
		$param = base64_encode($iv . $param);
        $param = str_replace(array('/', '+'), array('|', '-'), $param);
        
        return $param;
    }

    /**
     * ���Ѿ�ʹ����{@link self::enCryption} �������ܵ��ַ�������
     * libmcrypt > 2.4.x
     *
     * @return string �����Ѿ����ܵ��ַ���
     *
     * @since  1.0
     */
    public function deCryption()
    {
        $this->_string = str_replace(array('|', '-'), array('/', '+'), $this->_string);
        $param = base64_decode($this->_string);

        // ���ý����㷨
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv_dec = substr($param, 0, $iv_size);
		$ciphertext_dec = substr($param, $iv_size);
		$param = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
        $param = trim($param);
        
        // �Զ�������㷨
        $param = authcode($param, 'DECODE');
        $leftLength = substr($param, 0, strpos($param, self::SPLIT_STRING));
        $param = substr($param, strlen($leftLength) + strlen(self::SPLIT_STRING) + (int)$leftLength);
        $rightLength = substr(strstr($param, self::SPLIT_STRING), strlen(self::SPLIT_STRING));
        $param = substr($param, 0, strlen($param) - (int)$rightLength - strlen($rightLength) - strlen(self::SPLIT_STRING));
       
        return rawurldecode(base64_decode($param));
    }

    public static function getInstance( $string )
    {
        return new self($string);
    }
}