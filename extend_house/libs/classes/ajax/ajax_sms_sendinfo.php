<?php
/**
 * ����: ������Ϣ���ֻ�����
 *
 * @example
 * @author    icms <icms@foxmail.com>
 * @copyright 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_sms_sendinfo extends _08_Models_Base
{
    public function __toString()
    {
        $mcharset = $this->_mcharset;
        header("content-type: text/javascript; charset=$mcharset");

        $sms = new cls_sms();

        $mod = cls_cache::exRead('smsregcodes');
        $act = empty($act) ? 'init' : $act;
        $nostr = empty($nostr) ? '' : $nostr; //��Ҫ:document.write
        //var_dump($mod);

        foreach($mod as $k => $v){

            // ����js������modsendsms_falg �ж��Ƿ���ʾ�����͵��ֻ�����ش��룻
            if ( $k == $this->_get['varname'] && $sms->smsEnable($k) ) {
                $modsendsms_falg = 'can_send';
            } elseif ($k == $this->_get['varname']) {
                $modsendsms_falg = 'set_close';
            }

        }

        if($this->_get['act'] == 'isopen'){
            return $modsendsms_falg;

        } elseif ($this->_get['act'] == 'send'){
            $mob = $this->_get['mob'];
            $msg = cls_string::iconv('utf-8',$mcharset,$this->_get['msg']);
            $msg = str_replace(array("\t","\r","\n","  "),array("","","","��"),$msg);
            //if(empty($sms_cfg_api) || ($sms_cfg_api == '(close)')){ // �ֻ����Žӿ�-�ر�
            //var_dump($mob);
            if($modsendsms_falg != 'can_send'){
                die('�ù����Ѿ��ر�!');
            }
            if(!empty($mob)){
                $msg = $sms->sendSMS($mob,$msg,'sadm');
                //die("var sInfo = 'OK!'");
                die("var sInfo='OK!'");
               // die('OK!');
            }else{
                die('��������!');
            }

        }

    }
}