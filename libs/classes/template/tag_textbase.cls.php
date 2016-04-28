<?PHP
/**
* [�ı�����] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_TextBase extends cls_TagParse{
	
	protected $_TextContent = '';			# ��ҳ���ı�����
	
	protected function TagReSult(){
		return $this->TagResultText();
	}
	
	# ��ʼ����ǰ��ǩ
	protected function _TagInit(){
		$this->_TextContent = $this->tag['tname'];
		if(!$this->_TextContent) return '';
		unset($this->tag['tname']);
	}
	
	protected function TagResultText(){
		if($this->_TextContent){
			if(!empty($this->tag['dealhtml'])){
				switch($this->tag['dealhtml']){
				case 'clearhtml':
                    if (isset($this->tag['dealhtml_tags']))
                    {
                        if (is_string($this->tag['dealhtml_tags']))
                        {
                             $this->tag['dealhtml_tags'] = explode('|', $this->tag['dealhtml_tags']);
                             $this->tag['dealhtml_tags'] = array_fill_keys($this->tag['dealhtml_tags'], array('on'));
                        }
                        $tags = array_map('strtolower', array_keys($this->tag['dealhtml_tags']));
                        if (isset($tags['all']))
                        {
                            $this->_TextContent = cls_string::HtmlClear($this->_TextContent);
                        }
                        elseif(!empty($tags))
                        {
                        	$textContentInstance = _08_Documents_HTML::getInstance($this->_TextContent);
                            $this->_TextContent = $textContentInstance->pQuery($tags)->remove();
                        }
                    }
                    else # ��ʱ�����������ݣ�����Ӱ��ͻ�����
                    {
                    	$this->_TextContent = cls_string::HtmlClear($this->_TextContent);
                    }
					
					break;
				case 'disablehtml':
					$this->_TextContent = mhtmlspecialchars($this->_TextContent);
					break;
				case 'safehtml':
					$this->_TextContent = cls_string::SafeStr($this->_TextContent);
					break;
                // ��ʱ������������
				case 'html_cleara': //��ɾ��������(+�����Թ���Html)
					$this->_TextContent = cls_string::SafeStr($this->_TextContent);
					//$this->_TextContent = preg_replace('/(<a).+>(.)+</a>/i',"\${1}",$this->_TextContent);
					$this->_TextContent = preg_replace("/<a [^>]*>|<\/a>/i","",$this->_TextContent);
					break;
				case 'html_decode':
					$this->_TextContent = cls_env::deRepGlobalValue($this->_TextContent);
					break;
				//case 'html_keepa': //������������(����ʱ������)
					//$this->_TextContent = strip_tags($this->_TextContent, "<a>");
					//$this->_TextContent = nl2br($this->_TextContent); // �ο�cls_string::HtmlClear($str)
					//break;
				}
			}
			if(!empty($this->tag['trim'])) $this->_TextContent = cls_string::CutStr($this->_TextContent,$this->tag['trim'],empty($this->tag['ellip']) ? '' : $this->tag['ellip']);
			if(!empty($this->tag['color'])) $this->_TextContent = "<font color='".$this->tag['color']."'>".$this->_TextContent."</font>";
			if(!empty($this->tag['badword'])) cls_Tag::BadWord($this->_TextContent);
			if(!empty($this->tag['wordlink'])) cls_Tag::WordLink($this->_TextContent);
			if(!empty($this->tag['face'])) cls_Tag::Face($this->_TextContent);
			if(!empty($this->tag['nl2br'])) $this->_TextContent = mnl2br($this->_TextContent);
			if(!empty($this->tag['randstr'])){
				$this->_TextContent = preg_replace("/\<br\s?\/\>/ie", "cls_Tag::RandStr(0)", $this->_TextContent);
				$this->_TextContent = preg_replace("/\<\/p\>/ie", "cls_Tag::RandStr(1)", $this->_TextContent); // </p>
				$this->_TextContent = preg_replace("/\<p\>/ie", "cls_Tag::RandStr(2)", $this->_TextContent); // <p>
			}
			if(!empty($this->tag['injs'])) $this->_TextContent = addcslashes($this->_TextContent, "'\\\r\n");
			if(!empty($this->tag['noimgwh'])){
				foreach(array('width','height','style') as $_k){ 
					$this->_TextContent = preg_replace('/(<img.*?)('.$_k.'=["\'][^>]{1,}["\']).*?([^>]+>)/is','$1$3',$this->_TextContent);
				}
			}
			if(defined('IN_MOBILE')){				
				$this->_TextContent = cls_atm::image2mobile($this->_TextContent,@$this->tag['maxwidth']);
				$this->_TextContent = cls_url::tag2atm($this->_TextContent,1);
			}
			
		}
		// ��ǩ�����ҳ,��������з�ҳ���
		if(empty($this->tag['mp'])){
			$this->_TextContent = preg_replace('/\[#.*?#\]/','',$this->_TextContent);
		} //var_dump($this->tag['mp']);
		return $this->_TextContent;
	}
	
	
	
	# ��ҳ����Ĳ�ͬ���͵Ĳ��첿��
	protected function TagCustomMpInfo(){
		
		self::$_mp['limits'] = 1;
		if($bodysarr = SplitHtml2MpArray($this->_TextContent)){
			$i = 0;
			foreach($bodysarr as $k => $v){
				if(!($k % 2) && !preg_match("/^[\s|��| |\&nbsp;|<p>|<\/p>|<br \/>]*$/is",$v)){
					$i++;
					self::$_mp['titles'][$i] = isset($bodysarr[$k-1]) ? $bodysarr[$k-1] : '';
					if($i == self::$_mp['nowpage']){
						$this->_TextContent = $v.'</p>';
					}
				}
			}
			if($i) self::$_mp['acount'] = $i;
		}
		if(isset(self::$_mp['titles'][self::$_mp['nowpage']])){
			self::$_mp['mptitle'] = self::$_mp['titles'][self::$_mp['nowpage']];
		}
		unset($bodysarr);
	}
	
	
}
