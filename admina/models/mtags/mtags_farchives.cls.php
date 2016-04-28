<?php
/**
 * �������ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_farchives extends cls_mtagsHeader
{    
    const SCLASS_VAL = 'casource';
    
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
    	$sclass = (isset($this->params['sclass']) ? $this->params['sclass'] : @$oarr['setting'][self::SCLASS_VAL]);
        trbasic(
            '*ѡ�񸱼�����','mtagnew[setting]['.self::SCLASS_VAL.']',
            makeoption(cls_fcatalog::fcaidsarr(), cls_fcatalog::getNewFcaid($sclass)), 
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