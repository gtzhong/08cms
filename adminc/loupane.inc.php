<?PHP
//$chid = 4;//ָ��chid
//cls_env::SetG('chid',$chid);

$mchid = $curuser->info['mchid'];

$oA = new cls_archive();

//0Ϊ����༭��1Ϊ�ĵ����
$isadd = $oA->isadd;

//�ļ�ͷ��
$oA->top_head();

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ��Ա����ֻ�ܱ༭���˷������ĵ� */
//$oA->allow_self();
$sql_ids = "SELECT CONCAT(loupan,',',xiezilou,',',shaopu) as lpids FROM {$tblprefix}members_$mchid WHERE mid='$memberid'"; 
$lpids = $db->result_one($sql_ids); //echo $sql_ids.":$lpids<BR>$oA->aid";
if(empty($lpids)) $lpids = 0;
if(!strstr(",$lpids,",','.$oA->aid.',')) $oA->message('�Բ�����û��Ȩ�޹����¥�̡�');

/* ���ñ�������������������Ĭ��Ϊfmdata */
//$oA->setvar('fmdata','archivenew');

/* �������������ϵ������������������ϵ */
//$oA->setvar('coids',array(2,3,4));

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;

$skip = 'onlyold'; //����onlyold,onlynew
$ftemp = &$oA->fields; //cls_cache::Read('fields',$chid);
$fields = array(); 
foreach($ftemp as $k => $field){
	if($field['cname']){
		//$field['cname'] = str_replace('¥��','С��',$field['cname']);
		if(!isset($field[$skip])) $fields[$k] = $field; 
	}
} 
unset($fields['jgjj'],$fields['jdjj'],$fields['bdsm']);
$oA->fields = $fields;

#-----------------

if(!submitcheck('bsubmit')){
	
	if($isadd){//��Ӳ���Ҫ
		//���ʱԤ������Ŀ
		//��Ա����������ʾ��Ϣ,����a,b��ʽ��limit,valid���ݣ����ȼ������
		//a: $madd_msg = $oA->getmtips(array('check'=>1,'limit'=>array($rules['total'],$total),'valid'=>array($rules['valid'],$valid),),'');
		//   $oA->fm_guide_bm("madd_ch02",'fix'); //madd_ch02������ռλ����{$madd_msg},��$madd_msg���Զ��ӵ�madd_ch02��ȥ��
		//b: $msg = $oA->getmtips(array('check'=>1,'limit'=>array($rules['total'],$total),),'');
		//   $oA->fm_guide_bm($msg,'fix');
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	/*
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("","?action=$action");
	
	//����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_album('pid');
	
	$oA->fm_caid(array('hidden'=>1)); //������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	// ������ϵ,����-��Ȧ,����-վ��,������ϵ
	$oA->fm_rccid1(); // 1,2,
	$oA->fm_rccid3(); // 3,14
	$oA->fm_ccids(array()); 
	// ѡ������ǰ
	$oA->fm_fields(array('zxcd','lcs','hxs','tslp')); 
	$oA->fm_footer();
	
	$fix_arr = array('wydz','ltbk','keywords','abstract');
	$oA->fm_header('��������');
	$oA->fm_fields(array('subject','kprq','jfrq','dj','dt',)); //��ǰ��Ŀ
	//$oA->fm_relalbum('5',12, '¥����Ƶ');
	//$oA->fm_relalbum('6',13, '¥�̿�����');
	$oA->fm_fields(array_merge($fix_arr,array('content','thumb','loupanlogo','lphf','lppmtu',)),1); //�ų���Ŀ
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('ͼ��˵��');
	$oA->fm_fields(array('content'));
	$oA->fm_fields_other($fix_arr); //�ų���Ŀ,�ź���
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('��չ����','',array('hidden'=>1));
	$oA->fm_fields($fix_arr);
	$oA->fm_params(array('jumpurl','ucid','createdate','clicks',));
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	*/
	//($title,$url)��url�пɲ�ָ��chid��aid
	//$oA->coids_showed = array('41'); //����ʾ
	$oA->fm_header("$channel[cname] - ��������","?action=$action");
	$oA->fm_album('pid'); //����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_caid(array('hidden'=>1)); //������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_fields(array('subject')); //����
	$oA->fm_ccids(array('12','18')); //��ҵ����,����״̬
	$oA->fm_fields(array('kprq','jfrq','dj')); //��������,��������,����
	$oA->fm_fields(array('zxcd','lcs','tslp')); //װ�޳̶�,¥��,��ɫ¥��,
	$oA->fm_footer();
	
	$oA->fm_header("$channel[cname] - ������Ϣ");
	$oA->fm_rccid1(); // 1,2,����
	$oA->fm_rccid3(); // 3,14,����
	$oA->fm_fields(array('hxs','address','jtxl','dt','pano')); //����,¥�̵�ַ,��ͨ��·,��ͼ,�־�����ID
	$oA->fm_footer();
	
	$oA->fm_header("$channel[cname] - ������Ϣ");
	$oA->fm_fields(array('tel','sldz')); //���۵绰,��¥��ַ
	$oA->fm_fields(array('wyf','wygs','wydz')); //��ҵ��,��ҵ��˾,��ҵ��ַ
	//$oA->fm_relalbum('6',13, '¥�̿�����');
	$oA->fm_fields(array('xkzh','ltbk','qtbz')); //���֤��,��̳���,������ע
	$oA->fm_footer();
    
	// bgmj,symj,kjmj,bzccg,bzcmj,dtcg,dtmj,dts,dtfq,ktkfsj,wltx,wsj,afxt,gsfs,gdxt,pfxt,pwxt
	// spzmj,zlc,mk,js,tygl,wlm,dccg,wsj,dts,dtfq,ktkfsj,wltx,gsfs,gdxt,pfxt,pwxt,lnpt
    if($oA->arc->archive['chid']!=4){
    	$oA->fm_header("$channel[cname] - ��ҵ��Ϣ");
    	$oA->fm_fields(array('bgmj','symj','kjmj','bzccg','bzcmj','dtcg','dtmj','dts','dtfq','ktkfsj','wltx','wsj','afxt','gsfs','gdxt','pfxt','pwxt','spzmj','zlc','mk','js','tygl','wlm','dccg','wsj','dts','dtfq','ktkfsj','wltx','gsfs','gdxt','pfxt','pwxt','lnpt'));
    	$oA->fm_footer();
    }
	
	$a1 = array('thumb','loupanlogo','lphf','lppmtu'); //ͼ
	$a2 = array('keywords','abstract');
	$oA->fm_header("$channel[cname] - ��������");
	//$oA->fm_ccids(); //��ʾ������ϵ
	$oA->fm_fields_other(array_merge($a1,$a2,array('content'))); //�ų���Ŀ,�ź���
	$oA->fm_footer();
	
	$oA->fm_header('ͼ��˵��');
	//$oA->fm_relalbum('5',12, '¥����Ƶ');
	$oA->fm_fields_other(array_merge($a2,array('content'))); //ͼ
	$oA->fm_fields(array('content'));
	$oA->fm_footer();
	
	$oA->fm_header('��չ����','',array('hidden'=>1));
	//$oA->fm_fields_other(); //ʣ��,�Զ���
	$oA->fm_fields($a2);
	$oA->fm_params(array());
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{

	if($isadd){
		
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->sv_regcode('archive');
		
		//���ʱԤ������Ŀ���ɴ�$coids��array(1,2)
		$oA->sv_pre_cns(array());
		
	}
	
	//����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
	$oA->sv_allow();
	
	if($isadd){
		//����һ���ĵ�
		if(!$oA->sv_addarc()){
			//���ʧ�ܴ���
			$oA->sv_fail();
		}
	}
	
	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());

	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	//����������������̨Ĭ��Ϊarray('createdate')����Ա����Ĭ��Ϊarray('ucid')
	$oA->sv_params(array());
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid',0);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>

