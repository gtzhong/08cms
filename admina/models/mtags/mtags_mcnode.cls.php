<?php
/**
 * ��Ա�ڵ�
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_mcnode extends cls_mtags_mccatalogs 
{    
    const SCLASS_VAL = 'cnsource';
    
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
        $config['mcnid'] = '�Զ���ڵ�';
        
        trbasic(
            '*�б�չʾ��ϵ','mtagnew[setting]['.self::SCLASS_VAL.']',
            makeoption($config, $sclass), 
            'select', 
            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"")
        );
    }
    
}