<?php

//��չ����
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
$exfenxiao = $exconfigs['distribution']; // Array ( [num] => 3 [pnum] => 100 [vtime] => 15 [unvnum] => 10 [fxwords] => msg ) 
$exfenxiao['num'] = empty($exfenxiao['num']) ? 3 : max(1,intval($exfenxiao['num']));
$exfenxiao['vtime'] = empty($exfenxiao['vtime']) ? 15 : max(3,intval($exfenxiao['vtime']));

$cuid = 50; //�����ⲿ��chid����Ҫ��������
$cid = empty($cid) ? 0 : max(0,intval($cid));

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'e',
	'pchid' => 0,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>"",
	'from'=>"",
	'where' => "", //��������,ǰ����Ҫ[ AND ]
	'fnoedit' => array('xingming','dianhua','jine'),
);


if($cid){
	
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");	
		$oA->fm_items(array(),array(),array('noaddinfo'=>1));
		trbasic('��ȡ����','',date('Y-m-d H:i:s',$oA->predata['createdate']),'');
		$oA->fm_footer('bsubmit'); //bsubmit
		
		$dmy = get_yjdetail(get_fxlist($oA->predata['fxids'],''));
		$dsub = get_yjdetail(get_fxlist($oA->predata['fxidp'],''),'');

		if($dmy[0]){
			$cy_arr = array();
			tabheader("[{$oA->predata['mname']}]�����Ƽ��򷿻�õ�Ӷ����ϸ",'','',6);
			$cy_arr[] = '���Ƽ�����';
			$cy_arr[] = '��ϵ�绰';
			$cy_arr[] = '¥������';
			$cy_arr[] = 'Ӷ��(Ԫ)';
			$cy_arr[] = '������';
			$cy_arr[] = '��ע';
			trcategory($cy_arr);
			echo $dmy[1];
			tabfooter();
		}
		if($dsub[0]){
			$cy_arr = array();
			tabheader("���¼������˻�õ�Ӷ����ϸ",'','',6);
			$cy_arr[] = '���Ƽ�����';
			$cy_arr[] = '��ϵ�绰';
			$cy_arr[] = '¥������';
			$cy_arr[] = 'Ӷ��(Ԫ)';
			$cy_arr[] = '�¼�������';
			$cy_arr[] = '��ע';
			trcategory($cy_arr);
			echo $dsub[1];
			tabfooter();
		}
	}else{
		//�ύ��Ĵ���
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$oA->sv_update();//ִ���Զ��������������ϱ��
		$oA->sv_upload();//�ϴ�����
		$oA->sv_finish();//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
	}
	
}else{
	$oL = new $class($_init); 
	
	$oL->top_head();
    
	//������Ŀ **************************** 'a.subject'=>'�����',
	$oL->s_additem('keyword',array('fields' => array('cu.xingming' => '�����','cu.dianhua' => '��ϵ�绰','cu.mname' => '��Ա�ʺ�'),'custom'=>1));
	$oL->s_additem('status',array('xtype' =>'field'));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete');
	//$oL->o_additem('check');
	//$oL->o_additem('uncheck');	

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
        //$oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=jztgbm");	
		$oL->s_footer();	
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
		//$oL->m_additem('selectid'); 
		$oL->m_additem('xingming',array());
        $oL->m_additem('dianhua',array());
		$oL->m_additem('jine',array('title'=>'���(Ԫ)')); 
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'��ȡʱ��'));        
		$oL->m_additem('status',array('type'=>'field','empty'=>'<span style="color:#999">δ֧��</span>')); 
		$oL->m_additem('mname',array('title'=>'��Ա�ʺ�','width'=>80));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&cid={cid}",));
		$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_main(); 
		$oL->m_footer(); //��ʾ�б���β��
		
		$oL->o_header(); //��ʾ����������************
		$oL->o_view_bools(); //��ʾ��ѡ��
		
		$oL->o_footer(''); //
		$oL->guide_bm('','0');
		
	}else{
		
		$oL->sv_header(); //Ԥ����δѡ�����ʾ
		$oL->sv_o_all(); //��������������ݴ���
		$oL->sv_footer(); //��������
		
	}
			
}

?>