<?php
/**
 * 判断当前用户是否已经登录
 *
 * @example   请求范例URL：http://nv50.08cms.com/index.php?/ajax/is_login/
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
		$timegap = 10; //10分钟后让重新刷新页面扫描登录
		//global $m_cookie; echo $m_cookie['msid'];
        if($getsid){
			$re = array();
			$re['getsid'] = _08_M_Weixin_Qrcode::getSceneID(1);
			return $re;
        }elseif (!empty($this->_curuser->info['mid'])){ //已经是登录状态
            if(!empty($scene)){ //传二维码场景ID,不实现同步登录操作
				$sysparams = cls_cache::cacRead('sysparams');
				$sysparams['nouser']['mid'] = '-1';
				$this->status['user_info'] = $sysparams['nouser'];
				$this->status['user_ibak'] = $this->_curuser->info; //备份起来,用于js显示信息
				$this->status['message'] = "已经是登录状态，请先登出。";
			}else{
				$this->_userInfo = $this->_curuser->getter('info');
				$this->status['user_info'] = $this->filterUserInfo();
				$this->status['message'] = '登录成功。';
			}
        # 微信扫描二维码登录
		}elseif(!empty($scene)){
            $this->status['user_info'] = array();
            $this->status['error'] = '登录失败。';
			$db = _08_factory::getDBO();
			$time = TIMESTAMP - $timegap*60;
			$row = $db->select('mid')->from('#__msession')->where("mslastactive>$time")->_and(array('scene_id'=>$scene))->_and("mid>0")->exec()->fetch();
			if(!empty($row['mid'])){
				$newuser = new cls_userinfo(); //注意不用$curuser
				$newuser->activeuser($row['mid']);
				$newuser->OneLoginRecord();
				$this->status['message'] = "登录成功。";
				$this->status['error'] = '';
            	$this->_userInfo = $newuser->info;
            	$this->status['user_info'] = $this->filterUserInfo();
				$db->update('#__msession', array('scene_id'=>0))->where(array('scene_id'=>$scene))->exec();
			}
        }
        
        return $this->status;
    }
}