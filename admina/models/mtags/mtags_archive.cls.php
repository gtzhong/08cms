<?php
/**
 * �����ĵ����ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_archive extends cls_mtags_archives
{
    const CHID = 'chid';
    /**
     * ��ʾ��ʶ���������Ŀselectѡ��
     * 
     * @param array $oarr ��ǰ��ʶ��������
     */ 
    public function showCotypesSelect(array $oarr)
    {
        global $channels;
        // 0=����ģ��; 1=�ֶ�ָ��
        $config = array(0 => '������', -1 => '����ģ��');
    	foreach($channels as $k=>$v) 
        {
            $config[$k] = "($k)$v[cname]";
        }
        $sclasses = (isset($this->params['sclass']) && $this->params['sclass'] != '' ? $this->params['sclass'] : @$oarr['setting'][self::CHID]);
        
		trbasic("���������ĵ�ģ��",
			'',"<select onchange=\"setIdWithS(this);document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\" id=\"mselect_mtagnew[setting][".self::CHID."]\" style=\"vertical-align: middle;\">" . makeoption($config, $sclasses) . "</select><input type=\"text\" value=\"". $sclasses ."\" name=\"mtagnew[setting][".self::CHID."]\" id=\"mtagnew[setting][".self::CHID."]\" class=\"w55\" onblur=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"/>",'', array('guide' => '�ù�����Կ�ʼ���� "����ԭʼ��ʶ" ��ѡ�λ'));
    }
    
    /**
     * ��ȡ��ʶ�������sclass ID
     * 
     * @param array $setting ��ǰ��ʶ��������
     */ 
    public function getSclass(array $setting)
    {
        return @$setting[self::CHID];
    }
}