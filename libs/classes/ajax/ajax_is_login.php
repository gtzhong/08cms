<?php
/**
 * �жϵ�ǰ�û��Ƿ��Ѿ���¼
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/is_login/
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Is_Login extends _08_M_Ajax_Check_Login_Base
{
    public function __toString()
    {
        $getsid = intval(@$this->_get['getsid']);
        $scene = intval(@$this->_get['scene']);  
		$timegap = 10; //10���Ӻ�������ˢ��ҳ��ɨ���¼
		//global $m_cookie; echo $m_cookie['msid'];
        if($getsid){
			$re = array();
			$re['getsid'] = _08_M_Weixin_Qrcode::getSceneID(1);
			return $re;
        }elseif (!empty($this->_curuser->info['mid'])){ //�Ѿ��ǵ�¼״̬
            if(!empty($scene)){ //����ά�볡��ID,��ʵ��ͬ����¼����
				$sysparams = cls_cache::cacRead('sysparams');
				$sysparams['nouser']['mid'] = '-1';
				$this->status['user_info'] = $sysparams['nouser'];
				$this->status['user_ibak'] = $this->_curuser->info; //��������,����js��ʾ��Ϣ
				$this->status['message'] = "�Ѿ��ǵ�¼״̬�����ȵǳ���";
			}else{
				$this->_userInfo = $this->_curuser->getter('info');
				$this->status['user_info'] = $this->filterUserInfo();
				$this->status['message'] = '��¼�ɹ���';
			}
        # ΢��ɨ���ά���¼
		}elseif(!empty($scene)){
            $this->status['user_info'] = array();
            $this->status['error'] = '��¼ʧ�ܡ�';
			$db = _08_factory::getDBO();
			$time = TIMESTAMP - $timegap*60;
			$row = $db->select('mid')->from('#__msession')->where("mslastactive>$time")->_and(array('scene_id'=>$scene))->_and("mid>0")->exec()->fetch();
			if(!empty($row['mid'])){
				$newuser = new cls_userinfo(); //ע�ⲻ��$curuser
				$newuser->activeuser($row['mid']);
				$newuser->OneLoginRecord();
				$this->status['message'] = "��¼�ɹ���";
				$this->status['error'] = '';
            	$this->_userInfo = $newuser->info;
            	$this->status['user_info'] = $this->filterUserInfo();
				$db->update('#__msession', array('scene_id'=>0))->where(array('scene_id'=>$scene))->exec();
			}
        }
        
        return $this->status;
    }
}