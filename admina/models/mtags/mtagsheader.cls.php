<?php
/**
 * ��ʶ�����๫��ͷ��
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
 
class cls_mtagsHeader extends cls_modelsHeader
{ 
    public function __construct()
    {
        parent::__construct();
        foreach(array('ttype', 'tname', 'fn', 'types', 'textid', 'caretpos', 'src_type', 'floatwin_id', 'ename') as $v)
        {
            empty($this->params[$v]) || $this->url .= "&$v={$this->params[$v]}";
        }
        
        if(!empty($this->params['tclass']))
        {
            $this->url .= "&tclass={$this->params['tclass']}";
        }
        else if(!empty($this->params['mtagnew']['tclass']))
        {
            $this->url .= "&tclass={$this->params['mtagnew']['tclass']}"; 
        }
    }
    
    public static function showTagTitle( $mtag, $mtagnew )
    {
        global $tname, $iscopy, $tclass, $fn,$cms_abs, $ttype;
		
        @$mtag = _tag_merge((array) $mtag,(array) $mtagnew);
		
        empty($_POST) || $tname = @$mtag['ename'];
		trbasic('��ʶ����','mtagnew[cname]',(isset($mtag['cname']) ? $mtag['cname'] : '').($iscopy ? '_����' : ''),'text', array('validate' => makesubmitstr('mtagnew[cname]',0,0,3,30)));
		trbasic('*��ʶӢ������','mtagnew[ename]',$tname.($iscopy ? '_cp' : ''),'text', array('validate' => makesubmitstr('mtagnew[ename]',1,'tagtype',3,32)));
        $older = empty($iscopy)?(empty($mtag['ename'])?'':$mtag['ename']):'';
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC("ajax=check_mtagename&older={$older}&tag=$ttype&val=%1");
		if($ttype == 'rtag'){//���ϱ�ʶ��û����Ajax��֤�ظ�
			echo _08_HTML::AjaxCheckInput('mtagnew[ename]', $ajaxURL);
		}

        $mtagses = _08_factory::getMtagsInstance($tclass);
        if ( is_object($mtagses) )
        {
            $mtagses->showCotypesSelect($mtag);
            # ����Ǳ༭ѡ��ʱ�ö���sclass
            if( !empty($fn) && empty($_POST) )
            {
                trhidden('_sclass', @$mtagses->getSclass((array)$mtag['setting']));
            }
        }
        return $mtag;
    }
}

