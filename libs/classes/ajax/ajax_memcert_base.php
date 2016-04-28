<?php
/**
 * ��������֤����
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/memcert/datatype/xml/option/msgcode/&callback=$_iNp$JgYF8
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Memcert_Base extends _08_Models_Base
{
    public function __toString()
    {       
    	$timestamp = TIMESTAMP;
		$mctid = empty($this->_get['mctid']) ? '0' : $this->_get['mctid'];
		$sms = new cls_sms();
		$msg = $sms->smsTpl($mctid,1);
		
    	$info = array();
    	$mobile = empty($this->_get['mobile']) ? "" : $this->_get['mobile'];
		
		if($sms->isClosed()){ 
			$info = array(
				'time' => -1,
				'text' => 'ϵͳû�����ö��Žӿ�ƽ̨!'
			);
			return $info;
		}
		if($this->_get['option'] == 'msgcode'){
    		if(strlen($mobile)<10){
    			$info = array(
    				'time' => 0,
    				'text' => '�ֻ������ʽ����'
    			);
    		}elseif(preg_match("/^\d{3,4}[-]?\d{7,8}$/", $mobile)){
    			$msgcode = cls_string::Random(6, 1);
    			/*if(empty($sms_cfg_api) || ($sms_cfg_api == '(close)')){
    				$info = array(
    					'time' => -1,
    					'text' => 'ϵͳû�����ö��Žӿ�ƽ̨!'
    				);
    			}else{*/
    				@list($inittime, $initcode) = maddslashes(explode("\t", @authcode($m_cookie['08cms_msgcode'],'DECODE')),1);
    				if(($timestamp - $inittime) > 60){
    
    					$msg = str_replace(array('%s','{$smscode}'), $msgcode, $msg);
    
    					//$sms = new cls_sms();
    					$msg = $sms->sendSMS($mobile,$msg,'ctel');
    
    					if($msg[0]==1){
    						msetcookie('08cms_msgcode', authcode("$timestamp\t$msgcode", 'ENCODE'));
    					}else{
    						$info = array(
    							'time' => -1,
    							'text' => '������Ϣʧ�ܣ�����ϵ����Ա��'
    						);
    					}
    				}else{
    					$info = array(
    						'time' => 1,
    						'text' => '�벻Ҫ�ظ��ύ���ȴ�ϵͳ��Ӧ'
    					);
    				}
    			//}
    		}else{
    			$info = array(
    				'time' => 0,
    				'text' => '�ֻ������ʽ����'
    			);
    		}
    	}
    	//usleep(1000); //8.2*1000*
        return $info;
    }
}