<?php
!defined('M_COM') && exit('No Permission');
backnav('payonline','online');
if($curuser->getTrusteeshipInfo()) cls_message::show('���Ǵ����û�����ǰ������ԭ�û�������Ȩ�ޣ�');
$poids = _08_factory::getInstance(_08_Loader::MODEL_PREFIX . 'PayGate_Pays')->getPays();
empty($poids) && !empty($deal) && cls_message::show('û����Ч������֧���ӿ�');
$extra_param = empty($jumpurl) ? '' : '&jumpurl=' . rawurlencode($jumpurl);
if(empty($deal)){
	if(empty($poids)){
		tabheader("����֧��");
		trbasic('','',"<br>�ǳ���Ǹ����վ������Ҫ��ͨһ��֧���ӿڲſ�������֧����<br><br>������ѡ�� &nbsp;<a href=\"?action=payother$extra_param\">>>����֧��</a>",'');
		tabfooter();
	}else{
		tabheader("����֧��",'paynew',"?action=payonline&deal=confirm$extra_param",2,0,1);
        trhidden('WIDdefaultbank', '');
        trhidden('WIDdefaultbank_name', '');
		$amount = empty($amount) ? '' : max(0,round($amount,2));
		if(!$oldmsg = $db->fetch_one("SELECT * FROM {$tblprefix }pays WHERE mid='$memberid' ORDER BY pid DESC LIMIT 0,1")) $oldmsg = array();
		trbasic('֧���ӿ�','',makeradio('paynew[poid]',$poids),'');
		trbasic('֧�����','paynew[amount]',$amount,'text',array('guide' => '֧�����(�����)', 'validate' => makesubmitstr('paynew[amount]',1,'number',0,15)));
		trbasic('��ϵ������','paynew[truename]',empty($oldmsg['truename']) ? '' : $oldmsg['truename'],'text', array('validate' => makesubmitstr('paynew[truename]',0,0,0,80),'w'=>50));
		trbasic('��ϵ�绰','paynew[telephone]',empty($oldmsg['telephone']) ? '' : $oldmsg['telephone'],'text', array('validate' => makesubmitstr('paynew[telephone]',0,0,0,30),'w'=>50));
		trbasic('��ϵEmail','paynew[email]',empty($oldmsg['email']) ? '' : $oldmsg['email'],'text', array('validate' => makesubmitstr('paynew[email]',0,'email',0,100),'w'=>50));
		tr_regcode('payonline');
		tabfooter('bsubmit','����');
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC("ajax=show_bank&datatype=content");
        echo <<<EOT
        <script type="text/javascript">
            var bankObject = document.getElementById('_paynew[poid]alipay_direct_bankpay');
            if ( bankObject )
            {
                var htmlSpan = document.createElement("span");
                htmlSpan.id = 'bankname';
                htmlSpan.style.color = 'red';
                bankObject.parentNode.appendChild(htmlSpan);
                bankObject.onclick = function() {
                    selectBank(this.value);
                }
                function selectBank( _value )
                {
                    uploadwin('show_bank', function(data){}, 0, 0, 0, 0, 0, 800, 715, '$ajaxURL');
                }
            }            
        </script>
EOT;
	}
	m_guide("pay_notes",'fix');
}elseif($deal == 'confirm'){
	if(!regcode_pass('payonline',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤���������','?action=payonline');
	$paynew['amount'] = max(0,round(floatval($paynew['amount']),2));
	empty($paynew['amount']) && cls_message::show('������֧�����','?action=payonline');
	array_key_exists($paynew['poid'], $poids) || cls_message::show('֧��ģʽ�������Ϣ������','?action=payonline');
	$paynew['truename'] = trim(strip_tags($paynew['truename']));
	$paynew['telephone'] = trim(strip_tags($paynew['telephone']));
	$paynew['email'] = trim(strip_tags($paynew['email']));
	tabheader('ȷ�ϸ�����Ϣ','paynew',"?action=payonline&deal=send$extra_param");
	
    if ( isset($paynew['poid']) && (false !== stripos($paynew['poid'], 'alipay')) )
    {
        echo '<tr><td colspan="2">';
        $payname = '֧����';
        if ( empty($WIDdefaultbank) )
        {
            $WIDdefaultbank = '';
        }
        else
        {
        	$WIDdefaultbank = trim($WIDdefaultbank);
            empty($WIDdefaultbank_name) && $WIDdefaultbank_name = '';
            $payname .= "������<span style='color:red;'>{$WIDdefaultbank_name}</span>��";
        }
        trhidden('paynew[defaultbank]', empty($WIDdefaultbank) ? '' : trim($WIDdefaultbank));
        echo '</td></tr>';
        trbasic( '֧���ӿ�','', $payname, '' );
    }
    else
    {
    	trbasic('֧���ӿ�','',$poids[$paynew['poid']],'');
    }
    
	trbasic('֧�����','',$paynew['amount'],'',array('guide' => '֧�����(�����)'));
	trbasic('��ϵ������','',$paynew['truename'],'');
	trbasic('��ϵ�绰','',$paynew['telephone'],'');
	trbasic('��ϵEmail','',$paynew['email'],'');
	echo "<tr><td colspan=\"2\"><input type=\"hidden\" name=\"paynew[poid]\" value=\"$paynew[poid]\">\n";
	echo "<input type=\"hidden\" name=\"paynew[amount]\" value=\"$paynew[amount]\">\n";
	echo "<input type=\"hidden\" name=\"paynew[truename]\" value=\"$paynew[truename]\">\n";
	echo "<input type=\"hidden\" name=\"paynew[telephone]\" value=\"$paynew[telephone]\">\n";
	echo "<input type=\"hidden\" name=\"paynew[email]\" value=\"$paynew[email]\"></td></tr>\n";
	tabfooter('bsubmit','ȷ�ϲ�����');
}elseif($deal == 'send'){
	(empty($paynew) || !array_key_exists($paynew['poid'], $poids)) && cls_message::show('֧��ģʽ�������Ϣ������','?action=payonline');
    $paynew['subject'] = $hostname;
    $paynew['callback'] = _08_CMS_ABS . str_replace('&deal=send', '', substr($_SERVER['REQUEST_URI'], 1));
    _08_factory::getPays($paynew['poid'])->send($paynew);
}elseif($deal == 'receive'){
	$pid = empty($pid) ? 0 : (int)$pid;
	empty($pid) && cls_message::show('��ָ����ȷ��֧��');
	if(!$item = $db->fetch_one("SELECT * FROM {$tblprefix }pays WHERE pid='$pid'")) cls_message::show('��ָ����ȷ��֧����¼');
	$flagarr = array(
	0 => '��Ա�ֽ�֧������ɹ���',
	2 => '������֧���ӿڷ���֧��ʧ�ܵ���Ϣ',
	3 => '֧�����ͼ�¼����ͬ����ȴ�����Ա����',
	4 => '���ڵ�֧����¼���벻Ҫ�ظ�����',
	5 => '�ֽ��յ�����Ա�ֽ��Զ����治�ɹ�����֪ͨ����Ա��',
	6 => '�ֽ��յ����Զ���ֵ���ܹرգ���ȴ�����Ա��ϵ��',
	);
	tabheader('����֧����Ϣ�鿴');
	trbasic('֧�����״̬','',$flagarr[$flag],'');
	trbasic('֧�����(�����)','',$item['amount'],'');
	trbasic('������(�����)','',$item['handfee'],'');
	trbasic('֧���ӿ�','',$item['poid'] ? $poids[$item['poid']] : '-','');
	trbasic('֧��������','',$item['ordersn'] ? $item['ordersn'] : '-','');
	trbasic('��Ϣ����ʱ��','',date("$dateformat $timeformat",$item['senddate']),'');
	trbasic('�ֽ𵽴�ʱ��','',$item['receivedate'] ? date("$dateformat $timeformat",$item['receivedate']) : '-','');
#	trbasic('���ֱ���ʱ��','',$item['transdate'] ? date("$dateformat $timeformat",$item['transdate']) : '-','');
	tabfooter();
}
?>