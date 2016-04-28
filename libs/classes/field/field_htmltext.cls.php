<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ��cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_htmltext extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_mode();
		self::_fm_min_max();
		self::_fm_editor_height();
		parent::_fm_autoCompression();
		self::_fm_rpid();
		self::_fm_filter();
		self::_fm_wmid();
		self::_fm_cfgs();
		
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		self::$newField['mode'] = ( empty(self::$fmdata['mode']) ? 0 : (int) self::$fmdata['mode'] );
		foreach(array('min','max') as $key){
			self::$newField[$key] = max(0,intval(self::$fmdata[$key]));
			self::$newField[$key] = empty(self::$newField[$key]) ? '' : self::$newField[$key];
		}
	}
	# ��֮�༭��ģʽ
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : (int) self::$oldField['mode'];
		trbasic('�༭����ʾģʽ','',makeradio('fmdata[mode]',array(0 => '����༭��',1 => '���ױ༭��', 2 => '�����༭��'),$Value),'');
	}
	# ��֮����ֵ�ֽڳ�������
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : self::$oldField['min'];
		$ValueMax = empty(self::$oldField['max']) ? '' : self::$oldField['max'];
		trrange('����ֵ�ֽڳ�������', array('fmdata[min]',$ValueMin,'','&nbsp; -&nbsp; ',5, 'validate' => makesubmitstr('fmdata[min]',0,'int')),
								array('fmdata[max]',$ValueMax,'','',5, 'validate' => makesubmitstr('fmdata[max]',0,'int')));
	}
	# ��֮�ı��༭���߶�
    protected static function _fm_editor_height(){
		$Value = self::$isNew ? 500 : self::$oldField['editor_height'];
		trbasic('�༭����ʾ�߶�', 'fmdata[editor_height]', (int)$Value, 'text',array('guide'=>'��λ������'));
        
		$auto_page_size = self::$isNew ? 5 : @self::$oldField['auto_page_size'];
		trbasic('Ĭ���Զ���ҳ��С', 'fmdata[auto_page_size]', (int)$auto_page_size, 'text',array('guide'=>'��λ��KB'));
	}
	
	# ��֮�ύǰ����
    protected static function _fm_filter(){
		$Value = self::$isNew ? 0 : self::$oldField['filter'];
    	trbasic('�ύǰ����','fmdata[filter]',makeoption(array(0=>'������', 1=>'�����Թ���HTML'),$Value),'select');
	}
	
	
	
	
}
