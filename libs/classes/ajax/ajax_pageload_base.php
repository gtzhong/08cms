<?php
/**
 * ͨ��ajax�б���
 *
 * @example   ������URL��?/ajax/pageload_base/aj_model/a,3,1/caid/33/ccid20/1298/aj_thumb/thumb,120,90/aj_pagesize/2/aj_pagenum/2/domain/192.168.1.11/
 * @author    Peace@08cms.com
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_pageload_Base extends _08_Models_Base{
	
	public $mcfgs = array();//a/m/cu/co,3,1 (����,ģ��id,ģ�ͱ�) (explode���)
	public $_ajda = array();//_da����
	public $sqlarr = array('select','from','where','order','limit');
    public $fieldcfg = array('from_tables'=>array(),'vaild_fields'=>array());//�ֶ���ز���,from_tables:�ֶ���Դtable; vaild_fields:��Ч�ֶ�;  
	public $comkey = array(//ͨ�ò���
		'aj_model',     //ģ����Ϣ(a-�ĵ�/m-��Ա/cu-����/co-��Ŀ,3,1-ģ�ͱ�; ��:a,3,1)
		'aj_check',     //�Ƿ����(0/1������,Ĭ��Ϊ1,����������-1��ʾ����)
		'aj_vaild',     //�Ƿ���Ч(1������)
		'aj_arids',     //���ںϼ�arid,pmod,pid -=> �磺1,4,23567
		'aj_ids',       //ids(��:123,456,32,954)
		'aj_mid',       //mid(��:)
		'aj_pagenum'  , //��ǰ��ҳ(����,Ĭ��2)
		'aj_pagesize',  //��ҳ��С(����,Ĭ��10)
		'aj_thumb',     //����ͼ����(��ʽ:ͼƬ�ֶ�,��,��; ��:thumb,240,180)
		'aj_unsets',    //unset�ֶ�(��ߴ����ٶ�,��Լ����)
		'aj_nodemode',  //�Ƿ��ֻ���url; Ĭ��1,
        'aj_deforder',  //Ĭ������(orderbyΪ��ʱʹ�õ�Ĭ������)
        'aj_whrfields', //���������ֶ�(��ʽ������)
        'aj_minfo',     //ͬʱ���ػ�Ա����; ����,�ĵ�����
        'aj_ainfo',     //ͬʱ�����ĵ�����; ��������
		'caid',         //��Ŀ(�ĵ���)
		'searchword',   //�ؼ���(���������ֶ�,����չ������)
		'orderby',      //�����ֶ�
		'ordermode',    //����ģʽ
	);
	
    public function __toString(){
		//��ʼ����ģ��da����
		$this->_initDa(); 
        //����ģ�溯��
        include_once(cls_tpl::TemplateTypeDir('function').'utags.fun.php');
		//����sql����
		$this->_getSql(); 
        $this->_getOrder();
		foreach($this->sqlarr as $k){
			$$k = $this->$k;
		} 
		//ȫ��sql�����
		$sql = "SELECT $select FROM $from WHERE $where ORDER BY $order LIMIT $limit"; //echo "\n$sql\n<br>\n";
		$result = $this->_getData($sql); 
        return $result; 
    }
	
    public function _initDa($exkeys=array()){
		//inti
		$_ajkeys = array_merge($this->comkey,$exkeys); 
		foreach($_ajkeys as $key){
			$_ajda[$key] = isset($this->_get[$key]) ? $this->_get[$key] : '';
		} 
		$_ajda['aj_nodemode'] = strlen($_ajda['aj_nodemode'])==0 ? 1 : intval($_ajda['aj_nodemode']);
		// ģ������
		$mcfgs = explode(',',$_ajda['aj_model']); //a/m/cu,3,1 (����,ģ��id,ģ�ͱ�) /co
		if($mcfgs[0]=='a' && !empty($mcfgs[1])){ //�ĵ�
			$_ajda['aj_fdata'] = 'a';
			$_ajda['aj_keyid'] = 'aid';
		}elseif($mcfgs[0]=='m' && !empty($mcfgs[1])){
			$_ajda['aj_fdata'] = 'm';
			$_ajda['aj_keyid'] = 'mid';
		}elseif($mcfgs[0]=='cu' && !empty($mcfgs[1])){
			$_ajda['aj_fdata'] = 'cu';
			$_ajda['aj_keyid'] = 'cid';
		}elseif($mcfgs[0]=='co' && !empty($mcfgs[1])){
			$_ajda['aj_fdata'] = 'co';
			$_ajda['aj_keyid'] = 'ccid';
		}else{ //���ﲻֹͣ,�ڲ�ѯ���ݿ�ʱ��ֹͣ
			die("Error:".$_ajda['aj_model']);	
		}
		// 
		$this->mcfgs = $mcfgs;
		$this->_ajda = $_ajda;
	}
	
    public function _getSql(){
		$mcfgs = $this->mcfgs;
		$_ajda = $this->_ajda;
		$tblprefix = $this->_tblprefix;
		//select
		$select = "{$_ajda['aj_fdata']}.*".($_ajda['aj_fdata']=='m' ? ",s.*" : '');
		if(in_array($mcfgs[0],array('a','m')) && !empty($mcfgs[2])){
			$select .= ',c.*';
		}
		//from
		$from = "";
		if($mcfgs[0]=='a'){ //�ĵ�
			$atbl = atbl($mcfgs[1]); //
			if(empty($atbl)) die("Error:chid=$mcfgs[1]");
			$from .= "{$tblprefix}$atbl a";
            $this->fieldcfg['from_tables'][] = $atbl;
			if(!empty($mcfgs[2])){
				$from .= " INNER JOIN {$tblprefix}archives_{$mcfgs[1]} c ON c.aid=a.aid";
                $this->fieldcfg['from_tables'][] = "archives_{$mcfgs[1]}";
			}
		}elseif($mcfgs[0]=='m'){
			$mchannels = cls_cache::Read('mchannels');
			if(empty($mchannels[$mcfgs[1]])) die("Error:mchid=$mcfgs[1]");
			$from .= "{$tblprefix}members m";
			$from .= " INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid";
            $this->fieldcfg['from_tables'][] = "members";
            $this->fieldcfg['from_tables'][] = "members_sub";
			if(!empty($mcfgs[2])){
				$from .= " INNER JOIN {$tblprefix}members_{$mcfgs[1]} c ON c.mid=m.mid";
                $this->fieldcfg['from_tables'][] = "members_{$mcfgs[1]}";
			}
		}elseif($mcfgs[0]=='cu'){
			$cucfgs = cls_cache::Read('commu',$mcfgs[1]);
			if(empty($cucfgs['tbl'])) die("Error:cuid=$mcfgs[1]");
			$from .= "{$tblprefix}{$cucfgs['tbl']} cu";
            $this->fieldcfg['from_tables'][] = $cucfgs['tbl'];
		}elseif($mcfgs[0]=='co'){
			$cocfgs = cls_cache::Read('coclasses', $mcfgs[1]);
			if(empty($cocfgs)) die("Error:coid=$mcfgs[1]");
			$from .= "{$tblprefix}coclass{$mcfgs[1]} co";
            $this->fieldcfg['from_tables'][] = "coclass{$mcfgs[1]}";
		}else{
			//die("Error:".$_ajda['aj_model']);	
		}
        //vaild_fields
        foreach($this->fieldcfg['from_tables'] as $tab){
            $this->fieldcfg['vaild_fields'] = array_merge($this->fieldcfg['vaild_fields'],$this->getFields("{$tblprefix}$tab"));
        } // �����ظ�?  �����͹���?
		//where
		$where = $this->_getWhere();
        $where = $this->_whrFields($where);
		//order(Ĭ�ϵ�����)
		$order = "{$_ajda['aj_fdata']}.{$_ajda['aj_keyid']} DESC";
		//limit
		$aj_pagesize = empty($_ajda['aj_pagesize']) ? 10 : intval($_ajda['aj_pagesize']);
		$aj_pagenum = empty($_ajda['aj_pagenum']) ? 2 : max(1,intval($_ajda['aj_pagenum']));
		$aj_pageflag = ($aj_pagenum-1)*$aj_pagesize;
		$limit = "$aj_pageflag,$aj_pagesize";
		foreach($this->sqlarr as $k){
			$this->$k = $$k;
		}
	}
	
    public function _getWhere(){
		$mcfgs = $this->mcfgs;
		$_ajda = $this->_ajda;
		//where : 'aj_check','aj_vaild','aj_ids','aj_mid'
		$where = "";
		if($mcfgs[0]=='a'){ //�ĵ�ר�Ŵ���
			//caid
			if(!empty($_ajda['caid'])){
				$ids = sonbycoid(intval($_ajda['caid']), 0, 1); 
				$where .= (empty($where) ? '' : ' AND ')."a.caid IN(".implode(',',$ids).")";
			}
			//vaild
			if(!empty($_ajda['aj_vaild'])){
				$where .= (empty($where) ? '' : ' AND ')."(enddate=0 OR enddate>'".TIMESTAMP."')";
			}
			//aj_arids/arid,pmod,pid -=> 1,4,23567 (aj_arids����������Ҫ���ڻ�Ա)
			if(!empty($_ajda['aj_arids'])){
				$arstr = $this->_getArWhr($_ajda['aj_arids'],$mcfgs[1]);
				$arstr && $where .= (empty($where) ? '' : ' AND ').$arstr;
			}
			
		}
		if(!empty($_ajda['aj_ids'])){
			$ids = preg_replace('/[^\d|\,]/', '', $_ajda['aj_ids']);
			$ida = array_filter(explode(',',$ids));
			if(count($ida)>200) array_splice($ida,200); //���100��
			$ids = empty($ida) ? '0' : implode(',',$ida); 
			$where .= (empty($where) ? '' : ' AND ')."{$_ajda['aj_fdata']}.{$_ajda['aj_keyid']} IN($ids)";
		}
		if(!empty($_ajda['aj_mid']) && $mcfgs[0]!='co'){ //�ĵ���������(�ų���Ŀ)
			$aj_mid = intval($_ajda['aj_mid']);
			$where .= (empty($where) ? '' : ' AND ')."{$_ajda['aj_fdata']}.mid='$aj_mid'";
		}
		$aj_check = strlen($_ajda['aj_check']) ? intval($_ajda['aj_check']) : '1'; //0/1:������,Ĭ�Ͽ�('')��Ϊ1����
		$close = empty($aj_check) ? 1 : 0;
		if($mcfgs[0]=='cu' && $aj_check==-1){ //�ĵ�/��Ա:һ������˵�...,������Ҫchecked����
			$where .= (empty($where) ? '' : ' AND ')."1=1";
		}else{
			$where .= (empty($where) ? '' : ' AND ')."{$_ajda['aj_fdata']}.".($mcfgs[0]=='co' ? "closed=$close" : "checked=$aj_check")."";
		}
		return $where;
	}
    
	/** 
     $whrfix : aj_whrfields����, ��ʽ���磺
     .../aj_whrfields/field1,op1,v1;field2,op2,v2;field3,op3,v3.../... ÿһ����[;]�ֿ�,�����е�������[,]�ֿ�
     fieldX: ���ݿ��е��ֶ�;�ؼ�����������[-]�ֿ�����ֶ�
             .../aj_whrfields/subject,like,��/...                           -=> subject LIKE '%��%'
             .../aj_whrfields/subject-address,like,dong/...                 -=> subject LIKE '%��%' OR address LIKE '%��%'
     opX: �����Ա�����,�� like,=,>,<,>-,auto,mso1,inlike
             .../aj_whrfields/ccid12,auto,127/...                           -=> jiage>5 AND jiage<=10
             .../aj_whrfields/jiage,>=,10.5/...                             -=> jiage>=10.5  op��Ϊ >,<,=,>=,<=
			 .../aj_whrfields/fromaddress,in,4004,20/..                     -=> fromaddress IN(123,456,789,55...) 4004����ϵ20��һ���������
             .../aj_whrfields/ccid1,in,123-456/...                          -=> ccid1 IN(123,456)   ��[-]�ֿ����ֵ
             .../aj_whrfields/ccid2,in,123/...                              -=> ccid1 IN(123,124)   ���123�������
             .../aj_whrfields/mianccid1,inlike,26,1/...                     -=> (��Ӫmianccid1Ϊ��ѡ����ϵΪ1; ���ж������26��������µ�����)   AND (CONCAT(',',mianccid1,',') LIKE '%,3001,%' OR CONCAT(',',mianccid1,',') LIKE '%,3004,%')   ���123�������
             .../aj_whrfields/lcs,mso1,3/...                                -=> ¥��(��ѡ�ֶ�); CONCAT('\t',lcs,'\t') LIKE '%\t3\t%'
             .../aj_whrfields/ccid12,mcos,2/...                             -=> ccid12=',1,2,4,'(��ѡ��ϵ); ccid12 LIKE '%,2,%'
     �ۺ�:   .../aj_whrfields/subject,like,��;lcs,mso1,3/...                -=> subject LIKE '%��%' AND CONCAT('\t',lcs,'\t') LIKE '%\t3\t%'
     ÿһ����[field1,op1,v1]�ĵ���������Ϊֵ,�ɰ�������[,]��ֱ�Ӽ���; ����url�� ��.../fieldN/valN/.... ��ȡ; ǰ��ֵ����
     �磺    .../aj_whrfields/leixing,in,0,1;subjectstr,like;lcs,mso1/subjectstr/dong/lcs/2...
     ???
     ��ѡ��ϵ:ccid12
	*/        
    public function _whrFields($whrfix){
		$mcfgs = $this->mcfgs;
		$_ajda = &$this->_ajda;
        $_whrstr_paras = array();
        $searchword = isset($this->_get['searchword']) ? $this->_get['searchword'] : ''; 
        $searchword = trim(cls_string::iconv('utf-8',cls_env::getBaseIncConfigs('mcharset'),$searchword));
        $where = '';
		if(!empty($_ajda['aj_whrfields'])){
            $_itms = explode(';',$_ajda['aj_whrfields']);
            foreach($_itms as $itm){ //echo "\n$itm,";
                $_ia = explode(',',$itm); $_ik = $_ia[0]; 
                $_iop = empty($_ia[1]) ? '=' : $_ia[1]; 
                $_iv = empty($_ia[2]) ? ((strstr($_ik,'subject') || strstr($_ik,'company')) ? $searchword : @$this->_get[$_ik]) : cls_string::iconv('utf-8',cls_env::getBaseIncConfigs('mcharset'),$_ia[2]);
                if(empty($_ik) || empty($_iv)) continue;             
                if(strstr($_ik,'-')){
                    $_iks = '';
                    $_ika = explode('-',$_ik);
                    foreach($_ika as $_ikn){
                        if(in_array($_ikn,$this->fieldcfg['vaild_fields'])){
                            $_iks .= (empty($_iks) ? '' : ' OR ')."$_ikn ".sqlkw($_iv); 
                        }
                    }
                    $where .= (empty($_iks) ? '' : ' AND (')." $_iks) "; 
                }elseif($_iop=='auto'){ // �Զ�������ϵ
                    $coid = intval(str_replace('ccid','',$_ik));
                    if(empty($coid) || ($mcfgs[0]!='a')) continue;
                    $splitbls = cls_cache::Read('splitbls'); 
                    if(!in_array($coid,$splitbls[str_replace('archives','',atbl($mcfgs[1]))]['coids'])) continue; //Ҫ�ж��Ƿ����
                    $_tmp = cnsql($coid,$_iv);
                    $_tmp && $where .= " AND ".$_tmp;
                }else{
                    if(!in_array($_ik,$this->fieldcfg['vaild_fields'])) continue;
                    $_fmt = empty($_ia[3]) ? '' : intval($_ia[3]); //��ϵ
                    if($_iop=='like'){ //�ؼ���
    					$where .= " AND $_ik ".sqlkw($_iv);
    				}elseif(in_array($_iop,array('>','>=','<','<='))){ //���ֱȽ�
    					$where .= " AND $_ik$_iop'$_iv'";
    				}elseif(in_array($_iop,array('notnull','isnull'))){ // field!='' �� field=''
    					$where .= " AND $_ik".($_iop=='isnull' ? "=" : "!=")."''";
    				}elseif($_iop=='in'){ //����,�Է���; ��sonbycoid()����Ŀ/��Ŀ, caid IN(sonbycoid($caidx1))
    					$coid = isset($_ia[3]) ? $_ia[3] : intval(str_replace('ccid','',$_ik)); //caid Ϊ0
    					$ids = sonbycoid($_iv, $coid, 1); //echo "\n===$coid,$_ik,$_iv";
    					if(strstr($_iv,'-')){
    					    $where .= " AND $_ik ".multi_str(explode(',',str_replace('-',',',$_iv)));
    					}elseif($ids){
							$ids = preg_replace("/[^0-9\.]/i","",$ids);
							$where .= " AND $_ik IN(".implode(',',$ids).")"; //echo "(($where))";
    					}
    				//*
                    }elseif($_iop=='inlike'){ //��ѡ�ֶ�,�������ӷ���, CONCAT(',',mianccid1,',') LIKE '%\t$ccid1\t%' OR (...)
                        $coid = empty($_ia[3]) ? 0 : $_ia[3]; 
    					$ids = sonbycoid($_iv, $coid, 1); $itmp = ''; 
    					if($ids){
    						foreach($ids as $id){
    							$itmp .= (empty($itmp) ? '' : ' OR ')."CONCAT(',',$_ik,',') LIKE '%,$id,%'";	
    						}
    					}
                        $itmp && $where .= " AND (".$itmp.")";
    				}elseif(in_array($_iop,array('mso1'))){ //��ѡ�ֶ���1��([tab��]�ֿ�)
    					$where .= " AND CONCAT('\t',$_ik,'\t') LIKE '%\t$_iv\t%'";
                    }elseif(in_array($_iop,array('mcos'))){ //��ѡ��ϵ����1��([,]�ֿ�)
    					$where .= " AND $_ik LIKE '%,$_iv,%'";                      
    				}else{ //���������?! 
    					$where .= " AND $_ik='$_iv'"; 
    				}   
                }
            }
		}
        //print_r($_ajda); print_r($_whrstr_paras); //die();
        $where = $whrfix . (empty($where) ? '' : $where); //print_r("\nC:$where");
        return $where;       
	}
	
	//�����������δ����...
	public function _getArWhr($arCfgs,$mod,$filed='aid'){
		$arids = explode(',',$arCfgs); //1,4,23567
		$abrel = cls_cache::Read('abrel',$arids[0]); //print_r($abrel);
		if(!empty($arids[1]) && !empty($arids[2]) && !empty($abrel) && in_array($arids[1],$abrel['tchids']) && in_array($mod,$abrel['schids'])){
			if($abrel['tbl']){ 
				$ids = cls_DbOther::SubSql_InIds('inid', "{$abrel['tbl']}", "pid='".intval($arids[2])."'"); 
			}else{ 
				$ids = cls_DbOther::SubSql_InIds($filed, "{$this->A['tbl']}", "pid{$abrel['tbl']}='".intval($arids[2])."'");	
			} 
			return substr($filed,0,1).".$filed IN($ids)";	
		}else{
			return '';	
		}
	}
	
    public function _getOrder(){
		$mcfgs = $this->mcfgs;
		$_ajda = $this->_ajda;
        $order = '';
		if(!empty($_ajda['orderby'])){
			$order = $_ajda['orderby'];
            $order .= ($_ajda['ordermode'] ? '' : ' DESC');
		}elseif(!empty($_ajda['aj_deforder'])){ // ccid41 DESC,vieworder ASC
            $order = $_ajda['aj_deforder'];	
		} 
        // ����ֶδ��� �� ����ǰ׺ --- 
        if($order){
            $_ord_a = explode(',',$order); $order = '';
            foreach($_ord_a as $oitm){ 
                if($oitm){
                    $_ord_b = explode(' ',str_replace('+',' ',$oitm)); $ofield = trim($_ord_b[0]); 
                    foreach(array('a','c','m','s','cu') as $_fix) $ofield = str_replace("{$_ajda['aj_fdata']}.","",$ofield);                  
                    if(empty($ofield) || count($_ord_b)>2 || !in_array($ofield,$this->fieldcfg['vaild_fields'])) continue;
                    if($ofield==$_ajda['aj_keyid']) $ofield = $_ajda['aj_fdata'].".$ofield";  
                    $_omode = @trim(strtoupper($_ord_b[1])); $_omode = in_array($_omode,array('ASC','DESC')) ? $_omode : '';
                    $order .= (empty($order) ? '' : ',')." $ofield $_omode "; 
                }
            } 
        } 
        $this->order = empty($order) ? $this->order : $order; 
        return $this->order;  
	}
	
    public function _getData($sql){
		$mcfgs = $this->mcfgs;
		$_ajda = $this->_ajda;
		$query = $this->_db->query($sql);
		$result = array();
		if($mcfgs[0]=='a'){ //�ĵ�
			$ufields = cls_cache::Read('fields',$mcfgs[1]);
		}elseif($mcfgs[0]=='m'){
			$ufields = cls_cache::Read('mfields',$mcfgs[1]);
		}elseif($mcfgs[0]=='cu'){
			$ufields = cls_cache::Read('cufields',$mcfgs[1]);
		}elseif($mcfgs[0]=='co'){
			$ufields = cls_cache::Read('cnfields',$mcfgs[1]);
		}
		while($r = $this->_db->fetch_array($query)){ 
			//���������row
			if($mcfgs[0]=='a'){ //�ĵ�
				$r['nodemode'] = $_ajda['aj_nodemode']; //1:�ֻ���url
				cls_ArcMain::Parse($r); 
			}elseif($mcfgs[0]=='m'){
				$r['murl'] = @cls_Mspace::IndexUrl($r); //��ҳ��ʹ��
			}elseif($mcfgs[0]=='cu'){
				//;
			}elseif($mcfgs[0]=='co'){
				$node = cls_node::cnodearr("ccid{$mcfgs[1]}=$r[ccid]",$_ajda['aj_nodemode']); //�������ӿ���չ
				$r['def_courl'] = empty($node['indexurl']) ? '#' : $node['indexurl'];
			}else{
				//die("Error:".$_ajda['aj_model']);	
			}
			//����ͨ��thumb
			if(!empty($_ajda['aj_thumb'])){ // aj_thumb=thumb,240,180
				$tharr = explode(',',$_ajda['aj_thumb']);
				$r['thumbOrg'] = $r['thumb'] = preg_replace("/\#\d*/",'',$r[$tharr[0]]); 
				$r['thumbOrg'] = cls_url::tag2atm($r['thumbOrg']);				
				if(!empty($tharr[1]) && !empty($tharr[2])&&is_file($r['thumb'])){
					$r['thumb'] = cls_atm::thumb($r['thumb'],$tharr[1],$tharr[2],1,1); 
				}else{
					$r['thumb'] = $r['thumbOrg'];
				}
				
			}
            if(!empty($_ajda['aj_minfo']) && in_array($mcfgs[0],array('cu','a'))){ //ͬʱ���ػ�Ա����; ����,�ĵ�����
    			$user = new cls_userinfo;
    			$user->activeuser($r['mid']); //,$detail
    			$r['aj_minfo'] = $user->info;
            }
            if(!empty($_ajda['aj_ainfo']) && in_array($mcfgs[0],array('m')) && isset($r['aid'])){ //ͬʱ�����ĵ�����; ��������
    			$arc = new cls_arcedit;
    			$arc->set_aid($r['aid'],array('au'=>0)); //,'ch'=>$detail
    			$r['aj_ainfo'] = cls_ArcMain::Parse($arc->archive);	
            }
			/*/����ͨ������ (ǰ��js����))
			foreach(array('createdate','updatedate','refreshdate','enddate','regdate','lastvisit','lastactive') as $k){
				if(!isset($r[$k])){
					;//
				}elseif(!empty($r[$k])){
					$r[$k] = date('Y-m-d H:i:s',$r[$k]);
				}else{
					$r[$k] = '-';	
				}
			}*/
			//����unset
            if(empty($_ajda['aj_unsets']) && $mcfgs[0]!='cu') $_ajda['aj_unsets'] = 'abstract,content,nowurl';
			$_ajda['aj_unsets'] .= ",password,alipay,alipid,alikeyt,tenpay,tenkeyt";
			$arr = explode(',',$_ajda['aj_unsets']);
			foreach($arr as $k){
				unset($r[$k]);
				unset($r['aj_minfo'][$k]);
			}
			foreach($r as $k=>$v){
				if(isset($ufields[$k]) && in_array($ufields[$k]['datatype'],array('select','mselect'))){
					$arr = cls_field::options($ufields[$k]); 
					$re2 = ''; $ids = explode(',', str_replace(array(", ","\t",",,"),',',$v));
					foreach($ids as $k2){
						if(isset($arr[$k2])){ 
							$v2 = $arr[$k2]; 
							$re2 .= (empty($re2) ? '' : ', ').(is_array($v2) ? $v2['title'] : $v2);
						}
					}
					$r["{$k}title"] = $re2; //cls_uviewbase::field_value($v, $k, $mcfgs[1], '');	
				}
			}
			//һ����¼
			$result[] = $r; // $r[$_ajda['aj_keyid']] (ʹ��aj_keyid,json���Զ�����...)
		} //echo "<pre>"; //print_r($result); echo "</pre>"; die();
		return $result;
	}
	
	function getFields($fulltable){ 
		// û�п����Ƿ�֧��sqli������ȷ��
		$query = $this->_db->query("SHOW FULL COLUMNS FROM $fulltable",'SILENT');
		$a = array();
		while($row = $this->_db->fetch_array($query)){
			$a[] = $row['Field'];
		}
		return $a;
	}    
}
