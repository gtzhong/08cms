<?php
/**
 * CK����������
 *
 * �Ժ�ò���������CK������������ӿڣ������ظ�����
 *
 * @package   CkPublicClass
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
if ( !defined('M_ADMIN') && !defined('M_MCENTER') )
{
    define('M_ADMIN', TRUE);     /// Ŀǰ����CK���ֻ���ں�̨���Ա����ʹ��
}
//$memberid || die('���ȵ�¼��');
require_once M_ROOT . 'include' . DS . 'admina.fun.php';
abstract class CkPublicClass extends _08_Controller_Base
{
    /**
     * ���÷��ظ�CK��JS�������룬�������Ʊ�������Ϊ��getReturn
     *
     * @var   string
     * @since 1.0
     */
    private $_get_return_function_str = '';

    /**
     * ���õ�������
     *
     * @var   string
     * @since 1.0
     */
    protected $_title = '';

    /**
     * �ű��������
     *
     * @var   string
     * @since 1.0
     */
    protected $_action;

    /**
     * ��ѯ����
     *
     * @var   string
     * @since 1.0
     */
    protected $_subject = '';

    /**
     * ��ѯ����
     *
     * @var   int
     * @since 1.0
     */
    protected $_ccid1 = 0;

    /**
     * ��ѯ��Ȧ
     *
     * @var   int
     * @since 1.0
     */
    protected $_ccid2 = 0;

    /**
     * ��ѯ��Ϣ����
     *
     * @var   string
     * @since 1.0
     */
    protected $_where = '';

    /**
     * ��ѯSQL���
     *
     * @var   string
     * @since 1.0
     */
    protected $_sql = '';
    
    protected $_handlekey = 0;
    
    protected $_uri;

    /**
     * ����CkPublicClass �Ĺ��췽��
     *
     * �ù��췽��������Ĺ��췽�������ʼʱ����Ҫ���ã� �磺parent::__construct(); ��
     *
     * @since 1.0
     */
    public function __construct()
    {
        global $cms_abs, $mcharset, $fmdata, $cmsurl,$cms_top;
        parent::__construct();
        $this->_action = empty($this->_get['action']) ? 'init' : trim($this->_get['action']);
		// ����ֶβ�һ�����ɶ�action����Ϊ�� serach ֵȻ�������������»�ȡ����
        if(in_array($this->_action, array('search'))) {
            if(!empty($this->_get['subject'])) {
                $this->_subject = addcslashes(trim($this->_get['subject']), '%_');
                $this->_where .= " AND a.subject LIKE '%{$this->_subject}%'";
            }
            if(!empty($fmdata['ccid1'])) {
                $this->_ccid1 = (int)$fmdata['ccid1'];
                $this->_where .= " AND a.ccid1 = {$this->_ccid1}";
            }
            if(!empty($fmdata['ccid2'])) {
                $this->_ccid2 = (int)$fmdata['ccid2'];
                $this->_where .= " AND a.ccid2 = {$this->_ccid2}";
            }
        }
        
        self::_setNoCache();
        if ( empty($this->_get['parent_wid']) )
        {
            $wid = 'main';
            $parent_wid = 0;
        }
        else
        {
        	$parent_wid = (int)$this->_get['parent_wid'];
            $wid = "_08winid_{$parent_wid}";
        }
        
        $this->_handlekey = empty($this->_get['handlekey']) ? 0 : (int) $this->_get['handlekey'];
        $varname = empty($this->_get['varname']) ? '' : trim($this->_get['varname']);
        
        $this->_uri = "&varname={$varname}&parent_wid={$parent_wid}";
        echo <<< EOT
            <!DOCTYPE html PUBLIC "-W3CDTD XHTML 1.0 TransitionalEN" "http:www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http:www.w3.org/1999/xhtml" >
            <head>
            <title>{$this->_title}</title>
            <meta http-equiv="Content-Type" content="text/html; charset={$mcharset}" />
            <link href="{$cms_abs}images/admina/contentsAdmin.css" rel="stylesheet" type="text/css" />
            <link href="{$cms_abs}images/common/window.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript">var CMS_ABS = "{$cms_abs}" , CMS_URL = "{$cmsurl}", MC_ROOTURL = "MC_ROOTURL";var originDomain = originDomain || document.domain;document.domain = "{$cms_top}" || document.domain;</script>
            <script type="text/javascript" src="{$cmsurl}images/common/jquery-1.10.2.min.js"></script>
            <script type="text/javascript" src="{$cmsurl}include/js/common.js"></script>
            <script type="text/javascript" src="{$cmsurl}include/js/admina.js"></script>
            <script type="text/javascript" src="{$cmsurl}include/js/floatwin.js"></script>
            <script type="text/javascript" src="{$cmsurl}include/js/_08cms.js"></script>
            <script type="text/javascript" src="{$cmsurl}include/js/setlist.js"></script>
            <script type="text/javascript">
                var getParentObject = function() {
                    // ��̨������
                    var parentDocument = parent.document.getElementById('{$wid}');
                    // ��̨�����
                    if (!parentDocument)
                    {
                        parentDocument = parent.document.getElementById('main');
                    }
                    if (parentDocument)
                    {
                        var parentObject = parentDocument.contentWindow._08_ueditor_{$varname};
                    }
                    else// ��Ա����
                    {
                    	var parentObject = document._08_ueditor_{$varname};
                    }
                    
                    if (!parentObject)
                    {
                        parentObject = parent._08_ueditor_{$varname};
                    }
                    
                    return parentObject;
                };
            </script>
            </head>
            <body>
EOT;
    }

    /**
     * �����ļ�������
     *
     * @static
     * @since 1.0
     */
    private static function _setNoCache()
    {
        _08_Http_Request::clearCache();
    }

    /**
     * �ı���Ϣ��ʾ��ʽ
     *
     * ���÷��ظ�CK��JS�������룬�������Ʊ�������Ϊ��getReturn
     *
     * @since 1.0
     */
    protected function SetReturnFunctionStrInfo()
    {
        $this->_get_return_function_str = <<< EOT
            function getReturn(obj)
            {
                var parentObject = getParentObject();
                var _html = document.getElementById('show_style').innerHTML;                             
                parentObject.execCommand('inserthtml', _html,true); 
                winclose();
            }
EOT;
    }

    /**
     * �Ƿ���ʾ�ĵ�ͼƬ
     *
     * @param  string $file_url Ҫ�жϵ�ͼƬ��ַ
     *
     * @return bool   ��ʾ����TRUE�����򷵻�FALSE
     *
     * @static
     * @since  1.0
     */
    public static function isShowImg($file_url)
    {
        if (empty($file_url))
            return false;
        if (false !== stripos($file_url, ':') || cls_url::is_remote_atm($file_url))
            return true;
        return is_file(M_ROOT . $file_url) ? true : false;
    }
    
    /**
     * ͼƬ��ʾģʽ
     *
     * ���÷��ظ�CK��JS�������룬�������Ʊ�������Ϊ��getReturn
     *
     * @since 1.0
     */
    protected function SetReturnFunctionStrTu()
    {
        $this->_get_return_function_str = <<< EOT
            function getReturn(obj)
            {
                var parentObject = getParentObject();
                var contents = parentObject.getContent();
                var rule = new RegExp(obj.src);
                var rule2 = new RegExp('^<p.*' + obj.src + '.*</p>$');
                if (rule.test(contents))
                {
                    obj.style.border = 'none';
                    obj.style.height = '120px';
                    parentObject.setContent(contents.replace(rule2, ''));
                }
                else
                {
                    obj.style.border = '1px red solid';
                    obj.style.height = '118px';
                	parentObject.execCommand('inserthtml', '<p style="text-align:center;"><img src="' + obj.src + '" title="' + obj.title + '" /></p>');
                }
                
            }
EOT;
    }

    /**
     * �ýӿ�Ĭ����ʾͼƬ����ʽ
     *
     * @param  array  $row        �����ݿ��ѯ������������
     * @param  string $field_name ��ϵͳ�еĵط����õ��ֶ���tupian���е���thumb�����Ե���Ȩ����������
     *
     * @return string $str        ������ʽ�ַ���
     *
     * @since  1.0
     */
    protected function showImgStyle($row, $field_name = 'tupian')
    {
        global $cms_abs;
        cls_ArcMain::Url($row);
        $tupian = explode('#', $row[$field_name]);
        $is_afile = self::isShowImg($tupian[0]);
        $str = '<div style="float:left; margin:15px 0px 0px 27px; text-align:center;">
                    <a href="javascript:void(0);" title="' . $row['subject'] . '">
                        <img alt="' . $row['subject'] . '" src="' . ($is_afile ? cls_url::tag2atm($row[$field_name]) :
                        $cms_abs . 'images/common/nopic.gif') . '" width="160" height="120" ' .
                        ($is_afile ? 'onclick = "return getReturn(this);"' : 'onclick = "alert(\'����ͼƬ�ɲ���\');"') . '/>
                    </a><br />
                    <p style="width:160px; height:50px; line-height:25px; overflow:hidden;">
                        <a href="' . $row['arcurl'] . '" target="_blank" title="' . $row['subject'] . '">' .
                        $row['subject'] . '</a>
                    </p>
                </div>';
        return $str;
    }

    /**
     * ����CkPublicClass ����������
     *
     * ֻҪ�Ǽ̳��ڸû����µ����࣬����󶼻���ø÷���
     *
     * @since 1.0
     */
    public function __destruct()
    {
        echo <<< EOT
            <script type="text/javascript">
            {$this->_get_return_function_str}
                function winclose()
                {
                    floatwin('close_{$this->_handlekey}',-1);
                }
            </script>
            </body></html>
EOT;
    }
}
