<?php
/**
 * �������ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_pushs extends cls_mtagsHeader
{    
    const SCLASS_VAL = 'paid';
    
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
            '*ָ������λ','mtagnew[setting]['.self::SCLASS_VAL.']',
            umakeoption(self::_u_paidsarr(), $sclass), 
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
    
    /**
     * ��ȡ����λ����
     * 
     * @return array $re ���ػ�ȡ��������λ����
     */ 
    public static function _u_paidsarr()
    {
    	$pushtypes = cls_cache::Read('pushtypes');
		$pushareas = cls_PushArea::Config();
    	$re = array();
    	foreach($pushtypes as $k => $v)
        {
    		$na = array();
    		foreach($pushareas as $x => $y){
    			if($k == $y['ptid']) $na[$x] = array('title' => '&nbsp; &nbsp; '.$y['cname']."($x)");
    		}
    		if($na){
    			$re["-$k"] = array('title' => $v['title'],'unsel' => 1);
    			$re += $na;
    		}
    	}	
    	return $re;
    }
}