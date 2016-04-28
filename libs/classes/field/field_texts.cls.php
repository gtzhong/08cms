<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_texts extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_min_max();
		self::_fm_innertext();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		
		# ����ֵ�ֽڳ�������
		foreach(array('min','max') as $key){
			self::$newField[$key] = max(0,intval(self::$fmdata[$key]));
		}
		
		# ÿ����¼��������
		self::$newField['innertext'] = str_replace("\r","",empty(self::$fmdata['innertext']) ? '' : trim(self::$fmdata['innertext']));
		
	}
	# ��֮��������ļ�¼����
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : self::$oldField['min'];
		$ValueMax = empty(self::$oldField['max']) ? '' : self::$oldField['max'];
		trrange('����ֵ�ֽڳ�������', array('fmdata[min]',$ValueMin,'','&nbsp; -&nbsp; ',5, 'validate' => makesubmitstr('fmdata[min]',0,'int',0,10,'int')),
								array('fmdata[max]',$ValueMax,'','',5, 'validate' => makesubmitstr('fmdata[max]',0,'int',0,10,'int')));
	}
	# ��֮��ѡ������:ÿ����¼��������
    protected static function _fm_innertext(){
		$Value = self::$isNew ? '' : self::$oldField['innertext'];
		$guide = '�趨ÿ����¼�������ÿ��һ�ÿ�еĸ�ʽΪ�������|��С�ֽ�|����ֽ�';
		trbasic('ÿ����¼��������','fmdata[innertext]',$Value,'textarea',array('guide'=>$guide, 'validate' => makesubmitstr('fmdata[innertext]',1,0,2)));
	}
	
	
	
}
