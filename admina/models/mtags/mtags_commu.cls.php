<?php
/**
 * �������ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_commu extends cls_mtags_commus 
{
    /**
     * ��ʾ��ʶ���������Ŀselectѡ��
     * 
     * @param array $oarr ��ǰ��ʶ��������
     */ 
    public function showCotypesSelect(array $oarr)
    {
        global $commus;
    	$sclass = (isset($this->params['sclass']) ? $this->params['sclass'] : @$oarr['setting'][parent::SCLASS_VAL]);
        $config = array('�����ý�����Ŀ');
    	foreach($commus as $k=>$v) 
        {
            $config[$k] = "($k)$v[cname]";
        }
        trbasic(
            'ָ��������Ŀ','mtagnew[setting]['.parent::SCLASS_VAL.']',
            makeoption($config, $sclass), 
            'select', 
            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"", 'guide' => '�ù�����Կ�ʼ���� "����ԭʼ��ʶ" ��ѡ�λ')
        );
    }
}