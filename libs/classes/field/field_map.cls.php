<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_map extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_mode();
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_vdefault();
		self::_fm_search();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		if(self::$newField['vdefault'] = empty(self::$fmdata['vdefault']) ? '' : trim(self::$fmdata['vdefault'])){
			list($lng, $lat) = explode(',', self::$newField['vdefault']);
			if(is_numeric($lng) && is_numeric($lat)){
				$lng = floatval($lng); $lat = floatval($lat);
				if($lng < -90 || $lng > 90 || $lat < -180 || $lat > 180){
					self::$newField['vdefault'] = '';
				}else{
					self::$newField['vdefault'] = $lng.','.$lat;
				}
			}else{
				self::$newField['vdefault'] = '';
			}
		}
		# ���Ĭ��ֵ����ϵͳĬ��ֵ����������ֵ�����ڴ��
		if(self::$newField['vdefault'] == cls_env::GetG('init_map')) self::$newField['vdefault'] = '';
	}
	# ��֮��ͼ����
    protected static function _fm_mode(){
		trbasic('��ͼ����','',makeradio('fmdata[mode]',array(0 => 'baidu',),0),'');
	}
	# ��֮Ĭ������ֵ
    protected static function _fm_vdefault(){
		$Value = self::$isNew ? '' : self::$oldField['vdefault'];
		if(empty($Value)) $Value = cls_env::GetG('init_map');
		trbasic('��ͼ��ʼ��λ����','',"<input class=\"btnmap\" type=\"button\" onmouseover=\"this.onfocus()\" onfocus=\"_08cms.map.setButton(this,'marker','fmdata[vdefault]','','13','$Value');\" /> <label for=\"fmdata[vdefault]\">γ��,���ȣ�</label><input type=\"text\" id=\"fmdata[vdefault]\" name=\"fmdata[vdefault]\" value=\"$Value\" style=\"width:150px\">",'',
		array('guide'=>'�����ʼ��λλ�ã�γ��,���ȡ���������ΪϵͳĬ��λ��','w'=>50));
	}	
	
}
