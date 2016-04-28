<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_text extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_length();
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_vdefault();
		self::_fm_mode();
		self::_fm_min_max();
		self::_fm_nohtml();
		self::_fm_mlimit();
		self::_fm_regular();
		self::_fm_rpid();
		self::_fm_search();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		
		# ���ݱ��ֶγ���
		if(self::$isNew || isset(self::$fmdata['length'])){
			self::$newField['length'] = empty(self::$fmdata['length']) ? 10 : min(255,max(1,intval(self::$fmdata['length'])));
		}else{
			self::$newField['length'] = self::$oldField['length'];
		}
		
		# ����ֵ�ֽڳ�������
		foreach(array('min','max') as $key){
			self::$newField[$key] = max(0,intval(self::$fmdata[$key]));
			self::$newField[$key] = empty(self::$newField[$key]) ? '' : self::$newField[$key];
		}
		
		# Ĭ��ֵ
		self::$newField['vdefault'] = empty(self::$fmdata['vdefault']) ? '' : trim(self::$fmdata['vdefault']);
		
	}
	# ��֮������ֶγ���
    protected static function _fm_length(){
		if(in_array(self::$SourceType,array('mchannel')) && !empty(self::$oldField['iscommon']) && !empty(self::$SourceID)) return;
		$Value = self::$isNew ? '' : self::$oldField['length'];
		trbasic('���ݱ��ֶγ���','fmdata[length]',$Value,'text',array('guide'=>'�趨��Χ1-255', 'validate' => makesubmitstr('fmdata[length]',0,0,1,255,'int')));
	}
	# ��֮Ĭ������ֵ
    protected static function _fm_vdefault(){
		$Value = self::$isNew ? '' : self::$oldField['vdefault'];
		trbasic('Ĭ������ֵ','fmdata[vdefault]',$Value,'text',array('w'=>50));
	}
	# ��֮���ؼ�ģʽ
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : 1;
		trbasic('���ؼ�ģʽ','',makeradio('fmdata[mode]',array(0 => '����ߴ�',1 => '�Ӵ�ߴ�'),$Value),'');
	}
	# ��֮����ֵ�ֽڳ�������
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : self::$oldField['min'];
		$ValueMax = empty(self::$oldField['max']) ? '' : self::$oldField['max'];
		trrange('����ֵ�ֽڳ�������', array('fmdata[min]',$ValueMin,'','&nbsp; -&nbsp; ',5, 'validate' => makesubmitstr('fmdata[min]',0,'int')),
								array('fmdata[max]',$ValueMax,'','',5, 'validate' => makesubmitstr('fmdata[max]',0,'int')));
	}
	# ��֮�����ʽ����
    protected static function _fm_mlimit(){
		$limitarr = array(
			'' => '���޸�ʽ',
			'int' => '����',
			'number' => '����',
			'letter' => '��ĸ',
			'numberletter' => '��ĸ������',
			'tagtype' => '��ĸ��ʼ����ĸ�����»���',
			'date' => '����',
			'email' => 'E-mail',
		);
		$Value = self::$isNew ? '' : self::$oldField['mlimit'];
		trbasic('�����ʽ����','fmdata[mlimit]',makeoption($limitarr,$Value),'select');
	}
	
	
	
		
}
