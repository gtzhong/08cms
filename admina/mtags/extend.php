<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
$extype = empty($mtagnew['extype']) ? (empty($mtag['extype']) ? '' : $mtag['extype']) : $mtagnew['extype'];
if(!$extype || empty($extypes[$extype])) mtag_error('��ѡ����չ��ʶ����');
$_exfile = dirname(__FILE__).DS."..".DS."extags/$extype.php";
if(!file_exists($_exfile)) mtag_error('δ�ҵ���չ�ӿ��ļ�');
include $_exfile;
?>
