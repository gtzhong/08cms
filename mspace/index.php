<?PHP
define('_08_MSPACE', true);
defined('M_UPSEN') || define('M_UPSEN', TRUE);
defined('UN_VIRTURE_URL') || define('UN_VIRTURE_URL', TRUE);//��Ҫ����α��̬
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';

$_params = empty($mid) ? array() : array('mid' => $mid); # ���ݶ�̬ҳ��󶨶�������,mid����Ŀ¼�ű��ڶ��壬��GP����
cls_MspaceIndex::Create($_params);
