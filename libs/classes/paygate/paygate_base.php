<?php
/**
 * ֧������ģ�ͻ���
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
abstract class _08_M_PayGate_Base extends _08_Models_Base
{
    /**
     * ���ѽ��
     * 
     * @var double
     */
    protected $_total_fee = 0.00;
    
    /**
     * ������
     * 
     * @var string
     */
    protected $_out_trade_no = '';
    
    /**
     * ��������
     * 
     * @var string
     */
    protected $_subject = '';
    
    /**
     * ֧���ʻ�
     * 
     * @var string
     */
    protected $_seller_email = '';
    
    /**
     * ��������
     * 
     * @var array
     */
    protected $_configs = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->setConfigs();
    }
    
    /**
     * ��ȡ������Ϣ
     * 
     * @return array ����������Ϣ
     */
    public function getConfigs()
    {
        return $this->_configs;
    }
    
    /**
     * ���ûص���ַ
     * 
     * @parma string $url �ص���ַ
     */
    public function setCallBack( $url )
    {
        msetcookie('pays_callback', $url, 0, true);
    }
    
    /**
     * ��ʾ֧������״̬��ͬ��״̬��
     * 
     * @param string $status ״̬��Ϣ
     */
    public function showPaysStatus($status)
    {
        $mcookie = cls_env::_COOKIE();
        $jumpurl = empty($mcookie['pays_callback']) ? '' : $mcookie['pays_callback'];
        
        if( (string) $status == 'success' )
        {
        	$message = "֧����ɣ�";
        }
        else
        {
        	$message =  "֧��ʧ�ܣ�";
        }
        
        cls_message::show($message, $jumpurl);
    }
    
    /**
     * ���֧����¼
     * 
     * @param  array $parms Ҫ��ӵ�����
     * @return bool         �������״̬
     */
    public function addPays( array $params )
    {
        $params['mid'] = empty($this->_curuser->info['mid']) ? 0 : $this->_curuser->info['mid'];
        $params['mname'] = empty($this->_curuser->info['mname']) ? '�ο�' : $this->_curuser->info['mname'];
        $params['senddate'] = TIMESTAMP;
        $params['pmode'] = 1;
        $params['ip'] = cls_env::OnlineIP();
		$params['model'] = isset($params['model']) ? $params['model']  : '';
        return _08_factory::getInstance(_08_Loader::MODEL_PREFIX . 'PayGate_Pays',$params['model'])->add($params);
    }
    
    /**
     * ����ǰ�û����ӻ���
     * 
     * @param double $currency ��������
     * @param int    $mid      Ҫ��ֵ�Ļ�ԱID�������������Ϊ��ǰ����Ļ�ԱID
     **/
    public function addCurrency($currency, $mid = null)
    {
        if (empty($this->_mconfigs['onlineautosaving']))
        {
            return false;
        }
        
        # ��ʼ��ֵ����
        $currency = doubleval($currency);
        if (is_null($mid))
        {
            $mid = $this->_curuser->info['mid'];
            $this->_curuser->updatecrids(array(0 => $currency), 1, '����֧��');
        }
        else
        {
        	$mid = (int) $mid;
			$user = new cls_userinfo();
			$user->activeuser($mid);
			$user->updatecrids(array(0 => $currency), 1, '����֧��');
        }
        
        return true;
    }
    
    /**
     * ��ȡ������
     */
    public function getOrderSN()
    {
        return date("YmdHis") . cls_string::Random(6, 1);
    }
    
    abstract public function setConfigs();
    
    abstract public function send( array $params );
}