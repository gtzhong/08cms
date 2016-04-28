<?php
/**
 * ��֤����֤
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/regcode/
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 * @since     nv50
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Regcode_Base extends _08_Models_Base
{
    public function __toString()
    {
    	if ( empty($this->_get['verify']) )
        {
            $verify = '08cms_regcode';
        }
        else
        {
        	$verify = $this->_get['verify'];
        }
		
        $m_cookie = cls_env::_COOKIE();
        # ���COOKIE����ʱ����ʹ�ô˷���
		$session_name = session_name();
		if (empty($m_cookie) && array_key_exists($session_name, $this->_get))
		{
			session_write_close();
			session_name($session_name);
			session_id($this->_get[$session_name]);
			session_start();
			$m_cookie = $_SESSION;
		}
        
        # ��ʱ�ü��ݾɵ��÷�ʽ
        $msg = (!empty($this->_get['js']) ? 'var msg = "' : '');
    	@list($inittime, $initcode) = maddslashes(explode("\t", authcode($m_cookie[$verify], 'DECODE')), 1);
        $msg .= (TIMESTAMP - $inittime) > 1800 || strtolower($initcode) != strtolower($this->_get['regcode']) ? '��֤�����' : '';
        #$msg .= var_export($m_cookie, true);
        $msg .= (!empty($this->_get['js']) ? '";' : '');
    	
        return $msg;
    }
}