<?php
/**������Դ/������Ϣ; �޸ķ�Դ; (��etools/gpub_func.php����ֲ����...)
 * @example   ������URL��index.php?/ajax/addarc/...
 * @author    Peace@08cms.com
 * @copyright Copyright (C) 2008 - 2015 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_addarc extends _08_Models_Base
{
    private $fmpre = 'fmdata';
	
	public function __toString()
    {
		$mcharset = $this->_mcharset;	
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$timestamp = TIMESTAMP; 
		$cms_abs = cls_env::mconfig('cms_abs');
		$curuser = cls_UserMain::CurUser();
		
		$msgcode = @$this->_get['msgcode'];
		$GLOBALS[$this->fmpre] = &$this->_get['fmdata']; // archivebase.cls.php��������GLOBALS�����ġ�
		$fmdata = &$GLOBALS[$this->fmpre];
		$fmdata = cls_string::iconv('utf-8',$mcharset,$fmdata);
			
		include_once _08_INCLUDE_PATH."admin.fun.php";
		
		// ------------------- ���
		
			$aid = @$this->_get['aid'];
			$actdo = @$this->_get['actdo']; $actdo = empty($actdo) ? "save" : $actdo;
			$caid = @$this->_get['caid'];
			$action = @$this->_get['action']; $action = empty($action) ? "chushou" : $action;
			$ismob = @$this->_get['ismob'];

			if(!empty($ismob)){ //�ֻ��淢��
				if(!in_array($caid,array('3','4'))) cls_message::show('��������!');
				$chid = $caid==3 ? 3 : 2;
				$names = array('3'=>'���ַ�','4'=>'����'); 
			}else{ 
				$chids = array('chushou'=>3,'chuzu'=>2);
				$caids = array('chushou'=>3,'chuzu'=>4);
				$names = array('chushou'=>'���ַ�','chuzu'=>'����');
				if(!in_array($action,array('chushou','chuzu'))) cls_message::show('��������!');
				$chid = $chids[$action];
				$caid = $caids[$action];
			}
			cls_env::SetG('chid',$chid);
			cls_env::SetG('caid',$caid);
			$isadd = $actdo=='edit' ? 0 : 1; //echo "isadd=$isadd, actdo=$actdo, aid=$aid, ismob=$ismob,";
			if($aid && $ismob){ 
				$curuser = cls_UserMain::CurUser();
				$arc = new cls_arcedit;
				$arc->set_aid($aid,array('au'=>0,'ch'=>1));
				$data = $arc->archive;
				if($data['caid']!=$caid || $data['mid']!=$curuser->info['mid']){
					cls_message::show("��������[aid=$aid]! ");
				}
				$actname = '�༭';
				$f2dis = cls_env::mconfig('fcdisabled2');
				$f3dis = cls_env::mconfig('fcdisabled3');
			}else{
				$actname = '����';	
			}
			$mchid = empty($curuser->info['mchid']) ? 0 : $curuser->info['mchid'];
			if(in_array($mchid,array(1,2))){ // ��ͨ��Ա�뾭���˽����Ա���ķ���
				if(empty($ismob)){
					header("location:{$cms_abs}adminm.php?action={$action}add");	
				}
			}elseif(!empty($close_gpub)){
				cls_message::show('������Դ����ע���Ϊ��ͨ��Ա�򾭼��ˣ�','');	
			}elseif(!empty($mchid)){
				$curuser->info['mid'] = 0;
			}
			
			$oA = new cls_archive();
			$oA->isadd = $isadd;
			//$oA->message("��������췢��<span$style>�޶�����</span>,�����ٷ�����Դ��");
		
			$oA->read_data();
			resetCoids($oA->coids, array(9,19)); 
			
			/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
			$chid = &$oA->chid;
			$arc = &$oA->arc;
			$channel = &$oA->channel;
			$fields = &$oA->fields;
			$oA->fields['content']['mode'] = 1;
			
			// 
			$count_gpub = cls_env::mconfig('count_gpub'); //�οͷ�������
			$count_gpub = empty($count_gpub) ? 3 : $count_gpub;
			
			$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH); 
			$fyvalid = empty($exconfigs['fanyuan']['fyvalid']) ? 30 : $exconfigs['fanyuan']['fyvalid']; //������Ч����
			$sms = new cls_sms();
			
		// ------------------- Save-��ʼ
			if($isadd){
				$smskey = 'arcfypub'; $ckkey = 'smscode_'.$smskey; 
				if(empty($ismob) && $sms->smsEnable($smskey)){
					@$pass = smscode_pass($smskey,$msgcode,$fmdata['lxdh']);
					if(!$pass){
						cls_message::show('�ֻ�ȷ��������', M_REFERER);
					}
					msetcookie($ckkey, '', -3600);
					$tel_checked = 1;
				}else{ //�贫����֤�����ͣ�����Ĭ��Ϊ'archive' 
					$oA->sv_regcode("archive_fy");
					$tel_checked = 0;
				}
			}
			
			//*/������������
			$style = " style='font-weight:bold;color:#F00'";
			$sql = "SELECT count(*) FROM {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid WHERE a.mid='0' AND c.lxdh='$fmdata[lxdh]' AND a.createdate>'".($timestamp-85400)."' ";
			$all_gpub = $db->result_one($sql); $all_gpub = empty($all_gpub) ? 0 : $all_gpub;
			if($all_gpub>=$count_gpub){
				$oA->message("��������췢��<span$style>�޶�����</span>,�����ٷ�����Դ��");
			}//*/
			
			if($isadd && $ismob){ //�ֻ���ǰ̨Ϊtext,��̨Ϊhtml
				$fmdata = &$GLOBALS[$oA->fmdata];
				$fmdata['content'] = nl2br($fmdata['content']);
			}
			//���ʱԤ������Ŀ���ɴ�$coids��array(1,2)
			$oA->sv_pre_cns(array());
			
			//����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
			//$oA->sv_allow();
			
			//����һ���ĵ�
			//if(!$oA->sv_addarc()){ 
			empty($oA->arc) && $oA->arc = new cls_arcedit;
			if($isadd){
				$oA->aid = $oA->arc->arcadd($oA->chid,$oA->predata['caid']);
				if(!$oA->aid){ 
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
			$oA->sv_params(array('createdate','enddate',));
			
			$oA->arc->updatefield('enddate',$timestamp+$fyvalid*86400); //�����ϼ�
			// - �οͷ�������Ҫ���
			//$oA->sv_fyext();
			
			if($isadd){
				// �ֻ�������֤��Ĭ�����
				$tel_checked && $oA->arc->updatefield('checked',$tel_checked);
			}
			
			//�����ֶ�mchid����Ż�Ա��ģ��ID�������Ǹ��˷������Ǿ����˷���
			$oA->arc->updatefield('mchid',@$curuser->info['mchid']);
			
			//��Ч��
			$oA->sv_enddate();
			
			$oA->sv_update();
			
			//�ϴ�����
			#$oA->sv_upload(); //��ͼ���ڷ�Դid��,���ﲻ����; 
			
			//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
			$oA->sv_album('pid3',3);
			
			if($isadd){
				//����ͼƬ
				$fmdata['fythumb'] = cls_env::GetG('fmdata.fythumb'); 
				$imgscfg = array('chid'=>121,'caid'=>623,'pid'=>$oA->aid,'arid'=>38,);
				//$imgscfg['props'] = array(1=>'subject',2=>'lx');
				$mre = $oA->sv_images2arcs($fmdata,'thumb',$imgscfg,'fythumb');
				$db->update('#__'.atbl($chid), array('thumb' => $mre[1]))->where("aid = $oA->aid")->exec();
			}
			$oA->sv_fyext($fmdata,$chid);
			//�Զ����ɾ�̬
			$oA->sv_static();
			
			//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
			//$oA->sv_finish();
			
			$curuser = cls_UserMain::CurUser();
			$checked = $curuser->pmautocheck($channel['autocheck']);
			if($isadd){
				$acname2 = '���';
				$cmsg = ($checked || $tel_checked) ? "����Ϣ�Ѿ���ϵͳ[�Զ����]��" : "<br>����Ϣ<span style='color:red;'>��Ҫ����Ա���</span>������ǰ̨��ʾ"; 
				//echo 'end';
			}else{
				$acname2 = '�޸�';
				$cmsg = '';	
			}

			if(empty($ismob)){
				$remsg = "{$names[$action]} {$acname2}��ɣ�$cmsg";
			}else{
				$remsg = "{$names[$caid]} {$acname2}��ɣ�$cmsg";	
			}
			return array('error'=>'','message'=>$remsg);
		
		// ------------------- Save-����
		
	}

}