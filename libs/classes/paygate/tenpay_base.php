<?php
/**
 * �Ƹ�ͨ��ʱ��������ģ�ͻ���
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
if ( !defined('TENPAY_PATH') )
{
    define('TENPAY_PATH', _08_OUTSIDE_PATH . 'tenpay' . DIRECTORY_SEPARATOR);
}
class _08_M_Tenpay_Base extends _08_M_PayGate_Base
{
    /**
     * SDK�汾
     */
    const SDK_VERSION = 'v1.0.1';
    
    /**
     * �ӿ�����
     * 
     * @var string
     */
    protected $_poid = 'tenpay';
    
    /**
     * ������ɳ�������У���ʽ����������Ϊfalse
     * 
     * @var bool
     */
    private $isInSandBox = false;
    
    /**
     * ����֧������
     * 
     * @param   array  $params �������
     * @example _08_factory::getPays('tenpay')->send(
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
        $this->setCallBack($params['callback']);
        
        if ( isset($params['to_mid']) )
        {
            $this->setConfigs($params['to_mid']);
        }
        
        _08_Loader::import(TENPAY_PATH . 'PayRequest.class');
                
        /* ����֧��������� */
        $reqHandler = new PayRequest($this->_configs['key']);
        
        // ������ɳ�������У���ʽ����������Ϊfalse
        $reqHandler->setInSandBox($this->isInSandBox());
        
        // ���òƸ�ͨappid: �Ƹ�ͨappע��ʱ���ɲƸ�ͨ����
        $reqHandler->setAppid($this->_configs['appid']);
        
        // �����̻�ϵͳ�����ţ��Ƹ�ͨAPPϵͳ�ڲ��Ķ�����,32���ַ��ڡ��ɰ�����ĸ,ȷ���ڲƸ�ͨAPPϵͳΨһ
        $reqHandler->setParameter("out_trade_no", $params['ordersn']);  
        
        // ���ö����ܽ���λΪ��
        $reqHandler->setParameter("total_fee", doubleval($params['amount']) * 100);
        
        // ����֪ͨurl�����ղƸ�ͨ��̨֪ͨ��URL���û��ڲƸ�ͨ���֧���󣬲Ƹ�ͨ��ص���URL����Ƹ�ͨAPP����֧�������
        // ��URL���ܻᱻ��λص�������ȷ��������ҵ���߼�����δ������������·�������磺http://wap.isv.com/notify.asp
        $reqHandler->setParameter("notify_url", $this->_configs['notify_url']);				
        
        // ���÷���url���û����֧������ת��URL���Ƹ�ͨAPPӦ�ڴ�ҳ���ϸ�����ʾ��Ϣ�������û����֧����Ĳ�����
        // �Ƹ�ͨAPP��Ӧ�ڴ�ҳ������������ҵ������������û�����ˢ��ҳ�浼�¶�δ���ҵ���߼���ɲ���Ҫ����ʧ��
        // �������·�������磺http://wap.isv.com/after_pay.asp��ͨ����·��ֱ�ӽ�֧�������Get�ķ�ʽ����
        $reqHandler->setParameter("return_url", $this->_configs['return_url'] . _08_Http_Request::uri2MVC("out_trade_no={$params['ordersn']}", false) . '/');
        
        // ������Ʒ����:��Ʒ����������ʾ�ڲƸ�֧ͨ��ҳ����
        $reqHandler->setParameter("body", $params['subject']);	            
        
        // �����û��ͻ���ip:�û�IP��ָ�û��������IP�����ǲƸ�ͨAPP������IP
        $reqHandler->setParameter("spbill_create_ip", cls_env::OnlineIP());
        // **********************end*************************
        
        //֧�������URL
        $reqUrl = $reqHandler->getURL();
        #exit(urldecode($reqUrl));
        @ob_end_clean();
        header('Location:' . $reqUrl);
        exit;
    }
    
    /**
     * �������ò���
     * ע����ʹ�ö�����տ�ʱ����Ҫ��֤ͬʱ���ݱ�������������������Ǯ�п��ܻ���뵽ϵͳ�������
     * 
     * @param string $appid ���òƸ�ͨApp-id: �Ƹ�ͨAppע��ʱ���ɲƸ�ͨ���䣬����������Դ���Ϊ��׼��������ʱĬ��ʹ��ϵͳ����
     * @param string $key   ǩ����Կ: ������ע��ʱ���ɲƸ�ͨ���䣬������ʱĬ��ʹ��ϵͳ����
     */
    public function setConfigs( $mid = 0 )
    {
        $memberInfo = cls_UserMain::CurUser()->getPaysInfo($mid, 'tenpay');
        $this->_configs = array();
            
        //���òƸ�ͨApp-id: �Ƹ�ͨAppע��ʱ���ɲƸ�ͨ����
        $this->_configs['appid'] = $memberInfo['tenpay_seller_account'];
        
        //ǩ����Կ: ������ע��ʱ���ɲƸ�ͨ����
        $this->_configs['key'] = $memberInfo['tenpay_partnerkey'];
        
        // ����֪ͨurl�����ղƸ�ͨ��̨֪ͨ��URL���û��ڲƸ�ͨ���֧���󣬲Ƹ�ͨ��ص���URL����Ƹ�ͨAPP����֧�������
        // ��URL���ܻᱻ��λص�������ȷ��������ҵ���߼�����δ������������·�������磺http://wap.isv.com/notify.asp
        $this->_configs['notify_url'] = cls_url::create('paygate/tenpay_notify_url');
        
        // ���÷���url���û����֧������ת��URL���Ƹ�ͨAPPӦ�ڴ�ҳ���ϸ�����ʾ��Ϣ�������û����֧����Ĳ�����
        // �Ƹ�ͨAPP��Ӧ�ڴ�ҳ������������ҵ������������û�����ˢ��ҳ�浼�¶�δ���ҵ���߼���ɲ���Ҫ����ʧ��
        // �������·�������磺http://wap.isv.com/after_pay.asp��ͨ����·��ֱ�ӽ�֧�������Get�ķ�ʽ����
        $this->_configs['return_url'] = cls_url::create('paygate/tenpay_return_url');
    }
    
    /**
     * �Ƿ�ɳ��״̬
     * 
     * @return bool TRUEΪ�ǣ�FALSEΪ��
     */
    public function isInSandBox()
    {
        return (bool) $this->isInSandBox;
    }
}