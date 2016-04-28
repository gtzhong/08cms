<?php
/**
 * ģ�͹���ͷ��������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

abstract class cls_modelsHeader
{    
    /**
     * ��ǰҳ�����
     * 
     * @var array
     */ 
    protected $params = array();
    
    /**
     * ��ǰҳ�������ϳɵ�URI
     * 
     * @var string 
     */ 
    protected $url = '';
    
    public function __construct()
    {
        $front = cls_frontController::getInstance();
        $this->params = $front->getParams(); 
    	$this->url .= "?entry={$this->params['entry']}";
    	empty($this->params['action']) || $this->url .= "&action={$this->params['action']}";
    	empty($this->params['infloat']) || $this->url .= "&infloat={$this->params['infloat']}";
    	empty($this->params['handlekey']) || $this->url .= "&handlekey={$this->params['handlekey']}";
    }
}
