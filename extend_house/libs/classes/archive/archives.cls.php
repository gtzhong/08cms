<?php
class cls_archives extends cls_archivesbase{
    /**
     * ��ʾ������Ϣ�Ĵ���JS
     * @param object $curuser ��ǰ��Աʵ��
     * return string $str 
     */
    function landlordClickJs($curuser){
   	    $js = "<script type='text/javascript'>
                    function setfdinfo(e){
                    	eck = e.checked?'':'none'; //true:false;
                    	etr = document.body.getElementsByTagName('tr');
                    	for(i=0;i<etr.length;i++){
                    		id = etr[i].id.toString();
                    		if(id.indexOf('fdinfo_')==0) etr[i].style.display = eck;
                    	}
                    }
               </script>";
    	$str = $curuser->info['mchid']==2?"$js<div style='float:right;padding-right:10px'><label><input class=\"checkbox\" type=\"checkbox\" id=\'fdinfo\' name=\"fdinfo\" value=\"xx\" onclick='setfdinfo(this)'>&nbsp;��ʾ������Ϣ</label></div>":'';
        return $str;
    }
	
	// ��Դ-������Ϣ ��ʾ
	function m_view_main_fy($cfg=array(), $mchid=3){
		$rs = $this->m_db_array();
		foreach($rs as $k => $v){
			echo $this->m_one_row($v, $cfg);
			$fdname = $v['fdname']==''?'-':str_replace(array("'","\r","\n"),array("\'","<br>","<br>"),$v['fdname']);
			$fdtel = $v['fdtel']==''?'-':str_replace(array("'","\r","\n"),array("\'","<br>","<br>"),$v['fdtel']); 
			$fdnote = $v['fdnote']==''?'-':str_replace(array("'","\r\n","\r","\n"),array("\'","<br>","<br>","<br>"),$v['fdnote']); 
			if($mchid==2){
				$rstr = "\n<tr id='fdinfo_$v[aid]' class=\"bg bg2\" style='display:none'><td class=\"item\">&nbsp;</td>\n";
				$rstr .= "<td class=\"item\" colspan='10'>
				  <div style=' width:70px; float:left; text-align:left'>��������</div>
				  <div style=' width:150px; float:left; text-align:left'>$fdname</div>
				  <div style=' width:70px; float:left; text-align:left'>�����绰</div>
				  <div style=' width:180px; float:left; text-align:left'>$fdtel</div>
				  <div style='clear:both'></div>
				  <div style=' width:70px; float:left; text-align:left'>�ڲ���ע</div>
				  <div style=' width:650px; float:left; text-align:left'>$fdnote</div>
					  </td>\n
				</tr>\n";
				echo $rstr;
			}
		}
	}
	
	// ����/���� ��ϵ����
	function resetCoids(&$coids){
		$mconfigs = cls_cache::Read('mconfigs');
		$fcdisabled2 = $mconfigs['fcdisabled2'];
		$fcdisabled3 = $mconfigs['fcdisabled3'];
		$skipCoid = array(); //1,2,3,14
		if(!empty($fcdisabled2)) $skipCoid[] = 2;
		if(!empty($fcdisabled3)){ 
			$skipCoid[] = 3;
			$skipCoid[] = 14;
		}
		resetCoids($coids, $skipCoid); 		
	}
	
	//ɾ���ʴ�𰸽�������
	function  sv_o_cumu_all($info){
		global $db,$tblprefix;
		
		$cuid = $info['cuid'];
		$selectid = $info['selectid'];
		$actext = $info['actext'];
		$aid = $info['aid'];
		$action = $info['aid'];
		$arcdeal = $info['arcdeal'];
		
		$commu = cls_cache::Read('commu',$cuid);
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',axaction(1,M_REFERER));
		if(empty($selectid)) cls_message::show('��ѡ����ѯ��¼��',axaction(1,M_REFERER));
		foreach($selectid as $k){
			if(!empty($arcdeal['delete'])){
				$db->query("DELETE FROM {$tblprefix}$commu[tbl] WHERE cid='$k'",'UNBUFFERED');	
			}
		}
		$aid || cls_message::show('��ѯ���������ɹ���',"?action=$action&actext=$actext");
		$aid && cls_message::show('��ѯ���������ɹ���',"?action=$action&actext=$actext&aid=$aid");	
	}
	
	
	function s_footer_ex($url,$orther=array()){
		global $authkey;
		$where_str = '';
		if(!empty($this->oS->wheres)){
			foreach($this->oS->wheres as $k => $v){
				$where_str .= " AND $v";
			}
		}		
		//��������������ɵ�sql��������Ҫ�����sql��ɲ���
		if(!empty($orther) && !empty($orther['sql'])){
			$where_str .= " AND $orther[sql]";
		}
		$where_str = urlencode(trim($where_str));
        $p = md5($where_str.$authkey);//���۸ļ��ܲ���,���ݲ������ж�$where_str+$authkey���ܺ���ַ�����$p�Ƿ�һ��

		$html = "<a style=\"float:right;text-decoration:none;\" onclick=\"return floatwin('open_arcdetail',this)\" href=\"".$url."&q=".$where_str."&p=".$p."\"><input class='excel_button'  type=\"button\" value=\"EXCEL����\"></a>";
		if(empty($this->A['MoreSet'])){
			echo strbutton('bfilter','ɸѡ');
			echo $html;
		}else{
			echo $html;
			echo "</div></div>";//�߼�����β
		}
		tabfooter();
		unset($this->oS);
	}

    /**
     *¥�����,���͵������������չʾ,�����pid����
     *
     * @param    string   $key  ����λ��Ŀ�ؼ���
     * @return   html    ����html�ַ���
     */
    function o_view_upushs($title = '',$incs = array(),$numpr = 5){
        //$numprÿ����ʾ����
        $html = '';$i = 0;
        $incs || $incs = array_keys($this->oO->cfgs);
        foreach($incs as $k){
            if($re = $this->o_view_one_push($k)){
                if($numpr && $i && !($i % $numpr)) $html .= '<br>';
                $i ++;
                $html .= $re;
            }
        }
        //$html = str_replace("?entry=extend&","?entry=extend&pid3={$this->A['pid']}&",$html);
        $html = preg_replace("/=push_(\d+)/",'=push_${1}&pid3='.$this->A["pid"],$html);
        if($html){
            $title || $title = 'ѡ������λ';
            trbasic($title,'',$html,'');
        }
    }


}
