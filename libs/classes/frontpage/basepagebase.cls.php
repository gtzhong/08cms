<?php
/**
 * ����ǰ̨����(ҳ��/ģ�����/ģ���ǩ)���õĻ���
 * �������͵�ǰ̨ҳ��(��Ҫģ����ǩ����)�ľ��̳д˻���
 */
defined('M_COM') || exit('No Permission');
abstract class cls_BasePageBase{
		
	# ���±���Ϊ��̬��������������(ָ��ģ���������ǩ����)���� ************************************
	protected static $db = NULL;						# ���ݿ�����
	protected static $curuser = NULL;					# ��ǰ��Ա
	protected static $tblprefix = '';					# ���ݱ�ǰ׺
	protected static $timestamp = 0;					# ��ǰʱ���
	protected static $cms_abs = '';						# ϵͳ��ȫ����
	
	# ͬһҳ���е�����ҳ�洦��ģ���������ǩ������Ҫ�������±��� *************************
#	protected static $G = array();						# ҳ�湲�ñ�������$G����ʱά��global���Լ���Ŀǰ��ģ��
	protected static $_mp = array();					# ��ҳ���ü��������
	
	
}
