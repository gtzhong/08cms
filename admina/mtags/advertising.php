<?php
/**
 * ����ǩ
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */

(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!empty($mtagnew)) cls_Array::array_stripslashes($mtagnew);//�������ݿ⣬��ת��ȡ��
if(!submitcheck('bsubmit')){
    $mtag = cls_cache::Read($ttype,$mtagnew['ename'],'');
    tabheader("���ģ�壺>> <a href=\"?entry=$entry&extend=$extend&action=recache&src_type=other&fcaid=$fcaid\">���»���</a>",'adv_tpl',"?entry=$entry&extend=$extend&action=$action&src_type=other&fcaid=$fcaid",2,1,1);
	trbasic('���ID','', $fcaid, '');    
    trbasic('����״̬','adv[checked]', $advertising['checked'],'radio',array('guide' => '����������򲻻���ǰ̨ҳ����ʾ��'));
	templatebox('�����ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
    echo '<script type="text/javascript">
              if(parent.document.getElementById("operateitem").innerHTML != "") {
                  document.getElementById(\'mtagnew[template]\').parentNode.parentNode.align="center";
              }
          </script>';
	trbasic('��淵�ص���������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv����������Ƕ��ʱ���ò�������Ϊ��ͬ�����¼���档<br> �ڵ�ǰ����ڿ���{aaa}��{$v[aaa]}������Ϣ�����������Ϣֻ��ʹ��{$v[aaa]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? 10 : $mtag['setting']['limits']);
	trbasic('�ӵڼ�����¼��ʼ��ʾ','mtagnew[setting][startno]',empty($mtag['setting']['startno']) ? '' : $mtag['setting']['startno'],'text',array('guide'=>'���ð���ǰ���õĵڼ�����¼��ʼ��Ĭ��Ϊ0��'));
#	echo "<script>function setdisabled(showid,hideid){var showobj=\$id(showid),hideobj=\$id(hideid),sinput=showobj.getElementsByTagName('input');hinput=hideobj.getElementsByTagName('input');showobj.style.display='';hideobj.style.display='none';for(var i=0;i<sinput.length;i++){sinput[i].disabled=false}for(var i=0;i<hinput.length;i++){hinput[i].disabled=true}}</script>";
#	echo "<script>window.onload = function(){setdisabled(".(empty($mtag['setting']['ids'])?"'ids_mod1','ids_mod2'":"'ids_mod2','ids_mod1'").");}</script>";
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=adv_farchives&typeid=$fcaid\" target=\"_blank\">����</a>";
	echo '</div>';

	echo "<div id=\"ids_mod1\" style=\"display:".(empty($mtag['setting']['ids']) ? '' : 'none')."\">";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isfunc]\" name=\"mtagnew[setting][isfunc]\"".(empty($mtag['setting']['isfunc']) ? '' : ' checked').">�ִ����Ժ���";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isall]\" name=\"mtagnew[setting][isall]\"".(empty($mtag['setting']['isall']) ? '' : ' checked').">������ѯ�ִ�";
	tabfooter();

    // �߼�����
    echo '<div class="conlist1"><span style="float:left;">�߼����ã�</span><div style="float:left; width:18px; height:18px;overflow: hidden;margin-top: 6px;" onclick="toggers(this);" class="add2" title="�����չ����"></div></div>';
    echo '<div id="adv_config" style="display:none;"><table width="100%" border="0" cellpadding="0" cellspacing="0" class=" tb tb2 bdbot">';
    trbasic('��������λ����','adv[params]',$advertising['params'],'text',array('w'=>'50','guide' => '1. ����������ʽ�� "������":"����ֵ"�����������Ӣ�Ķ��Ÿ���<br />2.	����ֵ��������ֵ�ͣ�������ϵͳԭʼ��ʶ��Ҳ�����ǹ̶���ֵ'));

    $addstr = "&nbsp; >><a href=\"?entry=liststr&action=adv_farchives&typeid=$fcaid\" target=\"_blank\">����</a>";
    trbasic('ɸѡ��ѯ�ִ�'.$addstr,'mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '������ʽ��������(\'����1\',\'����2\')��������ѯ�ִ�����select��from��where,��Ҫ��order��limit��'));
    tabfooter();
    echo <<<EOT
        </div>
        <style type="text/css">
            .add2 { background:url(images/admina/add2.gif) no-repeat -5px -5px; }
            .sub2 { background:url(images/admina/sub2.gif) no-repeat -5px -5px; }
        </style>
        <script type="text/javascript">
            function toggers(obj)
            {
                if(obj.className == 'add2') {
                    obj.className = 'sub2';
                    document.getElementById('adv_config').style.display = '';
                } else {
                    obj.className = 'add2';
                    document.getElementById('adv_config').style.display = 'none';
                }
            }
        </script>
EOT;
    tabfooter('bsubmit','�ύ');
}else{
	if(empty($mtagnew['template'])) mtag_error('��������ģ��');    
    $mtagnew['setting']['casource'] = $fcaid;
	$mtagnew['setting']['startno'] = trim($mtagnew['setting']['startno']);
    $mtagnew['setting']['validperiod'] = '1';
	$mtagnew['setting']['orderstr'] = 'a.vieworder DESC ';
#    $mtagnew['setting']['js'] = 1;
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['alimits'] = (isset($mtagnew['setting']['alimits']) ? intval($mtagnew['setting']['alimits']) : 0);
	$mtagnew['setting']['length'] = (isset($mtagnew['setting']['length']) ? max(0,intval($mtagnew['setting']['length'])) : '');
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['isfunc'] = empty($mtagnew['setting']['isfunc']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['isall'] = empty($mtagnew['setting']['isall']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['ttl'] = (isset($mtagnew['setting']['ttl']) ? intval($mtagnew['setting']['ttl']) : 0);
	$mtagnew['setting']['forceindex'] = (isset($mtagnew['setting']['forceindex']) ? trim($mtagnew['setting']['forceindex']) : '');
	if(empty($mtagnew['setting']['forceindex'])) unset($mtagnew['setting']['forceindex']);
	$idvars = array('isfunc','isall',);
	foreach($idvars as $k) unset($mtagnew['setting'][$k]);
}