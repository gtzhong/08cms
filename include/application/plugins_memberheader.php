<?php
/**
 * ��Ա���Ĳ������������ͷ��
 * ע�⣺��չ�������Ҫ��������������
 * 1��$this->_weixin_fromid_type Ĭ��ֵΪmid�����Ϊaid��������ͬ������ʱ�붨��ɸ�����
 * 2��$this->_get['cache_id']    ���ǻ���ID����Ӧ��ģ���������΢�Ų˵������ļ������磺
 *                               /template/default/config/weixin_menus_{$this->_get['cache_id']}.cac.php
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('M_MCENTER') || exit('No Permission');
abstract class _08_Plugins_MemberHeader extends _08_Controller_Base implements _08_IPlugin_Member
{
    protected $_weixin_fromid_type;
    
    public function __construct()
    {        
        parent::__construct();
		if( empty($this->_curuser->info['mid']) ) cls_message::show('���ȵ�¼��');
        $this->_weixin_fromid_type = 'mid';
    }
    
    public function init()
    {
		$mconfigs = cls_cache::Read('mconfigs');
        $Weixin_DataBase = parent::getModels('Weixin_DataBase');
        // ��cache_id����չ�����ﶨ��
        if (empty($this->_get['cache_id']))
        {
            cls_message::show('���ȶ��壺cache_id', M_REFERER);
        }
        $mconfigsnew['weixin_cache_id'] = $this->_get['cache_id'];
        if( submitcheck('weixin_submit') )
        {
            $mconfigsnew = @$this->_get['mconfigsnew'];
            if ( !empty($mconfigsnew['weixin_enable']) && 
                 (empty($mconfigsnew['weixin_token']) || empty($mconfigsnew['weixin_appid']) || empty($mconfigsnew['weixin_appsecret'])) )
            {
                cls_message::show('�˺���Ϣ����Ϊ�ա�', M_REFERER);
            }
            
            $mconfigsnew['weixin_fromid_type'] = $this->_weixin_fromid_type;
            $mconfigsnew['weixin_fromid'] = $this->_get[$this->_weixin_fromid_type];
			$mconfigsnew['weixin_cache_id'] = $this->_get['cache_id'];
            if ( isset($mconfigsnew['weixin_url']) )
            {
                unset($mconfigsnew['weixin_url']);
            }
            
            if ( $Weixin_DataBase->saveConfig($mconfigsnew) )
            {
    		    cls_message::show('΢�������޸ĳɹ���', M_REFERER); 
            }
    		else
            {
              	cls_message::show('΢�������޸�ʧ�ܣ����Ժ����ԡ�', M_REFERER); 
            }
        }
        $this->_url = http_build_query($this->_get);
        tabheader('΢�Ź���ƽ̨���� <span style="color:red;">( ID: ' . $this->_get[$this->_weixin_fromid_type] . ' ) ��������Ϣ��΢�Ź���ƽ̨ ������ģʽ�� �µĽӿ�������Ϣ��Ӧ����</span>', 'weixin_form', "?{$this->_url}");
        
        _08_Loader::import(_08_PLUGINS_PATH . 'weixin::admin::admin_weixin_plugin_header');
        $config = (array) $Weixin_DataBase->getConfig($this->_weixin_fromid_type, $this->_get[$this->_weixin_fromid_type]);
        $type = $this->_weixin_fromid_type . '=' . $this->_get[$this->_weixin_fromid_type];
        _08_Admin_Weixin_Plugin_Header::configUI($config, "init&{$type}");
        $title = "����΢�Ų˵�"; 
        $url = _08_M_Weixin_Config::getWeixinURL("create_menu&cache_id={$mconfigsnew['weixin_cache_id']}&{$type}");
        echo <<<HTML
        <tr><td colspan="2" align="center" height="80">
        <input type="submit" name="weixin_submit" value="����" style="border:none; background:url(./$mconfigs[mc_dir]/images/icon.gif) no-repeat -297px -35px; width: 65px; height:25px" />&nbsp;&nbsp;
        <input class="use_menu" style="border:none; background:url(./$mconfigs[mc_dir]/images/icon.gif) no-repeat -305px -230px; width: 100px; height:25px" type="submit" name="weixin_create_menu" value="$title" onclick="_08cms_layer({type: 2, url:'$url', title: '$title' }); return false;" />
        </td></tr>
        </table></form>
HTML;
    }
}