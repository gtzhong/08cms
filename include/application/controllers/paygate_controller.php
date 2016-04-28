<?php
/**
 * ֧�����ؿ�����
 *
 *  ֧���������������� ������������ǩ���Ƿ�ƥ��
http://192.168.1.28/home/index.php?/paygate/alipay_direct_return_url&buyer_email=13712926549&buyer_id=2088802403051784&exterface=create_direct_pay_by_user&extra_common_param=Pays_Payment_Table%7C12%7C7%7Czhuangxiu%7C6727%7C%C8%FD%B6%E4%BD%F0%BB%AA%7C13456789021%7C%7C&is_success=T&notify_id=RqPnCoPT3K9%252Fvwbh3InTvaXPmweHHzms5oKJF%252FRAaJj%252FDDE9EQlEkckHBw%252FkBZe7lxhp&notify_time=2015-03-25+10%3A57%3A26&notify_type=trade_status_sync&out_trade_no=20150325105417568314&payment_type=1&seller_email=910377558%40qq.com&seller_id=2088311817647422&subject=%B5%D8%B0%E5%D7%A9%CD%C5%B9%BA&total_fee=0.01&trade_no=2015032500001000780050115871&trade_status=TRADE_SUCCESS&sign=8f0580b9ffdac65577eb2769be3313e8&sign_type=MD5
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_C_PayGate_Controller extends _08_Controller_Base
{    
    /**
     * ֧��ģ�Ͷ���
     * 
     * @var object
     */
    protected $_08_M_PayGate_Pays = null;
    
	protected $extra_param = array();
    /**
     * ����״̬map
     * 
     * @var array
     */
    protected $_paysStatusMap = array(
        'PAY_FAIL' => -2,         # ����ʧ��
        'PAY_FINISHED' => -1,     # �������
        'PAY_WAIT_PAY' => 0,      # �ȴ�����
        'PAY_WAIT_GOODS' => 1,    # �Ѹ���ȴ�����
        'PAY_CONFIRM_GOODS' => 2, # �Ѿ�����
        'PAY_CONFIRM_GOODS' => 3  # ȷ���ջ�
    );
    
    public function __construct()
    {
        parent::__construct();
		if(isset($this->_get['extra_common_param'])){
			$this->extra_param = @explode('|',$this->_get['extra_common_param']);
		}
        $this->_08_M_PayGate_Pays = _08_factory::getInstance(_08_Loader::MODEL_PREFIX . 'PayGate_Pays',isset($this->extra_param[0]) && !empty($this->extra_param[0]) ? $this->extra_param[0] : '');
    }
    
    /**
     * ֧������ʱ�����첽֪ͨ����
     * ע���첽֪ͨΪ��֧��״̬�ı�ʱ���͹���������һ�����ڸ��¶���״̬��
     * 
     * @param bool $returnStatus �Ƿ񷵻�״̬��TRUEΪ���أ�FALSEΪ������
     */
    public function alipay_direct_notify_url($returnStatus = false)
    {
        $status = 'fail';
        if ( !empty($this->_get['out_trade_no']) &&
             ($payInfo = $this->_08_M_PayGate_Pays->read('status, to_mid, amount', array('ordersn' => @$this->_get['out_trade_no']))) )
        {
			;
            _08_Loader::import(_08_OUTSIDE_PATH . 'alipay_direct' . DIRECTORY_SEPARATOR . 'lib:alipay_notify.class');
            $paysObject = _08_factory::getPays('alipay_direct');
            $alipay_config = $paysObject->getConfigs();
            if ( !empty($payInfo['to_mid']) )
            {
                $memberInfo = cls_UserMain::CurUser()->getPaysInfo($payInfo['to_mid']);
                $alipay_config['partner'] = $memberInfo['alipay_partnerid'];
                $alipay_config['key'] = $memberInfo['alipay_partnerkey'];
				$alipay_config['seller_email'] = $memberInfo['alipay_seller_account'];

            }
			
            //����ó�֪ͨ��֤���
            $alipayNotify = new AlipayNotify($alipay_config);
            $_POST = $this->_get;
            @$verify_result = $alipayNotify->verifyNotify();
			#var_dump($verify_result); exit();
            if ( $verify_result && isset($this->_get['trade_status']) )
            #if ( isset($this->_get['trade_status']) )  // ����ʱ��
            {
                switch ( $this->_get['trade_status'] )
                {
                    /**
                     * ע�⣺���ֽ���״ֻ̬����������³���
                     * 1����ͨ����ͨ��ʱ���ˣ���Ҹ���ɹ���
                     * 2����ͨ�˸߼���ʱ���ˣ��Ӹñʽ��׳ɹ�ʱ�����𣬹���ǩԼʱ�Ŀ��˿�ʱ�ޣ��磺���������ڿ��˿һ�����ڿ��˿�ȣ���
                     */
                    case 'TRADE_FINISHED':
                        
                    // ע�⣺���ֽ���״ֻ̬��һ������³��֡�����ͨ�˸߼���ʱ���ˣ���Ҹ���ɹ���
                    case 'TRADE_SUCCESS':
                        # �ö���δ�������Զ���ֵ���֣����������ִ���ֻ��ʾ�ɹ�����ֹ�û���ˢ��
                        if (($payInfo['status'] == $this->_paysStatusMap['PAY_WAIT_PAY']) && $this->_08_M_PayGate_Pays->setStatus(
                                $this->_paysStatusMap['PAY_WAIT_GOODS'], array('ordersn' => $this->_get['out_trade_no'])))
                        {
							if(isset($this->extra_param[1]) && !empty($this->extra_param[1])) #����֧�� �ж�cuid��ֵ
							{
								array_shift($this->extra_param);  //�Ƴ���һ��model����
								#payment������������չ����paygateĿ¼�� ��extend_home/libs/classes/paygate/
								if(method_exists($this->_08_M_PayGate_Pays, 'payment'))
									$this->_08_M_PayGate_Pays->payment($this->extra_param[0],$payInfo['amount'],$this->extra_param);
							}else                         #��ֵ����
                            	$paysObject->addCurrency($payInfo['amount']);
                        }
                        
                        $status = 'success';   
                        
                        break;
                }
            }            
        }
        
        if ( $returnStatus )
        {
            return $status;
        }
        
        exit($status);
    }
    
    /**
     * ֧������ʱ����ͬ��֪ͨ����
     */
    public function alipay_direct_return_url()
    {
        $status = $this->alipay_direct_notify_url(true);
        _08_factory::getPays('alipay_direct')->showPaysStatus($status);
    }
    
    /**
     * �ֻ�֧������ʱ�����첽֪ͨ����
     */
    public function alipay_direct_wap_notify_url()
    {
        $status = 'fail';
        if ( !empty($this->_get['out_trade_no']) &&
             ($payInfo = $this->_08_M_PayGate_Pays->read('status, to_mid, amount', array('ordersn' => @$this->_get['out_trade_no']))) )
        {
            _08_Loader::import(_08_OUTSIDE_PATH . 'alipay_direct_wap' . DIRECTORY_SEPARATOR . 'lib:alipay_notify.class');
            $paysObject = _08_factory::getPays('alipay_direct_wap');
            $alipay_config = $paysObject->getConfigs();
            if ( !empty($payInfo['to_mid']) )
            {
                $memberInfo = cls_UserMain::CurUser()->getPaysInfo($payInfo['to_mid']);
                $alipay_config['partner'] = $memberInfo['alipay_partnerid'];
                $alipay_config['key'] = $memberInfo['alipay_partnerkey'];
            }
            
            //����ó�֪ͨ��֤���
            $alipayNotify = new AlipayNotify($alipay_config);
            $_POST = $this->_get;
            @$verify_result = $alipayNotify->verifyNotify();
            
            if ( $verify_result )
            {                
            	$doc = new DOMDocument();	
            	if ($alipay_config['sign_type'] == 'MD5') {
            		$doc->loadXML($_POST['notify_data']);
            	}
            	
            	if ($alipay_config['sign_type'] == '0001') {
            		$doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
            	}
            	
            	if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) )
                {
            		//�̻�������
            		$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
            		//֧�������׺�
            		$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
            		//����״̬
            		$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
                    
                    switch ( $trade_status )
                    {
                        /**
                         * ע�⣺���ֽ���״ֻ̬����������³���
                         * 1����ͨ����ͨ��ʱ���ˣ���Ҹ���ɹ���
                         * 2����ͨ�˸߼���ʱ���ˣ��Ӹñʽ��׳ɹ�ʱ�����𣬹���ǩԼʱ�Ŀ��˿�ʱ�ޣ��磺���������ڿ��˿һ�����ڿ��˿�ȣ���
                         */
                        case 'TRADE_FINISHED':
                            
                        // ע�⣺���ֽ���״ֻ̬��һ������³��֡�����ͨ�˸߼���ʱ���ˣ���Ҹ���ɹ���
                        case 'TRADE_SUCCESS':
                            # �ö���δ�������Զ���ֵ���֣����������ִ���ֻ��ʾ�ɹ�����ֹ�û���ˢ��
                            if (($payInfo['status'] == $this->_paysStatusMap['PAY_WAIT_PAY']) && $this->_08_M_PayGate_Pays->setStatus(
                                    $this->_paysStatusMap['PAY_WAIT_GOODS'], array('ordersn' => $this->_get['out_trade_no'])))
                            {
                                $paysObject->addCurrency($payInfo['amount']);
                            }
                            
                            $status = 'success';                            
                            break;
                    }
            	}
            }
        }
        
        exit($status);
    }
    
    /**
     * �ֻ�֧������ʱ����ͬ��֪ͨ����
     */
    public function alipay_direct_wap_return_url()
    {
        $status = 'fail';
        if ( !empty($this->_get['out_trade_no']) &&
             ($payInfo = $this->_08_M_PayGate_Pays->read('status, to_mid, amount', array('ordersn' => @$this->_get['out_trade_no']))) )
        {
            _08_Loader::import(_08_OUTSIDE_PATH . 'alipay_direct_wap' . DIRECTORY_SEPARATOR . 'lib:alipay_notify.class');
            $paysObject = _08_factory::getPays('alipay_direct_wap');
            $alipay_config = $paysObject->getConfigs();
            if ( !empty($payInfo['to_mid']) )
            {
                $memberInfo = cls_UserMain::CurUser()->getPaysInfo($payInfo['to_mid']);
                $alipay_config['partner'] = $memberInfo['alipay_partnerid'];
                $alipay_config['key'] = $memberInfo['alipay_partnerkey'];
            }
            
            //����ó�֪ͨ��֤���
            $alipayNotify = new AlipayNotify($alipay_config);
            $verify_result = $alipayNotify->verifyReturn();
            
            if ( $verify_result )
            {
            	if (isset($this->_get['result']) && strtolower($this->_get['result']) === 'success')
                {
                    # �ö���δ�������Զ���ֵ���֣����������ִ���ֻ��ʾ�ɹ�����ֹ�û���ˢ��
                    if (($payInfo['status'] == $this->_paysStatusMap['PAY_WAIT_PAY']) && $this->_08_M_PayGate_Pays->setStatus(
                            $this->_paysStatusMap['PAY_WAIT_GOODS'], array('ordersn' => $this->_get['out_trade_no'])))
                    {
                        $paysObject->addCurrency($payInfo['amount']);
                    }
                    
                    $status = 'success';
                }
            }
        }
        
        _08_factory::getPays('alipay_direct_wap')->showPaysStatus($status);
    }
    
    /**
     * �Ƹ�֧ͨ���첽֪ͨ����
     * 
     * @param bool $returnStatus �Ƿ񷵻�״̬��TRUEΪ���أ�FALSEΪ������
     */
    public function tenpay_notify_url($returnStatus = false)
    {
        if ( isset($this->_get['out_trade_no']) )
        {
            $this->_get['out_trade_no'] = preg_replace('/[^\w]/', '', $this->_get['out_trade_no']);
        }
        else
        {
        	$this->_get['out_trade_no'] = '';
        }
        
        if ( !isset($this->_get['sign']) || empty($this->_get['out_trade_no']) ||
             !($payInfo = $this->_08_M_PayGate_Pays->read('status, to_mid, amount', array('ordersn' => $this->_get['out_trade_no']))) )
        {
            die('�����Ƿ���');
        }
        
        $status = 'fail';
        define('TENPAY_PATH', _08_OUTSIDE_PATH . 'tenpay' . DIRECTORY_SEPARATOR);
        $paysObject = _08_factory::getPays('tenpay');
        $config = $paysObject->getConfigs();
        
        if ( !empty($payInfo['to_mid']) )
        {
            $memberInfo = cls_UserMain::CurUser()->getPaysInfo($payInfo['to_mid']);
            $config['appid'] = $memberInfo['partnerid'];
            $config['key'] = $memberInfo['partnerkey'];
        }
        $_GET = $this->_get;
        if ( empty($_GET['input_charset']) )
        {
            $_GET['input_charset'] = @$this->_mcharset;
        }
        _08_Loader::import(TENPAY_PATH . 'PayResponse.class');
        _08_Loader::import(TENPAY_PATH . 'NotifyQueryRequest.class');
        
        /* ����֧��Ӧ����� */
        $resHandler = new PayResponse($config['key']);
        
        //��ȡ֪ͨid:֧�����֪ͨid��֧���ɹ�����֪ͨid��Ҫ��ȡ������ϸ������ô�ID����֪ͨ��֤�ӿڡ�
        $notifyId = $resHandler->getNotifyId();
        
        ob_start();
        // ��֪�Ƹ�֪ͨͨ���ͳɹ����粻�������д���ᵼ�²Ƹ�ͨ��ͣ��֪ͨ�Ƹ�ͨapp������ͣ����òƸ�ͨapp��notify_url����֪ͨ
        $resHandler->acknowledgeSuccess();
        $status = ob_get_clean();
        ob_end_clean();
        
        /* ��ʼ��֪ͨ��֤����:�Ƹ�ͨAPP���յ��Ƹ�ͨ��֧���ɹ�֪ͨ��ͨ���˽ӿڲ�ѯ��������ϸ�������ȷ��֪ͨ�ǴӲƸ�ͨ����ģ�û�б��۸Ĺ��� */
        // ������ɳ��������:��ʽ����������Ϊfalse
        $noqHandler = new NotifyQueryRequest($config['key']);
        
        // ������ɳ�������У���ʽ����������Ϊfalse
        $noqHandler->setInSandBox(_08_factory::getPays('tenpay')->isInSandBox());
        //----------------------------------------
        //��������ҵ��������Ʋο�����ƽ̨sdk�ĵ�-PHP
        //----------------------------------------
        // ���òƸ�ͨApp-id: �Ƹ�ͨAppע��ʱ���ɲƸ�ͨ����
        $noqHandler->setAppid($config['appid']);
        
        // ����֪ͨid:֧�����֪ͨid��֧���ɹ�����֪ͨid��Ҫ��ȡ������ϸ������ô�ID����֪ͨ��֤�ӿڡ�
        $noqHandler->setParameter("notify_id", $notifyId);
        // ************************************end*******************************
        
        // �������󣬲���ȡ���ض���
        $Response = $noqHandler->send();
        
        // ********************���·���ҵ��������Ʋο�����ƽ̨sdk�ĵ�-PHP*************************
        if( $Response->isPayed() && ($payInfo['status'] == $this->_paysStatusMap['PAY_WAIT_PAY']) )
        {    
             $flag = $this->_08_M_PayGate_Pays->setStatus( $this->_paysStatusMap['PAY_WAIT_GOODS'], 
                                                           array('ordersn' => $this->_get['out_trade_no']) );
             $paysObject->addCurrency($payInfo['amount']);
             
             // �Ѿ�֧��
             if ( !$flag )
             {
                 $status = 'fail';
             }
        }
        else
        {
        	$status = 'fail';
        }
        
        if ( $returnStatus )
        {
            return $status;
        }
        
        exit($status);
    }
    
    /**
     * �Ƹ�֧ͨ��ͬ��֪ͨ����
     */
    public function tenpay_return_url()
    {
        $status = $this->tenpay_notify_url(true);
        _08_factory::getPays('tenpay')->showPaysStatus($status);        
    }
}