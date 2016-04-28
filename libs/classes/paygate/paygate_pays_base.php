<?php
/**
 * ֧��ģ�ͻ���
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_PayGate_Pays_Base extends _08_Models_Base
{
    protected $_pays = null;
    
    /**
     * ���һ��֧����¼
     * 
     * @param array $recordInfo ��¼��Ϣ
     */
    public function add( array $recordInfo )
    {
        $recordInfo = $this->_filterFields($recordInfo);
        
        # �ѳ�������ԱIDҲ��Ϊϵͳ�ʺŴ���
        if ( isset($recordInfo['to_mid']) && ($recordInfo['to_mid'] == 1) )
        {
            $recordInfo['to_mid'] = 0;
        }
        return $this->_pays->create($recordInfo);
    }
    
    /**
     * ���˲����ڱ�����ֶ�
     * 
     * @return array ���ع��˺���ֶ�ֵ
     */
    protected function _filterFields( array $fields )
    {
        $newFileds = array();
        foreach ( $fields as $field => $value ) 
        {
            if ( in_array($field, array('mid', 'pmode', 'senddate', 'receivedate', 'transdate', 'to_mid', 'status')) )
            {
                $newFileds[$field] = intval($value);
            }
            else if ( in_array($field, array('mname', 'ordersn', 'poid', 'ip', 'truename', 'telephone', 'email',
                                             'remark', 'warrant')) )
            {
                $newFileds[$field] = trim($value);
            }
            else if ( in_array($field, array('amount', 'handfee')) )
            {
                $newFileds[$field] = doubleval($value);
            }
        }
        
        return $newFileds;
    }
    
    /**
     * ��ȡһ��֧����Ϣ
     * 
     * @param  string $fields Ҫ��ȡ���ֶ�
     * @param  array  $where  ɸѡ����
     * @return array          ���ض�ȡ����֧����Ϣ
     */
    public function read($fields = '*', $where = array())
    {
        if ( $where )
        {
            $this->_pays->where($where);
        }
        
        return $this->_pays->read($fields);
    }
    
    /**
     * ���¶���״̬
     * 
     * @param  int  $statusCode ״̬��
     * @return bool             ������³ɹ�����TRUE�����򷵻�FALSE
     */
    public function setStatus($statusCode, $where = array())
    {
        if ( $where )
        {
            $this->_pays->where($where);
        }
        
        return (bool) $this->_pays->update(array('status' => (int) $statusCode, 'receivedate' => TIMESTAMP, 'receivedate' => TIMESTAMP));  
    }
    
    /**
     * ��ȡ���е�֧�����ؽӿ���Ϣ
     * 
     * @return array �������е�֧�����ؽӿ���Ϣ
     */
    public function getPays()
    {
		$pays = $payarr = array();
		for($i = 0; $i < 32; $i++)if(@$this->_mconfigs['cfg_paymode'] & (1 << $i))$payarr[] = $i;
        if ( _08_Browser::getInstance()->isMobile() && in_array(5, $payarr) )
        {
            $pays = array('alipay_direct_wap' => '֧�����ֻ���ʱ����');
        }
        else
        {
            foreach ( $payarr as $value ) 
            {
                if ( $value == 2 )
                {
                    $pays['alipay_direct'] = '֧������ʱ����';
                }
                elseif ( $value == 3 )
                {
                    $pays['tenpay'] = '�Ƹ�ͨ��ʱ����';
                }                
                elseif ( $value == 4 )
                {
                    $pays['alipay_direct_bankpay'] = '֧��������֧��';
                }
            }
            
            if (defined('M_ADMIN'))
            {
                $pays['alipay_direct_wap'] = '֧�����ֻ���ʱ����';
            }
        }
        
        return $pays;
    }
    
    public function __construct($model = '')
    {
        parent::__construct();
        $this->_pays = parent::getModels(empty($model) ? 'Pays_Table' : $model);
    }
}