<?php
 
$cuid = 36; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(1,intval($caid));
$chid = empty($chid) ? 3 : max(2,intval($chid)); 
$cid = empty($cid) ? 0 : max(0,intval($cid));
$mid = $curuser->info['mid'];
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit';
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&chid=$chid", //��url���������Ҫ����mchid
	'select'=>"SELECT w.owerstatus,w.jjrstatus,w.weituodate,cu.cid,cu.pid,cu.louhao,cu.loushi,cu.mj,cu.shi,cu.ting,cu.wei,cu.zj,cu.lxr,cu.lpmc,cu.chid as cchid,cu.createdate as cucreate ",
	'from'=>" FROM {$tblprefix}weituos w INNER JOIN {$tblprefix}commu_weituo cu ON w.cid=cu.cid ",
	'where' => " AND w.tmid='$mid' AND cu.chid = '$chid'", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
    if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('ί�й����ѹرա�');
    if(empty($ysubmit) && empty($nsubmit)){
    $result = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] c INNER JOIN {$tblprefix}weituos w ON w.cid=c.cid WHERE c.cid='$cid' AND w.tmid='$memberid'");
    empty($result) && cls_message::show('�������󲻴���',axaction(6,M_REFERER));		
    echo form_str('viewweituo',"?action=$action&cid=$cid");
    tabheader('��ϵ��ʽ');
    trbasic('�ֻ�','',$result['tel'],'');
    trbasic('��ϵ��','',$result['lxr'],'');
    tabfooter();
    tabheader('������Ϣ');
    trbasic('С������','',$result['lpmc'],'');
    trbasic('¥����','',$result['louhao'].'��/��'.$result['loushi'].'��','');
    trbasic($result['chid']==3 ? '��֤���' : '�������','',$result['mj'].'ƽ����','');
    trbasic('����','',$result['shi'].'��'.$result['ting'].'��'.$result['wei'].'��','');
    trbasic($result['chid'] == 3 ? '�ۼ�' : '���','',$result['zj'].($result['chid'] == 3 ? '��Ԫ' : 'Ԫ/��'),'');
    $result['chid'] == 2 && trbasic('���޷�ʽ','',$result['zlfs'] ? '����' : '����','');
    tabfooter();
    
    if($result['jjrstatus'] != 2)echo '<div align="center"><input class="button" type="submit" name="ysubmit" value="���棬����ί��" onclick="return confirm(\'�ڽ���ί��֮ǰ����ȷ�������Ѿ����´��Э�顣\')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="button" type="submit" name="nsubmit" value="�ܾ�ί��"></div></form>';
    }elseif(!empty($ysubmit)){
    $fmdata = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] c INNER JOIN {$tblprefix}weituos w ON w.cid=c.cid WHERE c.cid='$cid' AND w.tmid='$memberid'");
    empty($fmdata) && cls_message::show('�������󲻴���',axaction(6,M_REFERER));
    $fmdata['jjrstatus'] == 2 && cls_message::show('�����ظ�������',axaction(6,M_REFERER));
    $fmdata['caid'] = $fmdata['chid'] == 3 ? '3' : '4';
    $fmdata['subject'] = $fmdata['address'];
    $fields = cls_cache::Read('fields',$fmdata['chid']);
    $cotypes = cls_cache::Read('cotypes');	
    $a_field = new cls_field;
    $arc = new cls_arcedit;
    if($aid = $arc->arcadd($fmdata['chid'],$fmdata['caid'])){
    
    	foreach($cotypes as $k => $v){
    		if(!$v['self_reg'] && !empty($fmdata["ccid$k"])){
    			$arc->arc_ccid($fmdata["ccid$k"],$k,$v['emode'] ? $fmdata["ccid{$k}date"] : 0);
    		}
    	}
    	foreach($fields as $k => $v){
    		if(isset($fmdata[$k])){
    			$arc->updatefield($k,$fmdata[$k],$v['tbl']);
    			if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $arc->updatefield($k.'_'.$x,$y,$v['tbl']);
    		}
    	}
    	//����������д��ĸ�Լ�ȫƴ
    	$arc->updatefield('subjectstr',cls_string::Pinyin(str_replace('\\','',$fmdata['subject']),1));
    	
    	//�����ֶ�mchid����Ż�Ա��ģ��ID�������Ǹ��˷������Ǿ����˷���
    	$arc->updatefield('mchid',$curuser->info['mchid']);
    			
    	$arc->updatefield('fdname',$fmdata['lxr'],"archives_$fmdata[chid]");
    	$arc->updatefield('fdtel',$fmdata['tel'],"archives_$fmdata[chid]");
    	$curuser->detail_data();
    	$arc->updatefield('lxdh',$curuser->info['lxdh'],"archives_$fmdata[chid]");
    	$arc->updatefield('xingming',$curuser->info['xingming'],"archives_$fmdata[chid]");
    	$validday = empty($validday) ? 30 : $validday;
    	$membervalidday = ($curuser->info['grouptype14date'] - $timestamp)/86400;
    	$arc->setend($curuser->info['grouptype14'] == 8 && $membervalidday > $validday ? $membervalidday : $validday);
    	$arc->auto();
    	$arc->autocheck();
    	$arc->updatedb();
    	$db->query("UPDATE {$tblprefix}weituos set jjrstatus='2' WHERE tmid='$memberid' AND cid='$cid'");
    	cls_message::show(($fmdata['chid'] == 3 ? '��ί�г��ۡ�' : '��ί�г��⡿')."��ӳɹ�<br/>����ȥ���Ʒ�Դ���� <a href=\"?action=".($fmdata['chid'] == 2 ? 'chuzu' : 'chushou')."add&aid=$aid\" onclick=\"return floatwin('open_inarchive',this)\">{$fmdata['subject']}</a>");
    }else{
    	cls_message::show('���ʧ��',axaction(6,M_REFERER));	
    }
    cls_message::show('�����ɹ�',axaction(6,M_REFERER));
    }elseif(!empty($nsubmit)){
    $db->query("UPDATE {$tblprefix}weituos set jjrstatus='1' WHERE tmid='$memberid' AND cid='$cid'");
    cls_message::show('�����ɹ�',axaction(6,M_REFERER));
    }

}else{
	$oL = new $class($_init); 
	$oL->top_head();
    
	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.lpmc'=>'С������'),'custom'=>1));
    $oL->s_additem('jjrstatus',array('pre'=>'w.'));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str();
	
    if(empty($tmp)){        
    	$_menu = $chid == 2 ? 'chuzu' : 'chushou';
    	backnav('weituo',$_menu);
    }
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();        
    $oL->s_footer();
    
	
	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
    $oL->m_additem('xqimg',array('title'=>'С��ͼƬ','side'=>'L'));
	$oL->m_additem('lpmc',array('title'=>'С������','mtitle'=>"С������:<font color='#36F'>{lpmc}</font>",'side'=>'L')); 
    $oL->m_additem('louhao',array('title'=>'¥��','mtitle'=>'{louhao}��/��')); 
    $oL->m_additem('loushi',array('title'=>'¥��','mtitle'=>'{loushi}��')); 
    $oL->m_additem('mj',array('title'=>'���','mtitle'=>'{mj}ƽ����')); 
    
    $oL->m_additem('shi',array('title'=>'��','mtitle'=>'{shi}��')); 
    $oL->m_additem('ting',array('title'=>'��','mtitle'=>'{ting}��')); 
    $oL->m_additem('wei',array('title'=>'��','mtitle'=>'{wei}��')); 
    
    if($chid==2){//����
	   $oL->m_additem('zj',array('title'=>'���','mtitle'=>"{zj}Ԫ/��"));
	}elseif($chid==3){//����
	   $oL->m_additem('zj',array('title'=>'�ܼ�','mtitle'=>"{zj}��Ԫ"));
	}
    	
    $oL->m_addgroup('{lpmc}<br/>��ַ��{louhao}&nbsp;{loushi}<br/>{mj}&nbsp;{shi}{ting}{wei}&nbsp;{zj}','������Ϣ');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���  
    
    
    $oL->m_additem('lxr',array('title'=>'��ϵ��','mtitle'=>'��ϵ�ˣ�{lxr}','side'=>'L'));		       
	$oL->m_additem('connectinfo',array('type'=>'url','title'=>'��ϵ','mtitle'=>'�鿴��ϵ','url'=>"?action=$action&cuid=$cuid&cid={cid}&chid=$chid",'width'=>40,));
    $oL->m_additem('cucreate',array('type'=>'date','title'=>'ί������','mtitle'=>'{cucreate}'));
    $oL->m_addgroup('{lxr}<br/>ί�����ڣ�{cucreate}<br/>{connectinfo}','������Ϣ'); 
     
    $oL->m_additem('entrusted_state',array('type'=>'date','title'=>'ί��״̬'));
    
	$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_main(); 
	$oL->m_footer(); //��ʾ�б���β��
	

	$oL->o_view_bools(); //��ʾ��ѡ��
	
	$oL->o_footer('');
	$oL->guide_bm('','0');
		

			
}

?>