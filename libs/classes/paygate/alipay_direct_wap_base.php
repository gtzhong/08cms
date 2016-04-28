<?php
/**
 * ֧�����ֻ���ʱ��������ģ�ͻ���
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
define('ALIPAY_DIRECT_PATH', _08_OUTSIDE_PATH . 'alipay_direct_wap' . DIRECTORY_SEPARATOR);
_08_Loader::import(ALIPAY_DIRECT_PATH . 'lib:alipay_submit.class');
class _08_M_Alipay_Direct_Wap_Base extends _08_M_Alipay_Direct_Base
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
    protected $_poid = 'alipay_direct_wap';
    
    /**
     * ����֧������
     * 
     * @param   array  $params �������
     * @example _08_factory::getPays('alipay_direct_wap')->send(
                    array(
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
            $this->setConfigs($params['to_mid']);
        }
        
        $input_charset = trim(strtolower($this->_configs['input_charset']));
        //����ҵ�������ϸ
        $req_data = '<direct_trade_create_req><notify_url>' . cls_url::create('paygate/alipay_direct_wap_notify_url') . '</notify_url><call_back_url>' . cls_url::create('paygate/alipay_direct_wap_return_url') . '</call_back_url><seller_account_name>' . $this->_configs['seller_email'] . '</seller_account_name><out_trade_no>' . $params['ordersn'] . '</out_trade_no><subject>' . $params['subject'] . '</subject><total_fee>' . doubleval($params['amount']) . '</total_fee><merchant_url>' . $params['callback'] . '</merchant_url></direct_trade_create_req>';
        
        //����Ҫ����Ĳ������飬����Ķ�
        $para_token = array(
        		"service" => "alipay.wap.trade.create.direct",
        		"partner" => trim($this->_configs['partner']),
        		"sec_id" => trim($this->_configs['sign_type']),
        		"format"	=> 'xml',
        		"v"	=> '2.0',
        		"req_id"	=> $params['ordersn'],
        		"req_data"	=> $req_data,
        		"_input_charset"	=> $input_charset
        );
        $para_token = cls_string::iconv($input_charset, 'UTF-8', $para_token);
        //��������
        $alipaySubmit = new AlipaySubmit($this->_configs);
        $html_text = $alipaySubmit->buildRequestHttp($para_token);
        #$html_text = mhtmlspecialchars($html_text);
        
        //URLDECODE���ص���Ϣ
        $html_text = cls_string::iconv('UTF-8', $input_charset, urldecode($html_text));
        //����Զ��ģ���ύ�󷵻ص���Ϣ
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        if ( !empty($para_html_text['res_error']) )
        {
            exit($para_html_text['res_error']);
        }
        
        //��ȡrequest_token
        $request_token = $para_html_text['request_token'];
        
        
        /**************************������Ȩ��token���ý��׽ӿ�alipay.wap.auth.authAndExecute**************************/
        
        //ҵ����ϸ
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        //����
        
        //����Ҫ����Ĳ������飬����Ķ�
        $parameter = array(
        		"service" => "alipay.wap.auth.authAndExecute",
        		"partner" => trim($this->_configs['partner']),
        		"sec_id" => trim($this->_configs['sign_type']),
        		"format"	=> 'xml',
        		"v"	=> '2.0',
        		"req_id"	=> $params['ordersn'],
        		"req_data"	=> $req_data,
        		"_input_charset"	=> $input_charset
        );
        
        //��������
        $alipaySubmit = new AlipaySubmit($this->_configs);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "����ת��֧��ҳ�棬�����ʱ�䲻��ת��������ת......");
        @ob_end_clean(); 
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
        parent::setConfigs($mid);
            
        //�̻���˽Կ����׺��.pen���ļ����·��
        //���ǩ����ʽ����Ϊ��0001��ʱ�������øò���
        $this->_configs['private_key_path']	= 'key/rsa_private_key.pem';
        
        //֧������Կ����׺��.pen���ļ����·��
        //���ǩ����ʽ����Ϊ��0001��ʱ�������øò���
        $this->_configs['ali_public_key_path']= 'key/alipay_public_key.pem';
    }
}