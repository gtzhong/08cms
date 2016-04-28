<?php
/**
 * ͨ��¥��/���ַ�/���ⷿԴ���ƻ�ȡ��Ӧ���ĵ���Ϣ
 *
 * @example   ������URL��index.php?/ajax/front_archiveinfo/wid/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Front_ArchiveInfo extends _08_Models_Base
{
    public function __toString()
    {
		$mcharset = $this->_mcharset;
		header("Content-Type:text/html;CharSet=$mcharset");
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$chid  = empty($this->_get['chid']) ? 0 : max(1,intval($this->_get['chid']));
        $aid  = empty($this->_get['aid']) ? '' : cls_string::iconv('utf-8',$mcharset,$this->_get['aid']);
        $limit  = empty($this->_get['limit']) ? 50 : max(1,intval($this->_get['limit']));

        if(!in_array($chid = max(1,intval($this->_get['chid'])),array(2,3,4))){
            echo "var data='���ṩ��ȷ���ĵ�ģ��ID';";
            exit();
        }

        if(empty($aid)){
            echo "var data='���ṩ��ȷ�������ؼ���';";
            exit();
        }
        $data = array();
        $data = $this->getArchiveData($chid,$aid,$limit);
        $data = cls_string::iconv($mcharset, "UTF-8", $data);
        echo 'var data = ' . json_encode($data) . ';';
	}

    /**
     * ¥��/���ַ�/�����У������ĵ�ģ���ֶβ��Ҷ�Ӧ�����ݣ���һ����ȫ�����ĵ��ֶΣ����ų�ĳЩ�ֶΣ�
     * @param int    $chid     �ĵ�ģ��ID
     * @param string $aid      ��ѯaid
     * @param int    $limit    ���Ʋ�ѯ����
     * return array  array(�ֶ�0=>�ֶ�ֵ0,�ֶ�1=>�ֶ�ֵ1) ������һ���ĵ�������
     */
    protected function getArchiveData($chid,$aid){
        $db = $this->_db;
        $tblprefix = $this->_tblprefix;
        $mconfigs = cls_cache::Read('mconfigs');
        //ͼƬ������
        $hostUrl = empty($mconfigs['ftp_enabled'])?$mconfigs['cms_abs']:$mconfigs['ftp_url'];
        //��ȡ�ĵ�ģ���ֶ�
        $archiveFields = cls_cache::Read('fields',$chid);
        //��Ҫ�ų����ֶ�
        $putAwayArr = array('subject','author','stpic','lphf','loupanlogo','dt','keywords','abstract','xqt','content','fdname','fdtel','fdnote','qqqun','xqjs','xqhs');
        //ѭ���ĵ�ģ���ֶΣ�����ѡ����ѡ���ֶζ�Ӧ��ѡ��������飬���������»���+�ֶ�������
        foreach($archiveFields as $k => $v){
            $arr = array();
            $fieldArr = array();
            if(in_array($v['datatype'],array('select','mselect'))){
               $arr = explode("\n",$v['innertext']);
               foreach($arr as $key => $val){
                    $arr_sub = explode("=",$val);
                    $fieldArr[$arr_sub[0]] = $arr_sub[1];
               } 
               $_{$k} = $fieldArr;            
            }                      
        }   
        unset($arr,$fieldArr);        
    

        //�����ֶ��ַ���
        $selectStr = 'SELECT subject,a.aid';
    	foreach($archiveFields as $k => $v){
    		if(!in_array($k, $putAwayArr)){
                $selectStr .= ','.$k;
    		}
    	}        
  
        $data = array();
       
        $row = $db->fetch_one(" $selectStr FROM {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_".$chid." c ON a.aid = c.aid WHERE a.aid='$aid' LIMIT 1 ");            
        foreach($row as $k => $v){              
            if(!in_array($k,array_keys($archiveFields))) continue;
            $arr = array();
            if($archiveFields[$k]['datatype'] == 'select'){
                $data[$k] = empty($_{$k}[$v])?'-':$_{$k}[$v];
            }else if($archiveFields[$k]['datatype'] == 'mselect'){
                $filedStr = '';            
                $arr = explode(" ",$v);
                preg_match_all('/\S\s/isU',$v,$arr);           
                if(!empty($arr)){
                    foreach($arr[0] as $key=>$val){       
                        $val = max(0,intval($val));
                        if(empty($_{$k}[$val])) continue;
                        $filedStr .= ",".$_{$k}[$val];
                    }
                    $data[$k] = substr($filedStr,1);
                }         
            }else if($archiveFields[$k]['datatype'] == 'image'){
                $data[$k] = $hostUrl.$v;
            }else{
                $data[$k] = $v;
            }
        }  
        $data['aid'] = $row['aid'];
        $row = $db->fetch_one(" select * FROM {$tblprefix}".atbl($chid)." a  WHERE a.aid='$aid' LIMIT 1 ");
        $data['url'] = cls_url::view_arcurl($row);      
        return $data;
    }

}

?>