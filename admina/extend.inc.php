<?php
if(!empty($extend)){
    _08_FilesystemFile::filterFileParam($extend);
	$extend_str = "&extend=$extend";
	if(@is_file($ex = dirname(__FILE__)."/extends/$extend.php")){
		include($ex);
	}else mexit('ָ�����ļ������ڡ�');
}else mexit('ָ���Ĳ���δ���塣');
