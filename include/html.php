<?php
/**
 * HTMLԪ����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('M_COM') || exit('No Permission');
class _08_HTML
{
	/**
     * ��ȡ��֤��Ԫ��
     * 
     * @param  string $codeName    ����һ����֤������ƺ��ȡ
     * @param  string $formName    ��֤�����ڵı�����
     * @param  string $class       input��class����
     * @param  string $inputName   input����
     * @param  string $inputString input�����ַ���
     * @return string              ������$codeNameΪ������֤���ַ���
     * 
     * @since  1.0
     */
    public static function getCode( $verify = '08cms_regcode', $formName = '', $class = 'regcode', $inputName = '', $inputString = '' )
    {
    	global $regcode_mode, $cms_abs, $timestamp;
        switch($regcode_mode)
        {
            // ������
            case 1 : $rule = 'number'; break;
            // ����ĸ
            case 2 : $rule = 'letter'; break;
            // ��������ĸ
            default : $rule = 'numberletter'; break;
        }

        if ( !empty($formName) && $formName !== NULL )
        {
            $str = '<script type="text/javascript">var ' . $formName . ' = _08cms.validator(\'' . $formName . '\');</script>';
        }
        else
        {
            $str = '';
        }

        $formName = empty($formName) ? '_08cms_validator' : trim($formName);
        $inputName = empty($inputName) ? 'regcode' : trim($inputName);
		$session_id = session_id();
		$session_name = session_name();
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC("ajax=regcode&verify=$verify&regcode=%1&$session_name=$session_id");
        $str .= <<<HTML
<input type="text" name="$inputName" id="$inputName" size="4" maxlength="4" rule="$rule" must="1" min="4" max="4" init="����������ʾ��֤��" rev="��֤��" offset="1" onblur="_08_Regcode.hide(this, '$verify');" onfocus="_08_Regcode.show(this, '$verify', event);" onkeyup="_08_Regcode.keyUpHide(this, '$verify', '$formName');" autocomplete="off" class="$class" $inputString />
        <img src="{$cms_abs}tools/regcode.php?verify={$verify}&t={$timestamp}" id="$verify" name="$verify" style="vertical-align: middle; cursor:pointer; position: absolute; z-index: 999; display:none" onclick="this.src += 1;" />
        <script type="text/javascript">
            var ajaxURL = '$ajaxURL';
            if ( typeof(uri2MVC) == 'function' )
            {
                ajaxURL += uri2MVC('&domain=' + document.domain, false);
            }
            window.$formName && $formName.init("ajax","$inputName",{ url: ajaxURL });
        </script>
        <input type="hidden" name="verify" value="$verify"/>
HTML;
?><?PHP
        return trim($str);
    }
	
    public static function Title($title = ''){
		return "<title>$title</title>";
	
	}
	
    public static function AjaxCheckInput($InputName,$Url){
		$jstag = 'script'; 
		return "<$jstag type='text/javascript'>_08cms_validator.init('ajax','$InputName',{url:'$Url'});</$jstag>";
	}
	
    public static function DirectUrl($Url){
		return "<html><head><meta http-equiv=\"expires\" content=\"-1\"><meta http-equiv=\"refresh\" content=\"0;url=".cls_env::mconfig('cms_abs').$Url."\"></head></html>";
	}
	
    /**
     * ��ȡ�༭�������ť
     * 
     * @param  string $plugins_names      �����ı༭�������ť����
     * @param  string $varname            �༭��ʹ�õ��ֶ�����
     * 
     * @return string                ���ػ�ȡ���Ĳ����ť
     */
    public static function getEditorPlugins( $plugins_names, $varname )
    {
        $plugins_button = '<div class="_08_plugins_button">';
        if ( is_string($plugins_names) )
        {
            $plugins_names = array_filter(explode(',', $plugins_names));
        }
        else
        {
            $plugins_names = (array) $plugins_names;
        }
        
        # ���δ����ʱĬ�Ͽ���һ����ҳ������
		if ( empty($plugins_names) && (defined('M_ADMIN')||defined('M_MCENTER')) )
        {
            $plugins_names = array('08cms_paging_management');
        }
        else
        {
        	$plugins_names = array_map('trim', $plugins_names);
        }
        
        $gets = cls_env::_GET('handlekey');
        $current_wid = (int) @$gets['handlekey'];
        foreach ( $plugins_names as $name ) 
        {
            if ( isset(self::$__editorPluginsMap[$name]) )
            {
                $plugins_name = self::$__editorPluginsMap[$name];
                $name = str_replace('08cms_', '', preg_replace('/[^\w]/', '', $name));
                $url = cls_env::mconfig('cmsurl') . _08_Http_Request::uri2MVC("editor={$name}&varname={$varname}&parent_wid={$current_wid}");
                $plugins_button .= <<<HTML
    <a title="{$plugins_name}" class="_08_plugins_button" onclick="return floatwin('open_{$name}',this)" href="{$url}">{$plugins_name}</a>
HTML;
            }
        }
        $plugins_button .= '</div>';
        
        return $plugins_button;
    }
    
    /**
     * �༭�����Map
     * 
     * @var   array
     * @since nv50
     */
    public static $__editorPluginsMap = array(
        '08cms_paging_management' => '��ҳ�������',
        '08cms_hangqing' => '��������',
        '08cms_chetu' => '����ͼƬ',
        '08cms_house_info' => '¥����Ϣ',
        '08cms_plot_pigure' => 'ѡС��ͼ',
        '08cms_size_chart' => 'ѡ����ͼ'                
    );
    
    /**
     * ���ɸ��ƴ���
     * 
     * @param string $value Ҫ�����Ƶ�ֵ
     * @param string $label ��ʾ����ǰ�ı�ǩ
     **/
    public static function createCopyCode($id, $value, $label = '')
    {
		$cms_abs = _08_CMS_ABS; 
        $value = rawurlencode(cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'),'utf-8',$value));
		$csflag = 'script'; //echo $value;
		
return <<<HTML
<$csflag src="$cms_abs/images/common/swfCopy/copyfuncs.js"></$csflag>
<span id="copySwfID_$id"></span>
<$csflag type="text/javascript">
var copyData_$id = "$value";
function copySuccess(){ 
	//����ж��copy, �����ⲿ����var copySwf_cbackID=1;, ʹ��ʾ��ʾ��Ϣ���������һ��button
	if(typeof(copySwf_cbackID)=='undefined'){
		var showid = 'copySwfID_$id';
		layer.tips('���Ƴɹ���', '#'+showid, {style:['background-color:#134d9d;color:#FFF;','#134d9d'], time:1}); 
	}else{
		layer.msg('���Ƴɹ���',1);
	}
}
$(document).ready(function(){
	loadRun('/images/common/swfCopy/swfobject.js',"copyReset(copyData_$id,'copySwfID_$id',{ isVal:1, noEnc:1 });");
});
</$csflag>
HTML;

    }
    
    /**
     * �ɴ����HTML��ǩ����
     * 
     * @return array ���ؿɴ����HTML��ǩ����
     **/
    public static function getDealHtmlTagsMap()
    {
        return array(
            'a' => '���� <a', 
            'tbody' => '����� <tbody', 
            'form' => '�� <form', 
            'table' => '��� <table', 
            'img' => 'ͼƬ <img', 
            'frame' => '��� <frame', 
            'tr' => '����� <tr', 
            'script' => '�ű� <script', 
            'li,ul,dd,dt' => '��� <li<ul<dd<dt', 
            'td' => '��Ԫ <td', 
            'b,strong' => '�Ӵ� <b<strong', 
            'tab' => '����|Tab \r\n\t', 
            'p' => '���� <p', 
            'br' => '���� <br', 
            'trim' => 'ȥ��β�հ��ַ�', 
            'font' => '���� <font', 
            'nbsp' => '�ո� &nbsp;', 
            'iframe' => '��� <iframe', 
            'div' => '�� <div', 
            'h' => 'H��ǩ <h1-7', 
            'sub,sup' => '���±� <sub<sup', 
            'span' => 'Span <span', 
            'hr' => 'hr��ǩ <hr', 
            'all' => '���б�ǩ'
        );
    }
}
