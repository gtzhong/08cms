<?PHP
$chid = 4;//ָ��chid
cls_env::SetG('chid',$chid);
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

$skip = 'onlynew'; //����onlyold,onlynew
$ftemp = &$oA->fields; //cls_cache::Read('fields',$chid);
$fields = array(); 
foreach($ftemp as $k => $field){
	if($field['cname']){
		$field['cname'] = str_replace('¥��','С��',$field['cname']);
		if(!isset($field[$skip])) $fields[$k] = $field; 
	}
} 
unset($fields['jgjj'],$fields['jdjj'],$fields['bdsm']);
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
	$a2 = array('keywords','abstract');
	$oA->coids_showed[] = '18'; //����ʾ
	$oA->coids_showed[] = '41'; //����ʾ
	
	$oA->fm_header("С�� - ��������","?entry=extend$extend_str");
	$oA->fm_album('pid'); //����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_caid(array('hidden'=>1)); //������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_fields(array('subject')); //����
	$oA->fm_fields($a2);
    $isadd && $oA->fm_lpExist(); //js
	$oA->fm_ccids(array('12','18')); //��ҵ����,����״̬
	$oA->fm_rccid1(); // 1,2,����
	$oA->fm_rccid3(); // 3,14,����
	$oA->fm_fields(array('hxs','address','jtxl','dt')); //¥�̵�ַ,��ͨ��·,��ͼ (����,
	$oA->fm_footer();
	
	$oA->fm_header("С�� - ������Ϣ");	
	$oA->fm_fields(array('wyf','wygs','wydz')); //��ҵ��,��ҵ��˾,��ҵ��ַ
	$oA->fm_relalbum('6',13, 'С��������');
	$oA->fm_fields(array('xkzh','ltbk','qtbz')); //��̳���,������ע (���֤��,
	$oA->fm_footer();
	

	
	$oA->fm_header("С�� - ��������");
	$oA->fm_ccids(); //��ʾ������ϵ
	$oA->fm_fields_other(array_merge($a2,array('xqjs'))); //�ų���Ŀ,�ź���
	$oA->fm_footer();
	
	$oA->fm_header('ͼ��˵��');
	$oA->fm_relalbum('5',12, 'С����Ƶ');
	$oA->fm_fields(array('xqjs'));
	$oA->fm_footer();
	
	$oA->fm_header('��չ����','',array('hidden'=>1));

	
	$oA->fm_params(array());
	$oA->fm_param('arctpls',array('addnums'=>array(7,8,9,10)));
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
	$oA->sv_params(array());
	$oA->sv_param('arctpls');
	
	//ִ���Զ��������������ϱ��
	isset($pid5) && $arc->updatefield('pid5',$pid5);
	isset($pid6) && $arc->updatefield('pid6',$pid6);
	$isadd && $arc->updatefield('leixing',2,"archives_$chid"); 
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid',0);
	
	//�Զ��ѷ�Χ�ڵ��ܱߺϼ���¥�̣��÷�Χ�ں�̨����������������
	$oA->isadd && $oA->sv_zhoubian($fmdata,$oA->aid,$chid);
	
	//�Զ����ɾ�̬,����ʱ��Ҫ������
	$oA->sv_static();
	$oA->sv_finish();
}
?>
