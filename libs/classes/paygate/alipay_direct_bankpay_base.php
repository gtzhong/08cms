<?php
/**
 * ֧������ʱ������������ģ�ͻ���
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Alipay_Direct_BankPay_Base extends _08_M_Alipay_Direct_Base
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
    protected $_poid = 'alipay_direct_bankpay';
    
    /**
     * ֧�����д���
     * 
     * @var string
     */
    protected $_defaultbank;
    
    /**
     * ����֧������
     * 
     * @param   array  $params �������
     * @example _08_factory::getPays('alipay_direct_bankpay')->send(
                    array(
                        # �������ƣ���Ҫ
                        'subject' => 'test', 
                        # ֧������Ҫ
                        'amount' => 10.05,
                        # �ص���ַ����Ҫ
                        'callback' => '֧����ɺ���ʾ��ҳ����ַ',
                        # ��ϵ�����䣬��ѡ
                        'email' => 'test@163.com',
                        # ֧�����д��ţ���Ҫ
                        'defaultbank' => '֧�����д���',
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
        if ( isset($params['defaultbank']) )
        {
            $params['defaultbank'] = preg_replace('/[^\w]/', '', $params['defaultbank']);
        }
        
        if ( empty($params['defaultbank']) )
        {
            cls_message::show('����ѡ��֧�����С�');
        }
        
        $this->_defaultbank = $params['defaultbank'];
        parent::send($params);
    }
    
    /**
     * ���ò���
     * 
     * @param array $parameter Ҫ���õĲ�������
     */
    public function setParameter( array &$parameter )
    {
        $parameter['paymethod']   = 'bankPay';
		$parameter['defaultbank'] = $this->_defaultbank;
    }
}