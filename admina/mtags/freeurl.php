<?php
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	trbasic('* ָ������ҳID','mtagnew[setting][fid]',empty($mtag['setting']['fid']) ? '' : $mtag['setting']['fid']);
	tabfooter();
}else{
	$mtagnew['setting']['fid'] = trim($mtagnew['setting']['fid']);
	if(empty($mtagnew['setting']['fid'])) mtag_error('��ָ������ҳ');
}
?>
