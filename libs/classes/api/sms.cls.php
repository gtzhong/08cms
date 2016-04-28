<?php

/**
 * �ֻ����Žӿ���
 *
 * ˵����
 *
 * @author    08CMS
 */
 
class cls_sms{

	public  $cfg_mchar = 70; // һ����Ϣ,���ָ���(С��ͨ65����)
	public  $cfg_mtels = 200; // һ�η���,���200���ֻ��������
	public  $cfg_timeout = 3; //��ʱʱ��
	
	public  $api       = ''; //api�ӿ�����(�ṩ��)
	public  $smsdo     = NULL; //api����
	public  $cfgs      = array(); //api����
	public  $cfga      = array(); //��������
	
	//public function __destory(){  }
	public function __construct(){ 
		$sms_cfg_api = cls_env::mconfig('sms_cfg_api');
		$sms_cfg_uid = cls_env::mconfig('sms_cfg_uid');
		$sms_cfg_upw = cls_env::mconfig('sms_cfg_upw');
		$sms_cfg_pr3 = cls_env::mconfig('sms_cfg_pr3');
		$sms_cfg_pr4 = cls_env::mconfig('sms_cfg_pr4');
		$sms_cfg_pr5 = cls_env::mconfig('sms_cfg_pr5');
		$this->api = $api = !empty($sms_cfg_api) ? $sms_cfg_api : ''; 
		require M_ROOT."include/sms/basic_cfg.php"; // ��������,����once,������һĳ��ҳ�����������һ�μ��ز���
		$this->cfga = $sms_cfg_aset;
		$this->cfg_timeout = !empty($sms_cfg_tmieout) ? $sms_cfg_tmieout : '3';
		if(isset($sms_cfg_aset[$api])){
			$this->cfgs = $sms_cfg_aset[$api];
			$class = "sms_$api";
			$uid = !empty($sms_cfg_uid) ? $sms_cfg_uid : '';
			$upw = !empty($sms_cfg_upw) ? $sms_cfg_upw : '';
			$pr3 = !empty($sms_cfg_pr3) ? $sms_cfg_pr3 : '';
			$pr4 = !empty($sms_cfg_pr4) ? $sms_cfg_pr4 : '';
			$pr5 = !empty($sms_cfg_pr5) ? $sms_cfg_pr5 : '';
			// ͳһʵ����һ�� api���� // load sms libs
            _08_FilesystemFile::filterFileParam($api);
			require_once M_ROOT."include/sms/api_{$api}.php";
			cls_env::SetG('sms_cfg_tmieout',$this->cfg_timeout);
			$this->smsdo = new $class($uid,$upw,$pr3,$pr4,$pr5);
			$this->smsdo->timeout = $this->cfg_timeout;
		}	
	}
	
	/**
	 * ���Žӿ��Ƿ�ر�
	 *
	 * @return	bool	---		0-����,1�ر�
	 *
	 **/
	public function isClosed(){
		if(!empty($this->cfgs)){
			return false;
		}else{
			return true;
		} //&&$sms_cfg_api!='(close)'
	}

    /**
     * ��ģ���Ƿ����� (�����ÿ������õģ�����������������磺commtpl��membexp)
     * @param	string	$module Ҫ���õĶ���ģ������
     * @param	bool	$tpl    1 ��ȡ����ģ������
     * @return	bool	---		0-����,1�ر�
     * @return	$smstpl	����ģ������
     *
     * @author icms <icms@foxmail.com>
     **/
    public function smsEnable($module){
		if(is_numeric($module)){
			$module = "confirm$module";	
		}
        if($this->isClosed()){
            return false;
        }else{
            $smsenalbe = cls_cache::cacRead('smsconfigs',_08_USERCACHE_PATH);
            if (empty($smsenalbe[$module]['open'])) {
                return false;
            }else{
                return true;
            }
        }
    }

    /**
     * ��ȡ��ģ�����ģ��
     * @param $module ģ�������磺register�����Ա��֤ID�磺1(ת��Ϊ��confirm$mctid)
	 * @param $checkcode �Ƿ���֤������
     *
     * @author icms <icms@foxmail.com>
     */
    function smsTpl($module,$checkcode=1){
        if(empty($module)){
			$module = 'commtpl';
		}elseif(is_numeric($module)){
			$module = "confirm$module";	
		}
		$smscfgsave = cls_cache::cacRead('smsconfigs',_08_USERCACHE_PATH);
        $smstpl = !empty($smscfgsave[$module]['tpl']) ? $smscfgsave[$module]['tpl'] : (empty($checkcode) ? '' : @$smscfgsave['commtpl']['tpl'] );
		if(empty($smstpl) && $checkcode){ //ģ��Ϊ����Ϊ��֤������
			$hostname = cls_env::mconfig('hostname'); //�ܶ�ӿ�Ҫ��ǩ��,�����Ĭ��ǩ��
			$smstpl = '����ȷ����Ϊ{$smscode}������Ϣ�Զ����ͣ�����ظ�����{$hostname}��';
		}
        return $smstpl;
    }

    /**
	 * ����ѯ
	 * ���˵����array(1,1234.5): �ɹ�,���Ϊ1234.5��array(-1,'ʧ��ԭ��'): 
	 *
	 * @return	array	---		�������	�磺array(1,1234.5)
	 *
	 **/
	public function getBalance(){
		return $this->smsdo->getBalance();	
	}
	
	/**
	 * ���ŷ��ͣ�֧�ֶ���ģ���滻��
	 * 
	 * @param	string	$mobiles 	�ֻ�����,�ο�sendSMS()
	 * @param	string	$tpl 		֧��ģ�棬�磺{$subject}{$name}���
	 * @param	array	$source		�滻Դ��array('subject'=>'hellow 08cms!','name'=>'peace',)
	 * @param	string	$type 		���ͷ�ʽ/�������,�ο�sendSMS()
	 *
	 * @return	array	---		�������,�ο�sendSMS()
	 *
	 **/
	public function sendTpl($mobiles,$tpl,$source,$type='scom'){
		$tpl = str_replace(array("\r\n","\r","\n"),array(' ',' ',' '),$tpl);
		if(preg_match_all('/{\s*(\$[a-zA-Z_]\w*)\s*}/i', $tpl, $matchs)){
			if(!empty($matchs[0])){
				foreach($matchs[0] as $v){
					$k = str_replace(array('{','$','}'),'',$v);
					$val = isset($source[$k]) ? $source[$k] : (isset($GLOBALS[$k]) ? $GLOBALS[$k] : "{\$$k}");
					$tpl = str_replace($v,$val,$tpl);
				}
			}
		}
		return $this->sendSMS($mobiles,$tpl,$type);
	}
	
	/**
	 * ���ŷ���
	 * 
	 * @param	string	$mobiles 	�ֻ�����,array/string(Ӣ�Ķ��ŷֿ�)
	 * @param	string	$content 	255���ַ�����
	 * @param	string	$type 		���ͷ�ʽ,������� ��
	 *					scom=Ĭ��,��ͨ��Ա����,������, 
	 *					sadm=����Ա(��������), 
	 *					ctel=�ֻ���֤(������½,ÿ��һ������,70������)
	 *					$mid=��Աid(����),��$mid���û����Ͳ������,(!!!)���÷��͵ĵط�����ƺ�Ȩ��,����,�����$mid�����
	 *
	 * @return	array	---		�������,�磺array(1,'�����ɹ�'): 
	 *
	 **/
	public function sendSMS($mobiles,$content,$type='scom'){
		global $db,$tblprefix,$timestamp,$onlineip;
		$curuser = cls_UserMain::CurUser();
		// ��ʽ�� $mobiles,$content, 
		$atel = $this->telFormat($mobiles);
		if($type=='ctel'){
			$amsg = $this->msgCount($content,$this->cfg_mchar);
			$atel = array($atel[0]); //ֻȡ��һ������
		}else{
			$amsg = $this->msgCount($content);
		} //echo "::::"; print_r($atel);
		if(empty($atel)) return array('-2','���벻��ȷ!');
		if(empty($amsg)) return array('-2','��Ϣ����Ϊ��!');
		if($smax = $this->check_smax($atel)) return array('-2','ͬһ����һ�����������Ϣ�������ܳ���'.$smax.'��!');
		if($ipmax = $this->check_ipmax()) return array('-2','ͬһIP���ͼ��̫��,��Ҫ����'.$ipmax.'��!');
		$nmsg = count($atel)*$amsg[1];
		// ��۷Ѽ�������,������
		$balance = $this->smsdo->getBalance();
		if((float)$balance[1]<=0){
			$mobiles = implode(',',$atel);
			$this->balanceWarn("--tels:$mobiles\n --cmsg:$content"); //д��¼
			return array('-2','ϵͳ����,����ϵ����Ա!');		
		}
		$is_send = 1; // ָ����Աmid��״̬,�ɷ���
		$m_id = $curuser->info['mid'];	
		$m_name = $curuser->info['mname'];	
		$m_charge = isset($curuser->info['sms_charge']) ? $curuser->info['sms_charge'] : 0;
		if(intval($type)){
			$send_user = new cls_userinfo;
			$send_user->activeuser($type, 1);
			$m_id = $send_user->info['mid'];	
			$m_name = $send_user->info['mname'];	
			$m_charge = $send_user->info['sms_charge'];
			if($nmsg>$m_charge){
				$is_send = 0; // ���ɷ���
			}
		}
		if($type=='scom'&&$nmsg>$m_charge){
			return array('-2','����!');	
		}
		if($is_send){ // ��$mid���û�����,�û�Ա�����ŷ��� 
			// ��������ܹ����͵ĺ���,���鷢��
			if(count($atel)>$this->cfg_mtels){
				$groups = array_chunk($atel,$this->cfg_mtels);
				$res = array('-2','Ⱥ��ʧ��!');
				$flag = false; //�ɹ����
				foreach($groups as $group){ 
					$res_temp = $this->smsdo->sendSMS($group,$amsg[0]);
					if($res_temp[0]=='1'){ //ֻҪһ�鷢�ͳɹ�,����ɹ�.
						$res = $res_temp;	
					}
				}
			}else{
				$res = $this->smsdo->sendSMS($atel,$amsg[0]);
			}
			// �����(��)
			if(($type=='scom'||intval($type)) && $res[0]=='1'){
				$sql = "UPDATE {$tblprefix}members SET sms_charge='".($m_charge-$nmsg)."' WHERE mid='$m_id'";
				$db->query($sql);
			}
			$restr = "".implode('|',$res)."|$nmsg";
		}else{ // $mid��Աû�����,��ִ�з���
			$res = array('-2','����');
			$restr = "-2|����|$nmsg";
		}
		// д��¼-db
		$stel = implode(',',$atel); 
		if(strlen($stel)>255) $stel = substr($stel,0,240).'...'.substr($stel,strlen($stel)-5,255);
		$sql = "INSERT INTO {$tblprefix}sms_sendlogs SET 
		  mid='".($type=='ctel' ? 0 : $m_id)."',mname='$m_name',stamp='$timestamp',ip='$onlineip',
		  tel='$stel',msg='".maddslashes($amsg[0],1)."',res='$restr',api='".$this->api."/$type',cnt='$nmsg'";
		$db->query($sql);
		// ��Ǯ for 0test_balance.txt
		if($this->api=='0test' && $res[0]=='1'){
			$this->smsdo->deductingCharge($nmsg);
		}
		return $res;
	}
	
	//��������,һ��������ܽ��ն��ŵĴ���; Ⱥ��ȡǰ24�������ַ���
	//����: 0:�ɷ���, �������֣�����޶���ɷ���
    public function check_smax($tel)
    {
		$db = _08_factory::getDBO();
		$smax = intval(cls_env::mconfig('sms_cfg_smax'));
		$smax || $smax = 10;
		if(is_array($tel)) $tel = implode(',',$tel);
		$row = $db->select('COUNT(*)')->from('#__sms_sendlogs')
			->where("stamp >= ".(TIMESTAMP-86400)."")
			->_and('tel')->like($tel, '_%')
			->exec()->fetch(); //var_dump($row['COUNT(*)']); ->setDebug()
		$cnt = empty($row['COUNT(*)']) ? 0 : $row['COUNT(*)'];
		$flag = $cnt >= $smax ? $smax : 0;
		return $flag;
	}
	
	//����IP���η�����Ϣ�����ʱ������0Ϊ�����ƣ�����ݶ�����Ӫ�����á�
	//����: 0:�ɷ���, �������֣�����޶���ɷ��� $db->select('*')->from('#__members')->where('mname')->like('a')->_and('mname')->like('d')->exec()->fetch();
    public function check_ipmax()
    {
		$db = _08_factory::getDBO();
		$ipmax = intval(cls_env::mconfig('sms_cfg_ipmax')); ;
		if(empty($ipmax)) return false;
		$ip = cls_env::OnlineIP();
		$row = $db->select('stamp')->from('#__sms_sendlogs')
			->where(array('ip'=>$ip)) // "ip='$ip'"
			->_and('res')->like('1|OK!')
			->order("cid DESC")//->setDebug()
			->exec()->fetch(); //var_dump($row); 
		$stamp = empty($row['stamp']) ? 0 : $row['stamp'];
		$flag = TIMESTAMP-$stamp >= $ipmax ? 0 : $ipmax;
		return $flag;
	}
	
	/**
	 * �������,������¼
	 * 
	 * @param	int		$flag 	int/string����/
	 *					����,����Сʱ���޸�(��¼������)��,
	 *					flag=str,��¼��Ϣ����
	 *
	 * @return	NULL	
	 *
	 **/
	function balanceWarn($flag){
		global $db,$tblprefix,$timestamp,$onlineip; 
		$curuser = cls_UserMain::CurUser();
		$path = M_ROOT."dynamic/sms";  
		$file = "$path/balance_apiwarn.wlog"; 
		if(is_numeric($flag)){ //����ļ�,����ʱ��(day)���޸Ĺ�
			if(is_file($file)){ 
				$flag = $flag*24*3600; //��
				if($timestamp - filemtime($file) < $flag) return true;
				else return false;
			}else{
				return false;
			}
		}else{ 
			mmkdir($path,0);
			$data = '';
			if(is_file($file)){
				$data = file_get_contents($file);
			}
			$fp = fopen($file, 'wb');
			$data = date('Y-m-d H:i:s')."^ ".$curuser->info['mname']." ^ $onlineip \n $flag\r\n\r\n$data";
			flock($fp, 2); fwrite($fp, $data); fclose($fp);
		}
	}

	/**
	 * �绰���� ��ʽ��/����
	 * 
	 * @param	array	$tel 	��ʼ�ĵ绰����array/string
	 * @return	array	$re		��ʽ�������˺�ĵ绰����
	 *
	 **/
	public function telFormat($tel){
		if(is_string($tel)){
			$tel = str_replace(array("-","("," ",')'),'',$tel);
			$tel = str_replace(array("\r\n","\r","\n",';'),',',$tel);
			$arr = explode(',',$tel);
		}else{
			$arr = $tel;	
		}
		$arr = array_filter($arr);
		$re = array();
		for($i=0;$i<count($arr);$i++){
			//  �ֻ�/^1\d{4,10}$/; 95168�Ϸ�����/^[1-9]{1}\d{4,10}$/; 0769-12345678С��ͨ
			if(preg_match('/^\d{5,12}$/',$arr[$i])) $re[] = $arr[$i];
		}
		return $re;	
	}
	/**
	 * �������� ��ȡ/����
	 * 
	 * @param	string	$msg 	��ʼ�Ķ�������
	 * @param	int		$slen 	����ȡ��������
	 * @return	array	$re		����array(����,��Ϣ����,���ָ���)
	 *
	 **/
	public function msgCount($msg,$slen=255){
		//global $mcharset; 
		$hostname = cls_env::mconfig('hostname'); //�ܶ�ӿ�Ҫ��ǩ��,�����Ĭ��ǩ��
		$msg = str_replace('{$hostname}',$hostname,$msg);
		$mcharset = cls_env::getBaseIncConfigs('mcharset');	
		$clen = $mcharset=='utf-8' ? 3 : 2; //���Ŀ��
		$cmax = min(array($slen,255)); //���ȡ255����
		$n = strlen($msg); //php����ԭʼ����
		$p = 0; //ָ��
		$cnt = 0; // ����,Ӣ����һ���ַ�
			for($i=0; $i<$n; $i++) {
				if($p>=$n) break; //��β
				if($cnt>=$cmax) break; //������ָ���
				if(ord($msg[$p]) > 127) { $p += $clen; }
				else { $p++; }
				$cnt++;
			}
			$msg = substr($msg,0,$p);
		if($cnt>$this->cfg_mchar){ // >70��
			$ncnt = ceil($cnt/($this->cfg_mchar-5)); //(70-3)������һ����Ϣ
			// (dxton.com�����ĵ�) --- �����ų��ȡ�����շѣ�
			// 70�ַ���1���շѣ���70�ַ�,��65�ַ�/���������շѡ�(Ŀǰ��Ӫ����ҵ��׼��
		}else{
			$ncnt = 1;
		}
		return array($msg,$ncnt,$cnt); 
	}
	
	// ����ΪĳЩ�ӿ� ����չ������
	public function login(){
		return $this->smsdo->login();
	}
	public function logout(){
		return $this->smsdo->logout();
	}
	public function chargeUp($charge){
		return $this->smsdo->chargeUp($charge);
	}

	//��inculde�����http(),����������ȥ,�ô����
	static function getHttpData($url){
		$options = array(  
			CURLOPT_RETURNTRANSFER => true,  
			CURLOPT_HEADER         => false,  
			CURLOPT_POST           => true,  
			CURLOPT_POSTFIELDS     => '',  
		);  
		$ch = curl_init($url);  
		curl_setopt_array($ch, $options);  
		$html = curl_exec($ch);  
		curl_close($ch);
		return $html;	
	}

}
