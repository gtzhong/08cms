<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!submitcheck('bcommudetail')) {
	tabheader('������Ŀ����-'.$commu['cname'],'commudetail',"?entry=$entry&action=$action&cuid=$cuid",2,0,1);
	setChidsBar(@$commu['cfgs']['chids'],'chid');
    setPermBar('����Ȩ������', 'communew[cfgs][pmid]', @$commu['cfgs']['pmid'], 'cuadd', 'open', '');
    setPermBar('�Զ����Ȩ������', 'communew[cfgs][autocheck]', @$commu['cfgs']['autocheck'], 'cuadd', 'check', '');
    trbasic('�ظ�����ʱ����(����)','communew[cfgs][repeattime]',@$commu['cfgs']['repeattime'],'text',array('validate' => " rule=\"int\" min=\"0\"",'w' => 10,'guide' => '��λ������'));
	trbasic('��������','','�ӻ���(���)��<input type="text" min="0" rule="int" value="'.@$commu['cfgs']['acurrency'].'" name="communew[cfgs][acurrency]" id="communew[cfgs][acurrency]" size="10"> �ۻ���(ɾ��)��<input type="text" min="0" rule="int" value="'.@$commu['cfgs']['ccurrency'].'" name="communew[cfgs][ccurrency]" id="communew[cfgs][ccurrency]" size="10">','',array('guide'=>'����������ۼӻ��֣�������Աɾ���ۻ��֡�'));
	trbasic('��ע','communew[remark]',$commu['remark'],'text',array('w'=>50));
	trbasic('����˵��','communew[content]',$commu['content'],'textarea',array('w' => 500,'h' => 300,));
	tabfooter('bcommudetail','�޸�');
}else{
	empty($communew['cfgs']['chids']) && $communew['cfgs']['chids'] = array();
    $communew['cfgs']['chids'] = array_filter($communew['cfgs']['chids']);
	$communew['content'] = empty($communew['content']) ? '' : trim($communew['content']);
	$communew['remark'] = empty($communew['remark']) ? '' : trim(strip_tags($communew['remark']));
	$communew['cfgs'] = !empty($communew['cfgs']) ? addslashes(var_export($communew['cfgs'],TRUE)) : '';
	$cfgs = ",cfgs='$communew[cfgs]'";
	$db->query("UPDATE {$tblprefix}acommus SET 
				remark='$communew[remark]',
				content='$communew[content]' $cfgs				
				WHERE cuid='$cuid'");
	cls_CacheFile::Update('commus');
	adminlog('�༭������Ŀ'.$commu['cname']);
	cls_message::show('������Ŀ������ɡ�',axaction(6,"?entry=$entry&action=$action&cuid=$cuid"));
}

?>
