<?php
/**
 * ��Ա���ͱ�ʶ������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_mtags_members extends cls_mtagsHeader
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
        global $mchannels;
    	$sclass = (isset($this->params['sclass']) ? $this->params['sclass'] : @$oarr['setting'][self::CHIDS]);
        $config = array();
    	foreach($mchannels as $k=>$v) 
        {
            $config[$k] = "($k)$v[cname]";
        }
        $chsourcearr = array('0' => '����ģ��','1' => '����ģ��','2' => '�ֶ�ָ��',);
//        trbasic(
//            '*�������»�Աģ��','mtagnew[setting]['.self::CHIDS.']',
//            makeoption($config, $sclass), 
//            'select', 
//            array('validate' => "onchange=\"document.forms[0].action='{$this->url}&sclass=' + this.value;document.forms[0].submit();\"")
//        );
        sourcemodule('��Աģ������',
			'mtagnew[setting]['.self::CHSOURCE.']',
			$chsourcearr,
			empty($oarr['setting'][self::CHSOURCE]) ? '' : $oarr['setting'][self::CHSOURCE],
			'2',
			'mtagnew[setting]['.self::CHIDS.'][]',
			cls_mchannel::mchidsarr(),
			!empty($oarr['setting'][self::CHIDS]) ? explode(',',$oarr['setting'][self::CHIDS]) : array()
		);
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