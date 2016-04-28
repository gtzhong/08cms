<?php
/**
* ��ǩ����Ĳ�����
*/

defined('M_COM') || exit('No Permission');
abstract class cls_TagAdminBase{
	
	/**
	 * �ַ���ת�ɱ�ǩ���飨����ת�ɷǷ�װ��ʶ���ݣ�
	 * ������settingԪ��ֵ��Ԫ�أ�cname,ename,tclass,template,vieworder,disabled
	 * ע��$tagArr['tag_type']Ϊ���͵ı�ǩ���ͣ�non-encapsulatedΪ�Ƿ�װ��ʶ��encapsulated Ϊ��װ��ʶ�������Զ���
	 *
	 * @param  string $string Ҫת�����ַ���
	 * @return array  $tagArr ת����ı�ǩ��������
	 */
	public static function CodeToTagArray($string)
	{
		$tagArr = array('old_str' => $string);
		if(empty($string)) return $tagArr;
		$index = array('cname', 'ename', 'tclass', 'template', 'vieworder', 'disabled');
		preg_match('@\{(u|c|p)\$([^\s]+?)(.*)\]\s*\}(.*)\{/\1\$\2\}@isU', $string, $matches);
		$tagArr['ename'] = @trim($matches[2]);
		if(!empty($matches[3]))
		{
			$matches[3] .= ']';
			preg_match_all('@\[(\w+)=(.*)/\]@isU', $matches[3], $setting);
			if(!empty($setting[1]) && !empty($setting[2]))
			{
				$tagArr['template'] = $matches[4];
				$len = count($setting[1]);
				for($i = 0; $i < $len; ++$i)
				{
					$k = trim($setting[1][$i]);
					$v = trim($setting[2][$i]);
					if(in_array($k, $index)) {
						$tagArr[$k] = $v;
					} else {
						$tagArr['setting'][$k] = $v;
					}
				}
				// ��ʶΪ�Ƿ�װ��ʶ
				$tagArr['tag_type'] = 'non-encapsulated';
			}
		} else {
			// ����֮ǰ�ķ�װ��ʶ
			if(preg_match("/\{(u|c|p)\\$(.+?)(\s|\})/is",$string,$matches))
			{
				$tagArr = cls_cache::Read($matches[1] . 'tag', $matches[2]);
				// ��ʶΪ��װ��ʶ
				$tagArr['tag_type'] = 'encapsulated';
				$tagArr['old_str'] = $string;
			}
		}
	
		return $tagArr;
	}
	
}
