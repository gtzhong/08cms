<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ��cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_cacc extends cls_fieldconfig{
	
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		
		self::_fm_coid();
		self::_fm_innertext();
		self::_fm_cnmode();
		self::_fm_mode();
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_vdefault();
		self::_fm_search();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		
		# ��ϵѡ��
		if(self::$isNew){
			self::$newField['coid'] = empty(self::$fmdata['coid']) ? 0 : max(0,intval(self::$fmdata['coid']));
		}else{
			self::$newField['coid'] = self::$oldField['coid'];
		}
		
		# ��Ŀid������Դ�ڴ��뷵��ֵ
		self::$newField['innertext'] = empty(self::$fmdata['innertext']) ? '' : trim(self::$fmdata['innertext']);
		
		# Ĭ��ֵ
		self::$newField['vdefault'] = str_replace('[##]',",",empty(self::$fmdata['vdefault']) ? '' : trim(self::$fmdata['vdefault']));
		
		# �����ѡ��ʽ
		self::$newField['cnmode'] = empty(self::$fmdata['cnmode']) ? 0 : max(2,intval(self::$fmdata['cnmode']));
	}
	# ��֮��Դ��ϵ
    protected static function _fm_coid(){
		$Value = self::$isNew ? (empty(self::$fmdata['coid']) ? 0 : self::$fmdata['coid']) : self::$oldField['coid'];
		$coidsarr = array(0 => '��Ŀ') + cls_cotype::coidsarr(1,1);
		trbasic('��Դ��ϵ','',$coidsarr[$Value],'');
		if(self::$isNew) trhidden('fmdata[coid]',$Value);
	}
	# ��֮��ѡ������:��Ŀid������Դ�ڴ��뷵��ֵ
    protected static function _fm_innertext(){
		$Value = self::$isNew ? '' : self::$oldField['innertext'];
		trbasic('��Ŀid������Դ�ڴ��뷵��ֵ','fmdata[innertext]',$Value,'textarea',array('guide' => '����дPHP���룬ʹ��return array(��������);�õ�ѡ�����ݡ�������Ĭ��Ϊ������Ŀ����ϵ�����з��ࡣ<br>��ʹ����չ�������붨�嵽'._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php',));
	}
	# ��֮�����ѡ��ʽ
    protected static function _fm_cnmode(){
		# ���ͷ���ֻ�ܵ�ѡ
		if((self::$SourceType == 'pusharea') && !empty(self::$oldField['ename']) && in_array(self::$oldField['ename'],array('classid1','classid2'))) return;
		
		$arr = array(0 => '��ѡ',);
		for($i = 2;$i < 21;$i ++) $arr[$i] = "{$i}ѡ";
		$Value = self::$isNew ? 0 : self::$oldField['cnmode'];
		
		
		if(in_array(self::$SourceType,array('mchannel')) && !empty(self::$oldField['iscommon'])){ # ��Աͨ���ֶε�ѡ��ģʽ���ɸ���
			trbasic('�����ѡ��ʽ','',$arr[$Value],'');
			trhidden('fmdata[cnmode]',$Value);
		}else trbasic('�����ѡ��ʽ','',makeradio('fmdata[cnmode]',$arr,$Value),'',array('guide'=>'���������������ѡ��Ӱ��ĳЩ��ѯЧ�ʣ���ѡ���ѡ���л����������ݿ�Ĵ������ݡ�<br>��ѡתΪ��ѡʱ����ֻ������һ��ԭ��ѡ���Ҳ��ɻָ���'));
	}
	# ��֮����ѡ���б�ģʽ
    protected static function _fm_mode(){
		$Value = self::$isNew ? 0 : self::$oldField['mode'];
		$vmodearr = array('0' => '��ͨѡ���б�','2' => '�༶����','3' => '�༶����(ajax)',);
		trbasic('����ѡ���б�ģʽ','',makeradio('fmdata[mode]',$vmodearr,$Value),'');
	}
	
}
