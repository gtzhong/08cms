<?php
/**
 * WindID��ʾ��Ϣ��
 *
 * @package    PHPWIND
 * @subpackage WindID
 * @author     Wilson <Wilsonnet@163.com>
 * @copyright  Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
 
defined('PW_EXEC') || exit('No Permission');
class cls_Windid_Message
{
    /**
     * Ҫ����PW�û�COOKIE������
     * ��COOKIEֻ��֤ͬ�û�����ͬIDʱ��ĵ�¼���˳����������ʹ�ø�ƽ̨���ǰ��֤���û�����IDһ������COOKIE���Ǳ���
     * 
     * @var string
     */
	const PW_UID_COOKIE = 'pw_uid';
	
    /**
     * Ҫ����PW�û�COOKIE��ʱ��
     * 
     * @var int
     */
	const PW_UID_COOKIE_TIME = 31536000;
    
    /**
     * WINDID������ڱ�ϵͳ�����ݱ�����û��ֶα�ʶ��
     * 
     * @var string
     */
    const PW_UID = 'pw_uid';
    
    /**
     * WINDID������ڱ�ϵͳ�����ݱ����˽��ID�ֶα�ʶ��
     */
    const PW_MESSAGE_ID = 'pw_message_id';
    
    /**
     * ����״̬�뷵��״̬��Ϣ
     * 
     * @param  int    $code ״̬��
     * @return string       ״̬��Ϣ
     * @since  1.0 
     */
    public static function get($code)
    {
        $msg = array(
            '1'=>'�����ɹ�',
            '0'=>'����ʧ��',
            
            '-1'=>'�û���Ϊ��',
            '-2'=>'�û������ȴ���',
            '-3'=>'�û������зǷ��ַ�',
            '-4'=>'�û������н����ַ�',
            '-5'=>'�û����Ѿ�����',
            '-6'=>'����Ϊ��',
            '-7'=>'�Ƿ������ַ',
            '-8'=>'���䲻�ڰ�������',
            '-9'=>'�����ں�������',
            '-10'=>'�����ַ�ѱ�ע��',
            '-11'=>'���볤�ȴ���',
            '-12'=>'���뺬�зǷ��ַ�',
            '-13'=>'ԭ�������',
            '-14'=>'�ʺŲ�����',
            '-20'=>'������������벻һ��',
                    
            '-30'=>'˽�ų��ȴ���',
                
            '-40'=>'ѧУΪ��',
            '-42'=>'ѧУ����Ϊ��',
            '-42'=>'ѧУ����Ϊ��',
          
            '-80'=>'�ϴ�ʧ��',
            '-81'=>'�ϴ����ʹ���',
            '-82'=>'�ϴ��ļ�̫С',
            '-83'=>'�ϴ��ļ�̫��',
            '-84'=>'�ϴ��ļ�����',
        
            '-90'=>'���ӳ�ʱ',
            '-91'=>'��������',
            '-92'=>'��������',
            '-93'=>'����������',      
        );
        
        return (isset($msg[$code]) ? ('[WindID] ' . $msg[$code]) : $code);
    }
}