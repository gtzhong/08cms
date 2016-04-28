<?php
/* 
** Ȩ�޷�����ط�������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_PermissionBase{
	/**
	 * ����Ȩ�޷�����������Ȩ�ޣ�������Ȩ�޵�Ȩ��
	 *
	 * @param  array  $info  ��Ա��������Ϣ����Ҫ������Ա����Ϣ����֤��Ϣ
	 * @param  int    $pmid  Ȩ�޷���ID
	 * @return string $str   ��Ȩ��ʱ������ԭ����Ȩ���򷵻�''
	 */
	function noPmReason($info = array(),$pmid=0){
		$str = '';
		if($re = _mem_noPm($info,$pmid)){//��Ȩ�ޣ�����ԭ��
			if(!empty($re['nouser'])){
				$str = '��Աδ��¼��Ȩ��';
			}elseif(!empty($re['nouser'])){
				$str = '��Ч��Ȩ�޷���';
			}else{
				if(!empty($re['mctids_and']) || !empty($re['mctids_or'])){
					$_str = '';
					$mctids = !empty($re['mctids_or']) ? $re['mctids_or'] : $re['mctids_and'];
					$mctypes = cls_cache::Read('mctypes');
					foreach($mctids as $k) empty($mctypes[$k]) || $_str .= ($_str ? (!empty($re['mctids_or']) ? '"��"' : '"��"') : '').$mctypes[$k]['cname'];
					$_str && $str .= '<br>��Ҫ������֤��'.$_str;
				}
				if(!empty($re['nougids'])){//����Ļ�Ա��
					if(in_array('-',$re['nougids'])){
						$_str = '<br>���������л�Ա��';
					}else{
						$_str = '';
						$grouptypes = cls_cache::Read('grouptypes');
						foreach($grouptypes as $k => $v){
							if(!$v['forbidden']){
								$ugs = cls_cache::Read('usergroups',$k);
								foreach($ugs as $x => $y) in_array($x,$re['nougids']) && $_str .= ($_str ? '"��"' : '').$y['cname'];
							}
						}
					}
					$_str && $str .= '<br>��������Ȩ�ޣ�'.$_str;
				}
				if(!empty($re['inugids'])){//��ֹ�Ļ�Ա��
					$_str = '';
					$grouptypes = cls_cache::Read('grouptypes');
					foreach($grouptypes as $k => $v){
						if(!$v['forbidden']){
							$ugs = cls_cache::Read('usergroups',$k);
							foreach($ugs as $x => $y) in_array($x,$re['inugids']) && $_str .= ($_str ? '"��"' : '').$y['cname'];
						}
					}
					$_str && $str .= '<br>�����鱻��ֹ��'.$_str;
				}
				$str && $str = substr($str,4);
			}
		}
		return $str;
	}
	
	
}