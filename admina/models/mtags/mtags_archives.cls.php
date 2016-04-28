<?php
/**
 * �ĵ����ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_archives extends cls_mtagsHeader
{
    const CHSOURCE = 'chsource';
    
    const CHIDS = 'chids';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * ��ʾ��ʶ���������Ŀselectѡ��
     * 
     * @param array $oarr ��ǰ��ʶ��������
     */ 
    public function showCotypesSelect(array $oarr)
    {
        global $channels;
        // 0=����ģ��; 1=�ֶ�ָ��
        $config = array(' - ����ģ�� - ');
    	foreach($channels as $k=>$v) 
        {
            $config[$k] = "($k)$v[cname]";
        }
        $sclasses = (isset($this->params['sclass']) && $this->params['sclass'] != '' ? $this->params['sclass'] : @$oarr['setting'][self::CHIDS]);
        
		trbasic("*���������ĵ�ģ��",
			'',"<select onchange=\"setIdWithS(this);document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\" id=\"mselect_mtagnew[setting][".self::CHIDS."]\" style=\"vertical-align: middle;\">" . makeoption($config, $sclasses) . "</select><input type=\"text\" value=\"". $sclasses ."\" name=\"mtagnew[setting][".self::CHIDS."]\" id=\"mtagnew[setting][".self::CHIDS."]\" class=\"w55\" onblur=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"/>",'');
    }
    
    /**
     * ��ȡ��ʶ�������sclass ID
     * 
     * @param array $setting ��ǰ��ʶ��������
     */ 
    public function getSclass(array $setting)
    {
        return @$setting[self::CHIDS];
    }
}