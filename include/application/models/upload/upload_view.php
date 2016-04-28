<?php
/**
 * �ϴ������ͼģ�ͣ�Ŀǰû�ܹ������ͼ��������ʱ���øüܹ�ģʽ��
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Upload_View extends _08_Models_Base
{
    private static $instance = null;
    
    protected $_userfiles = null;
    
    /**
     * ��ʾ�����ϴ���ť
     * 
     * @param  string $config       �����ֶ�������Ϣ
     * @param  bool   $onlyOne      TRUEΪ��������ģʽ��FALSEΪ�������ģʽ
     * @return string $buttonString ���ذ�ť��ʽ�ַ���
     * 
     * @static
     * @since  nv50
     */
    public static function showButton( array $config, $onlyOne = true )
    {
        global $handlekey;
        $_this = self::getinstance();
        $varname = $config['varname'];
        $config['wmid'] = empty($config['wmid']) ? 0 : (int)$config['wmid'];
        if ( !empty($config['value']) )
        {
            $oldremote = self::fileUrlFromat($config['value']);
        }
          
        $oldremotes = $serverData = array();
        $watermarkString = '';
        $get = cls_env::_GET();
        
        $watermarksnewable = cls_cache::Read('watermarks');
        if ( $config['wmid'] && $watermarksnewable[$config['wmid']]['Available'] && in_array($config['type'], array('image', 'images')) )
        {
            $watermarkString .= <<<HTML
            <span style="float:left; margin: 5px;"><input type="checkbox" name="wmid" id="wmid_{$varname}" value="{$config['wmid']}" checked="checked" /><label for="wmid_{$varname}">ˮӡ</label> <span style="color:red;">(��ʾ��ÿ���ϴ�ǰ��ѡ���Ƿ�ʹ��ˮӡ)</span></span>
HTML;
        }
        
        if ( self::isSingle($config['type']) )
        {
            @$oldremotes[0] = array('remote' => $oldremote[0], 'width' => $oldremote[1], 'height' => $oldremote[2]);
        }
        else
        {
            @$oldremotes = (array) unserialize($oldremote[0]);
        }
        
        $userfiles = parent::getModels('Upload_Base');
        $imgValue = '';
        foreach ( $oldremotes as $key => $data ) 
        {
            if ( $data['remote'] )
            {
                $index = $key + 1;
                
                # ������޸�״̬ʱ��userfiles���ȡufid
//                if ( isset($get['aid']) )
//                {
//                    $ufid = $userfiles->getUFidForAid($get['aid'], $data['remote']);
//                }
//                
//                if ( !isset($ufid) )
//                {
//                    $ufid = $data['remote'];
//                }
                
                $data['remote'] = cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'), 'UTF-8', $data['remote']);
                $isSingleImgUrl = $data['remote'] = cls_url::tag2atm($data['remote']);
                
                if ( isset($data['title']) )
                {
                    $title = cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'), 'UTF-8', $data['title']);
                }              
                
                $ufid = str_replace('/', '_', base64_encode($data['remote'] . "#{$varname}#{$index}"));
                @$serverData[$key] = array('id' => "SWFUpload_0_{$varname}_0_{$index}", 'name' => $data['remote'], 'filestatus' => -4, 
                                          'title' => empty($title) ? '' : $title, 'ufid' => $ufid, 'index' => $index,
                                          'width' => $data['width'], 'height' => $data['height'], 'isUpload' => 0,);
                
                if('images'==$config['type']){
                    if ( isset($data['link']) ) {
                        $data['link'] = cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'), 'UTF-8', $data['link']);
                    }
                    $serverData[$key]['link'] = empty($data['link']) ? '' : $data['link'];
                    $imgValue .= ($data['remote'] . '|' . $data['title'] . "\n");
                }         
                else
                {
                	$imgValue = $data['remote'];
                }  
            }
        }
                
        $string = '';
        if ( !empty($serverData) )
        {
            $serverData = json_encode($serverData);
            $string .= '<script type="text/javascript">';
            $string .= ' var _08_uploadData_'.preg_replace('/[^\w]/', '', $varname).' = '. $serverData .';';
            $string .= ' </script>';
        }
        
        $uri_params = array(
            'upload' => 'select_button',
            'field' => $varname,
            'type' => $config['type'],
            'wmid' => $config['wmid'],
            'handlekey' => $handlekey,
            'auto_compression_width' => @intval($config['auto_compression_width'])
        );
		
		if($cms_top = cls_env::mconfig('cms_top')){
			$uri_params['domain'] = $cms_top;
		}
        
        if('images'===$config['type']) {
            if (@in_array($config['imgFlag'], array('S', 'H'), true))
            {
                $uri_params['imgsFlag'] = $config['imgFlag'];
            }
            
            $uri_params['imgsCom'] = @$config['imgComment'];
        }
                      
        $upload_button_url = $_this->_mconfigs['cmsurl'] . _08_Http_Request::uri2MVC($uri_params);
        if ( !isset($config['validator']) )
        {
            $config['validator'] = '';
        }
        
        if ( self::isSingle($config['type']) )
        {
            $buttonString = <<<HTML
            <div style="clear:both;">
                {$string}
                <div id="_08_upload_inputIframe_$varname" style="float:left;">
                    <input type="text" id="_08_upload_$varname" size="60" name="$varname" $config[validator] value="$imgValue" style="float:left; margin: 0px 10px 15px 0">
                    <div style="clear:both;">
                        <span id="loading_{$varname}" style="float:left">loading...</span>
                        <iframe id="iframe_{$varname}" src="{$upload_button_url}" frameborder="0" scrolling="no" style="border:0px; width: 85px; height:30px; float:left; margin-right:5px;"></iframe>
                        $watermarkString
                    </div>
                </div>
                <div id="imgbox_{$varname}" style="float:left;"></div>
            </div>
HTML;
        }
        else
        {   
            if ( class_exists('_08_Browser') && _08_Browser::getInstance()->isMobile() )
            {
                $style = 'width: 94px; height:74px;';
            }
            else
            {
            	$style = 'width: 180px; height:30px; float:left;';
            }
        	$buttonString = <<<HTML
            <span id="loading_{$varname}" style="position: absolute;">loading...</span>
            <div style="clear:both;">
                <div id="imgbox_{$varname}"></div>
                <div style="clear:both; display:none"></div>
                {$string}
                <iframe id="iframe_{$varname}" src="{$upload_button_url}" frameborder="0" scrolling="no" style="border:0px; $style margin-right:5px; clear:both;"></iframe>
                $watermarkString
                <input type="hidden" id="_08_upload_{$varname}" name="{$varname}" value="$imgValue">
            </div>
HTML;
        }
        
        return $buttonString;
    }
    
    /**
     * �����ϴ��ļ���URL
     * 
     * @param  string $value �ļ���ַ
     * @param  string $mode  �Զ�����ֶ�����
     * @return string        ���ؽ�����ĸ���URL
     * 
     * @static
     * @since  nv50
     */
    public static function parseUploadFileUrl( $value, $mode )
    {
    	$oldarr = self::fileUrlFromat($value);
        $oldremote = $oldarr[0];
    #	$oldremote = cls_url::tag2atm($oldarr[0]);
        $mode == 'media' && $oldremote .= empty($oldarr[1]) ? '' : '|'.$oldarr[1];
        return $oldremote;
    }
    
    /**
     * �ж��Ƿ�Ϊ���������ϴ�
     * 
     * @param  string $type �ϴ�����
     * @return bool         �Ƿ���TRUE�����򷵻�FALSE
     *      
     * @since  nv50                    
     */    
    public static function isSingle( $type )
    {
        if ( strtolower(substr($type, strlen($type) - 1)) === 's' )
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * ��ʽ���ϴ��ļ�URL
     * 
     * @param  string $fileUrl �ϴ��ļ�URL
     * @return array  $oldarr  ���ظ�ʽ�����URL��Ϣ
     * 
     * @since  nv50
     */
    public static function fileUrlFromat( $fileUrl )
    {
        $oldarr = explode('#', (string) $fileUrl);
        return $oldarr;
    }
    
    public static function getinstance()
    {
        if ( empty(self::$instance) )
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
}