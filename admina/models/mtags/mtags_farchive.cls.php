<?php
/**
 * �������ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_farchive extends cls_mtags_farchives
{
    /**
     * ��ʾ��ʶ���������Ŀselectѡ��
     * 
     * @param array $oarr ��ǰ��ʶ��������
     */ 
    public function showCotypesSelect(array $oarr)
    {
    	$sclass = (isset($this->params['sclass']) ? $this->params['sclass'] : @$oarr['setting'][parent::SCLASS_VAL]);
        $config = cls_fcatalog::fcaidsarr();
        $config[0] = '��ѡ��';
        ksort($config);
        trbasic(
            'ѡ�񸱼�����','mtagnew[setting]['.parent::SCLASS_VAL.']',
            makeoption($config, cls_fcatalog::getNewFcaid($sclass)), 
            'select', 
            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"", 'guide' => '�ù�����Կ�ʼ���� "����ԭʼ��ʶ" ��ѡ�λ')
        );
    }
}