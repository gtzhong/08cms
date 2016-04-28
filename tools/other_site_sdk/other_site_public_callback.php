<?php
/**
 * ������վ�󶨻ص��࣬���øýӿ��뱾������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined( 'DS' ) || define( 'DS', DIRECTORY_SEPARATOR );
isset($_GET['type']) || die('No Permission');
include dirname(dirname(dirname(__FILE__))) . DS . 'include' . DS . 'general.inc.php';
defined( 'OTHER_SITE_BIND_PATH' ) || define( 'OTHER_SITE_BIND_PATH', dirname(__FILE__) . DS );
require_once OTHER_SITE_BIND_PATH . '08cms_bind_interface.php';
# ��֤����
otherSiteBind::checkAction();
/**
 * ǿ��������վ�ӿڱ���̳иó�����Ͷ�����󷽷�
 */
abstract class Auther extends otherSiteBind
{
    /**
     * ��ȡ��վ�û�����
     *
     * ���������ע��󶨻��¼��ʱ�Զ���ʾ�û���ʱ��ֻ�������ﶨ�庯������
     *
     * @return string Ҫ��ȡ���û�����
     * @since  1.0
     */
    abstract public function getUserName();

    /**
     * ��װ�ű�
     *
     * @since 1.0
     */
    abstract public function Setup();

    /**
     * ��ȡ�û�ͷ��
     *
     * ��������ȡ����Ϣʱ��ֻ�������ﶨ�庯������
     *
     * @return string �����û�ͷ��URL
     * @since  1.0
     */
    abstract public function getUserAvatar();
}

/**
 * ������Ȩ��֤������
 */
class otherAuthFactory
{
    private static $_instance = null;

    /**
     * ������������
     *
     * @param  string $type   ��¼���ͣ���������Ȩ�󷵻ظ�ҳ���ַ������URL����
     * @return object         ���ع���Ĺ�������
     * @since  1.0
     */
    public static function Create($type)
    {
        _08_FilesystemFile::filterFileParam($type);
        // ���칤�����ӿ��ļ���������Ϊ����¼���� + '_auth.php'
        $class = $type . 'Auth';
        if(is_file(OTHER_SITE_BIND_PATH . $type . '_auth.php')) {
            require_once OTHER_SITE_BIND_PATH . $type . '_auth.php';
        } else {
            cls_message::show("$class �ӿ��ļ������ڣ�");
        }
                
        if( class_exists($class) ) {
            if ( !(self::$_instance instanceof $class) )
            {
                self::$_instance = new $class();
            }            
        } else {
            cls_message::show("$class �ӿڲ����ڣ�");
        }
        (self::$_instance instanceof Auther) || cls_message::show("$class �ӿڱ���̳��� auther�����࣡");

        return self::$_instance;
    }

    /**
     * ������ִ��ˢ�¸����ڲ��رձ�����
     *
     * @param string $msg �����ʾ����Ϣ
     * @since 1.0
     */
    public static function UcActive($msg = '')
    {
		$cms_top = cls_env::mconfig('cms_top');
        $str = '<script type="text/javascript">document.domain = "'. $cms_top .'" || document.domain;window.opener.location.reload(); ';
        $str .= (empty($msg) ? '' : "alert(\"$msg\");");
        $str .= 'window.close(); </script>';
        exit($str);
    }
    
    public static function checkPass()
    {
        $curuser = cls_UserMain::CurUser();
        $post = cls_env::_POST('password, check_pass');
        if (!empty($post['check_pass']))
        {
            if (empty($post['password']) || (_08_Encryption::password($post['password']) != $curuser->info['password']))
            {
                cls_message::show('���벻��ȷ��', M_REFERER);
            }
            else
            {
                # ������ת��Ȩҳ�������µ�¼��Ȩ
            	header('Location:' . self::$_instance->getCallBack());
                exit;
            }
        }
        echo <<<HTML
        <form method="post">
            �������û����룺<input type="password" name="password" /> <input type="submit" name="check_pass" value="�ύ" />
        </form>
HTML;
    }
}


// �����������󣬲��ڶ������Զ�ִ����Ȩ��֤
$auth = & otherAuthFactory::Create($type);
$auth->Setup();

# ���°�
if( isset($act) )
{
    if ($act === ($type . '_reauth'))
    {
        if (empty($curuser->info['mid']))
        {
            cls_message::show('���ȵ�¼��');
        }
        
        otherAuthFactory::checkPass();
        exit;
    }
    else
    {
        switch(strtolower($act)) {
            case 'uc_action' : otherAuthFactory::UcActive(); break;
            # ������Ȩ
            default : bind08CMSInterface::actionBind($act); break;
        }
    }
}

# ���ǰ̨��¼���ɹ������д�벻��SESSION���
(empty($_SESSION[otherSiteBind::$authfields[$type]]) && (@$act != ($type . '_reauth'))) && cls_message::show('�������󣬵�ǰ��½��Ϣ�Ѿ����ڣ�');

$minfo = $db->fetch_one("
    SELECT `mid`, `mname`, `email`, `password`, `checked`, `mchid`, `isfounder` FROM `{$tblprefix}members`
    WHERE `" . otherSiteBind::$authfields[$type] . "` = '" . $_SESSION[otherSiteBind::$authfields[$type]] . "' LIMIT 1");
if(empty($minfo)) {
    bind08CMSInterface::BindTemplate($auth, $minfo);
} else {
    bind08CMSInterface::Login08CMS($minfo);
}