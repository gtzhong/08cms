<?php
/**
 * @package   08CMS.Site
 * @copyright Copyright (C) 2008 - 2014, 08CMS Inc. All rights reserved.
 */
if (version_compare(PHP_VERSION, '5.2.3', '<'))
{
	die('����������Ҫʹ��PHP 5.2.3����߰汾�������д˰汾��08CMS !');
}

defined('M_UPSEN') || define('M_UPSEN', TRUE);
defined('UN_VIRTURE_URL') || define('UN_VIRTURE_URL', TRUE);//��Ҫ����α��̬
include_once dirname(__FILE__).'/include/general.inc.php';
# cls_env::CheckSiteClosed(); # ���Ҫ���ݲ�ͬ����ڷֱ���
# ����ת��һ��ڼܹ�
if ( _08_factory::getApplication()->run() )
{
    exit;
}
cls_CnodePage::Create();
