<?php
/**
* ��ģ��(�ļ�/��ǩ/��Ա���Ľű�)�еı�ǩ(�����ǩ/ԭʼ��ǩ/���ϱ�ǩ)����Ϊ��ִ�е�PHP��������ΪPHP�����ļ�
*/
class cls_Refresh{
	
	# ��һ����Դ(ģ��/��ǩ/PHP�ļ�)�ڵ�ģ���ǩ����Ϊ��ִ�е�PHP����ΪPHP�����ļ�������������
	public static function OneSource($ParseSource,$SourceType = 'tplname'){
		
		if(!($PHPCacheFileName = cls_Parse::PHPCacheFileName($ParseSource,$SourceType))) return false;
		switch($SourceType){
			case 'tplname':	# ������ͨģ��
				$TplContent = cls_tpl::load($ParseSource);
				self::_OneSourceRefresh($TplContent,$PHPCacheFileName);
			break;
			case 'js':	# ��̬JS��ǩ����
			case 'adv':	# ��涯̬���õı�ǩ����
				self::_OneSourceRefresh($ParseSource,$PHPCacheFileName);
			break;
			case 'fragment':	# ��Ƭ��ǩ����
				if(!empty($ParseSource['tclass'])){ # ���ϱ�ʶ
					self::_OneSourceRefresh($ParseSource,$PHPCacheFileName);
				}else{ # �����ʶ�е�templateֻ��ģ������
					$TplContent = $ParseSource['template'];
					self::OneSource($TplContent);
				}
			break;
			case 'adminm': # ���ڻ�Ա����ģ�建�棬����ʹ�ø��ϱ�ʶ�������ʶ
				_08_FilesystemFile::filterFileParam($ParseSource); # Ŀǰֻ�����ڻ�Ա���ĵĸ�Ŀ¼��
				$sfile = MC_ROOTDIR.$ParseSource; # ?????����������Ҫ����
				$TplContent = @file2str($sfile);
				$TplContent = cls_tpl::ReplaceRtag($TplContent);
				self::_OneSourceRefresh($TplContent,$PHPCacheFileName);
			break;
		}
		return is_file($PHPCacheFileName) ? $PHPCacheFileName : false;
	}
	
	# ����һ���������ݵ���Դ��
	# ��$sourceΪ��������ԴΪ����C��ʶ������Ϊҳ��ģ���ִ�
	# $SavePathFile:���ͺ����ݵı����ļ�(��ȫ·��)
	private static function _OneSourceRefresh($source,$SavePathFile){
		if(!$SavePathFile) return;
		if(!$source){
			str2file('',$SavePathFile);
		}else{
			SetRefreshVars();//��ʼ����������ջ
			if(is_array($source)){
				$str = self::_OneOpenCtag($source);
			}else{
				$str = self::_AllBtagsInStr($source);
				$str = self::_AllCtagsInStr($str);
			}
			$str = self::_AllPseudoCode($str);
			str2file($str,$SavePathFile);
		}
	}
	
	# �����ִ������е�C��ǩ
	private static function _AllCtagsInStr($str){
		if(!$str) return $str;
		$str = self::_CloseCtagsToOpenStr($str);//ת������ķ�װ��ʶ=>���ű�ʶ
		$str = preg_replace("/\{c\\$(.+?)\s+(.*?)\{\/c\\$\\1\}/ies","self::_OneOpenCtagStr('\\1','\\2')",$str);
		return $str;
	}
	
	# ���ִ������з�װC��ǩתΪ�ִ���ʽ�Ŀ��ű�ǩ
	private static function _CloseCtagsToOpenStr($str){//���ַ����еķ�װ��ʶ=>���ű�ʶ
		if(!$str) return $str;
		$str = preg_replace("/\{c\\$([^\s]+?)\}/ies","self::_OneCloseCtagToOpenStr('\\1')",$str);
		return $str;
	}
	
	# ������װC��ʶ=>�ִ���ʽ�Ŀ��ű�ǩ
	private static function _OneCloseCtagToOpenStr($tname){//������װ��ʶ=>���ű�ʶ
		if(!($tag = cls_cache::ReadTag('ctag',$tname)) || empty($tag['tclass'])) return '{Error_c_$'.$tname.'}';//����һ�£�������ظ�����
		$template = empty($tag['template']) ? '' : $tag['template'];
		$str = '{c$'.$tname;//��ʼ��
		foreach(array('vieworder','template',) as $k) unset($tag[$k]);//ȥ��$tag�е�ĳЩ����Ҫ���ڽ���������
		foreach($tag as $k => $v) $str .= ' ['.$k.'='.$v.'/]';
		$str .= "}";//������ֹ
		$str .= self::_CloseCtagsToOpenStr($template);//�ݹ鴦��ģ���ڵķ�װ��ʶ
		$str .= '{/c$'.$tname.'}';//���������
		return $str;
	}
	
	# ���͵����ִ���ʽ�Ŀ�����C��ǩ
	private static function _OneOpenCtagStr($tname,$tstr){
		$tstr = RefreshStripSlashes($tstr);
		$tag = self::_OpenTagStrToConfig($tname,$tstr);
		if(empty($tag) || empty($tag['tclass'])) return '{Error_c_$'.$tname.'}';//�Ƿ���ʶֻ��ʾ��ʶ��
		return self::_OneOpenCtag($tag);
	}
	
	# �������ִ���ʽC��ǩתΪ�����ʽ��ǩ
	private static function _OpenTagStrToConfig($tname,$tstr){
		$tag = array();
		if(preg_match("/^\s*(.+?)\/\]\s*\}/is",$tstr,$matches)){
			if($str = $matches[0]){
				if(preg_match_all("/\[\s*(.+?)\s*\=\s*(.*?)\s*\/\]/is",$str, $matches)){
					$tag['ename'] = $tname;
					foreach($matches[1] as $k => $v) $tag[$v] = $matches[2][$k];
					$tag['template'] = preg_replace("/^\s*(.+?)\/\]\s*\}/is",'',$tstr);
				}
			}
		}
		return $tag;
	}
	
	# ��������(��������)���ϱ�ǩ
	private static function _OneOpenCtag($tag){
		if(empty($tag) || !is_array($tag) || !empty($tag['disabled'])) return '';
		$tname = $tag['ename'];
		$tc = $tag['tclass'];
		$val_var = empty($tag['val']) ? 'v' : $tag['val'];
		if(!in_array($tc,array_keys(cls_Tag::TagClass())) && !in_array($tc,array('advertising',))) return '';
		if(!empty($tag['js']) || !empty($tag['pmid'])){ # �����Ϊjs���ñ�ǩ���ǩ��������Ȩ�޷�����������Ϊjs
			
			# is_p�������ã�1)��js����api�Ƿ�Ҫ��ʼ��ǰ��Ա 2)�Ƿ���Ҫjsҳ�滺��
			if($tc == 'regcode'){
				$is_p = 1;
			}elseif($tc == 'member'){
				if(@$tag['id'] == -1){
					$is_p = 1;
				}
			}elseif(!empty($tag['pmid']) && in_array($tc,cls_Tag::TagClassByType('pmid'))){
				$is_p = 1;
			}
			
			# ������������
			$TagCacheName = substr(md5(var_export($tag,TRUE)),0,10);
			
			# ȥ��js��ǣ���pmid��棬ʹ�ñ�ǩ��js���ý���ʱ�����볣���ǩ�������̡�
			if(!empty($tag['pmid'])) $tag['jspmid'] = $tag['pmid'];
			unset($tag['js'],$tag['pmid']);
						
			# ��ͬ������ģ�建�棬�����ܱ�����ģ�建�����
			cls_CacheFile::cacSave($tag,'js_tag_'.$TagCacheName,cls_Parse::TplCacheDirFile('',2));
			
			$ReturnCode = '';
			if ( empty($tag['hidden']) )
			{
				$jsfile = 'tools/js.php?'.(empty($is_p) ? '' : 'is_p=1&').'tname='.$TagCacheName;
				$ReturnCode .= '<? $js_file=cls_Parse::$cms_abs.\''.$jsfile.'\';'.($tc == 'regcode' ? ' ?>' : 'if($_ActiveParams = cls_Parse::Get(\'_a\')) foreach($_ActiveParams as $_k_ => $_v_){ $_v_ && $js_file.= \'&data[\'.$_k_.\']=\'.rawurlencode($_v_);} ?>');
				$ReturnCode .= '<script type="text/javascript" src="<?=$js_file?>"></script><? unset($_k_,$_v_,$js_file);?>';
			}
			else
			{
                if (!empty($tag['jsVarname']))
                {
                    $jsVarname = preg_replace('/[^\w]/', '', $tag['jsVarname']);
                }
                
                if (empty($jsVarname))
                {
                    $jsVarname = '_08JSHidden';
                }
                
				$ReturnCode .= "<script type=\"text/javascript\"> var {$jsVarname} = '{$TagCacheName}'; </script>";
			}
					
			return $ReturnCode;
		}elseif(in_array($tc,array('regcode',))){//������$tag['val']
			$ReturnCode = '<? if(cls_Parse::Tag(array('.self::_CtagCongfigToStr($tag).'))){ ?>';
			$ReturnCode .= self::_AllBtagsInStr($tag['template']);
			$formIDStr = '';
			if ( preg_match('@_08_HTML::getCode.*\(.+, [\'|"](.*)[\'|"]@isU', $tag['template'], $formID) )
			{
				$formIDStr .= '<script type="text/javascript"> if( !'.$formID[1].' ) { var ' . $formID[1] . ' = _08cms.validator(\'' . $formID[1] . '\'); } </script>';
			}
			$ReturnCode .= '<? } else { echo "'.addcslashes($formIDStr, '"').'"; } ?>';
			return $ReturnCode;
		}elseif(in_array($tc,cls_Tag::TagClassByType('string'))){
			$ReturnCode = '<?=cls_Parse::Tag(array('.self::_CtagCongfigToStr($tag).'))?>';
			$ReturnCode .= '<?'.self::_ExtractMpConfig($tag).'?>';# ����Ŀǰ�ķ�ҳ����
			return $ReturnCode;
		}elseif(in_array($tc,cls_Tag::TagClassByType('list'))){
			$TagListResultVar = '_'.$tname;
			$t = self::_OuterOfRowBlock($tag['template']);
			$ReturnCode = '<? if($'.$TagListResultVar.'=cls_Parse::Tag(array('.self::_CtagCongfigToStr($tag).'))){';# �����ǩ������->��ʼ
			$ReturnCode .= self::_ExtractMpConfig($tag);# ����Ŀǰ�ķ�ҳ����
			$ReturnCode .= $t[1] ? '?>'.$t[1].'<? ' : '';
			$ReturnCode .= 'foreach($'.$TagListResultVar.' as $'.$val_var.'){ ';
			if($tc == 'advertising') $ReturnCode .= 'echo "<!--$'.$val_var.'[aid]-->";';
			$ReturnCode .= 'cls_Parse::Active($'.$val_var.');?>';//�����˾��������֮�󼤻�
			SetRefreshVars($val_var);//��ѭ��֮ǰ����������
			$t[2] = self::_AllBtagsInStr($t[2]);
			$t[2] = self::_AllCtagsInStr($t[2]);
			QuitRefreshVars();
			$ReturnCode .= $t[2];
			$ReturnCode .= '<? cls_Parse::ActiveBack();} unset($'.$TagListResultVar.',$'.$val_var.');?>';
			$ReturnCode .= $t[3];
			unset($t);
			$ReturnCode .= '<? }else{ '.self::_ExtractMpConfig($tag).' } ?>';# �����ǩ������->����
			return $ReturnCode;
		}else{
			$ReturnCode = '<? if($'.$val_var.'=cls_Parse::Tag(array('.self::_CtagCongfigToStr($tag).'))){';
			$ReturnCode .= 'cls_Parse::Active($'.$val_var.');?>';
			if(!empty($tag['jspmid']) && in_array($tc,cls_Tag::TagClassByType('pmid'))){
				$str = self::_TemplateByPmid($tag['template'],$tag['jspmid']);
			}else $str = $tag['template'];
			SetRefreshVars($val_var);
			$str = self::_AllBtagsInStr($str);
			$str = self::_AllCtagsInStr($str);
			QuitRefreshVars();
			
			$ReturnCode .= $str;
			unset($str);
			$ReturnCode .= '<? cls_Parse::ActiveBack();} unset($'.$val_var.');?>';
			return $ReturnCode;
		}
	}
	# ����Ŀǰ�ķ�ҳ����(����ҳ��Ϣ$_mp�еı�����Ϊԭʼ��Ϣ����������ҳ��extract)
	private static function _ExtractMpConfig($tag){
		if(!in_array($tag['tclass'],cls_Tag::TagClassByType('mp')) || empty($tag['mp'])) return '';
		return "extract(cls_Parse::Get('_mp'),EXTR_OVERWRITE);";
	}
	
	# ����Ȩ��չʾ��ͬ���ݵ�ģ��
	private static function _TemplateByPmid($template,$pmid=0){
		if(!$pmid) return;
		$arr = explode('[#pm#]',$template);
		return '<? if(cls_Parse::Pm('.$pmid.')){ ?>'.$arr[0].'<? }else{ ?>'.(empty($arr[1]) ? 'NoPermission' : $arr[1]).'<? } ?>';
	}
	
	# �����������ʽC��ǩƴװ���ִ���ʽ��ģ��PHP��������
	private static function _CtagCongfigToStr($tag){
		$re = '';
		foreach($tag as $k => $v){
			if(!in_array($k,array('cname','val','template','vieworder','disabled',))){
				if(in_array($k,array('tname','color')) && preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/is",$v)){
					//tname��color��ֱ�����������xxx����ȡ�ϲ��ʶ�����ݣ�$v['xxx']������һ���ر����ڡ�
					$v = self::_TagResultVar() ? '$'.self::_TagResultVar().'['.$v.']' : '$'.$v; 
				}
				$re .= is_numeric($v) ? "'$k'=>$v," : "'$k'=>\"$v\",";
			}
		}
		return $re;
	}
	
	# �����ִ��е�ԭʼ��ǩ(�ų���Ƕ�ĸ��ϱ�ǩ��PHP�����е�ԭʼ��ǩ)
	private static function _AllBtagsInStr($str){
		if(!$str) return $str;
		$hiddens = self::_HiddenInnerCode($str);
		/*$str = preg_replace("/<\\?(?!php\\s|=|\\s)/i", '<?=\'<?\'?>', $str);//����<?xml �ȷ�PHP���*/
		$str = str_replace('{else}','{else }',$str);//α�����е�{else}�ܿ�ԭʼ��ʶ��ʽ
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/is",self::_TagResultVar() ? '{$'.self::_TagResultVar().'[\\1]}' : '{$\\1}', $str);//��{xxx}����{$v[xxx]}��{$xxx}���ӵ�ǰ����ȡֵ
		$str = preg_replace("/\{((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)\}/es", "self::_AddQuote('<?=\\1?>')", $str);/*��{$xxx}תΪ<?=$xxx?>*/
		$str = self::_ViewInnerCode($str,$hiddens);
		return $str;
	}
	
	# ����[row]***[/row]����֮��Ĳ���ģ���б�ǩ
	# ����row��Ĭ��ȫ��Ϊrow���飬row�����ڲ��������б�ѭ�����������ﴦ��
	# ��Ϊ���������ݵĴ��ڣ���ʹ������ʶ��Ҳ��Ҫ��ʼ����������ջ
	# ����array(1 => row����ǰ�Ĳ���,2 => row�ڲ�����,3 => row�����Ĳ���)
	private static function _OuterOfRowBlock($str){
		if(!$str) return $str;
		$hiddens = self::_HiddenInnerCode($str);
		$narr = array(1 => '',2 => $str,3 => '',);
		if(preg_match("/^(.*?)\[row\](.*)\[\/row\](.*?)$/is",$str,$matches)){
			unset($matches[0]);
			foreach($matches as $x => $y){
				if($y){
					$y = self::_ViewInnerCode($y,$hiddens);
					if($x != 2){
						$y = self::_AllBtagsInStr($y);
						$y = self::_AllCtagsInStr($y);
					}
					$narr[$x] = $y;
				}
			}
		}else $narr[2] = self::_ViewInnerCode($narr[2],$hiddens);
		return $narr;
	}
	
	# ���ִ��еĸ��ϱ�ʶ��PHP���������ز��ݴ�����
	private static function _HiddenInnerCode(&$str){ # ע��ʹ�����ô���
		$na = array(
			'TAG' => "/\{c\\$(\w+)\s+(.*?)\{\/c\\$\\1\}/is",
			'PHP' => "/<\\?(php|=|\\s)(.*?)($|\\?>)/is",
		);
		$re = array();
		foreach($na as $k => $v){
			if(preg_match_all($v,$str,$matches)){//ֻ����Ƿ�װ��ʶ
				$re[$k] = $matches[0];
				$re[$k] = RefreshMultisort($re[$k]);
				foreach($re[$k] as $kk => $vv) $str = str_replace($vv,"_{$k}_{$kk}_",$str);
			}
		}
		return $re;
	}
	# �ָ��ִ������صĸ��ϱ�ʶ��PHP����
	private static function _ViewInnerCode($str,$arr){
		if(!$str || !$arr) return $str;
		foreach(array('PHP','TAG') as $k){
			if(!empty($arr[$k])){
				foreach($arr[$k] as $kk => $vv) $str = str_replace("_{$k}_{$kk}_",$vv,$str);
			}
		}
		return $str;
	}
	
	# α���봦��֧�֣�{if}��{else}��{loop}��{/if}��{/loop}
	private static function _AllPseudoCode($str){
		$str = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "self::_stripvtags('<? echo \\1; ?>','')", $str);
		$str = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/ies", "self::_stripvtags('\\1<? if(\\2) { ?>\\3','')", $str);
		$str = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "self::_stripvtags('\\1<? } elseif(\\2) { ?>\\3','')", $str);
		$str = preg_replace("/\{else\s*\}/i", "<? } else { ?>", $str);
		$str = preg_replace("/\{\/if\s*\}/i", "<? } ?>", $str);
		$str = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "self::_stripvtags('<? if(is_array(\\1)) foreach(\\1 as \\2) { ?>','')", $str);
		$str = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "self::_stripvtags('<? if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>','')", $str);
		$str = preg_replace("/\{\/loop\s*\}/i", "<? } ?>", $str);
		/*$str = preg_replace("/\{\?(.*?)\?\}/is", "<?\\1?>", $str);//????*/
		$str = preg_replace("/\{\\\$ (.*?)\}/is", "{\$\\1}", $str);//����֮ǰ�汾����php��ʹ�õ�{$ xx}���û���{$xx}
		return $str;
	}
	private static function _AddQuote($var){
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
	private static function _stripvtags($expr, $statement) {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}
	
	# ��ǰ��ǩ�ĵ�����¼������(��ǩ�е�val)�������κα�ǩʱΪ''
	private static function _TagResultVar(){
		return cls_env::GetG('_a_var');
	}
		
	
}
