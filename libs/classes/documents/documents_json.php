<?php
/**
 * JSON�ĵ�������
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_Documents_JSON
{
    /**
     * ������ת��JSON��ʽ����֧��������ʾ��
     * 
     * @param  mixed $datas         Ҫת��������
     * @param  bool  $restoreCoding ��ԭ���룬���ΪTRUEʱ�����ݻ�ԭ��ԭ�����ݱ���
     * @param  bool  $conversion    �Ƿ�ת�����ģ�TRUEΪת����FALSEΪ��ת����ע����JQuery��getJSON����ʱ�벻Ҫת��
     * @return mixed                ����ת�����JSON��ʽ����
     * 
     * @since  nv50
     */
    public static function encode( $datas, $restoreCoding = false, $conversion = true )
    {
        $mcharset = cls_env::getBaseIncConfigs('mcharset');
        $datas = cls_string::iconv($mcharset, 'UTF-8', $datas);
        
        if (version_compare(PHP_VERSION, '5.4.0') >= 0)
        {
            $datas = json_encode($datas, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            if ($conversion)
            {
                $datas = cls_url::encode( $datas );
            }
            
            $datas = json_encode($datas);
            if ($conversion)
            {
                $datas = cls_url::decode( $datas );
            }
        }
        
        if ($restoreCoding)
        {
            $datas = cls_string::iconv('UTF-8', $mcharset, $datas);
        }
        
        return $datas;
    }
    
    /**
     * ���Ѿ���{@see self::encode}ת������JSON���ݽ��л�ԭ
     * 
     * @param  string $json_datas Ҫ��ԭ��JSON����
     * @param  bool   $assoc      ���ò���Ϊ TRUE ʱ�������� array ���� object
     * @return mixed              ���ػ�ԭ���JSON����
     * @since  nv50
     */
    public static function decode( $json_datas, $assoc = true )
    {
        $mcharset = cls_env::getBaseIncConfigs('mcharset');
        $json_datas = cls_string::iconv($mcharset, 'UTF-8', $json_datas);        
        $json_datas = json_decode($json_datas, $assoc);        
        $json_datas = cls_string::iconv('UTF-8', $mcharset, $json_datas);
        
        return $json_datas;
    }
}