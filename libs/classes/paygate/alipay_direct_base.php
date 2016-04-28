<?php
/**
 * ֧������ʱ��������ģ�ͻ���
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
if ( !defined('ALIPAY_DIRECT_PATH') )
{
    define('ALIPAY_DIRECT_PATH', _08_OUTSIDE_PATH . 'alipay_direct' . DIRECTORY_SEPARATOR);
    _08_Loader::import(ALIPAY_DIRECT_PATH . 'lib:alipay_submit.class');
}
class _08_M_Alipay_Direct_Base extends _08_M_PayGate_Base
{
    /**
     * SDK�汾
     */
    const SDK_VERSION = 'v3.3';
    
    /**
     * �ӿ�����
     * 
     * @var string
     */
    protected $_poid = 'alipay_direct';
	
	protected $parameter = array();
	
	protected $extraParameter = '';
    
    /**
     * ����֧������
     * 
     * @param   array  $params �������
     * @example _08_factory::getPays('alipay_direct')->send(
                    array(
						# ����֧��ģ�ͱ� ��Ҫ
						'model' => 'Pays_Table'  Ĭ��ֵ��Pays_Table��ֵ֧���� 
                        # �������ƣ���Ҫ
                        'subject' => 'test', 
                        # ֧������Ҫ
                        'amount' => 10.05,
                        # �ص���ַ����Ҫ
                        'callback' => '֧����ɺ���ʾ��ҳ����ַ',
                        # ��ϵ�����䣬��ѡ
                        'email' => 'test@163.com',
                        # �տ��ԱID����ѡ�������������Ϊϵͳ�ʺţ������������Ǯ����û�Ա��Ӧ���ʺ��ϡ�
                        'to_mid' => 0,
                        # ������������ѡ
                        'remark' => '��������',
                        # �����ѣ���ѡ
                        'handfee' => 0,
                        # ֧�������ƣ���ѡ
                        'truename' => '֧��������',
                        # ֧���˵绰����ѡ
                        'telephone' => '֧���˵绰'
                    )
                );
     */
    public function send( array $params )
    {
        if ( empty($params['subject']) || empty($params['amount']) || empty($params['callback']) )
        {
            cls_message::show('��������');
        }
        
        $params['ordersn'] = $this->getOrderSN();
        $params['poid'] = $this->_poid;
        $this->addPays($params);
        
        if ( isset($params['to_mid']) )
        {
            $memberInfo = cls_UserMain::CurUser()->getPaysInfo($params['to_mid']);
            if ( empty($memberInfo['alipay_partnerid']) || empty($memberInfo['alipay_seller_account']) || empty($memberInfo['alipay_partnerkey']) )
            {
                cls_message::show('�̼�֧�����������ô���');
			}

            $this->setConfigs($params['to_mid']);
        }

        $alipaySubmit = new AlipaySubmit($this->_configs);
        //����Ҫ����Ĳ������飬����Ķ�
        $parameter = array(
    		"service" => "create_direct_pay_by_user",
            
    		"partner" => trim($this->_configs['partner']),
            
            //֧������
    		"payment_type"	=> '1',
            
            //��������޸�
            //�������첽֪ͨҳ��·������http://��ʽ������·�������ܼ�?id=123�����Զ����������ϵͳMVC�ܹ����˴����ǿ��Դ��ݵ�
    		"notify_url"	=> cls_url::create('paygate/alipay_direct_notify_url'),
            
            //ҳ����תͬ��֪ͨҳ��·������http://��ʽ������·�������ܼ�?id=123�����Զ������������д��http://localhost/
    		"return_url"	=> cls_url::create('paygate/alipay_direct_return_url'),
             
            //����֧�����ʻ�
    		"seller_email"	=> $this->_configs['seller_email'],
              
            //�̻�������
    		"out_trade_no"	=> $params['ordersn'],
            
            //��������
    		"subject"	=> $params['subject'],
            
            //������
    		"total_fee"	=> doubleval($params['amount']),
            
            //��������
    		"body"	=> empty($params['remark']) ? '' : (string) $params['remark'],
            
            //��Ʒչʾ��ַ
    		"show_url"	=> $params['callback'],          
            
            //������ʱ�������Ҫʹ����������ļ�submit�е�query_timestamp����
    		"anti_phishing_key"	=> '',  
            
            //�ͻ��˵�IP��ַ    cls_env::OnlineIP()
    		"exter_invoke_ip"	=> '',
			
			//�Զ������
			"extra_common_param" => $this->extraParameter ? $this->extraParameter: '',
            
    		"_input_charset"	=> trim(strtolower($this->_configs['input_charset']))
        );

		$this->parameter = $this->parameter ? array_merge($this->parameter,$parameter) : $parameter;

        $this->setCallBack($params['callback']);
        @ob_end_clean();
        $html_text = $alipaySubmit->buildRequestForm($this->parameter, "get", "����ת��֧��ҳ�棬�����ʱ�䲻��ת��������ת......");
        #$html_text = mhtmlspecialchars($html_text);  
        exit($html_text);
    }
    
    /**
     * �������ò���
     * ע����ʹ�ö�����տ�ʱ����Ҫ��֤ͬʱ���ݱ�������������������Ǯ�п��ܻ���뵽ϵͳ�������
     * 
     * @param int $mid �̼һ�ԱID
     */
    public function setConfigs($mid = 1)
    {
        $memberInfo = cls_UserMain::CurUser()->getPaysInfo($mid);

        $this->_configs = array();
        //���������id����2088��ͷ��16λ������
        $this->_configs['partner'] = $memberInfo['alipay_partnerid'];
        
        //��ȫ�����룬�����ֺ���ĸ��ɵ�32λ�ַ�
        $this->_configs['key'] = $memberInfo['alipay_partnerkey'];
        
        //��ȫ�����룬�����ֺ���ĸ��ɵ�32λ�ַ�
        $this->_configs['seller_email'] = $memberInfo['alipay_seller_account'];
        
        //ǩ����ʽ �����޸�
    	$this->_configs['sign_type'] = 'MD5';
        
        //�ַ������ʽ Ŀǰ֧�� gbk �� utf-8
        $this->_configs['input_charset'] = @$this->_mcharset;
        
        //ca֤��·����ַ������curl��sslУ�飬ע����ʱ������ʹ��֤����֤����Ϊ����û������鷳
        $this->_configs['cacert'] = ALIPAY_DIRECT_PATH . 'cacert.pem';
        
        //����ģʽ,�����Լ��ķ������Ƿ�֧��ssl���ʣ���֧����ѡ��https������֧����ѡ��http
        $this->_configs['transport'] = substr(_08_Browser::getInstance()->getHttpConnection(), 0, -3);
    }
	
	/**
     * �������ò���
     * ע�� ���������˳�� �ȵ���setParameter 
     */
	public function setParameter($params){
		$this->parameter = $params;
	}
	
	
	/**
     * �����Զ������
     * ע�� ���������˳�� �ȵ���setExtraParameter 
     * 
     * @params $model  string ģ���ļ���
	   @params $cuid   int    ����ID
	 * @params $params array  ��������� '|'���� 
     */
	public function setExtraParameter($model,$cuid,$params){
		$model = trim($model);
		$cuid = trim($cuid);
		$params = array_map("trim",$params);
		$this->extraParameter = $model.'|'.$cuid.'|'.implode('|',$params);
	}
}