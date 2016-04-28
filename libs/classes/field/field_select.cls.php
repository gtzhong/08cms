<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_select extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_length();
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_vdefault();
		self::_fm_mode();
		self::_fm_search();
		self::_fm_innertext();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		
		# ���ݱ��ֶγ���
		if(self::$isNew || isset(self::$fmdata['length'])){
			self::$newField['length'] = empty(self::$fmdata['length']) ? 0 : min(255,max(0,intval(self::$fmdata['length'])));
		}else{
			self::$newField['length'] = self::$oldField['length'];
		}
		
		# ��֮��ѡ������
		self::$newField['fromcode'] = empty(self::$fmdata['fromcode']) ? 0 : 1;
		self::$newField['innertext'] = empty(self::$fmdata['innertext']) ? '' : trim(self::$fmdata['innertext']);
		if(empty(self::$newField['fromcode'])) self::$newField['innertext'] = str_replace("\r","",self::$newField['innertext']);
		
		# Ĭ��ֵ
		self::$newField['vdefault'] = empty(self::$fmdata['vdefault']) ? '' : trim(self::$fmdata['vdefault']);
		
	}
	# ��֮������ֶγ���
    protected static function _fm_length(){
		if((self::$SourceType == 'pusharea') && !empty(self::$oldField['ename']) && in_array(self::$oldField['ename'],array('classid1','classid2'))) return;
		if(in_array(self::$SourceType,array('mchannel')) && !empty(self::$oldField['iscommon']) && !empty(self::$SourceID)) return;
		$Value = self::$isNew ? '' : self::$oldField['length']; //ע�������Ϊ��[����int(10)]�����²���Ϊ1,255������0,255
		trbasic('���ݱ��ֶγ���','fmdata[length]',$Value,'text',array('guide'=>'�趨��Χ1-255�����������ݱ��ֶ�Ϊ����int(10)', 'validate' => makesubmitstr('fmdata[length]',0,0,0,255,'int')));
	}
	# ��֮���ؼ�ģʽ
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : 1;
		trbasic('���ؼ�ģʽ','',makeradio('fmdata[mode]',array(0 => '��ѡ�б�',1 => '��ѡ��(radio)'),$Value),'');
	}
	# ��֮Ĭ������ֵ
    protected static function _fm_vdefault(){
		$Value = self::$isNew ? '' : self::$oldField['vdefault'];
		trbasic('Ĭ������ֵ','fmdata[vdefault]',$Value,'text',array('w'=>50));
	}	
	# ��֮��ѡ������
    protected static function _fm_innertext(){
		
		$fromcodestr = OneCheckBox('fmdata[fromcode]','���Դ��뷵������',empty(self::$oldField['fromcode']) ? 0 : 1);
		$Value = self::$isNew ? '' : self::$oldField['innertext'];
		$guide = 'ÿ����дһ��ѡ�';
		if((self::$SourceType == 'pusharea') && !empty(self::$oldField['ename']) && in_array(self::$oldField['ename'],array('classid1','classid2'))){
			$guide .= '����id=������ʾ���⡣';
		}else{
			$guide .= '��ʽ1��ѡ��ֵ��ͬʱΪ��ʾ���⣩����ʽ2��ѡ��ֵ=ѡ����ʾ���⡣';
		}
		$guide .= '<br> ��ѡ ���Դ��뷵�����飬����дPHP���룬ʹ��return array(��������);�õ�ѡ�����ݡ�<br>��ʹ����չ�������붨�嵽'._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php';
		trbasic('ѡ����������<br>'.$fromcodestr,'fmdata[innertext]',$Value,'textarea',array('guide'=>$guide));
	}
}
