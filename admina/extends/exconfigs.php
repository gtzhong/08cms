<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('webparam') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
empty($action) && $action = 'gaoji';
if($action == 'gaoji'){
	backnav('house','gaoji');
	$rules = $exconfigs['gaoji'];
	if(!submitcheck('bsubmit')){
		$i = 0;
		foreach($rules as $k => $v){
			$i ? tabheader("$v[title] ��������") : tabheader("$v[title] ��������",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
			trbasic('����',"rulesnew[$k][available]",empty($v['available']) ? 0 : 1,'radio');
			trbasic('������������',"rulesnew[$k][title]",empty($v['title']) ? '' : $v['title'],'text',array('validate' => makesubmitstr("rulesnew[$k][title]",1,0,3,30)));			
			trbasic('��Ҫ֧�����',"rulesnew[$k][price]",empty($v['price']) ? 0 : $v['price'],'text',array('validate' => makesubmitstr("rulesnew[$k][price]",1,0,1,'','int'),'guide' => '��ԪΪ��λ����������'));
			trbasic('��Ч����',"rulesnew[$k][month]",empty($v['month']) ? 0 : $v['month'],'text',array('validate' => makesubmitstr("rulesnew[$k][month]",1,0,1,'','int'),'guide' => '����Ϊ��λ����������'));
			trbasic('�����ö�����',"rulesnew[$k][zds]",empty($v['zds']) ? '' : $v['zds'],'text',array('validate' => makesubmitstr("rulesnew[$k][zds]",1,0,1,'','int'),'guide' => '����Ϊ��λ����������'));
			trbasic('����ԤԼˢ������',"rulesnew[$k][yys]",empty($v['yys']) ? '' : $v['yys'],'text',array('validate' => makesubmitstr("rulesnew[$k][yys]",1,0,1,'','int'),'guide' => '�Դ�Ϊ��λ����������'));
			$i ++;
			tabfooter($i == count($rules) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['gaoji'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'vipgs'){
	backnav('house','vipgs');
	$rules = $exconfigs['vipgs'];
	if(!submitcheck('bsubmit')){
		$i = 0;
		foreach($rules as $k => $v){
			$i ? tabheader("$v[title] ��������") : tabheader("$v[title] ��������",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
			trbasic('����',"rulesnew[$k][available]",empty($v['available']) ? 0 : 1,'radio');
			trbasic('������������',"rulesnew[$k][title]",empty($v['title']) ? '' : $v['title'],'text',array('validate' => makesubmitstr("rulesnew[$k][title]",1,0,3,30)));			
			trbasic('��Ҫ֧�����',"rulesnew[$k][price]",empty($v['price']) ? 0 : $v['price'],'text',array('validate' => makesubmitstr("rulesnew[$k][price]",1,0,1,'','int'),'guide' => '��ԪΪ��λ����������'));
			trbasic('��Ч����',"rulesnew[$k][month]",empty($v['month']) ? 0 : $v['month'],'text',array('validate' => makesubmitstr("rulesnew[$k][month]",1,0,1,'','int'),'guide' => '����Ϊ��λ����������'));
			trbasic('����ˢ�´���',"rulesnew[$k][refnum]",empty($v['refnum']) ? '' : $v['refnum'],'text',array('validate' => makesubmitstr("rulesnew[$k][refnum]",1,0,1,'','int'),'guide' => '�Դ�Ϊ��λ����������'));
			$i ++;
			tabfooter($i == count($rules) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['vipgs'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'vipsj'){
	backnav('house','vipsj');
	$rules = $exconfigs['vipsj'];
	if(!submitcheck('bsubmit')){
		$i = 0;
		foreach($rules as $k => $v){
			$i ? tabheader("$v[title] ��������") : tabheader("$v[title] ��������",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
			trbasic('����',"rulesnew[$k][available]",empty($v['available']) ? 0 : 1,'radio');
			trbasic('������������',"rulesnew[$k][title]",empty($v['title']) ? '' : $v['title'],'text',array('validate' => makesubmitstr("rulesnew[$k][title]",1,0,3,30)));			
			trbasic('��Ҫ֧�����',"rulesnew[$k][price]",empty($v['price']) ? 0 : $v['price'],'text',array('validate' => makesubmitstr("rulesnew[$k][price]",1,0,1,'','int'),'guide' => '��ԪΪ��λ����������'));
			trbasic('��Ч����',"rulesnew[$k][month]",empty($v['month']) ? 0 : $v['month'],'text',array('validate' => makesubmitstr("rulesnew[$k][month]",1,0,1,'','int'),'guide' => '����Ϊ��λ����������'));
			trbasic('����ˢ�´���',"rulesnew[$k][refnum]",empty($v['refnum']) ? '' : $v['refnum'],'text',array('validate' => makesubmitstr("rulesnew[$k][refnum]",1,0,1,'','int'),'guide' => '�Դ�Ϊ��λ����������'));
			$i ++;
			tabfooter($i == count($rules) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['vipsj'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'upmemberhelp'){
	backnav('house','upmemberhelp');
	$rule = $exconfigs['upmemberhelp'];
	if(!submitcheck('bsubmit')){
		$i = 0;
		foreach($rule as $k=>$v){
			$i ? tabheader("$v[title]����˵��") : tabheader("$v[title]����˵��",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
			trbasic('��Ա����',"rulesnew[$k][title]",empty($v['title']) ? '' : $v['title'],'text',array('guide'=>'������Ա����'));
			trbasic("$v[title]˵��","rulesnew[$k][des]",empty($v['des']) ? '' : $v['des'],'textarea',array('w'=>'500','h'=>'300','guide' => "�û�����$v[title]����ʾ�������Ϣ��"));
			$i++;
			tabfooter($i==count($rule) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['upmemberhelp'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'gssendrules'){
	backnav('house','gssendrules');
	$rule = $exconfigs['gssendrules'];
	$gid = 31;$i = 0;
	$ugname = cls_cache::Read('usergroups',$gid);
	if(!submitcheck('bsubmit')){		
		foreach($rule as $mchid=>$m){	
			$i ? tabheader($ugname[$mchid]['cname'].'��������') : tabheader($ugname[$mchid]['cname'].'��������','exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
				foreach($m as $k=>$v){
					if(is_array($v)){
						$c = cls_cache::Read('channels');$v['title'] = $c[$k]['cname'];
						trbasic("$v[title]������","rulesnew[$mchid][$k][total]",empty($v['total']) ? '' : $v['total'],'text',array('validate' => makesubmitstr("rulesnew[$mchid][$k][total]",1,0,1,'','int'),'guide' => "��Ա���Է�����$v[title]������"));
						//trbasic("��Ա����$v[title]��Ч����","rulesnew[$mchid][$k][valid]",empty($v['valid']) ? '' : $v['valid'],'text',array('validate' => makesubmitstr("rulesnew[$mchid][$k][valid]",1,0,1,'','int'),'guide' => "������ʾ��ǰ̨��$v[title]������"));
					}else{
						trbasic('ÿ��ˢ�´���',"rulesnew[$mchid][refresh]",empty($v) ? '' : $v,'text',array('validate' => makesubmitstr("rulesnew[$mchid][refresh]",1,0,1,'','int'),'guide' => "ÿ������ִ��ˢ�²����Ĵ�����"));
					}					
				}
			$i++;
			tabfooter($i == count($rule) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['gssendrules'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'sjsendrules'){
	backnav('house','sjsendrules');
	$rule = $exconfigs['sjsendrules'];
	$gid = 32;$i = 0;
	$ugname = cls_cache::Read('usergroups',$gid);
	if(!submitcheck('bsubmit')){
		foreach($rule as $mchid=>$m){	
			$i ? tabheader($ugname[$mchid]['cname'].'��������') : tabheader($ugname[$mchid]['cname'].'��������','exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
			foreach($m as $k=>$v){
				if(is_array($v)){
					$c = cls_cache::Read('channels');$v['title'] = $c[$k]['cname'];
					trbasic("$v[title]������","rulesnew[$mchid][$k][total]",empty($v['total']) ? '' : $v['total'],'text',array('validate' => makesubmitstr("rulesnew[$mchid][$k][total]",1,0,1,'','int'),'guide' => "��Ա���Է�����$v[title]������"));
					//trbasic("��Ա����$v[title]��Ч����","rulesnew[$mchid][$k][valid]",empty($v['valid']) ? '' : $v['valid'],'text',array('validate' => makesubmitstr("rulesnew[$mchid][$k][valid]",1,0,1,'','int'),'guide' => "������ʾ��ǰ̨��$v[title]������"));
				}else{
					trbasic('ÿ��ˢ�´���',"rulesnew[$mchid][refresh]",empty($v) ? '' : $v,'text',array('validate' => makesubmitstr("rulesnew[$mchid][refresh]",1,0,1,'','int'),'guide' => "ÿ������ִ��ˢ�²����Ĵ�����"));
				}
			}
			$i++;
			tabfooter($i == count($rule) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['sjsendrules'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'zding'){
	backnav('house','zding');
	$rule = $exconfigs['zding'];
	if(!submitcheck('bsubmit')){
		tabheader('��Դ�ö�����','exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
		trbasic('��Դ�ö�ÿ�����',"rulenew[price]",empty($rule['price']) ? 0 : $rule['price'],'text',array('validate' => makesubmitstr("rulenew[price]",1,0,1,'','int'),'guide' => '��ԪΪ��λ����������'));
		trbasic('�ö�һ�����ٶ�����',"rulenew[minday]",empty($rule['minday']) ? 0 : $rule['minday'],'text',array('validate' => makesubmitstr("rulenew[minday]",1,0,1,'','int'),'guide' => '����Ϊ��λ����������'));
		tabfooter('bsubmit');
	}else{
		$exconfigs['zding'] = $rulenew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'yysx'){
	backnav('house','yysx');
	$rule = $exconfigs['yysx'];
	if(!submitcheck('bsubmit')){
		tabheader('��ԴԤԼˢ�¹���','exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
		$usergroups = cls_cache::Read('usergroups',14);
		$rules = empty($exconfigs['yysx']['allowgroup'])? array() : $exconfigs['yysx']['allowgroup']; 
		$arr = array(); 
		foreach($usergroups as $g => $v){ 
			$arr[$g] = "$v[cname]";
		}		
		trbasic("������ˢ�µĻ�Ա","rulenew[allowgroup]",makecheckbox('rulenew[allowgroup][]',$arr,$rules,5),'');			
		trbasic("ʱ�������","rulenew[time]",empty($rule['time']) ? 0 : $rule['time'],'text',array('validate' => makesubmitstr("rulenew['time']",1,0,1,'','char'),'guide' => '��������ˢ�µ�ʱ��㣬24Сʱʱ���ƶȣ��ɾ�ȷ���֣����ʱ����ã��ŷֿ�����6,10:00,11:25,12,18,20:01'));		
		trbasic('ÿ���ԤԼ��Դ����',"rulenew[totalnum]",empty($rule['totalnum']) ? 0 : $rule['totalnum'],'text',array('validate' => makesubmitstr("rulenew[totalnum]",1,0,1,'','int'),'guide' => 'һ����Զ�n����Դ����ԤԼˢ�����ã�����Ϊ��λ����������'));	
		trbasic('ԤԼÿ����Դ�ķ���',"rulenew[price]",empty($rule['price']) ? 0 : $rule['price'],'text',array('validate' => makesubmitstr("rulenew[price]",0,0,0,'','float'),'guide' => '��ԪΪ��λ'));	
		trbasic('��ԤԼ����',"rulenew[yyday]",empty($rule['yyday']) ? 0 : $rule['yyday'],'text',array('validate' => makesubmitstr("rulenew[yyday]",1,0,0,'','float'),'guide' => '����Ϊ��λ'));
		trbasic("ԤԼˢ��˵��","rulenew[directions]",empty($rule['directions']) ? '' : $rule['directions'],'textarea',array('w'=>'500','h'=>'150','guide' => "ԤԼˢ�µ���ʾ�������Ϣ��"));
		tabfooter('bsubmit');
	}else{
		$ugidsnew = implode(',',$rulenew['allowgroup']);		
		$db->query("UPDATE {$tblprefix}permissions SET ugids='$ugidsnew' WHERE pmid='118'");	
		cls_CacheFile::Update('permissions');
		$exconfigs['yysx'] = $rulenew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'fanyuan'){
	backnav('house','fanyuan');
	$rules = $exconfigs['fanyuan'];
	if(!submitcheck('bsubmit')){
		$usergroups = cls_cache::Read('usergroups',14);
		$i = 0;
		foreach($rules as $k => $v){
			$ugname = empty($usergroups[$k]) ? '��ͨ��Ա' : $usergroups[$k]['cname'];
			$i ? tabheader("$ugname �����޶�") : tabheader("$ugname �����޶�",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
			trbasic('���۷�Դ����',"rulesnew[$k][total]",empty($v['total']) ? '' : $v['total'],'text',array('validate' => makesubmitstr("rulesnew[$k][total]",1,0,1,'','int'),'guide' => '��Ա���Է����ķ�Դ������'));
			trbasic('������Ч����',"rulesnew[$k][fyvalid]",empty($v['fyvalid']) ? '' : $v['fyvalid'],'text',array('validate' => makesubmitstr("rulesnew[$k][fyvalid]",1,0,1,'','int'),'guide' => '��Ч����(��)��'));
			trbasic('�����շ�����',"rulesnew[$k][daymax]",empty($v['daymax']) ? '' : $v['daymax'],'text',array('validate' => makesubmitstr("rulesnew[$k][daymax]",1,0,1,'','int'),'guide' => '��Աÿ�տ��Է����ĳ���ͳ��۵�������'));
			//trbasic('���۷�Դ�ϼ�����',"rulesnew[$k][valid]",empty($v['valid']) ? '' : $v['valid'],'text',array('validate' => makesubmitstr("rulesnew[$k][valid]",1,0,1,'','int'),'guide' => '������ʾ��ǰ̨�ķ�Դ������'));
			trbasic('ÿ��ˢ�´���',"rulesnew[$k][refresh]",empty($v['refresh']) ? '' : $v['refresh'],'text',array('validate' => makesubmitstr("rulesnew[$k][refresh]",1,0,1,'','int'),'guide' => 'ÿ������ִ��ˢ�²����Ĵ�����'));
			trbasic('���󷢲�����',"rulesnew[$k][xuqiu]",empty($v['xuqiu']) ? '' : $v['xuqiu'],'text',array('validate' => makesubmitstr("rulesnew[$k][xuqiu]",1,0,1,'','int'),'guide' => '��������������Ϣ������'));
			trbasic('������Ч����',"rulesnew[$k][xqvalid]",empty($v['xqvalid']) ? '' : $v['xqvalid'],'text',array('validate' => makesubmitstr("rulesnew[$k][xqvalid]",1,0,1,'','int'),'guide' => '��Ч����(��)��'));
			//trbasic('��Դ�Ƽ�λ����',"rulesnew[$k][tuijian]",empty($v['tuijian']) ? '6' : $v['tuijian'],'text',array('validate' => makesubmitstr("rulesnew[$k][tuijian]",1,0,1,'','int'),'guide' => '�����������Ϣ������'));
			$i ++; 
			tabfooter($i == count($rules) ? 'bsubmit' : '');
		}
	}else{
		$exconfigs['fanyuan'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
}elseif($action == 'shangye'){
    backnav('house','shangye');
    $rules = $exconfigs['shangye'];
    if(!submitcheck('bsubmit')){
        $usergroups = cls_cache::Read('usergroups',14);
        $i = 0;
        foreach($rules as $k => $v){
            $ugname = empty($usergroups[$k]) ? '��ͨ��Ա' : $usergroups[$k]['cname'];
            $i ? tabheader("$ugname �����޶�") : tabheader("$ugname �����޶�",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
            trbasic('������ҵ�ز�����',"rulesnew[$k][total]",empty($v['total']) ? '' : $v['total'],'text',array('validate' => makesubmitstr("rulesnew[$k][total]",1,0,1,'','int'),'guide' => '��Ա���Է�������ҵ�ز�������'));
            trbasic('������Ч����',"rulesnew[$k][fyvalid]",empty($v['fyvalid']) ? '' : $v['fyvalid'],'text',array('validate' => makesubmitstr("rulesnew[$k][fyvalid]",1,0,1,'','int'),'guide' => '��Ч����(��)��'));
            trbasic('�����շ�����',"rulesnew[$k][daymax]",empty($v['daymax']) ? '' : $v['daymax'],'text',array('validate' => makesubmitstr("rulesnew[$k][daymax]",1,0,1,'','int'),'guide' => '��Աÿ�տ��Է����ĳ���ͳ��۵�������'));
            //trbasic('���۷�Դ�ϼ�����',"rulesnew[$k][valid]",empty($v['valid']) ? '' : $v['valid'],'text',array('validate' => makesubmitstr("rulesnew[$k][valid]",1,0,1,'','int'),'guide' => '������ʾ��ǰ̨�ķ�Դ������'));
            trbasic('ÿ��ˢ�´���',"rulesnew[$k][refresh]",empty($v['refresh']) ? '' : $v['refresh'],'text',array('validate' => makesubmitstr("rulesnew[$k][refresh]",1,0,1,'','int'),'guide' => 'ÿ������ִ��ˢ�²����Ĵ�����'));
            trbasic('���󷢲�����',"rulesnew[$k][xuqiu]",empty($v['xuqiu']) ? '' : $v['xuqiu'],'text',array('validate' => makesubmitstr("rulesnew[$k][xuqiu]",1,0,1,'','int'),'guide' => '��������������Ϣ������'));
            trbasic('������Ч����',"rulesnew[$k][xqvalid]",empty($v['xqvalid']) ? '' : $v['xqvalid'],'text',array('validate' => makesubmitstr("rulesnew[$k][xqvalid]",1,0,1,'','int'),'guide' => '��Ч����(��)��'));
            //trbasic('��Դ�Ƽ�λ����',"rulesnew[$k][tuijian]",empty($v['tuijian']) ? '6' : $v['tuijian'],'text',array('validate' => makesubmitstr("rulesnew[$k][tuijian]",1,0,1,'','int'),'guide' => '�����������Ϣ������'));
            $i ++;
            tabfooter($i == count($rules) ? 'bsubmit' : '');
        }
    }else{
        $exconfigs['shangye'] = $rulesnew;
        cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
        cls_message::show('ϵͳ�������óɹ���',M_REFERER);
    }
}elseif($action == 'weituo'){
	backnav('house','weituo');
	$rules = $exconfigs['weituo'];
	if(!submitcheck('bsubmit')){
		tabheader('ί�з�Դ�Ƽ�������ɸѡ����','weituo',"?entry=$entry$extend_str&action=$action",2,0,1);
		trbasic('�����Ƽ���ͨ������',"rulesnew[allowptjjr]",empty($rules['allowptjjr']) ? 0 : 1,'radio',array('guide'=>'�Ƿ�����ί�з�Դ����ͨ�����ˡ�'));
		trbasic('���޾���������',"rulesnew[allowccid1]",empty($rules['allowccid1']) ? 0 : 1,'radio',array('guide'=>'�Ƿ�����������������ľ����ˡ�'));
		tabfooter('bsubmit');
	}else{
		$exconfigs['weituo'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('ί�д�Դ�������óɹ���',M_REFERER);		
	}
}elseif($action == 'distribution'){
	backnav('house','distribution');
	$rules = $exconfigs['distribution'];
	if(!submitcheck('bsubmit')){
		tabheader('¥�̷�����������','distribution',"?entry=$entry$extend_str&action=$action",2,0,1);
		trbasic('�����Ƽ�¥�̸���',"rulesnew[num]",empty($rules['num']) ? 3 : max(0,intval(@$rules['num'])),'text',array('guide'=>'ÿ���Ƽ������������Ƽ���¥�̸�����'));
		trbasic('�����Ƽ����Ѹ���',"rulesnew[pnum]",empty($rules['pnum']) ? 3 : max(0,intval(@$rules['pnum'])),'text',array('guide'=>'ÿ���������ܹ������Ƽ������Ѹ�����'));
        trbasic('�Ƽ���Чʱ��',"rulesnew[vtime]",empty($rules['vtime']) ? 15 : max(0,intval(@$rules['vtime'])),'text',array('guide'=>'�ɹ��Ƽ����������Чʱ��(��)��'));
        trbasic('��Ч�ͻ�����',"rulesnew[unvnum]",empty($rules['unvnum']) ? 10 : max(0,intval(@$rules['unvnum'])),'text',array('guide'=>'�������Ƽ�N����Ч�ͻ����Զ����������������������ľ����˲��ܽ����Ƽ�������'));
		trbasic('����Ĭ���ƹ�ں�',"rulesnew[fxwords]",@$rules['fxwords'] ,'textarea',array('w' => 400,'h' => 50,'guide'=>'¥�̷���-�ƹ�����-��Ĭ�Ͽںš�'));
		//trbasic('����Ĭ���ƹ�ں�','fxwords','�ںſں�ģ��','textarea', array('w' => 400,'h' => 50,'validate' => makesubmitstr('fxwords',1,0,0,100)));
		tabfooter('bsubmit');
	}else{
		$exconfigs['distribution'] = $rulesnew;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_message::show('¥�̷����������óɹ���',M_REFERER);		
	}
	
}elseif($action == 'closemod'){

	backnav('house','closemod');
	
	$closemstr = empty($exconfigs['closemstr']) ? '' : $exconfigs['closemstr'];
	$sarr = cmod('',$type='seta'); 
	if(!submitcheck('bsubmit')){
		 tabheader("��ѡģ��ر�����",'exconfigs',"?entry=$entry$extend_str&action=$action",2,0,1);
		 foreach($sarr as $k=>$v){
		 	$ischeck = strstr(",$closemstr,","$k,") ? 'checked="checked"' : '';
			$gmsg = "�Ƿ�ر� $v[cname] ģ�顣 (ģ���ʶID:$k)";
			trbasic($v['cname']."ģ��","","�ر�".$v['cname']."ģ�� <input name='closemods[]' type='checkbox' value='$k' $ischeck />",'',array('guide' =>$gmsg ));
		 }
		 tabfooter('bsubmit');
		 a_guide('excmod');
	}else{
		$closestr = empty($closemods) ? 'n.o.n.e' : implode(",", $closemods);
		$exconfigs['closemstr'] = $closestr;
		cls_CacheFile::cacSave($exconfigs,'exconfigs',_08_EXTEND_SYSCACHE_PATH);
		cls_CacheFile::Update('linknodes');
		cmod('','setdb');  
		cls_message::show('ϵͳ�������óɹ���',M_REFERER);
	}
	//a_guide('ext_model');

}elseif($action == 'fccotype'){
	
	backnav('house','fccotype');
	if(!submitcheck('bsubmit')){
		tabheader('����������������','cffang',"?entry=$entry$extend_str&action=$action");
		//trbasic('�رռ�װģ��','mconfigsnew[jzmodelset]',empty($mconfigs['jzmodelset']) ? 0 : 1,'radio',array('guide'=>'ǰ̨��ʾ�������ؼ�װģ��'));
		trbasic('�ر��οͷ���','mconfigsnew[close_gpub]',empty($mconfigs['close_gpub']) ? 0 : 1,'radio',array('guide'=>'�˿��ؿ����ο��Ƿ�ɷ�����Դ,����'));
		trbasic('�ر���Ȧ��ϵ','mconfigsnew[fcdisabled2]',empty($mconfigs['fcdisabled2']) ? 0 : 1,'radio');
		trbasic('�رյ�����·��վ��','mconfigsnew[fcdisabled3]',empty($mconfigs['fcdisabled3']) ? 0 : 1,'radio');
		trbasic('��Ա�绰�����Ƿ�Ψһ','mconfigsnew[telisunique]',empty($mconfigs['telisunique']) ? 0 : 1,'radio',array('guide'=>'ѡ��[��],������Ƿ����ظ��绰���룬�ظ������ύ���������ûḲ����������:[��վ�ܹ�-��Ա�ܹ�-��֤����-�ֻ���֤:�����Ƿ�Ψһ]��'));
		trbasic('�οͷ�������','mconfigsnew[count_gpub]',empty($mconfigs['count_gpub']) ? 5 : $mconfigs['count_gpub'],'text',array('guide'=>'ͬһ���룬һ���ڿɷ�����Դ,������Ϣ������'));

/*
		trbasic('��Ƹ��Ч����',"mconfigsnew[zpvalid]",empty($mconfigs['zpvalid']) ? '30' : $mconfigs['zpvalid'],'text',array('validate' => makesubmitstr("mconfigsnew[zpvalid]",1,0,1,'','int'),'guide' => '��Ч����(��)��'));

*/
		trbasic('��¥��˾ÿ��ˢ�´���','mconfigsnew[salesrefreshes]',empty($mconfigs['salesrefreshes']) ? 30 : $mconfigs['salesrefreshes'],'text',array('guide'=>'��¥��˾ÿ�տ���ִ��ˢ�µĴ�����'));
		trbasic('�ܱ������Զ�������Χ','mconfigsnew[circum_km]',empty($mconfigs['circum_km']) ? 3 : $mconfigs['circum_km'],'text',array('guide'=>'��λ:����˷�Χ�ڵ�¥��/С�����Զ����ܱ߹���'));
		trbasic('΢���ܱ������Զ�������Χ','mconfigsnew[weixin_circum_km]',empty($mconfigs['weixin_circum_km']) ? 1 : $mconfigs['weixin_circum_km'],'text',array('guide'=>'��λ:����˷�Χ�ڵ�¥��/С�����Զ����ܱ߹���'));
		tabfooter('bsubmit');
	}else{
		$mconfigsnew['pictolp'] = empty($mconfigsnew['pictolp']) ? 0 : 1;
		$mconfigsnew['fcdisabled2'] = empty($mconfigsnew['fcdisabled2']) ? 0 : 1;
		$mconfigsnew['fcdisabled3'] = empty($mconfigsnew['fcdisabled3']) ? 0 : 1;
		$telisunique = empty($mconfigsnew['telisunique']) ? 0 : 1;
		$db->query("UPDATE {$tblprefix}mctypes SET isunique='$telisunique' WHERE mctid='1'");
		cls_CacheFile::Update('mctypes');
		saveconfig('fang');
		adminlog('��վ����','����������������');
		cls_message::show('�������������������',"?entry=$entry$extend_str&action=$action");
	}
	
}
?>
