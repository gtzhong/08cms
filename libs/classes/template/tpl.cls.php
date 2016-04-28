<?php
/**
* ��ģ����صķ����㼯
* 
*/
class cls_tpl{
	
	// ����ģ��-��չģ��:����·��
	// flag: dir=��ǰģ��, base=����ģ��, get=��get:isbase�����ж�
	public static function rel_path($tname,$flag='dir'){
		$tplbase = cls_env::GetG('templatebase');
		if($flag=='get' && empty($tplbase)){ 
			$flag = 'dir'; // ��ǰ�ǻ���ģ��,��λ����ǰģ��
		}elseif($flag=='get'){
			$flag = cls_env::GetG('isbase') ? 'base' : 'dir';	
		}
		return M_ROOT.'template'.DS.cls_env::GetG('template'.$flag).DS.'tpl'.DS.$tname;
	}
	
	/**
	 * ����ģ��
	 *
	 * @param  string  $str  ģ����
	 * @param  int     $rt   �Ƿ��û�ҳ���ڵ������ǩ
	 * @return string  $str  ģ���ļ�����
	 */
	public static function load($tplname,$ReplaceRtag = true){
		_08_FilesystemFile::filterFileParam($tplname);
		$tpl = @file2str(self::TemplateTypeDir('tpl').$tplname);
		//��չģ��,����̳�:����ʹ����չģ��
		if($templatebase = cls_env::GetG('templatebase')){
			if(file_exists($path = self::rel_path($tplname))){
				$tpl = @file2str($path); 
			}
		}
		if($ReplaceRtag) $tpl = self::ReplaceRtag($tpl);
		return $tpl;
	}
	
	
	/**
	 * �滻ģ�������ִ��е������ǩ
	 *
	 * @param  string  $Content  	��Դ�ִ�
	 * @return string    
	 */
	public static function ReplaceRtag($Content){
		$Content = preg_replace("/\{tpl\\$(.+?)\}/ies", "self::rtagval('\\1')",$Content);
		return $Content;
	}
	
	/**
	 * ��ȡָ�������ǩ�е�ģ������
	 *
	 * @param  string  $tname  	�����ǩ����
	 * @return string  $str		���������ǩ�е�ģ������
	 */
	public static function rtagval($tname){
		$TplContent = '';
		if($rtag = cls_cache::ReadTag('rtag',$tname)){
			if(empty($rtag['disabled']) && isset($rtag['template'])){
				$TplContent = self::load($rtag['template'],true);
			}
		}else $TplContent = "{tpl\$$tname}"; # ʹ���˲����ڵ������ǩ
		return $TplContent;
	}
	
	/**
	 * ��ǰģ��Ŀ¼�в�ͬ�������ݵĴ洢��Ŀ¼
	 *
	 * @param  string  $Type		���ͣ�config(����)��tpl(ģ��ҳ�漰�����ǩ�ڵ�ģ��)��tag(ģ���ǩ)��function(ģ�庯��)��css(CssĿ¼)��js(JSĿ¼)
	 * @param  bool    $OnlySelf   	(true)������Ŀ¼���Ʊ�������(flase)��������Ŀ¼
	 * @return string  $str    		ģ��Ŀ¼��ͬ�������ݵĴ洢��Ŀ¼
	 */
	public static function TemplateTypeDir($Type = 'tpl',$OnlySelf = false){
		$css_dir = cls_env::GetG('css_dir');
		$js_dir = cls_env::GetG('js_dir');
		$TypeArray = array(
			'config' => 'config',
			'tpl' => 'tpl',
			'tag' => 'tag',
			'function' => 'function',
			'tpl_model' => 'tpl_model',
			'css' => $css_dir ? $css_dir : 'css',
			'js' => $js_dir ? $js_dir : 'js',
		);
		$TypeDir = empty($TypeArray[$Type]) ? '' : $TypeArray[$Type];
		if(!$OnlySelf){
			$templatedir = cls_env::GetG('templatedir');
			$templatebase = cls_env::GetG('templatebase'); //��չģ��,������Ŀ¼��λ������ģ��Ŀ¼
			if(!empty($templatebase) && !in_array($Type,array('js','css'))){
				$templatedir = $templatebase;
			} //echo $templatedir.", ";
			_08_FilesystemFile::filterFileParam($templatedir);
			$TypeDir = M_ROOT.'template'.DS.$templatedir.DS.($TypeDir ? $TypeDir.DS : '');
		}
		return $TypeDir;
	}
	
	/**
	 * ˵����
	 *
	 * @param  int     		$chid   �ĵ�ģ��ID
	 * @param  int     		$caid   ��ĿID
	 * @param  int		    $Nodemode  �Ƿ��ֻ���
	 * @return array   		�����ĵ����÷���     
	 */
	public static function arc_tpl($chid,$caid = 0,$Nodemode = 0){
		foreach(array('arc_tpls','ca_tpl_cfgs','arc_tpl_cfgs',) as $var){
			$$var = cls_cache::Read($Nodemode ? "o_$var" : $var);
		}
		if(!empty($ca_tpl_cfgs[$caid]) && !empty($arc_tpls[$ca_tpl_cfgs[$caid]])){
			return $arc_tpls[$ca_tpl_cfgs[$caid]];
		}elseif(!empty($arc_tpl_cfgs[$chid]) && !empty($arc_tpls[$arc_tpl_cfgs[$chid]])){
			return $arc_tpls[$arc_tpl_cfgs[$chid]];
		}else return array();
	}
	
	/**
	 * ��ͨ�÷�ʽ��tplcfgs�л�ȡ���ģ������
	 *
	 * @param  string  $type ����
	 * @param  array   $id   
	 * @param  string  $name ��ʶ
	 * @return string  $str  ���ص�ģ������
	 */
	public static function CommonTplname($type,$id,$name,$NodeMode = 0){
		$tplcfgs = cls_cache::Read($NodeMode ? 'o_tplcfgs' : 'tplcfgs');
		return empty($tplcfgs[$type][$id][$name]) ? '' : $tplcfgs[$type][$id][$name];
	}
	/**
	 * ���ĵ�ģ�ͻ���Ŀ�õ��ĵ�����ҳ��ģ������
	 *
	 * @param  array  $config ��Դ����(chid-ģ��id,caid-��Ŀid,addno-����ҳid,nodemode-�Ƿ��ֻ����)
	 * @return string  $str  ���ص�ģ������
	 */
	public static function SearchTplname($config = array()){
		$arc_tpl = self::arc_tpl(empty($config['chid']) ? 0 : $config['chid'],empty($config['caid']) ? 0 : $config['caid'],empty($config['nodemode']) ? 0 : $config['nodemode']);
		$re = @$arc_tpl['search'][empty($config['addno']) ? 0 : $config['addno']];
		return $re ? $re : '';
	}
	
	/**
	 * ��ȡ��Ŀ�ڵ�ģ������
	 *
	 * @param  string  $cnstr	��Ŀ�ڵ��ִ�
	 * @param  array   $cnode	�Ѽ���cntpl(�ڵ�����)�Ľڵ���Ϣ���Ѿ����ֳ��Ƿ��ֻ���
	 * @param  string  $addno	����ҳ
	 * @param  string  $tn		��������ģ�壬��rsstplָ��rssģ��
	 * @return string  $str		���ص�ģ������
	 */
	public static function cn_tplname($cnstr,&$cnode,$addno=0,$tn=''){
		if(!$tn){
			$addno = max(0,intval($addno));
			return empty($cnode['cfgs'][$addno]['tpl']) ? '' : $cnode['cfgs'][$addno]['tpl'];
		}else return empty($cnode[$tn]) ? '' : $cnode[$tn];
	}
	
	/**
	 * ��ȡ��Ա�ڵ�ģ������
	 *
	 * @param  string  $cnstr ��Ա�ڵ��ִ�
	 * @param  string  $addno ����ҳ
	 * @return string  $str   ���ص�ģ������
	 */
	public static function mcn_tplname($cnstr,$addno=0){
		if(!$cnstr){//��ԱƵ����ҳ
			return cls_tpl::SpecialTplname('m_index');
		}else{//�ڵ�ģ��
			$cnode = cls_node::mcnodearr($cnstr);
			return empty($cnode['cfgs'][$addno]['tpl']) ? '' : $cnode['cfgs'][$addno]['tpl'];
		}
	}
	
	# ȡ�ó���ģ����в�ͬ����ģ���ѡ�����飬��ʱ�����Լ��ݾɰ汾
	public static function mtplsarr($tpclass = 'archive',$chid = 0){
		return cls_mtpl::mtplsarr($tpclass,$chid);
	}
	
	# ȡ���ֻ�ģ����в�ͬ����ģ���ѡ�����飬��ʱ�����Լ��ݾɰ汾
	public static function o_mtplsarr($tpclass = 'archive'){
		return cls_mtpl::o_mtplsarr($tpclass);
	}
	
	/**
	 * ȡ�ù���ҳ�����󶨵�ģ������
	 *
	 * @param  string $name 	����ҳ������
	 * @param  int		$NodeMode  �Ƿ��ֻ��ڵ�
	 * @return string			����ģ������
	 */
	 
    public static function SpecialTplname($name,$NodeMode = 0){
		if(!$name) return '';
        $sptpls = cls_cache::Read($NodeMode ? 'o_sptpls' : 'sptpls');
        return empty($sptpls[$name]) ? '' : $sptpls[$name];
    }
	
	/**
	 * ����ĳЩͨ�õĹ���ҳ���ģ�岢����ҳ�����
	 *
	 * @param  string $spname 	����ҳ������
	 * @param  int		$NodeMode  �Ƿ��ֻ��ڵ�
	 * @return string				���ؽ����������
	 */
   public static function SpecialHtml($spname='',$_da=array(),$NodeMode = 0,$LoadAdv = false){
	   $re = cls_SpecialPage::Create(array('spname' => $spname,'_da' => $_da,'NodeMode' => $NodeMode,'LoadAdv' => $LoadAdv,));
	   return $re;
    } 
	 

	/*��ά������  ǰ̨���÷�ʽ <img src="<?=cls_tpl::QRcodeImage('��������')?>" />
	* @param string $content ��ά����������
	* @param int $level   ����ȼ� Ĭ��Ϊ0  ����ȡֵ 1 2 3 ����Խ��ͼƬԽ��
	* @param int $size    �ߴ��С Ĭ��Ϊ 4
	* @param int $margin  ��߿򳤶�
	* @return string ��ά��ͼƬ����ʾurl����ά��ͼƬ����userfiles/qrcode/
	*/
	function QRcodeImage($content,$level=0,$size=4,$margin=3){
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$dir_userfile = cls_env::mconfig('dir_userfile');
		$blankfile = 'images/common/nopic.gif';
		if(!empty($content)){
			$imagefile = $dir_userfile.'/qrcode/'.md5($content.$level.$size.$margin).'.png';
			if(!is_file(M_ROOT.$imagefile)){
				mmkdir($imagefile,1,1);
				include_once M_ROOT."include/phpqrcode.php";
				$content = cls_string::iconv($mcharset,'utf-8',$content);//����ת��UTF-8����
				QRcode::png($content,M_ROOT.$imagefile,$level,$size,$margin,FALSE);
				if(!is_file(M_ROOT.$imagefile)) $imagefile = $blankfile;
			}
		}else $imagefile = $blankfile;
		return cls_url::view_url($imagefile);
	}	 
	
}
