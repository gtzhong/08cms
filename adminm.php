<?php
define('NOROBOT', TRUE);
define('M_UPSEN', TRUE);
define('M_MCENTER', TRUE); // ���ڴ������²��������Ի�Ա����
include_once dirname(__FILE__).'/include/general.inc.php';
include_once M_ROOT."include/adminm.fun.php";
include_once M_ROOT."include/field.fun.php";

# ͨ��entry.php��������$action.inc.php
cls_AdminmPage::Create(array('isEntry' => true,));