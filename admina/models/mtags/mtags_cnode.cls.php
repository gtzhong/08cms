<?php
/**
 * ��Ŀ�ڵ�
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_cnode extends cls_mtags_catalogs
{
    /**
     * ��ʾ��ʶ���������Ŀselectѡ��
     * 
     * @param array $oarr ��ǰ��ʶ��������
     */ 
    public function showCotypesSelect(array $oarr)
    {
        global $cotypes;
        // 0=��Ŀ; 1=��ϵ
    	$sclass = (isset($this->params['sclass']) && $this->params['sclass'] != '' ? $this->params['sclass'] : @$oarr['setting'][parent::SCLASS_VAL]);
        $config = array('-1' => '��ѡ��', 'ca' => '(0)��Ŀ');
    	foreach($cotypes as $k=>$v) 
        {
            $v['sortable'] && $config['co' . $k] = "($k)$v[cname]";
        }
        ksort($config);
        
        trbasic(
            '�ڵ�չʾ��ϵ','mtagnew[setting]['.parent::SCLASS_VAL.']',
            makeoption($config, $sclass), 
            'select', 
            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"", 'guide' => '�ù�����Կ�ʼ���� "����ԭʼ��ʶ" ��ѡ�λ')
        );
    }
}