<?PHP
/**
* ��Ա���ĵĻ�Ա����ű�
* ����ϵͳ����Ҫ�������롢����
*/


/* ������ʼ������ */
$_init = array(//��Ա���Ŀ��Բ������κβ���
);
#-----------------

$oA = new cls_member($_init);

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

/*��ʼ��������Ŀ-->*/
$grouptypes = cls_cache::Read('grouptypes');
foreach($grouptypes as $k => $v) {//��Ա���Ľ��û��ֶ�����Ч
	$oA->additem('ugid'.$k,array('_type' => 'ugid','onlyset' => 1));//��Ա��
}

$oA->additem('email');//�����ʼ�
$oA->additem('mtcid');//�ռ�ģ�巽��
foreach($oA->fields as $k => $v){//��̨�ܹ��ֶ�
	$oA->additem($k,array('_type' => 'field'));
}
$oA->additem('webcall');//400�绰����
//$oA->items_did[] = 'mtcid';
//���ؾ�����ģ���е��ֶΣ�������
$oA->items_did[] = 'blacklist';
// ר���ֶ�-����
// mtcid - ֻһ��ģ��-����
$mfexp = array('dantu','ming','danwei','quaere','mtcid');
foreach($mfexp as $k){//��̨�ܹ��ֶ�
	$oA->items_did[] = $k;
}

//����Ƕ��ַ������ⷿԴ��ת���������ӣ��ύ֮��ֱ�ӷ��ط���ҳ��
$type = empty($type)?'':$type;
$typeStr = strstr(M_REFERER,'chuzuadd')?"&type=chuzuadd":(strstr(M_REFERER,'chushouadd')?'&type=chushouadd':'');

$curuser = cls_UserMain::CurUser(); 
$lxdh = $curuser->info['lxdh'];

if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��mchid��mid
	$oA->fm_header("","?action=$action$typeStr");
	
	//$oA->fm_items('email,image,xingming,szqy,lxdh,companynet,companyadr');
	$oA->fm_items();
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	echo "<script type='text/javascript'>_08cms_validator.init('ajax','fmdata[lxdh]',{url:'{$cms_abs}"._08_Http_Request::uri2MVC("ajax=checkUserPhone&old=$lxdh&val=%1")."'});</script>";
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
    //�ϴ�ͷ�����ӻ��֡���Ա���ķ���ҳ����ת�����������Ϻ��Զ�����ԭ������ҳ��
    $oA->sv_all_common_ex($type);
}
?>