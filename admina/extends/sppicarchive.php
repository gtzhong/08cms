<?PHP
/*
** �����̨�ű����������ĵ����������༭�����������߽ű�����������ű���ȥ�����ר�ò��ֵĴ���
** ��ͨ��url����$chid���ɻ������ݲ�ͬģ�͵��ĵ�����
*/
/* ������ʼ������ */
# $chid = 5;//ָ��chid
#-----------------
	$chid = empty($chid) ? 4 : intval($chid);
	$caid = 2;
	cls_env::SetG('chid',$chid);
	cls_env::SetG('caid',$caid);
	$_url_str = "&chid=$chid&aid=$aid&pid=$pid";
	$oA = new cls_archive();
	
	/* 0Ϊ����༭��1Ϊ�ĵ����ϵ */
	$isadd = $oA->isadd;
	
	$oA->top_head();//�ļ�ͷ��
	
	//���Ҫ���ٺϼ�����ӣ���Ҫѡ�������ϼ��������ô����pid=-1
	//$pchid = 4; //���ʱ-ѡ�������ϼ�(¥��)
	
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
		$oA->fm_header("","?entry=extend$extend_str$_url_str");
		
		//����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
		$oA->fm_album('pid');
		
		//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
		$oA->fm_caid(array('hidden' => 1));		
		
		//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
		$oA->fm_fields(array('stpic'),0);
		
	
		//�����submitcheck(��ť����)��ͬ��ֵ
		$oA->fm_footer('bsubmit');
		
		//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
		//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
		$oA->fm_guide_bm('','0');
		
	}else{
		/*
		** ע�⣺���ݴ����ͬ��Ҫ�ϸ�ָ����Щ����Ҫ������ֶλ���ϵ!
		** 
		** 
		*/
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
		//����������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
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
