<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
foreach(array('grouptypes','currencys','mchannels',) as $k) $$k = cls_cache::Read($k);
if(empty($gtid) || empty($grouptypes[$gtid])) cls_message::show('��ָ����ȷ�Ļ�Ա����ϵ');

$grouptype = $grouptypes[$gtid];
$usergroups = fetch_arr();
$gtcname = $grouptypes[$gtid]['cname'];
$no_deepmode = in_array($gtid,@explode(',',$deep_gtids)) ? $no_deepmode : 0;
if($action == 'usergroupsedit'){
	if(!submitcheck('busergroupsadd') && !submitcheck('busergroupsedit')){
		$items = '';
		foreach($usergroups as $k => $usergroup){
			$items .= "<tr  class=\"txtcenter txt\">".
					"<td class=\"txtC\">$k</td>\n".
					"<td class=\"txtL\"><input type=\"text\" size=\"12\" name=\"usergroupsnew[$k][cname]\" value=\"$usergroup[cname]\"></td>\n".
					"<td class=\"txtC\">" . (empty($usergroup['ico']) ? '-' : "<img src=\"$usergroup[ico]\" border=\"0\" onload=\"if(this.height>20) {this.resized=true; this.height=20;}\" onmouseover=\"if(this.resized) this.style.cursor='pointer';\" onclick=\"if(!this.resized) {return false;} else {window.open(this.src);}\">") . "</td>\n".
					"<td class=\"txtC\"><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"usergroupsnew[$k][prior]\" value=\"$usergroup[prior]\"></td>\n".
					"<td class=\"txtC\">".($grouptype['mode'] < 2 ? '-' : "<input type=\"text\" size=\"12\" name=\"usergroupsnew[$k][currency]\" value=\"$usergroup[currency]\">")."</td>\n".
					"<td class=\"txtC\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
					"<td class=\"txtC\"><a href=\"?entry=$entry&action=usergroupcopy&gtid=$gtid&ugid=$k\" onclick=\"return floatwin('open_usergroupsedit',this)\">����</a></td>\n".
					"<td class=\"txtC\"><a href=\"?entry=$entry&action=usergroupdetail&gtid=$gtid&ugid=$k\" onclick=\"return floatwin('open_usergroupsedit',this)\">����</a></td></tr>\n";
		}
		tabheader('�༭��Ա��-'.$grouptype['cname']."&nbsp;&nbsp;&nbsp;&nbsp;".($grouptype['mode'] == 2?"<a href=\"?entry=$entry&action=update_grouptype&gtid=$gtid\" onclick=\"return floatwin('open_inarchive',this)\"><font color=\"#FF0000\">�޸�ȫ����Ա���ֵȼ�</font></a>":''),'usergroupsedit',"?entry=$entry&action=usergroupsedit&gtid=$gtid",'7');
		$cr_title = '��ػ���';
		if($grouptype['mode'] == 2){
			$cr_title = '��������'.'('.$currencys[$grouptype['crid']]['cname'].')';
		}elseif($grouptype['mode'] == 3){
			$cr_title = '�һ�����'.'('.(empty($grouptype['crid']) ? '�ֽ�': $currencys[$grouptype['crid']]['cname']).')';
		}
		trcategory(array('ID','��Ա��|L','ͼ��','����',$cr_title,"<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,$no_deepmode,checkall,this.form, 'delete', 'chkall')\">ɾ?",'����','�༭'));
		echo $items;
		tabfooter('busergroupsedit','�޸�');

		tabheader('��ӻ�Ա��-'.$grouptype['cname'],'usergroupsadd',"?entry=$entry&action=usergroupsedit&gtid=$gtid");
		trbasic('��Ա������','usergroupadd[cname]');
		($grouptype['mode'] > 1) && trbasic($cr_title,'usergroupadd[currency]');
		tabfooter('busergroupsadd','���');
		a_guide('usergroupsedit');
	}elseif(submitcheck('busergroupsadd')){
		if(!$usergroupadd['cname']) cls_message::show('��Ա�����ϲ���ȫ',"?entry=$entry&action=usergroupsedit&gtid=$gtid");
		$usergroupadd['currency'] = $grouptype['mode'] < 2 ? 0 : max(0,intval($usergroupadd['currency']));
		$db->query("INSERT INTO {$tblprefix}usergroups SET
					ugid=".auto_insert_id('usergroups').",
					cname='$usergroupadd[cname]',
					currency='$usergroupadd[currency]',
					gtid='$gtid'");

		adminlog('��ӻ�Ա��');
		cls_CacheFile::Update('usergroups',$gtid);
		cls_message::show('��Ա��������', "?entry=$entry&action=usergroupsedit&gtid=$gtid");
	}elseif(submitcheck('busergroupsedit')){
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $ugid) {
				$db->query("DELETE FROM {$tblprefix}mcnodes WHERE mcnvar='ugid$gtid' AND mcnid='$ugid'");
				$db->query("DELETE FROM {$tblprefix}usergroups WHERE ugid='$ugid'");
				$db->query("UPDATE {$tblprefix}members SET grouptype$gtid=0 WHERE grouptype$gtid='$ugid'",'SILENT');
				unset($usergroupsnew[$ugid]);
			}
			cls_CacheFile::Update('mcnodes');
		}

		if(!empty($usergroupsnew)){
		
			$_update_cu = array();//��Ÿı��˻��ֵĵȼ�������
			$_is_chushihua = 0;//�Ƿ���ֳ�ʼ��
			$_usergroup_key = array_keys($usergroupsnew);
			$_count_num = count($usergroupsnew);
			$_last_ugid = $_usergroup_key[$_count_num-1];
			$_second_last = $_count_num == 1 ?$_last_ugid:$_usergroup_key[$_count_num-2];
			$_first_ugid = $_usergroup_key[0];			
			
			foreach($usergroupsnew as $ugid => $usergroup){
				$usergroup['currency'] = $grouptype['mode'] < 2 ? 0 : max(0,intval($usergroup['currency']));
				$usergroup['prior'] = max(0,intval($usergroup['prior']));
				$usergroup['cname'] = empty($usergroup['cname']) ? $usergroups[$ugid]['cname'] : $usergroup['cname'];
				if(($usergroup['cname'] != $usergroups[$ugid]['cname']) || ($usergroup['prior'] != $usergroups[$ugid]['prior'] || ($usergroup['currency'] != $usergroups[$ugid]['currency']))){
					if($usergroup['currency'] != $usergroups[$ugid]['currency']){					
						$_new_a1 = $_new_a2 = array();
						foreach($usergroupsnew as $k => $v)	$_new_a1[] = $v['currency'];
						$_new_a2 = $_new_a1;arsort($_new_a2);
						//���ȫ���Ļ����Ƿ�Ӵ�С���У�Ϊ�˷�ֹ�޸�ʱ�ͼ��Ļ��ֱȸ߼���Ҫ��					
						if($_new_a1 !== $_new_a2) cls_message::show('�����޸Ĵ��󣬵͵ȼ��Ļ��ֲ��ܸ��ڸߵȼ��Ļ���',M_REFERER);	
						unset($_new_a1,$_new_a2);
						//�ų���ͼ��������ȼ����������Ϊ0����$_is_chushihua++�������ж��Ƿ��ǵ�һ�����øû��ֵȼ�����
						$ugid != $_last_ugid && empty($usergroups[$ugid]['currency']) && $_is_chushihua++;
						$_f_ugid = $ugid == $_first_ugid ?$ugid: $_usergroup_key[array_search($ugid,$_usergroup_key)-1];
						if(empty($_is_chushihua)){
							$_n_ugid = $ugid == $_last_ugid ? $ugid:$_usergroup_key[array_search($ugid,$_usergroup_key)+1];
							$_front_c = $usergroups[$_f_ugid]['currency'];
							$_next_c  = $usergroups[$_n_ugid]['currency'];
						
							if($ugid != $_first_ugid  && $usergroup['currency'] >= $_front_c){
								cls_message::show('�����޸Ĵ��󣬵͵ȼ��Ļ��ֲ��ܸ��ڻ��ߵ����޸�ǰ�ĸߵȼ��Ļ���',M_REFERER);
							}
							if($ugid != $_last_ugid && $usergroup['currency'] <= $_next_c){
								cls_message::show('�����޸Ĵ��󣬸ߵȼ��Ļ��ֲ��ܵ��ڻ��ߵ��ڸߵȼ��Ļ���',M_REFERER);
							}
							$_update_cu[$ugid]['oldcurrency']=$usergroups[$ugid]['currency'];
							if($usergroup['currency'] > $usergroups[$ugid]['currency'])	$_update_cu[$ugid]['up'] = 1;							
						}else{//���ǵ��Ƿ��ǵ�һ�����õ�ʱ�򣬵ȼ��Ļ���ȫΪ0
							$_update_cu[$ugid]['oldcurrency'] = $ugid== $_first_ugid ? 'the_first' : $usergroupsnew[$_f_ugid]['currency'];							
							if($usergroupsnew[$_last_ugid]['currency'] <= 0 && !empty($usergroupsnew[$_second_last]['currency'])){							
								$_update_cu[$_last_ugid]['oldcurrency'] = $usergroupsnew[$_second_last]['currency'];
								$_update_cu[$_last_ugid]['newcurrency'] = 0;
							}
						}
						$_update_cu[$ugid]['newcurrency'] = $usergroup['currency'];
					}
					$db->query("UPDATE {$tblprefix}usergroups SET
								cname='$usergroup[cname]',
								currency='$usergroup[currency]',
								prior='$usergroup[prior]'
								WHERE ugid='$ugid'");
				}
			}
		}
		adminlog('�༭��Ա��');
		cls_CacheFile::Update('usergroups',$gtid);
		update_grouptype();		
		cls_message::show('��Ա���޸����', "?entry=$entry&action=usergroupsedit&gtid=$gtid");
	}
}elseif($action == 'usergroupcopy' && $gtid && $ugid){
	if(!($usergroup = $usergroups[$ugid])) cls_message::show('��ָ����ȷ�Ļ�Ա��');
	if(!submitcheck('busergroupcopy')){
		tabheader('��Ա�鸴��'.'-'.$grouptype['cname'],'usergroupcopy',"?entry=$entry&action=usergroupcopy&gtid=$gtid&ugid=$ugid",2,0,1);
		trbasic('Դ��Ա������','',$usergroup['cname'],'');
		trbasic('�»�Ա������','usergroupnew[cname]','','text',array('validate'=>makesubmitstr('usergroupnew[cname]',1,0,0,30)));
		tabfooter('busergroupcopy');
		a_guide('usergroupcopy');
	}else{
		$usergroupnew['cname'] = trim(strip_tags($usergroupnew['cname']));
		if(empty($usergroupnew['cname'])) cls_message::show('���ϲ���ȫ',M_REFERER);
		$sqlstr = "cname='$usergroupnew[cname]'";
		foreach($usergroup as $k => $v) if(!in_array($k,array('ugid','cname'))) $sqlstr .= ",$k='".addslashes($v)."'";
		$db->query("INSERT INTO {$tblprefix}usergroups SET ugid=".auto_insert_id('usergroups').",$sqlstr");
		$ugid = $db->insert_id();
		adminlog('���ƻ�Ա��');
		cls_CacheFile::Update('usergroups',$gtid);
		cls_message::show('��Ա�鸴�����',"?entry=$entry&action=usergroupdetail&gtid=$gtid&ugid=$ugid");
	}
}elseif(($action == 'usergroupdetail') && $gtid && $ugid){
	if(!($usergroup = $usergroups[$ugid])) cls_message::show('��ָ����ȷ�Ļ�Ա��');
	if(!submitcheck('busergroupdetail')){
		tabheader('�༭��Ա��'.'-'.$grouptype['cname'],'usergroupdetail',"?entry=$entry&action=$action&gtid=$gtid&ugid=$ugid",2,0,0);
		trbasic('��Ա������','usergroupnew[cname]',$usergroup['cname']);
		trbasic('��ѡ���û�Աģ��','',makecheckbox('usergroupnew[mchids][]',cls_mchannel::mchidsarr(),!empty($usergroup['mchids']) ? explode(',',$usergroup['mchids']) : array(),5),'');
		trbasic('��Ա����Ч��','usergroupnew[limitday]',empty($usergroup['limitday']) ? '' : $usergroup['limitday'],'text',array('w' => 6,'guide' => '��λ���죬����Ϊ������'));
		$na = array(0 => '���������');
		foreach($usergroups as $k => $v) $k == $ugid || $na[$k] = $v['cname'];
		trbasic('���ں�ת��������','usergroupnew[overugid]',makeoption($na,$usergroup['overugid']),'select');
		trspecial('��Ա��ͼ��',specialarr(array('type' => 'image','varname' => 'usergroupnew[ico]','value' => $usergroup['ico'],)));
		if(!$grouptype['issystem'] && $grouptype['mode'] != 2) trbasic('ע���Զ���Ϊ�����Ա','usergroupnew[autoinit]',$usergroup['autoinit'],'radio',array('guide'=>'����Ա�����Զ����������У�ѡ�����ȼ��ߵ�һ����Ա�顣'));
		if($grouptype['forbidden']){
			$ugforbids = cls_cache::exRead('ugforbids');
			trbasic('��ֹ���²���','',makecheckbox('usergroupnew[forbids][]',$ugforbids,empty($usergroup['forbids']) ? array() : explode(',',$usergroup['forbids']),5),'');
		}else{
			if($grouptype['afunction']){
				$amconfigs = cls_cache::Read('amconfigs');
				$arr = array();foreach($amconfigs as $k => $v) $arr[$k] = $v['cname'];
				trbasic('��̨�����ɫ','',makecheckbox("usergroupnew[amcids][]",$arr,empty($usergroup['amcids']) ? array() :explode(',',$usergroup['amcids']),5),'',array('guide' => '-���������Ϊ��վid��0Ϊ��վ��'));
			}elseif($grouptype['mode'] > 1){
				trbasic('��ػ�������','usergroupnew[currency]',$usergroup['currency']);
			}
			$ugallows = cls_cache::exRead('ugallows');
			trbasic('�����Ա������Ȩ��','',makecheckbox('usergroupnew[allows][]',$ugallows,empty($usergroup['allows']) ? array() : explode(',',$usergroup['allows']),5),'');
			trbasic('������������','usergroupnew[maxpms]',$usergroup['maxpms']);
			trbasic('�ϴ�����'.'(M)','usergroupnew[maxuptotal]',$usergroup['maxuptotal']);
			trbasic('��������'.'(M)','usergroupnew[maxdowntotal]',$usergroup['maxdowntotal']);

		}
		tabfooter('busergroupdetail','�޸�');
		a_guide('usergroupdetail');
	}else{
		$sqlstr = '';
		if($grouptype['forbidden']){
			$usergroupnew['forbids'] = empty($usergroupnew['forbids']) ? '' : implode(',',$usergroupnew['forbids']);
			$sqlstr .= "forbids='$usergroupnew[forbids]',";
		}else{
			if($grouptype['afunction']){
				$usergroupnew['amcids'] = empty($usergroupnew['amcids']) ? '' : implode(',',$usergroupnew['amcids']);
				$sqlstr .= "amcids='$usergroupnew[amcids]',";
			}
			$usergroupnew['currency'] = ($grouptype['mode'] < 1) || empty($usergroupnew['currency']) ? 0 : max(0,intval($usergroupnew['currency']));
			//�������ָı�
			$_is_one = 0;$_isadd = 0;    
			$_issame = array_values($usergroupnew['mchids'])==array_values(explode(',',$usergroups[$ugid]['mchids']))?'1':'0';    
			$_isadd = array_diff($usergroupnew['mchids'],explode(',',$usergroups[$ugid]['mchids']));
  
			if($usergroupnew['currency'] != $usergroups[$ugid]['currency'] || !$_issame){
				$_is_one = '1';
				update_grouptype();	
			}
			$usergroupnew['allows'] = empty($usergroupnew['allows']) ? '' : implode(',',$usergroupnew['allows']);
			$usergroupnew['maxuptotal'] = empty($usergroupnew['maxuptotal']) ? 0 : max(0,intval($usergroupnew['maxuptotal']));
			$usergroupnew['maxdowntotal'] = empty($usergroupnew['maxdowntotal']) ? 0 : max(0,intval($usergroupnew['maxdowntotal']));
			$usergroupnew['maxpms'] = empty($usergroupnew['maxpms']) ? 0 : max(0,intval($usergroupnew['maxpms']));
			$sqlstr .=  "maxpms='$usergroupnew[maxpms]',
			currency='$usergroupnew[currency]',
			allows='$usergroupnew[allows]',
			maxuptotal='$usergroupnew[maxuptotal]',
			maxdowntotal='$usergroupnew[maxdowntotal]',";
		}
		$usergroupnew['cname'] = empty($usergroupnew['cname']) ? $usergroup['cname'] : $usergroupnew['cname'];
		$usergroupnew['mchids'] = !empty($usergroupnew['mchids']) ? implode(',',$usergroupnew['mchids']) : '';
		$usergroupnew['limitday'] = empty($usergroupnew['limitday']) ? 0 : max(0,intval($usergroupnew['limitday']));
		$usergroupnew['autoinit'] = $grouptype['issystem'] || $grouptype['mode'] == 2 || empty($usergroupnew['autoinit']) ? 0 : 1;

		$c_upload = cls_upload::OneInstance();
		$usergroupnew['ico'] = upload_s($usergroupnew['ico'],$usergroup['ico'],'image');
		if($k = strpos($usergroupnew['ico'],'#')) $usergroupnew['ico'] = substr($usergroupnew['ico'],0,$k);
		$c_upload->closure(2,$ugid,'usergroup');
		$c_upload->saveuptotal(1);

		$sqlstr .= "cname='$usergroupnew[cname]',
				mchids='$usergroupnew[mchids]',
				autoinit='$usergroupnew[autoinit]',
				ico='$usergroupnew[ico]',
				limitday='$usergroupnew[limitday]',
				overugid='$usergroupnew[overugid]'
				";
		$db->query("UPDATE {$tblprefix}usergroups SET $sqlstr WHERE ugid='$ugid'");
		adminlog('�����޸Ļ�Ա��');
		cls_CacheFile::Update('usergroups',$gtid);
		cls_message::show('��Ա��༭���',axaction(6,"?entry=$entry&action=usergroupsedit&gtid=$gtid"));
	}
}elseif($action == 'update_grouptype'){
	$_currency_id = $db->result_one("SELECT crid FROM {$tblprefix}grouptypes where gtid = '$gtid'");
	$_usergroups_key = array_keys($usergroups);
	$_first_key = $_usergroups_key[0];
	foreach($usergroups as $k => $v){
		if(!empty($v['mchids'])){
			$_mchid_arr = mimplode($v['mchids']);
			$k != $_first_key && $_front_key = $_usergroups_key[array_search($k,$_usergroups_key)-1];			
			$_where_str = " WHERE mchid in (".$_mchid_arr.") AND currency".$_currency_id." >= '$v[currency]' ".($k == $_first_key ? '' : " AND currency".$_currency_id." < ".$usergroups[$_front_key]['currency']);
			$db->query("UPDATE {$tblprefix}members set grouptype".$gtid." = '$k' ".$_where_str);
		}	
	}
	cls_message::show('��Ա�ȼ��޸����',axaction(6,"?entry=$entry&action=usergroupsedit&gtid=$gtid"));	
}
function fetch_arr(){
	global $db,$tblprefix,$gtid;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}usergroups WHERE gtid='$gtid' ORDER BY currency DESC,prior desc,ugid desc");
	while($r = $db->fetch_array($query)){
		$rets[$r['ugid']] = $r;
	}
	return $rets;
}

function fetch_one($ugid){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}usergroups WHERE ugid='$ugid'");
	return $r;
}


//����ȼ��������û����޸�ʱ���޸Ļ�Ա��Ϣ�ֶ�grouptypes17
//$_update_cu:		��������޸�ʱ�����ָı��ugid�Լ�����
//$usergroups:		��ȡ�ɵĻ��ֵȼ�����
//$usergroupnew:	��ȡ�޸�֮���ύ�Ļ��ֵȼ�����
//$_is_one:			�ж��Ƿ�����Ե�һ�ĵȼ�����༭�����޸�
//$_issame:			�ж��û�ģ�͸ı�
//$_isadd:			�����ж���������Щģ��

function update_grouptype(){
	global $db,$tblprefix,$grouptype,$_update_cu,$usergroups,$ugid,$usergroupnew,$gtid,$_is_one,$_issame,$_isadd;
	
	if($grouptype['mode'] == 2){
		$_currency_id = $db->result_one("SELECT crid FROM {$tblprefix}grouptypes where gtid = '$gtid'");
 
    
		if(!empty($_update_cu)){
			$_arr_keys = array_keys($usergroups);
			foreach($_update_cu as $k=>$v){
				$_next_key = array_search($k,$_arr_keys)+1;
				//$v['up']��ʶ���޸ĺ�Ļ��ֱ�ԭ���ĸ�
				$_ugid = !empty($v['up']) ? $_arr_keys[$_next_key] : $k;
				$_mchid_str = " AND mchid IN (".mimplode($usergroups[$_ugid]['mchids']).") ";
				$_where_str = !empty($v['up']) ? " WHERE currency".$_currency_id." >= '$v[oldcurrency]' AND currency".$_currency_id." < '$v[newcurrency]' ".$_mchid_str : " WHERE currency".$_currency_id." >= '$v[newcurrency]' ".($v['oldcurrency'] == 'the_first'? '':" AND currency".$_currency_id." < '$v[oldcurrency]' ").$_mchid_str;
				!empty($usergroups[$_ugid]['mchids']) && $db->query("UPDATE {$tblprefix}members SET grouptype".$gtid." = '$_ugid' ".$_where_str);
			}
		}else{
			if(empty($_is_one)) return;
			$_arr_keys  = array_keys($usergroups);
			$_now_key   = array_search($ugid,$_arr_keys);
            //ע�������Ƿ�����ͼ������ؽ��
			$_next_ugid = array_search($ugid,$_arr_keys)+1 >(count($_arr_keys)-1)?$_arr_keys[array_search($ugid,$_arr_keys)]:$_arr_keys[array_search($ugid,$_arr_keys)+1];
            //ע�������Ƿ�����߼������ؽ��
			$_front_ugid = array_search($ugid,$_arr_keys)-1 < 0 ? $_arr_keys[array_search($ugid,$_arr_keys)] : $_arr_keys[array_search($ugid,$_arr_keys)-1];           
			if($_now_key > 0 && $_now_key < (count($_arr_keys)-1)){
				if($usergroupnew['currency'] >= $usergroups[$_front_ugid]['currency'] || $usergroupnew['currency'] <= $usergroups[$_next_ugid]['currency']){
					cls_message::show('���ֲ��ܴ��ڵ�����һ�����ֻ���С�ڵ�����һ������',M_REFERER);
				}
			}
			$_ugid  = $usergroupnew['currency'] > $usergroups[$ugid]['currency'] ? $_next_ugid : $ugid;
			if(!$_issame && $_isadd){
				$_isadd = mimplode($_isadd);
                //������޸���ߵȼ���
				if(array_search($ugid,$_arr_keys)-1 < 0){
                    $_where_str = " WHERE currency".$_currency_id." >= '".$usergroups[$ugid]['currency']."'";  
				}else if(array_search($ugid,$_arr_keys)+ 1 > count($_arr_keys)-1){//�����޸���ͼ�
				    $_where_str = " WHERE currency".$_currency_id." < '".$usergroups[$_front_ugid]['currency']."'";
				}else{
                    $_where_str = " WHERE currency".$_currency_id." >= '".$usergroups[$ugid]['currency']."' AND currency".$_currency_id." < '".$usergroups[$_front_ugid]['currency']."'";
                }
                #echo "UPDATE {$tblprefix}members SET grouptype".$gtid." = '$_ugid' ".$_where_str." AND mchid IN  (".$_isadd.")";
				!empty($_isadd) && $db->query("UPDATE {$tblprefix}members SET grouptype".$gtid." = '$_ugid' ".$_where_str." AND mchid IN  (".$_isadd.")");		
			}
			if($usergroupnew['currency'] != $usergroups[$ugid]['currency']){
				$_where_str = $usergroupnew['currency'] > $usergroups[$ugid]['currency'] ? " WHERE currency".$_currency_id." >= '".$usergroups[$ugid]['currency']."' AND currency".$_currency_id." < '$usergroupnew[currency]' " : " WHERE currency".$_currency_id." >= '$usergroupnew[currency]' AND currency".$_currency_id." < '".$usergroups[$ugid]['currency']."' ";
				$_mchid_arr = mimplode($usergroupnew['mchids']);
				!empty($usergroupnew['mchids']) && $db->query("UPDATE {$tblprefix}members SET grouptype".$gtid." = '$_ugid' ".$_where_str." AND mchid IN  (".$_mchid_arr.")");
			}
		}
	}
}

?>
