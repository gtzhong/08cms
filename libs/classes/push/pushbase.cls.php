<?php
/*
** ������Ϣ�ֶ���ӻ�༭�Ĳ�����
** sv����������return_error��ʾ����ʱ����error����������message����
*/
!defined('M_COM') && exit('No Permisson');
class cls_pushbase{
	protected $mc = 0;//��Ա����
	public $isadd = 0;//���ģʽ
	public $pushid = 0;//������Ϣid
	public $paid = 0;//����λid
	public $fmdata = 'fmdata';//form�е���������//������������
	public $predata = array();//Ԥ����������
	public $area = array();//����λ����
	public $fields = array();//�����ֶ�
	public $fields_did = array();//�ݴ��Ѵ�������ֶ�
	
    function __construct(){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		
		$this->paid = cls_env::GetG('paid');
		$this->paid = cls_PushArea::InitID($this->paid);
		$this->pushid = cls_env::GetG('pushid');
		$this->pushid = empty($this->pushid) ? 0 : max(0,intval($this->pushid));
		
		$this->isadd = $this->pushid ? 0 : 1;
		$this->area = cls_PushArea::Config($this->paid);//����λ����
		$this->fields = cls_PushArea::Field($this->paid);//�����ֶ�
		if($this->area){
			$this->predata = cls_pusher::oneinfo($this->pushid,$this->paid,true);
		}
    }
	
	//$return_errorΪ1ʱ�������������ش�����Ϣ
	function message($str = '',$url = '',$return_error = 0){
		if($return_error){
			return $str;
		}else{
			call_user_func('cls_message::show',$str,$url);
		}
	}
	
	function setvar($key,$var){
		$this->$key = $var;	
	}
	function top_head(){
		if($this->mc){
			if(!defined('M_COM')) exit('No Permission');
		}else{
			if(!defined('M_COM') || !defined('M_ADMIN')) exit('No Permission');
			aheader();
			$curuser = cls_UserMain::CurUser();
			if($re = $curuser->NoBackFunc('normal')) $this->message($re);
		}
		echo "<title>����".($this->isadd ? '���' : '����')."</title>";
	}
	function pre_check(){
		if(!$this->paid || !$this->area) $this->message('��ָ����ȷ������λ');
		if($this->pushid && !$this->predata) $this->message('��ָ����ȷ��������Ϣ');
		//Ȩ�޷���??????????????????
		return;
	}	
	
	function fm_header($title = '',$url = ''){
		$title || $title = $this->area['cname'].'&nbsp; -&nbsp; ����';
		if($url){
			if(!in_str('paid=',$url)) $url .= "&paid={$this->paid}"; 
			if($this->pushid){//���ʱ��Ҫ����pushid
				if(!in_str('pushid=',$url)) $url .= "&pushid={$this->pushid}"; 
			}
			tabheader($title,'pushdetial',$url,2,1,1);
		}else{
			tabheader($title);
		}
	}
	function fm_footer($button = '',$bvalue = ''){
		tabfooter($button,$button ? ($bvalue ? $bvalue : ($this->isadd ? '���' : '�ύ')) : '');
	}
	
	//չʾ�ĵ��ֶ�
	//$arrΪ�գ�չʾ������Ч�ֶΡ�$noinc=1��ָ�ų�$arr�е��ֶΣ�����Ϊָ��������
	function fm_fields($arr = array(),$noinc = 0){
		if(!$arr || $noinc){
			foreach($this->fields as $k => $v){
				if(!$arr || !in_array($k,$arr)) $this->fm_field($k);
			}
		}else{
			foreach($arr as $k) $this->fm_field($k);
		}
	}	
	//չʾ����ʣ���ֶ�,���ں��������ֶε��Զ�չʾ
	function fm_fields_other($nos = array()){
		foreach($this->fields as $k => $v){
			if(!in_array($k,$this->fields_did) && (!$nos || !in_array($k,$nos))) $this->fm_field($k);
		}
	}
	
	//�����ĵ��ֶ�չʾ
	//cfg���봫������ã��Դ������������
	function fm_field($ename,$cfg = array()){
		$this->fm_subject_unique();
		if(!empty($this->fields[$ename]) && $this->fields[$ename]['available'] && !in_array($ename,$this->fields_did)){
			$a_field = new cls_field;
			$cfg = array_merge($this->fields[$ename],$cfg);
			$a_field->init($cfg,isset($this->predata[$ename]) ? $this->predata[$ename] : '');
			$a_field->isadd = $this->isadd;
			$a_field->trfield($this->fmdata);
			$this->fields_did[] = $ename;
			unset($a_field);
		}
	}
	
	//���������жϵ��ĵ�����
	function fm_subject_unique(){
		global $subject_table;
		$subject_table || $subject_table = cls_pusher::tbl($this->paid);
	}	
	
	//չʾ���������
	//��ѡ��Ŀarray('startdate','enddate',)
	function fm_params($incs = array()){
		if(empty($incs)) $incs = array('startdate','enddate','norefresh',);
		foreach($incs as $k) $this->fm_param($k);
	}
	
	//չʾָ�����������ѡ��Ŀarray('startdate','enddate',)
	function fm_param($ename){
		global $timestamp;
		switch($ename){
			case 'startdate':
				trbasic('��Ч����',"{$this->fmdata}[startdate]",empty($this->predata['startdate']) ? '' : date('Y-m-d',$this->predata['startdate']),'calendar');
			break;
			case 'enddate':
				trbasic('��ֹ����',"{$this->fmdata}[enddate]",empty($this->predata['enddate']) ? '' : date('Y-m-d',$this->predata['enddate']),'calendar',array('guide'=>'Ϊ�����ʾ������Ч'));
			break;
			case 'norefresh':
				trbasic('������������','',OneCheckBox("{$this->fmdata}[norefresh]",'�Ժ��ٴ���Դ�����Զ�����',empty($this->predata['norefresh']) ? 0 : 1),'');
			break;
		}
	}
	
	//��ʾ��֤��
	function fm_regcode($type = 'archive'){
		if($type && $this->isadd && $this->mc){
			tr_regcode($type);
		}
	}
	
	//�����̨��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	function fm_guide_bm($str = '',$type = 0){
		if($this->mc){
			m_guide($str,$type ? $type : '');
		}else{
			if(!$str){
				$str = $this->isadd ? 'pushadd' : 'pushdetail';
				if(is_file(M_ROOT."dynamic/aguides/{$str}_{$this->paid}.php")) $str .= "_{$this->paid}";
				$type = 0;
			}
			a_guide($str,$type);
		}
	}
	
	//������ǰ��Ա�ķ���Ȩ�޼�����Ȩ�ޣ���fm_pre_cns֮��ִ��
	function sv_allow($return_error = 0){
		//$curuser = cls_UserMain::CurUser();
		//if(!$this->mc && !$this->admin_pm($this->predata['caid'])) return $this->message('��û��ָ����Ŀ�ĺ�̨����Ȩ��',axaction(2,M_REFERER),$return_error);
		//if($this->isadd && ($re = $curuser->arcadd_nopm($this->chid,$this->predata))) $this->message($re,axaction(2,M_REFERER),$return_error);
	}
		
	function sv_fields($nos = array(),$return_error = 0){//$nos�����ų��ֶ�
		foreach($this->fields as $k => $v){
			if(!$nos || !in_array($k,$nos)){
				if($re = $this->sv_field($k,array(),$return_error)) return $re;
			}
		}
	}
	
	//�����ֶδ�������ָ���ֶ�ĳ�����ò���
	function sv_field($ename,$cfg = array(),$return_error = 0){
		$fmdata = &$GLOBALS[$this->fmdata];
		if(isset($fmdata[$ename]) && $v = @$this->fields[$ename]){
			$cfg && $v = array_merge($v,$cfg);
			cls_pusher::SetArea($this->paid);
			if($re = cls_pusher::onefield($v,$fmdata[$ename],isset($this->predata[$ename]) ? $this->predata[$ename] : '')){//��׽������Ϣ
				cls_pusher::rollback();
				return $this->message($re,axaction(2,M_REFERER),$return_error);
			}
		}
	}
	
	//������������
	//��ѡ��Ŀarray('startdate','enddate',)
	function sv_params($incs = array()){
		if(empty($incs)) $incs = array('startdate','enddate','norefresh',);
		foreach($incs as $k) $this->sv_param($k);
	}
	
	//����ָ�����������ѡ��Ŀarray('startdate','enddate',)
	function sv_param($ename){
		global $timestamp;
		$fmdata = &$GLOBALS[$this->fmdata];
		if($ename && isset($fmdata[$ename])){
			cls_pusher::SetArea($this->paid);
			if($ename == 'startdate'){//��ʼ����
				cls_pusher::onedbfield($ename,empty($fmdata[$ename]) ? 0 : strtotime($fmdata[$ename]),isset($this->predata[$ename]) ? $this->predata[$ename] : 0);
			}elseif($ename == 'enddate'){//����ʱ��
				cls_pusher::onedbfield($ename,empty($fmdata[$ename]) ? 0 : strtotime($fmdata[$ename]),isset($this->predata[$ename]) ? $this->predata[$ename] : 0);
			}elseif($ename == 'norefresh'){//�رո���
				cls_pusher::onedbfield($ename,empty($fmdata[$ename]) ? 0 : 1,isset($this->predata[$ename]) ? $this->predata[$ename] : 0);
			}
		}
	}
	
	function sv_fail($return_error = 0){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1);
		return $this->message('�ĵ����ʧ��',axaction(2,M_REFERER),$return_error);
	}
	
	//ִ���Զ��������������ϱ��
	function sv_update(){
		if($this->isadd){ //���ã�11.�ֶ����
			cls_pusher::onedbfield('loadtype',11);
		}
		cls_pusher::SetArea($this->paid);
		cls_pusher::updatedb($this->pushid);
	}
	
	//�ϴ�����
	function sv_upload(){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1,$this->pushid,'pushs');
		$c_upload->saveuptotal(1);
	}
	
		
	//����ʱ��Ҫ������ �磺������¼���ɹ���ʾ
	function sv_finish(){
		$modestr = $this->isadd ? '���' : '�޸�';
		$this->mc || adminlog($modestr.'������Ϣ');
		$this->message('������Ϣ'.$modestr.'���',axaction(6,M_REFERER));
	}
}