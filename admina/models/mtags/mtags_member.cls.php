<?php
/**
 * ��Ա���ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_member extends cls_mtags_members
{    
    /**
     * ��ʾ��ʶ���������Ŀselectѡ��
     * 
     * @param array $oarr ��ǰ��ʶ��������
     */ 
    public function showCotypesSelect(array $oarr)
    {
        global $mchannels;
    	$sclass = (isset($this->params['sclass']) ? $this->params['sclass'] : @$oarr['setting'][parent::CHIDS]);
        $config = array('0' => '����ģ��', '');
    	foreach($mchannels as $k=>$v)
        {
            $config[$k] = "($k)$v[cname]";
        }
        trbasic(
            '�������»�Աģ��','mtagnew[setting]['.parent::CHIDS.']',
            makeoption($config, $sclass), 
            'select', 
            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"", 'guide' => '�ù�����Կ�ʼ���� "����ԭʼ��ʶ" ��ѡ�λ')
        );
    }
}