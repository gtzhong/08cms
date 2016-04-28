<?php
/**
 * PHPת��JavaScript������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_phpToJavascript
{
    /**
     * ����ָ�����ݵ������ڵ�ָ���ڵ㴦
     * 
     * @param string $parent_window_id ������ID
     * @param string $dom_id           �����ڽڵ�ID
     * @param string $string           Ҫ���������
     * @param int    $caretpos         ������ڵĸ��������꿪ʼ��
     *
     * @since 1.0
     */  
    public static function insertParentWindowString($parent_window_id, $dom_id, $string, $caretpos = 0, $flag = true)
    {
        echo '<script type="text/javascript">
                var obj = window.parent.document.getElementById("'.$parent_window_id.'").contentWindow;';
        if($flag) 
        {
            // �����±�ǩ������Ϣ
            echo "obj.insertTagStr('{$dom_id}', '$string', $caretpos);";
        }
        echo '</script>';
    }
    
    /**
     * ���һ������JQ�ļ��Ĵ���
     */
    public static function loadJQuery( $file_name = '', $return = false )
    {
        global $cms_abs;
    /*	echo self::str_js_src("{$cms_abs}images/common/$file_name");
        */
        if ( empty($file_name) )
        {
            $file_name = 'jquery-1.10.2.min.js';
        }
        $str = <<<EOT
        <script type="text/javascript">window.jQuery || document.write('<script src="{$cms_abs}images/common/$file_name"><\/script>');</script>
EOT;
?><?PHP
        if ( $return )
        {
            return $str;
        }
        else
        {
        	echo $str;
        }
    }
    
    /**
     * ��װһ��JS�ļ���������
     * 
     * @param string $val JS�ļ���srcԴ��ַ
     * @return string     ������ɺõ�JS���ô���
     */
    public static function str_js_src($val, $charset = '')
    {
		if(empty($charset)) $charset = cls_env::getBaseIncConfigs('mcharset');
    	return '<script type="text/javascript" src="' . $val . '" charset="'.$charset.'"></script>';
    }
	
    /**
     * ��ptool.php��ص�JS���ô���($Params������������ToolJS)
     * 
     * @param array			$Params		��������
     * @return string		������õ�JS���ô���
     */
    public static function PtoolJS($Params = '')
    {
		if(is_array($Params)){
			$ParamStr = '';
			foreach($Params as $k => $v){
				$ParamStr .= is_numeric($k) ? "&$v" : "&$k=$v"; # ʹ�����ּ���������a=*&b=*����ƴ�ִ�
			}
			if($ParamStr) $ParamStr = '?'.substr($ParamStr,1);
			return self::str_js_src(cls_env::mconfig('cms_abs').'tools/ptool.php'.$ParamStr);
		}else return '';
    }
    
    /**
     * ��ӡ��ݵ�¼��Ҫ�õ�JS����
     */ 
    public static function showOtherBind()
    {
        echo '<script type="text/javascript"> var urls = ' . new otherSiteBind() . ';';
        echo <<<EOT
            var childWindow;
            function OtherWebSiteLogin(type, width, height)
            {
                if(urls[type] == 'close')
                {
                    alert('�õ�¼�����Ѿ��رգ�');
                    return false;
                }
                else
                {
                    childWindow = window.open(urls[type], type, "width=" + width + ",height=" + height + ",left="+((window.screen.availWidth-width)/2)+",top="+((window.screen.availHeight-height)/2));
                }
            }
            </script>
EOT;
?><?PHP
    }
    
    #���ع�����
    public static function LoadAdv()
    {
        $cms_abs = _08_CMS_ABS;
        $str = self::loadJQuery('', true);
     #   $str .= self::str_js_src($cms_abs . 'include/js/common_footer.js');
        $str .= self::str_js_src($cms_abs . 'include/js/common_footer.min.js');
        return $str;
    }
    
    /**
     * ����������תΪJS��ʽ����
     * 
     * @param string		$Content		��Դ�ִ�
     * @return string		����תΪJS��ʽ�Ĵ���
     */
    public static function JsFormat($Content = ''){
		if(!$Content) return $Content; //������:''��0�� �����0��Ҫ����0������''
		$Content = trim(addcslashes($Content, "'\\\r\n"));
		return $Content;
    }
    /**
     * �����������װΪdocument.write��JS����
     * 
     * @param string		$Content		��Դ�ִ�
     * @return string		����תΪJS��ʽ�Ĵ���
     */
    public static function JsWriteCode($Content = ''){
		$Content = cls_phpToJavascript::JsFormat($Content);
		$Content = "document.write('". $Content ."');";
		return $Content;
    }
    
    /**
     * ��Ĭ�ϵ�UC/PHPWindͬ������ת��AJAX��ʽ
     * 
     * @param string $contents Ĭ�ϵ�UCͬ�������ַ���
     **/
    public static function toAjaxSynchronousRequest($contents)
    {
        if (preg_match_all('/src="(.*)"/isU', $contents, $src))
        {
            foreach ($src[1] as $_src)
            {
                echo <<<JS
                var _document = parent.document;
                if (!_document)
                {
                    _document = document;
                }
                var _script = _document.createElement('script');
                _script.type = 'text/javascript';
                _script.src = '$_src';
                document.body.appendChild(_script);
                
JS;
            }
        }
    }
}