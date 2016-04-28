<?php
/**
 * PHPWINDӦ�������
 * 
 * ���ļ�����Ӧ����������������Ϊ�����Ա���ԭ���ƣ������պ�Ҫ��չPHPWIND������Ӧ�ö��ɴӴ���ڽ��뼴��
 */
define('PW_EXEC', TRUE);
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

include dirname(__FILE__). DS . 'include' . DS . 'general.inc.php';

// ����һ��PHPWINDӦ��·��
define('_08_PHPWIND_PATH', _08_INCLUDE_PATH . 'phpwind' . DS);

# ע��pw_ǰ׺����·�����ø�·���ű�֧���Զ�����
_08_Loader::registerPrefix('pw_', _08_INCLUDE_PATH . 'phpwind');

# ����Ӧ��ID���ʶ��
$configs = array();
if ( empty($action) || (isset($action) && ($action == 'windid_client')) )
{
    empty($action) && $action = 'windid_client';
    $configs = array('config' => cls_env::_GET_POST(), 'mconfig' => $mconfigs);
}
else
{
    _08_FilesystemFile::filterFileParam($action);
    if ( is_file(_08_PHPWIND_PATH . $action . '.config.php') )
    {
        $configs = (include _08_PHPWIND_PATH . $action . '.config.php');
    }
}

$app = _08_factory::getApplication($action, $configs, 'pw_');
if ( method_exists($app, 'run') )
{
    $app->run();
}