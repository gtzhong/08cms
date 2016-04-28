<?php
/**
 * ��Ŀ���ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_catalogs extends cls_mtagsHeader
{    
    const SCLASS_VAL = 'listby';
    
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
        global $cotypes;
        // 0=��Ŀ; 1=��ϵ
    	$sclass = (isset($this->params['sclass']) && $this->params['sclass'] != '' ? $this->params['sclass'] : @$oarr['setting'][self::SCLASS_VAL]);
        $config = array('ca' => '(0)��Ŀ');
    	foreach($cotypes as $k=>$v) 
        {
            $v['sortable'] && $config['co' . $k] = "($k)$v[cname]";
        }
        
        trbasic(
            '*�б�չʾ��ϵ','mtagnew[setting]['.self::SCLASS_VAL.']',
            makeoption($config, $sclass), 
            'select', 
            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"")
        );
    }
    
    /**
     * ��ȡ��ʶ�������sclass ID
     * 
     * @param array $setting ��ǰ��ʶ��������
     */ 
    public function getSclass(array $setting)
    {
        return @$setting[self::SCLASS_VAL];
    }
}
