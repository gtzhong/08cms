<?PHP
/*
** ���ڰ�����ļ����Ϊ�ĵ��뽻�� 
** ��ֺ����ɾ��  arccols.cls.php �ļ� �����function type_cid()����
				   archives.cls.php �ļ� �����function  sv_o_cumu_all()����

** 
*/
/* ������ʼ������ */
$chid = 106;//���붨�壬�����ܴ�url�Ĵ���
$cuid = 37;
$caid = 516;
$actext = empty($actext)?'qget':$actext;
$aid = empty($aid)?'':max(0,intval($aid));
$aidstr =  empty($aid)?'':"&aid=$aid";
$cid = empty($cid)?'':max(0,intval($cid)); 
$ajax = empty($ajax)?'':$ajax; 
$my_q = empty($my_q)?'':max(0,intval($my_q)); 
$isa = empty($isa)?'':$isa; 
$info= array('cuid'=>$cuid,'actext'=>$actext,'aid'=>$aid,'action'=>$action);


$prestr = '';
$selectstr = '';
$fromstr = '';
$wherestr = '';
if(in_array($actext,array('qget','qout'))){
	$selectstr = "a.*,b.currency";
	$fromstr = "{$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid b ON a.aid=b.aid ";
}else{
	$selectstr = " cu.*,cu.createdate AS ucreatedate,a.createdate,a.initdate,a.caid,a.chid,a.customurl,a.nowurl,a.subject ";
	$fromstr = "{$tblprefix}commu_answers cu INNER JOIN {$tblprefix}".atbl($chid)." a ON a.aid=cu.aid";
}
$actext == 'qget'   && $wherestr = " a.tomid='$memberid' AND a.chid='$chid' AND a.checked='1' ";
$actext == 'qout'   && $wherestr = " a.mid='$memberid' AND a.chid='$chid' ";
if($actext == 'answer'){
	$aid || $wherestr = " cu.mid='$memberid'";
	$aid && $wherestr = "a.aid='$aid' AND cu.toaid='0' and cu.mid='$memberid'";
}


#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action&actext=$actext$aidstr",//��url���������Ҫ����chid��pid
'pre' => "",//Ĭ�ϵ�����ǰ׺
'where' => $wherestr,//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => $fromstr,//sql�е�FROM����
'select' => $selectstr,//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$aid || $oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.aid' => '�ĵ�ID'),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$aid || $oL->s_additem('indays');
$aid || $oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��

if($cid){
	if(!($commu = cls_cache::Read('commu',$cuid))) cls_message::show('�����ڵĽ�����Ŀ��');
	if(!($row = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] WHERE cid='$cid'"))) cls_message::show('ָ������ѯ��¼�����ڡ�');
	$arc = new cls_arcedit;
	$arc->set_aid($row['aid'],array('au'=>0));
	if(!$arc->aid) cls_message::show('ָ�����ĵ������ڡ�');	
	if($my_q){
		if($my_q != $arc->archive['aid']) cls_message::show('��ָ�������յ��Ļش�');
	}elseif($memberid != $row['mid'])cls_message::show('��ָ�������ύ�Ļش�');
	
	$fields = cls_cache::Read('cufields',$cuid);
	if(!submitcheck('bsubmit')){
		tabheader("����Ļش� &nbsp;<a href=\"".cls_ArcMain::Url($arc->archive)."\" target=\"_blank\">>>{$arc->archive['subject']}</a>",'newform',"?action=$action&cid=$cid",2,1,1);
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			$a_field->init($v,isset($row[$k]) ? $row[$k] : '');
			$a_field->trfield('fmdata');
		}
		unset($a_field);
		tabfooter('bsubmit');
	}else{//���ݴ���
		$sqlstr = '';
		$c_upload = new cls_upload;	
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			if(isset($fmdata[$k])){
				if($isa && !in_array($k,array('huida'))) continue;
				if(!$isa && in_array($k,array('huida'))) continue;
				$a_field->init($v,isset($row[$k]) ? $row[$k] : '');
				$fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
				$sqlstr .= ",$k='$fmdata[$k]'";
				if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $sqlstr .= ",{$k}_x='$y'";
			}
		}
		unset($a_field);
		$isa && $fmdata['huida'] && $sqlstr .= ",amid='$memberid',aname='{$curuser->info['mname']}',dafutime='$timestamp'";
		$sqlstr = substr($sqlstr,1);
		$sqlstr && $db->query("UPDATE {$tblprefix}$commu[tbl] SET $sqlstr  WHERE cid='$cid'");
		$c_upload->closure(1,$cid,"commu$cuid");
		$c_upload->saveuptotal(1);
		cls_message::show('��ѯ��¼�༭���',axaction(6,M_REFERER));
	}
}else{	
	if(!submitcheck('bsubmit')){	
		//$aid���б��У����Ӵ��ݹ�������
		//ͷ��ѡ������
		$aid || backnav('kuaiwen',$actext);		
		
		//�������� ******************
		$oL->s_header();
		$aid || $oL->s_view_array();
		$aid || $oL->s_footer();
		
	
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
		

		
		//�����б���Ŀ������б����а������������Ҫ�����ݴ���ʱ������������Ĵ���
		//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
		
		$actext == 'answer' || $oL->m_additem('selectid');
		$actext == 'answer' && $oL->m_additem('cid',array('type'=>'cid'));
		$oL->m_additem('subject',array('len' => 40,'title'=>'��������'));
		if(in_array($actext,array('qget','qout'))){
			$oL->m_additem('checked',array('type'=>'bool','title'=>'���','len' => 40,));
			//$oL->m_additem('close',array('title'=>'״̬',));
			$oL->m_additem('currency',array('title'=>'���ͷ�','mtitle'=>'{currency}��'));
			$oL->m_additem('close',array('type'=>'close','title'=>'״̬','width'=>35,));			
			$oL->m_additem('clicks',array('title'=>'���','mtitle'=>'{clicks}'));
			
			$oL->m_additem('createdate',array('type'=>'date','title'=>'���ʱ��','mtitle'=>'{createdate}','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
		}
		if($actext == 'qget'){			
			$oL->m_additem('stat_1',array('type'=>'url','title'=>'��','mtitle'=>'[�ش�]','url'=>"etools/answer.php?aid={aid}&isfull=1&mid=$memberid"));				
		}
		if($actext == 'answer'){
			$oL->m_additem('content',array('type'=>'other','title'=>'�ش�����','len'=>10));
			$oL->m_additem('isanswer',array('type'=>'bool','title'=>'��Ѵ�',));
			$oL->m_additem('tocid',array('type'=>'ShowContent','title'=>'�ʴ���ʽ'));
			$oL->m_additem('ucreatedate',array('type'=>'date','title'=>'�ύʱ��',));
			//$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=wenda_manage&cid={cid}&my_q={aid}",'width'=>40,));
		}


		
		
		//$oL->m_mcols_style("{selectid} &nbsp;{subject}<br>{shi}/{ting]/{chu}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
		
		//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_top();
		
		//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
		$oL->m_view_main();
		
		//��ʾ�б���β��
		$oL->m_footer();
		
		//��ʾ����������************
		$oL->o_header();
		
		//��ʾ��ѡ��
		//$oL->o_view_bools('���б���',array('bool1','bool2',));
		$oL->o_view_bools();
		
		//��ʾ������
		$oL->o_view_rows();
		
		$oL->o_footer('bsubmit');
		$oL->guide_bm('','0');		
	}else{	
		//Ԥ����δѡ�����ʾ
		$oL->sv_header();
		$info['selectid'] = $selectid;
		$info['arcdeal'] = $arcdeal;
		
		//�б���������������ݴ���
	//	$oL->sv_e_additem('clicks',array());
	//	$oL->sv_e_all();
		
		//��������������ݴ���
		$actext == 'answer' || $oL->sv_o_all();
		$actext == 'answer' && $oL->sv_o_cumu_all($info);
		
		//��������
		$oL->sv_footer();
	}
}
?>