<?php
/* 

// �淶: �ӷ�: ����Ϊacurrency, ���ʱ,�����Ƿ����,(���ֶ����벻ͬ,�����޸�)
//       ����: ����Ϊccurrency, ɾ��(����)ʱ,��˲���������
//       ǰ׺: cu-������, a-archivesXXX, m-member, s=member_sub c-�ĵ�/��Աģ�ͱ�

** ������������,��addbase,editbase,listbase������̳�
** ������(������)�ط����ã��򹫹��Ĵ��룬�ŵ�����

*/
!defined('M_COM') && exit('No Permission');

class cls_cubasic{
	
	public $cuid = 0; //������ĿID
	public $ptype = ''; //������������:a-�ĵ�, m-��Ա, e-����
	public $pchid = 0; //��������ģ��ID,chid,mchid(���ģ��,Ϊʹ�߼���Ҳ�������)
	
	public $mc = ''; //������:1-��Ա����,0-��̨
	public $A = array();//��ʼ��������š���cuid��...
	public $act = ''; //��������:list, edit, add
	public $burl = ''; //����url, edit,list��top_head()����, etools:add��add_init()����
	// ��ʼ�����õĲ�����&��ʼ, $action,$entry,$extend_str,$cuid,$inajax,$js���ô���,�Զ�����
	//public $cid = 0;
	
	public $cucfgs = array(); //������Ŀ����
	public $fields = array(); //������Ŀ�ֶ�
	public $fhidden = array(); //ʹ��cls_string::SubReplace($row[$k]),'');���ز�����ϵ��ʽ,���ڵ绰����,Email���ֶ�, ��'fhidden' => is_hidden_connect() ? array('dianhua','email') : array(), 
	
	public $db = null;
	
    function __construct($_cfg){
		$this->init($_cfg);
	}
	
	# ��ʼ�� (cuid)
	function init($_cfg){
		
		$this->cuid = $_cfg['cuid'];
		$this->ptype = @$_cfg['ptype']; //��һЩͶƱ�Ȳ����п���ֻ��һ��cuid����,û��ptype��
		!empty($_cfg['pchid']) && $this->pchid = $_cfg['pchid'];
		
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $_cfg;
		
		$this->cucfgs = cls_cache::Read('commu',$this->cuid);
		$this->fields = cls_cache::Read('cufields',$this->cuid); 
		if(isset($_cfg['fhidden'])){	
			$this->fhidden = $_cfg['fhidden'];
		}
		
		$this->db = _08_factory::getDBO();
	}
	
	// culist+cuedit:�����̨+��Ա����:ʹ��
	// cfg['chkData']: ��������Ƿ����
	// cfg['setCols']: ������ñ༭�ֶ�
	function top_head($cfg=array()){
		global $action,$entry,$extend_str,$infloat;
		$curuser = cls_UserMain::CurUser();
		if($this->mc){
			!defined('M_COM') && exit('No Permission');
		}else{
			(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
			aheader();
			if($re = $curuser->NoBackFunc('commu')) $this->message($re);
		}
		// ����url, cuedit,culistʹ��
		if($this->mc){ //?action=$action&cuid=$cuid&pid=$pid
			$burl = "?action=$action&cuid=$this->cuid";
		}else{ //?entry=$entry$extend_str&caid=$caid&cuid=$cuid&pid=$pid&reid=$reid
			$burl = "?entry=$entry$extend_str&cuid=$this->cuid";
		}
		empty($this->A['caid']) || $burl .= "&caid={$this->A['caid']}";
		$this->burl = $burl;
		$this->A['url'] = $this->burl.(empty($this->A['url']) ? '' : $this->A['url']);
		foreach(array('infloat') as $k){ //'aid','mid','pid'
			global $$k; $v = $$k;
			if($v && !strstr('$v=',$this->A['url'])) $this->A['url'] .= "&$k=$v";
		}//*/

		$this->cucfgs || $this->message('�����ڵĽ�����Ŀ��');
		echo "<title>�������� - ".$this->cucfgs['cname']."</title>";
		empty($this->A['cid']) || $this->predata = $this->getRow($this->cid, array());
		
		// ����cfg����
		if(!empty($cfg['chkData'])){
			if(empty($this->predata)) $this->message('���������ݣ�'); 
		}
		if(!empty($cfg['setCols'])){
			$this->additems();
		}
	}
	
	# �õ�һ�ʽ�������
	function getRow($cid, $whrarr=array()){
		$cid = intval($cid);
		$this->db->select('*')->from(self::table());
		$this->db->where(empty($cid) ? '1=1' : array('cid'=>$cid));
		if($whrarr){
			foreach($whrarr as $k=>$v){
				$this->db->_and(array($k=>$v));	
			}
		}
		return $this->db->exec()->fetch();
	}
	
	# �õ���������(�ĵ�/��Ա)����Ϣ(������)
	function getPInfo($type='a',$pid,$detail=0){
		$pid = intval($pid);
		$pinfo = array();
		if($type=='a'){
			$arc = new cls_arcedit;
			$arc->set_aid($pid,array('au'=>0,'ch'=>$detail));
			$pinfo = $arc->archive;
			$pinfo && cls_ArcMain::Parse($pinfo);	
		}elseif($type=='m'){
			$user = new cls_userinfo;
			$user->activeuser($pid,$detail);
			$pinfo = $user->info;
		} 
		if(!empty($pinfo)) $pinfo['_pid'] = $pid; //ͳһ����pid
		return $pinfo;
	}
	
	# �õ���������(�ĵ�/��Ա)��һ������(��a��ǩ)
	// ����etools�ύ, ��̨�༭��
	// ��������cfg������cucolsbase.cls.php :: user_subject($mode = 0,$data = array())
	// $data�������������ݣ����Ϊ����,�����getPInfo()��ȡ������������
	function getPLink($data, $cfg=array()){
		if(is_numeric($data)) $data = $this->getPInfo($this->ptype,$data); 
		$len = empty($cfg['len']) ? 40 : $cfg['len'];
		$dkey = empty($cfg['field']) ? ($this->ptype=='m' ? 'mname' : 'subject') : $cfg['field'];
		$dre = htmlspecialchars(cls_string::CutStr($data[$dkey],$len));
		if(empty($cfg['url']) && ($this->ptype=='a' || $this->ptype=='u')){ //�ĵ�   //ptype=='u'Ϊ�û��Զ���sql������
			$addno = empty($cfg['addno']) ? 0 : max(0,intval($cfg['addno']));
			if(!empty($cfg['mc'])){  //��Ա�ռ�    
				cls_ArcMain::Url($data,-1);
				$url = $data['marcurl'];
			}else{ 
				cls_ArcMain::Url($data); 
				$url = $data['arcurl'.(empty($addno)?'':$addno)];
			}
		}elseif(empty($cfg['url']) && $this->ptype=='m'){ //�ĵ�
			$url = cls_Mspace::IndexUrl($data);
		}elseif($cfg['url'] == '#'){  // ����Ҫurl����
			return $dre;
		}else $url = key_replace($cfg['url'],$data); //�����Զ���url��ʽ
		return "<a href=\"$url\" target=\"_blank\">$dre</a>";
	}
	
	function getFields($re=''){
		$dbtable = $this->table(1); //û�п����Ƿ�֧��sqli������ȷ��
		$query = $this->db->query("SHOW FULL COLUMNS FROM $dbtable",'SILENT');
		$a = array();
		while($row = $this->db->fetch_array($query)){
			$a[] = $row['Field'];
		}
		return $a;
	}
	
	//ɾ����������ĸ��� //upload.cls.php�Խ�������������,�������cid�����ظ�...
	//������һ�㲻�ø���,�����ĸ��������ʹ��,����delete֮ǰ��������
	function delatt($cid){ 
		$query = $db->query("SELECT * FROM {$tblprefix}userfiles WHERE aid='{$cid}' AND tid=16");
		while($r = $db->fetch_array($query)){
			atm_delete($r['url'],$r['type']);
			$uploadsize = ceil($r['size'] / 1024);
			if($mid = $r['mid']){
				$user = new cls_userinfo; //ͼƬ���ܷ�Ϊ����Ա,��Ա/�ο��ϴ�,�ֱ���
				$user->activeuser($mid,0);
				$user->updateuptotal($uploadsize,1,1);
			}
		}
		$db->query("DELETE FROM {$tblprefix}userfiles WHERE aid='{$cid}' AND tid=16", 'UNBUFFERED');
	}
	
	// ɾ��һ����������
	// exkey: ����ͬʱɾ���ظ�, ��ϵ:exkey=$cid
	function delete($cid, $exkey=''){
		//ɾ�������Ĺ���������Ϣ
		cls_pusher::DelelteByFromid($cid,'commus',$this->cuid);
		//$this->delatt($cid); //�ĸ��������ʹ��,����delete֮ǰ��������
		$this->db->delete($this->table())->where('cid='.$cid)->exec();
		if(!empty($exkey)){
			$this->db->delete($this->table())->where($exkey.'='.$cid)->exec();
		}
	}
	
	# ���û��֣��������-����, ɾ������-����
	function setCrids($act, $mid=0){
		$actname = $this->cucfgs['cname'];
		if(empty($mid)){
			$user = cls_UserMain::CurUser();
		}else{
			$user = new cls_userinfo;
			$user->activeuser($mid);
		}
		if($act=='add'){
			$num = 0+intval(@$this->cucfgs['acurrency']);	
			$actname = "����$actname";
		}else{
			$num = 0-intval(@$this->cucfgs['ccurrency']);	
			$actname = "����$actname";	
		}
		if($num){
			$user->updatecrids(array(1=>$num),1,$actname);
		}
	}
	
	function table($old=0){
		global $tblprefix;
		if($old) return $tblprefix.$this->cucfgs['tbl'];
		else     return '#__'.$this->cucfgs['tbl'];
	}
	// ͳһ��ʾһ����ʾ��Ϣ
	function message($str = '',$url = ''){
		//call_user_func($this->mc ? 'mcmessage' : 'amessage',$str,$url);
		//$this->top_head();
		cls_message::show($str, $url);
	}
	// ͳһ��ʾajax��ʾ��Ϣ(��Ҫ��:�ղ�,��ע��)
	function msgajax($key, $cfg=array()){
		$arr = array(
			'OK' => 'succeed',
			'noLogin' => '���ȵ�¼��Ա��',
			'Repeat' => '�����ظ�������',
			'Error' => '����',
		);
		if(isset($cfg[$key])){
			$msg = $cfg[$key];	
		}elseif(isset($arr[$key])){
			$msg = $arr[$key];	
		}else{
			$msg = "δ֪����[$key]!";	
		}
		cls_message::show($msg);
	}
	
	//�����̨��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	function guide_bm($str = '',$type = 0){
		if($this->mc){
			m_guide($str,$type ? $type : '');
		}else{
			a_guide($str,$type);
		}
	}
	
	//���ý����ӷ�(��û�н���cuedit������ʹ��), demo: cls_cubasic::setCridsOuter($cuid);
	static function setCridsOuter($cuid,$act='add'){
		$cu = new cls_cuedit(array('cuid'=>$cuid));
		$cu->setCrids($act);
	}
	
	//�����ĵ���Ŀ���ĵ�ģ��ID
	static function caid2chid($caid){
		$_tcaid = cls_cache::Read('catalog', $caid);
		$chid = preg_replace("/[^\d]/","",$_tcaid['chids']);
		return $chid;
	}
}
