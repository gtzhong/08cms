<?PHP
$caid = in_array($caid,array(612,616)) ? $caid : 612;
$chid = $caid==616 ? 116 : 115; //echo $caid;
cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);
#-----------------

$oA = new cls_archive();

/* 0Ϊ����༭��1Ϊ�ĵ����ϵ */
$isadd = $oA->isadd;

$oA->top_head();//�ļ�ͷ��


/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

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
unset($fields['jgjj'],$fields['jdjj']);
if(!$isadd) unset($fields['dj'],$fields['bdsm']);
$oA->fields = $fields;
$oA->fields_did[] = 'stpic';
#-----------------

if(!submitcheck('bsubmit')){
	
	if($isadd){//��Ӳ���Ҫ
		//���ʱԤ������Ŀ
		$oA->fm_pre_cns();
	}
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	$a1 = array('thumb','loupanlogo','lphf','lppmtu'); //ͼ
	$a2 = array('keywords','abstract');
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("$channel[cname] - ��������","?entry=extend$extend_str&caid=$caid");
	$oA->fm_album('pid'); //����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_caid(array('hidden'=>1)); //������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_fields(array('subject')); //����
	$oA->fm_fields($a2);
	$isadd && $oA->fm_lpExist(); //js
	$oA->fm_ccids(array('12','18')); //��ҵ����,����״̬
	$oA->fm_fields(array('kpsj','kprq','jfrq','dj','bdsm')); //����˵��,��������,��������,����
    !$oA->isadd && $oA->fm_dj_edit_url();//�۸�༭����ת����
    $oA->fm_kp_info();//����ʱ����趨�Զ���ֵ������˵����JS
	$oA->fm_fields(array('zxcd','lcs','tslp')); //װ�޳̶�,¥��,��ɫ¥��,
	$oA->fm_ccids(array('41')); //¥���ö�
	$oA->fm_footer();
	
	$oA->fm_header("$channel[cname] - ������Ϣ");
	$oA->fm_rccid1(); // 1,2,����
	$oA->fm_rccid3(); // 3,14,����
	
	$oA->fm_fields(array('hxs','address','jtxl','dt','pano')); //����,¥�̵�ַ,��ͨ��·,��ͼ,�־�����ID
	$oA->fm_footer();
	
	$oA->fm_header("$channel[cname] - ������Ϣ");
	$oA->fm_fields(array('tel','sldz')); //���۵绰,��¥��ַ
	$oA->fm_fields(array('wyf','wygs','wydz')); //��ҵ��,��ҵ��˾,��ҵ��ַ
	$oA->fm_relalbum('6',13, '¥�̿�����');
	$oA->fm_fields(array('xkzh','ltbk','qtbz')); //���֤��,��̳���,������ע
	$oA->fm_footer();
	
	// bgmj,symj,kjmj,bzccg,bzcmj,dtcg,dtmj,dts,dtfq,ktkfsj,wltx,wsj,afxt,gsfs,gdxt,pfxt,pwxt
	// spzmj,zlc,mk,js,tygl,wlm,dccg,wsj,dts,dtfq,ktkfsj,wltx,gsfs,gdxt,pfxt,pwxt,lnpt
	$oA->fm_header("$channel[cname] - ��ҵ��Ϣ");
	$oA->fm_fields(array('bgmj','symj','kjmj','bzccg','bzcmj','dtcg','dtmj','dts','dtfq','ktkfsj','wltx','wsj','afxt','gsfs','gdxt','pfxt','pwxt','spzmj','zlc','mk','js','tygl','wlm','dccg','wsj','dts','dtfq','ktkfsj','wltx','gsfs','gdxt','pfxt','pwxt','lnpt'));
	$oA->fm_footer();
	
	$oA->fm_header("$channel[cname] - ��������");
	$oA->fm_ccids(); //��ʾ������ϵ
	$oA->fm_fields(array_merge($a1,$a2,array('content')),1); //�ų���Ŀ,�ź���
	$oA->fm_footer();
	
	$oA->fm_header('ͼ��˵��');
	$oA->fm_relalbum('5',12, '¥����Ƶ');
	$oA->fm_fields_other(array_merge($a2,array('content'))); //ͼ
	$oA->fm_fields(array('content'));
	$oA->fm_footer();
	
	$oA->fm_header('��չ����','',array('hidden'=>1));
	
	$oA->fm_params(array('createdate','clicks','jumpurl','relate_ids','subjectstr'));
	$oA->fm_customurl();//¥���ĵ�ҳ��̬�����ʽ
	$oA->fm_param('arctpls',array('addnums'=>array(1,2,3,4,5,6,11)));	
	$oA->fm_footer('bsubmit');
	
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
	$oA->sv_params(array('createdate','clicks','jumpurl','relate_ids','subjectstr'));
	$oA->sv_param('arctpls');
	$oA->sv_customurl();
	
	//ִ���Զ��������������ϱ��    
	isset($pid5) && $arc->updatefield('pid5',$pid5);
	isset($pid6) && $arc->updatefield('pid6',$pid6);
    //����������
    isset($fmdata['kfsname']) && $arc->updatefield('kfsname',$fmdata['kfsname']);
	$isadd && $arc->updatefield('leixing',1,"archives_$chid"); 
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	#$oA->sv_album('pid',0);
	
	//�Զ��ѷ�Χ�ڵ��ܱߺϼ���¥�̣��÷�Χ�ں�̨����������������
	if($oA->isadd){ 
		$dj = max(0,floatval($fmdata['dj']));
		if($dj){
			$sql = "highest='$dj',average='$dj',lowest='$dj',message='��ʼ����'";
			$sql .= ",aid=".$oA->aid.",isnew=1,createdate='$timestamp'";
			$sql = "INSERT INTO {$tblprefix}housesrecords SET $sql";
			$db->query($sql);
		}
		$oA->sv_zhoubian($fmdata,$oA->aid,$chid);
	}
	
	//�Զ����ɾ�̬,����ʱ��Ҫ������
	$oA->sv_static();
	$oA->sv_finish();
}
?>
