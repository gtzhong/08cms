<?php
define('M_ROOT', substr(dirname(__FILE__), 0, -7));
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');
@include M_ROOT . 'base.inc.php';

//��������ģʽ����ʾ���д���
empty($phpviewerror) || @ini_set('display_errors', 'On');
if ( $phpviewerror == 3 )
{
    error_reporting(E_ALL);
}
else
{
	error_reporting(0);
}

//����ϵͳ����
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'defines.php';

//�������Ĭ�ϱ��룬���������ļ��������룬��AJAX����ʱ�Ϳɲ�����ajax.php��һ�������ñ��룬
@header("Content-type:text/html;charset=$mcharset");

//��������չ���棬ֻ��base.inc.php�������й�
include_once _08_INCLUDE_PATH . 'excache.cls.php';
$m_excache = cls_excache::OneInstance();

// �����Զ�������
require _08_INCLUDE_PATH . 'loader.cls.php';
_08_Loader::setup();
cls_env::__checkEnvironment();

//����php������
cls_env::mob_start();

//����ͨ�ú���
require _08_INCLUDE_PATH .'general.fun.php';

//��������
define('ISROBOT',cls_env::IsRobot());
cls_env::RobotFilter();

//ȫ�ֱ���
cls_env::GLOBALS();
cls_env::_FILES();
extract((array)cls_env::_GET_POST(),EXTR_SKIP);
$m_cookie = cls_env::_COOKIE();
$onlineip = cls_env::OnlineIP();

//��ʼ��ϵͳ����
$mconfigs = cls_cache::Read('mconfigs');
extract($mconfigs,EXTR_OVERWRITE );

#ini_set('date.timezone','ETC/GMT'.(empty($timezone) ? 0 : $timezone));
@date_default_timezone_set('ETC/GMT'.(empty($timezone) ? 0 : $timezone));

/**
 * ����ȫ��SESSION���ã����memcache�������Զ���SESSION�浽memcache��
 * ��ע�⣺�뾡����memcache���SESSION���ر��ǳ��������ڶ�������ϣ�
 */
if ( strtolower(@$m_excache->__cache_type) == 'memcached' && !empty($m_excache->obj->enable) )
{
    @ini_set('session.save_handler', "memcache");
    @ini_set('session.save_path', "tcp://$ex_memcache_server:$ex_memcache_port");
}
else # ����浽�ļ���
{
    @ini_set('session.save_handler', "files");
    $tmpPath = sys_get_temp_dir();
	if ( is_writable($tmpPath) )
    {
        @ini_set('session.save_path', $tmpPath);
    }
}

if ( !headers_sent() )
{
    session_start();
}

$timestamp = TIMESTAMP;
if(!empty($disable_htmldir)){
	$mconfigs['cnhtmldir'] = $cnhtmldir = '';
}
$authorization = md5($authkey);

//IP��ֹ����ȫ����
if(cls_env::IpBanned($onlineip)) exit('IP_Fobidden'); 

//�Ƿ�ģ�����ģʽ
$debugtag = $onlineip && ($v = explode(',',@$debugtag_ips)) && in_array($onlineip,$v) ? 1 : 0;
define('_08_DEBUGTAG', $debugtag);

//���ؼ��ܺ���
cls_env::LoadZcore();

// ����������վ��¼�ӿ�
_08_Loader::register('otherSiteBind', M_TOOLS_PATH . 'other_site_sdk' . DS . 'other_site_bind.php');

//ҳ�������Ϣ
$_mdebug = new cls_debug;

//�������ݿ�
$dbcharset = !$dbcharset && in_array(strtolower($mcharset),array('gbk','big5','utf-8')) ? str_replace('-', '', $mcharset) : $dbcharset;
$db = _08_factory::getDBO();

//������ǰ����߶���
$curuser = cls_UserMain::CurUser();
$curuser->vsrecord();
$memberid = $curuser->info['mid'];

# �ı� cms_abs ��������
defined( '_08_CMS_ABS' ) || define( '_08_CMS_ABS', $cms_abs );
# ����MVC·�����
defined( '_08_ROUTE_ENTRANCE' ) || define( '_08_ROUTE_ENTRANCE', 'index.php?/' );

//��������ģʽ
if(($phpviewerror == 1) && $curuser->isadmin()){
	error_reporting(E_ALL);
}elseif(($phpviewerror == 2) && $curuser->info['mid']){
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

cls_env::filterClickJacking();
