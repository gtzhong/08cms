<?PHP
$_08_extend_dir = 'extend_house';	//核心样本为extend_sample

$dbhost = 'localhost';
$dbuser = 'root';
$dbpw = 'root';
$dbname = 'db08cmshouse7';
$pconnect = 0;

$tblprefix = 'cms_';
$dbcharset = '';		// MySQL 字符集, 可选 'gbk', 'big5', 'utf8', 'latin1', 留空为按照系统字符集设定
$mcharset = 'gbk';		// 系统页面默认字符集, 可选 'gbk', 'big5', 'utf-8'
$cms_version = '7.0';
$lan_version = 'sc';	//简体sc,繁体tc

$ckpre = 'ANq_';
$ckdomain = '';
$ckpath = '/';
$adminemail = 'admin@your.com';
$phpviewerror = 1;//是否报告程序出错信息，0-不报告，1-只报告给管理员，2-报告给所有会员，3-报告给所有人

$excache_prefix = 'Dw31F3_';
$ex_memcache_server = '';
#$ex_memcache_server = '';
$ex_memcache_port = 11211;
$ex_memcache_pconnect = 1;
$ex_memcache_timeout = 2;
$ex_xcache = 0;
$ex_secache = 0;
$ex_secache_size = 100;//单位M