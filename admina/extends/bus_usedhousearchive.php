<?PHP
$caid = in_array($caid,array(613,617)) ? $caid : 613;
$chid = $caid==617 ? 118 : 117; //echo $caid;
cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

$oA = new cls_archive(); //_08House_Archive

# CK������ã���������ýű�ʱ��̳���ȥ
#$ck_plugins_enable = "{$oA->__ck_paging_management}"; //{$oA->__ck_plot_pigure},{$oA->__ck_size_chart},

/* 0Ϊ����༭��1Ϊ�ĵ����ϵ */
$isadd = $oA->isadd;

$oA->top_head();//�ļ�ͷ��


/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------

if(!submitcheck('bsubmit')){    
    
	if($isadd){//��Ӳ���Ҫ
		//���ʱԤ������Ŀ
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("$channel[cname] - ��������","?entry=extend$extend_str&caid=$caid");
	$oA->fm_ccids(array('9','19'));
	//$oA->fm_album('pid3'); //����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_caid(array('hidden' => 1)); //������Ŀ��
	$oA->fm_ulpmc(); // С������ 'lpmc'  $this->arc
	$oA->fm_rccid1(); // ����-��Ȧ1,2,
	$oA->fm_rccid3(); // ����-վ��3,14
	//$oA->fm_czumode(); // ���޷�ʽ,���ʽ
	$oA->fm_cprice(); // ���,�۸�
	$oA->fm_footer();
	
	$oA->fm_header('����');	
	$oA->fm_fields(array('subject')); //����
	$oA->fm_fields(array('address','dt'),0); //��ַ/��ͼ
	$oA->fm_ctypes(); // ���/����(fwjg-���ݽṹ,zxcd-װ�޳̶�,cx-����,fl-����)
	$oA->fm_clouceng(); // ¥��/¥��,
	$oA->fm_fields(array('louxing')); // ¥��
	$oA->fm_ccids($oA->coids); //������ϵ(��ҵ����)
	
	$skip1 = array('content','thumb'); //ͼ����Ϣ array(content,thumb) +����ͼ,С��ͼ
	$skip2 = array('lxdh','xingming','fdname','fdtel','fdnote'); //��ϵ��,������Ϣ
	$skip3 = array('keywords','abstract'); //�ؼ��֣�ժҪ  
	$oA->fm_fields_other(array_merge($skip1,$skip2,$skip3)); //����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	$oA->fm_fields($skip1,0); // ͼ����Ϣ
	$oA->fm_footer();
	
	$oA->fm_header('��ϵ��ʽ');
	$oA->fm_cfanddong(array('lxdh','xingming'));
	$oA->fm_fields($skip2,0); 
	$oA->fm_footer();
	
	$oA->fm_header('��չ����','',array('hidden'=>1));	
	$oA->fm_fields($skip3,0);
	$oA->fm_params(array('clicks','createdate','arctpls','customurl','subjectstr'));

	$oA->fm_footer('bsubmit');
	$oA->fm_fyext(); //��չ��js,��������-ȫѡ
	
	$oA->fm_guide_bm('','0');
	
}else{

	if($isadd){
		$oA->sv_regcode('archive');
		$oA->sv_pre_cns(array());
	}
	$oA->sv_allow(); //����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
	if($isadd){
		if(!$oA->sv_addarc()){
			$oA->sv_fail();
		}
	}
	
	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());
	
	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	//����������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->sv_params(array('clicks','createdate','arctpls','customurl','subjectstr'));
	$oA->sv_fyext($fmdata);
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid36',36);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>
