<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
foreach(array('btagnames','channels','fchannels') as $k) $$k = cls_cache::Read($k);
aheader();
backnav('btags','search');
$bclasses = array(
	'common' => 'ͨ����Ϣ',
	'archive' => '�ĵ����',
	'cnode' => '��Ŀ���',
	'freeinfo' => '�������',
	'commu' => '�������',
	'member' => '��Ա���',
	'other' => '����',
);
$datatypearr = array(
	'text' => '�����ı�',
	'multitext' => '�����ı�',
	'htmltext' => 'Html�ı�',
	'image' => '��ͼ',
	'images' => 'ͼ��',
	'flash' => 'Flash',
	'flashs' => 'Flash��',
	'media' => '��Ƶ',
	'medias' => '��Ƶ��',
	'file' => '��������',
	'files' => '�������',
	'select' => '����ѡ��',
	'mselect' => '����ѡ��',
	'cacc' => '��Ŀѡ��',
	'date' => '����(ʱ���)',
	'int' => '����',
	'float' => 'С��',
	'map' => '��ͼ',
	'vote' => 'ͶƱ',
	'texts' => '�ı���',
);
tabheader('����ԭʼ��ʶ  >><a id="btags_update" href="?entry=btags&action=update" onclick="return showInfo(this.id,this.href)">����</a>','btagsearch','?entry=btagsearch');
trbasic('��ʶID���ִ�','bsearch[ename]',empty($bsearch['ename']) ? '' : $bsearch['ename']);
trbasic('��ʶ���ƺ��ִ�','bsearch[cname]',empty($bsearch['cname']) ? '' : $bsearch['cname']);
trbasic('��ʶ����','bsearch[bclass]',makeoption(array('' => '����') + $bclasses,empty($bsearch['bclass']) ? '' : $bsearch['bclass']),'select');
tabfooter('bbtagsearch','����');
if(submitcheck('bbtagsearch')){
	$ename = trim(strtolower($bsearch['ename']));
	$cname = trim($bsearch['cname']);
	$bclass = trim($bsearch['bclass']);
	if(empty($ename) && empty($cname) && empty($bclass)) cls_message::show('�����������ִ�');
	tabheader('ԭʼ��ʶ��������б�','','','8');
	trcategory(array('���','��ʶ����',array('ʹ����ʽ'.'1','txtL'),array('ʹ����ʽ'.'2','txtL'),array('ʹ����ʽ'.'3','txtL'),'��ʶ���','��ϸ����','�ֶ�����'));
	$i = 1;
	foreach($btagnames as $k => $v){
		if((!$ename || in_str($ename,$v['ename'])) 
			&& (!$cname || in_str($cname,$v['cname']))
			&& (!$bclass || $v['bclass'] == $bclass)){
			$sclasses = array();
			if($v['bclass'] == 'archive'){
				foreach($channels as $chid => $channel){
					$sclasses[$chid] = $channel['cname'];
				}
			}elseif($v['bclass'] == 'cnode'){
				$sclasses = array(
					'catalog' => '��Ŀ',
					'coclass' => '����',
				);
			}elseif($v['bclass'] == 'freeinfo'){
				foreach($fchannels as $chid => $channel){
					$sclasses[$chid] = $channel['cname'];
				}
			}elseif($v['bclass'] == 'commu'){
				$sclasses = array(
					'comment' => '����',
					'purchase' => '����',
					'answer' => '����',
				);
			}elseif($v['bclass'] == 'other'){
				$sclasses = array(
					'attachment' => '����',
					'vote' => 'ͶƱ',
				);
			}
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\">$i</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtL\">{<b>$v[ename]</b>}</td>\n".
				"<td class=\"txtL\">{\$<b>$v[ename]</b>}</td>\n".
				"<td class=\"txtL\">{\$<b>v[$v[ename]]</b>}</td>\n".
				"<td class=\"txtC w80\">".@$bclasses[$v['bclass']]."</td>\n".
				"<td class=\"txtC w80\">".(empty($sclasses[$v['sclass']]) ? '-' : $sclasses[$v['sclass']])."</td>\n".
				"<td class=\"txtC w80\">".$datatypearr[$v['datatype']]."</td>\n".
				"</tr>";
			$i ++;
		}
	}
	tabfooter();
}
?>