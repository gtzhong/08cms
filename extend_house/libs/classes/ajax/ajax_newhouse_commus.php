<?
/**
 * ¥��(�·�)���� ajax ����
 *
 * @example   ������URL��index.php?/ajax/delweituo/cid/...
 * @author    icms <icms@foxmail.com>
 * @copyright 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_newhouse_commus extends _08_Models_Base
{
    public function __toString()
    {
        global $onlineip,$memberid;
        $mcharset = cls_env::getBaseIncConfigs('mcharset');
        header("Content-Type:text/html;CharSet=$mcharset");
        $tblprefix = $this->_tblprefix;
        $db = $this->_db;
        $curuser   = $this->_curuser;
        $timestamp = TIMESTAMP;

        $aid  = empty($this->_get['aid']) ? 0  : max(1,intval($this->_get['aid']));
        $m_cookie  = cls_env::_COOKIE();
        $onlineip = cls_env::OnlineIP();


        $action = $this->_get['action'];
        if($action == 'yixiang'){
            //�ж���֤��
            $cuid=3;
            $verify  = isset($this->_get['verify']) ? trim($this->_get['verify']) : '';
            cls_env::SetG('verify',$verify);
            $regcode = $this->_get['regcode'];
            if(!regcode_pass("commu$cuid",empty($regcode) ? '' : trim($regcode))) exit('var sInfo = "��֤�����";');

            if(!$aid) exit('var sInfo = "��ָ���������¥�̡�";');
            if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) exit('var sInfo = "��ǰ�����ѹرա�";');

            $arc = new cls_arcedit;
            $arc->set_aid($aid,array('chid'=>4,'au'=>0));
            if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])) exit('var sInfo = "��ָ���������¥�̡�";');

            $fields = cls_cache::Read('cufields',$cuid);

            //���ݴ���
            if(!$curuser->pmbypmid($commu['pmid'])) exit('var sInfo = "��û�з��������Ȩ�ޡ�";');
            if(!empty($commu['repeattime']) && !empty($m_cookie["08cms_cuid_{$cuid}_{$aid}"])) exit('var sInfo = "�����벻Ҫ����Ƶ����";');



            //exit('var sInfo = "��Ҫ����";');
            $sqlstr = "aid='$aid',ip='$onlineip',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1";
            $c_upload = new cls_upload;
            $a_field = new cls_field;
            $fmdata = $this->_get['fmdata'];
            //$fmdata = $this->array_iconv("UTF-8","GBK",$fmdata);
            foreach($fmdata as $k=>$v){
                $fmdata[$k] = @cls_string::iconv("UTF-8",$mcharset,$v);
            }

            //var_dump($fields);exit();
            foreach($fields as $k => $v){
                if(isset($fmdata[$k])){
                    $a_field->init($v);
                    $fmdata[$k] = $a_field->DealByValue($fmdata[$k],'');
                    //$fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
                    $sqlstr .= ",$k='$fmdata[$k]'";
                    if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $sqlstr .= ",{$k}_x='$y'";
                }
                $a_field->error = substr($a_field->error, 4);
                if ($a_field->error) exit('var sInfo = "'.$a_field->error.'��";');
            }
            unset($a_field);die('info');
            $db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlstr");
            if($cid = $db->insert_id()){
                if(!empty($commu['repeattime'])) msetcookie("08cms_cuid_{$cuid}_{$aid}",1,$commu['repeattime'] * 60);
                #���ò����ɹ�������cookiey
                $c_upload->closure(1,$cid,"commu$cuid");
                $c_upload->saveuptotal(1);
                $curuser->basedeal("commu$cuid",1,1,"����$commu[cname]",1);
                exit('var sInfo = 1;');
            }else{
                $c_upload->closure(1);
                exit('var sInfo = "�����Ͳ��ɹ���";');
            }
        }
        if($action == 'sendphone'){
            echo '���͵��ֻ�';
        }
        if($action == 'comment'){
            $tocid   = empty($this->_get['tocid']) ? 0 : max(1,intval($this->_get['tocid']));
            $cuid    = isset($this->_get['cuid']) ? max(1,intval($this->_get['cuid'])) : 48;
            $regcode = isset($this->_get['regcode']) ? trim($this->_get['regcode']) : '';
            $verify  = isset($this->_get['verify']) ? trim($this->_get['verify']) : '';
            cls_env::SetG('verify',$verify);

            $content = isset($this->_get['content']) ? iconv('utf-8',$mcharset,$this->_get['content']): 0;


            //�������ݲ���Ϊ��
            if(empty($content)){
                echo "var data='�������ݲ���Ϊ��';";
                exit();
            }


            //��֤�����
            if(!regcode_pass("commu$cuid",$regcode)) {
                echo "var data='��֤�����'; ";
                exit();
            }

            $commu = cls_cache::Read('commu',$cuid);

            //���۹����ѹر�
            if(!$commu['available']) {
                echo "var data='���۹����ѹر�';";
                exit();
            }

            //��û������Ȩ��
            if(!$curuser->pmbypmid($commu['pmid'])){
                echo "var data='��û������Ȩ��';";
                exit();
            }

            //�����벻Ҫ����Ƶ����
            if(!empty($commu['repeattime'])){
                if(empty($tocid) && !empty($m_cookie["08cms_cuid_{$cuid}_{$aid}"])){
                    echo "var data='�����벻Ҫ����Ƶ����';";
                    exit();
                }
                if(!empty($tocid) && !empty($m_cookie["08cms_cuid_{$aid}_{$tocid}"])){
                    echo "var data='�ظ������벻Ҫ����Ƶ����';";
                    exit();
                }
            }

            if($tocid && !$db->result_one("SELECT cid FROM {$tblprefix}$commu[tbl] WHERE aid='$aid' AND cid='$tocid'")) $tocid = 0;
            $memberstr = '';
            if(empty($curuser->info['mid'])){
                $memberstr = "mid=0,mname='�ο�'";
            }else{
                $memberstr = "mid='{$curuser->info['mid']}',mname='{$curuser->info['mname']}'";
            }

            $sqlstr = "aid='$aid',ip='$onlineip',tocid='$tocid',$memberstr,createdate='$timestamp',comment='$content'";

            if($curuser->pmautocheck($commu['autocheck'],'cuadd')) $sqlstr .= ",checked=1";
            $c_upload = new cls_upload;
            $a_field = new cls_field;
            $fields = cls_cache::Read('cufields',$cuid);
            foreach($fields as $k => $v){
                if(isset($fmdata[$k])){
                    $a_field->init($v);
                    //$fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
                    $fmdata[$k] = $a_field->DealByValue($fmdata[$k],'');

                    $sqlstr .= ",$k='$fmdata[$k]'";
                    if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $sqlstr .= ",{$k}_x='$y'";
                }
            }
            unset($a_field);
            $db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlstr");
            if($cid = $db->insert_id()){
                #���ò����ɹ�������cookie
                if(!empty($commu['repeattime'])) {
                    empty($tocid) && msetcookie("08cms_cuid_{$cuid}_{$aid}",1,$commu['repeattime'] * 60);
                    !empty($tocid) && msetcookie("08cms_cuid_{$aid}_{$tocid}",1,$commu['repeattime'] * 60);
                }

                $c_upload->closure(1,$cid,"commu$cuid");
                $c_upload->saveuptotal(1);
                $curuser->basedeal("commu$cuid",1,1,"����$commu[cname]",1);
                $data = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] WHERE cid = '$cid'");

                if(!$curuser->pmautocheck($commu['autocheck'])){
                    echo 'var data="�ȴ����";';			//�ȴ����
                }else{
                    $mconfigs = cls_cache::Read('mconfigs');
                    $data['time'] = date('Y-m-d H:i:s',$data['createdate']);
                    $data = cls_string::iconv($mcharset, "UTF-8", $data);
                    //var_export($data);

                    echo 'var data = ' . json_encode($data) . ';';
                }
            }else{
                $c_upload->closure(1);
                //���۷����ɹ�
                echo "var data='���۷����ɹ�';";
            }

        }

    }

}



?>

