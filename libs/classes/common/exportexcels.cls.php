<?php
/**
 * ���ݵ�����excel��
 * �����ϴ���chid/cuid/aid  
 * ���÷�����
 *     ģ���е��ã�$oL->s_footer_ex($cms_abs."admina.php?entry=extend&extend=export_excel&chid=$chid&filename=userhouse");
 *     �����е��ã�	echo "<a style=\"float:right;text-decoration:none;\" onclick=\"return floatwin('open_arcdetail',this)\" href=\"".$cms_abs."admina.php?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=".($chid==3?'esfyyx':'czfyyx')."&q=".$where_str."&p=".$p."\"><input class='button' type=\"button\" value=\"EXCEL����\"></a>";
 *
 * ���ݴ��Σ�����ʾ�ĵ�ģ���ֶΣ��ĵ������ֶ��Լ������ֶ� ��  �ĵ�����/�ĵ�ģ���ֶ�  
 * ��  �����ֶ�(�ֶ��в���datatypeΪimage��images��map��htmltext���ֶ�)
 *
 * �������ݣ�����excel�ļ� 
 */
class cls_exportexcels{	
	private $mc=0;
	private $chid;
	private $cuid;
	
	function __construct(){
		global $chid,$cuid;
		$this->mc = defined('M_ADMIN') ? 0 : 1;	
		$this->chid = empty($chid) ? 0 : max(1,intval($chid));
		$this->cuid = empty($cuid) ? 0 : max(1,intval($cuid));
    }
	
	
	
	/**
	  *��ʾ��ѡȡ������excel���ֶ�
	  *@param string $table_title		table�����ı���˵��
	*/
	public function ShowFieldsTable($chid,$cuid,$table_title,$where_str,$url){
		$fields_arr = $this->GetExportFields($chid,$cuid);
		$this->ShowExportFields($fields_arr[0],$fields_arr[1],$table_title,$where_str,$url);
	}


	##��ȡ�ɵ�����Excel���ֶ�
	protected function GetExportFields($chid,$cuid){
		$chid_fields = $chid ? cls_cache::Read('fields',$chid) : array();
		$cuid_fields = $cuid ? cls_cache::Read('cufields',$cuid) : array();	
		$chk_arr = array();
		$chk_count = 0;	
		foreach(array($chid_fields,$cuid_fields) as $arr){
			if(!empty($arr)){
				foreach($arr as $k => $v){
					if($v['available'] && !in_array($v['datatype'],array('image','images','map','htmltext','vote'))){
						$chk_arr[$v['tbl']][$k] = $v['cname'];
						$chk_count ++;
					}
				}
			}
		}
		return array($chk_arr,$chk_count);
	}
	
	##���ɵ�����Excel���ֶ��Ƴɱ��
	protected function ShowExportFields($chk_arr,$chk_count,$table_title,$where_str,$url){
		global $extend_str,$chid,$cuid,$aid,$filename,$authkey;
		$chid = empty($chid) ? 0 : max(1,intval($chid));
		$cuid = empty($cuid) ? 0 : max(1,intval($cuid));
		$aid = empty($aid) ? 0 : max(1,intval($aid));
		$td_num = 5;//table��ÿ�еĵ�Ԫ�����
		
		$where_str = urlencode($where_str);
        $p = md5($where_str.$authkey);//���۸ļ��ܲ���,���ݲ������ж�$where_str+$authkey���ܺ���ַ�����$p�Ƿ�һ��
		$table_title || $table_title = "��ѡ�񵼳����ݵ���Ŀ";
		$table_title .="<input type=\"checkbox\" id=\"checkall\" style=\"margin:0 0 0 30px;\" onclick=\"check_all('newform')\">(ȫѡ)";
		echo form_str('newform',"$url&chid=$chid&cuid=$cuid&aid=$aid&filename=$filename&q=$where_str&p=$p",1,1,1,'post');
		echo "<div class=\"conlist1\">$table_title</div>";
		$checkbox_str = 111;//��ֵ��㸳ֵ��ֻ����������checkbox��һ���ַ���
		foreach($chk_arr as $k => $v){
			$title = strstr($k,'archives')?(strstr($k,'archives_')?"�ĵ���Ϣ2":"�ĵ���Ϣ1"):"������Ϣ";
			echo "<div class=\"conlist1\">$title</div>";
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\" tb tb2 bdbot\" style=\"text-align:left !important; \">\n";
			$_count = count($v);
			$i = 0;				
			foreach($v as $key => $val){
				$i ++;
				if($i % $td_num == 1){
					$checkbox_str ++;
					echo "<tr><td width=\"60\"><input style=\"margin-left:20px;\" type=\"checkbox\" onclick=\"check_each_row_input('newform','checkbox".$checkbox_str."',this)\"></td>";
				}
				echo "<td width=\"140\"><input type=\"checkbox\" id=\"checkbox".$checkbox_str.$key."\" name=\"fmdata[$k][$key]\" value=\"$val\">$val</td>";				
				if(($i % $td_num != 0) && ($i == $_count)){
					for($j=0;$j<$td_num - $i % $td_num;$j++){
						 echo "<td width=\"140\"></td>";
					}
				}
				if(($i % $td_num == 0) || ($i == $_count)) echo "</tr>";
			}
			echo "</table>";
		}
			
		echo "<br /><span style=\"margin-right: 50px;\">����<input id=\"fmdata[limit]\" name=\"fmdata[limit]\" type=\"text\" value=\"\" style=\"width:30px;\">������</span><input class=\"btn\" type=\"submit\" name=\"bsubmit\" value=\"����\"></form>";
		echo "<div class=\"blank9\"></div>";
		?>
        <script type="text/javascript">
        	function check_all(formname){
				var form = document.forms[formname];
				for(var i = 0; i < form.elements.length; i++){
					var e = form.elements[i];
					e.checked = document.getElementById('checkall').checked;					
				}
			}			
			function check_each_row_input(formname, sid, obj){	
				var form = document.forms[formname];
				for(var i = 0; i < form.elements.length; i++) {
					var e = form.elements[i];		
					if(e.type == 'checkbox' && e.id.indexOf(sid) != -1) {									
						e.checked = obj.checked;
					}					
				}
			}
			function ajaxform(form){
				var num = 0;
				for(var i = 0; i < form.elements.length; i++) {
					var e = form.elements[i];		
					if(e.type == 'checkbox' && e.checked == true) {									
						num ++;
					}					
				}
				if(num == 0){alert("��ѡ�񵼳���Ŀ");return false ;}
				return true;
			}
		</script>
        <?php
	}
	
	##�����ݿ��л�ȡ����������excel������	 
	protected function GetDataForExcel($fmdata,$where_str){
		global $db,$tblprefix;
		//���д��ݹ����� sql����������������
		$limit = empty($fmdata['limit']) ? 100 : max(1,intval($fmdata['limit']));
		//���д��ݹ����� �б�����������������б���������ɵ��ַ�����		
		unset($fmdata['limit']);

		$_fields = array();
		$_cufields = array();
		$this->chid && $_fields = cls_cache::Read('fields',$this->chid);	
		$this->cuid && $_cufields = cls_cache::Read('cufields',$this->cuid);
		$field_arr = array();//����ֶ�����Ϊselect,mselect,data���ֶε�innertext��ֺ������
		$datatype_arr = array('select','mselect');
		$mselect_arr = array();
		foreach($fmdata as $k => $v){//��ȡinnertext�����в��
			foreach($v as $key => $val){
				if(strstr($k,'archives'))	$_cache_name = '_fields';
				else $_cache_name = '_cufields';
				$arr_name = $$_cache_name;			

				if(in_array($arr_name[$key]['datatype'],$datatype_arr)){
					$re = array();
					$arr_name[$key]['datatype'] == 'mselect' && ($mselect_arr[] = $key);
					if(!$arr_name[$key]['fromcode']){
						$temps = explode("\n",$arr_name[$key]['innertext']);
						foreach($temps as $v){
							$temparr = explode('=',str_replace(array("\r","\n"),'',$v));
							$temparr[1] = isset($temparr[1]) ? $temparr[1] : $temparr[0];
							$re[$temparr[0]] = $temparr[1];
						}
						unset($temps,$temparr);
					}else{
						$re = @eval($arr_name[$key]['innertext']);
					}
					$field_arr[$key] = $re;
				}elseif($arr_name[$key]['datatype'] == 'date'){
					$field_arr[$key] = 'date';
				}
			}
			
		}
		$field_arr_key = array_keys($field_arr);		
		
		$sql = $this->contruct_full_sql($where_str,$limit,$fmdata); 
		$query = $db->query($sql);
		$data = array();
		while($row = $db->fetch_array($query)){
			$r =array();
			foreach($row as $k => $v){
				if(in_array($k,$field_arr_key)){
					if($field_arr[$k] != 'date'){					
						if(in_array($k,$mselect_arr)){//����ѡ��
							$str = '';
							$v_arr = array_filter(explode("\t",$v));
							foreach($v_arr as $key){		
								$str .= $field_arr[$k][$key]."/";
							}
							$r[] = $str;
						}else{//����ѡ��							
							$r[] = (empty($v) || !isset($field_arr[$k][$v])) ?'-': $field_arr[$k][$v];
						}
						
					}else $r[] = empty($v) ? '-' : date('Y-m-d',$v);
				}else{
					$r[] = $v;
				}
			}
			$data[] = $r;
		}

		
		$title_arr = array();
		foreach($fmdata as $k => $v){
			foreach($v as $key => $val){
				$title_arr[] = $val;
			}
		}
		return array("content"=>$data,"title"=>$title_arr);
	}
	
	/**
	 *����������sql
	 *@param  string  $filter   �б��������������filterstr
	 *@param  limit   $limit    sql�������ݿ���������
	 *@param  data    $fmdata  	�����ݵ��ֶ�����
	 *@return string  			��ɵĲ�ѯ���
	 */
	protected function contruct_full_sql($where_str,$limit,$fmdata){
		global $tblprefix,$chid,$cuid,$aid,$mid;
		$chid = empty($chid) ? 0 : max(1,intval($chid));
		$cuid = empty($cuid) ? 0 : max(1,intval($cuid));
		$aid = empty($aid) ? 0 : max(1,intval($aid));
		$mid = empty($mid) ? 0 : max(1,intval($mid));
		$_select_str = '';
		$_from_str = '';
		$_where_str = '';
		$table_header_arr = array();
		$table_content_arr = array();
		
		$_tbl_name_arr = array_keys($fmdata);
		$_tblpre_pre = '';//����ѭ������ʱ����¼ǰһ����ı���
		$_tblpre_arr = array();//���ڴ��ѭ������ʱ���ֹ��ı���
		for($i =0;$i<count($_tbl_name_arr);$i++){			
			//$_tblpre_cur    ����ѭ������ʱ����¼��ǰ��ı���
			$_tblpre_cur = strstr($_tbl_name_arr[$i],'archives')?(strstr($_tbl_name_arr[$i],'_')? "c" : "a"):"cu";
			if($i == 0){				
				$_tblpre_pre = $_tblpre_cur;
				$_from_str .= "{$tblprefix}".$_tbl_name_arr[$i]." as $_tblpre_cur";
			}else{
				$_from_str .= " INNER JOIN {$tblprefix}".$_tbl_name_arr[$i]." as $_tblpre_cur ON ".$_tblpre_pre.".aid = ".$_tblpre_cur.".aid ";
			}
		}
		
		foreach($fmdata as $k => $v){
			$_tblpre = '';
			if(strstr($k,'archives')){	
				$_tblpre = strstr($k,'_')? "c" : "a";	
			}else{
				$_tblpre = "cu";		
			}
			$_tblpre_arr[] = $_tblpre;
			foreach($v as $key => $val){
				$table_header_arr[] = $val;
				$_select_str .= ",".$_tblpre.".$key ";
			}
		}
		
		//����
		$orderby_str = '';
		if(in_array('a',$_tblpre_arr)){
			$orderby_str = " a.aid DESC ";		
		}elseif(in_array('c',$_tblpre_arr)){
			$orderby_str = " c.aid DESC ";		
		}
		if(in_array('cu',$_tblpre_arr)){
			$orderby_str = " cu.cid DESC ";		
		}
		
		foreach(array('aid','chid') as $k){
			if($$k){
				if(in_array('a',$_tblpre_arr) && in_array($k,array('aid','chid'))){
					$_where_str .=  " AND a.$k='".$$k."' ";
				}
				if(in_array('cu',$_tblpre_arr) && in_array($k,array('aid'))){
					$_where_str .=  " AND cu.aid='".$$k."' ";
				}
			}
		}		
		//�����������������sql
		$_where_str .= $this->deal_width_filterstr($where_str,$_tblpre_arr);
		
		//��Ա����
		($this->mc && !empty($mid)) && $_where_str .= " AND a.mid='".$mid."' ";
		
		return "SELECT ".substr($_select_str,1)." FROM $_from_str WHERE 1=1 ".$_where_str."   ORDER BY ".$orderby_str." LIMIT $limit";
	}

	/**
	 *������������,���� where �������
	 *@param   array    $wherearr     �б����������
	 *@param   array    $_tblpre_arr  ���ύ���ֶ����ڵı�ı�����ɵ����飬eg�� array('a','c','cu')
	 *@return  string   $where_str    �����ַ���
	 */
	protected function deal_width_filterstr($where_str,$_tblpre_arr){
		//����filterstr����װsql
		//�ų����������ĵ�ģ���ֶε������archives_$chid
		$_str = '';
		if(!empty($where_str) && (in_array('a',$_tblpre_arr) || in_array('cu',$_tblpre_arr))){
			//��������ڱ���a����Ϊ�ĵ�			
			if(strstr($where_str,'a.') && !strstr($where_str,'cu.')){
				$_str .= $where_str;
			}
			
			//������ڱ���cu,���������ֲ����������������
			//1.���ѡ���ĵ��ֶ�
			//2.û��ѡȡ�ĵ��ֶΣ�ͬʱ���������в�����a.�����
			if(strstr($where_str,'cu.')){
				if(in_array('a',$_tblpre_arr) || (!in_array('a',$_tblpre_arr) && !strstr($where_str,'a.'))){
					$_str .= $where_str;
				}				
			}
		}
		return $_str;
	}
	
	
	//����excel��
	public function  ExportExcel($filename,$fmdata,$where_str){
		global $mcharset,$timestamp;		
		$data = $this->GetDataForExcel($fmdata,$where_str);	
		$charset = strstr(strtoupper($mcharset),'GB') ? 'GB2312' : 'UTF-8';
		$xls = new cls_XmlExcelExport($charset);//Ĭ��UTF-8����
		$xls->generateXMLHeader($filename.date('Y-md-His',$timestamp));  //excel�ļ���
		$xls->worksheetStart('message');
		$xls->setTableHeader($data['title']);  //���ֶ���
		$xls->setTableRows($data['content']); //����
		$xls->worksheetEnd();
		$xls->generateXMLFoot();
		unset($xls);
		die();
	}
}







?>