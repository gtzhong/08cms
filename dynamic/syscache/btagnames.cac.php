<?php
/*
���������µ���������bclass���ó�other��sclass���ó����¼������ɣ�
'mp' => '��ҳ',
'attachment' => '����',
'vote' => 'ͶƱ',

****** ��ʶ���� ***************
    	'common' => 'ͨ����Ϣ',
    	'archives' => '�ĵ����',
    	'catalogs' => '��Ŀ���',
    	'farchives' => '�������',
        'pushs' => '�������',
    	'commus' => '�������',
    	'members' => '��Ա���',
    	'others' => '����',
********************************


****** �������� ***************
    	'text' => '�����ı�',
    	'multitext' => '�����ı�',
    	'htmltext' => 'Html�ı�',
    	'image' => '��ͼ',
    	'images' => 'ͼ��',
    	'flash' => 'Flash',
    	'flashs' => 'Flash��',
    	'media' => '��Ƶ',
    	'medias' => '��Ƶ��',
    	'file' => '��������',
    	'files' => '�������',
    	'select' => '����ѡ��',
    	'mselect' => '����ѡ��',
    	'cacc' => '��Ŀѡ��',
    	'date' => '����(ʱ���)',
    	'int' => '����',
    	'float' => 'С��',
    	'map' => '��ͼ',
    	'vote' => 'ͶƱ',
    	'texts' => '�ı���',
********************************


****** �����ʽ ***************
	public static $aidsdd = array (
		'ename' => 'aidsdd',//��ʶӢ������
		'cname' => '�ĵ�IDssssssssssssssss',//��ʶ��������
		'bclass' => 'archives',//��ʶ����
		'sclass' => 40,//��ʶ�ӷ���ID
		'datatype' => 'int',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 0,//�Ƿ�������
	);
********************************
*/
class cac_btagnames
{
/******** ͨ����Ϣ **********/
	public static $hostname = array (
		'ename' => 'hostname',
		'cname' => 'վ������',
		'bclass' => 'common',
		'sclass' => '',
		'datatype' => 'text',
		# �Ƿ�Ϊ��ǰ�ӷ����¹���
		'iscommon' => 1,
		# �Ƿ�������
		'maintable' => 1
	);
	
	public static $hosturl = array (
	  'ename' => 'hosturl',
	  'cname' => 'վ������',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cmsname = array (
	  'ename' => 'cmsname',
	  'cname' => 'վ������',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cms_abs = array (
	  'ename' => 'cms_abs',
	  'cname' => 'վ����ҳURL',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $memberurl = array (
	  'ename' => 'memberurl',
	  'cname' => '��ԱƵ����ҳ',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $mspaceurl = array (
	  'ename' => 'mspaceurl',
	  'cname' => '���˿ռ���ҳ',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cmsindex = array (
	  'ename' => 'cmsindex',
	  'cname' => 'վ����ҳ',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cmstitle = array (
	  'ename' => 'cmstitle',
	  'cname' => 'վ�����',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	public static $cmskeyword = array (
	  'ename' => 'cmskeyword',
	  'cname' => 'վ��ؼ���',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cmsdescription = array (
	  'ename' => 'cmsdescription',
	  'cname' => 'վ������',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $tplurl = array (
	  'ename' => 'tplurl',
	  'cname' => 'ģ��λ��URL',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $mcharset = array (
	  'ename' => 'mcharset',
	  'cname' => 'վ��ҳ�����',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cms_version = array (
	  'ename' => 'cms_version',
	  'cname' => 'cms�汾���',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cmslogo = array (
	  'ename' => 'cmslogo',
	  'cname' => 'վ��LOGO',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'image',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $copyright = array (
	  'ename' => 'copyright',
	  'cname' => 'վ���Ȩ��Ϣ',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $cms_icpno = array (
	  'ename' => 'cms_icpno',
	  'cname' => 'վ�㱸����Ϣ',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $bazscert = array (
	  'ename' => 'bazscert',
	  'cname' => '����֤��',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'text',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	
	public static $timestamp = array (
	  'ename' => 'timestamp',
	  'cname' => '��ǰϵͳʱ���',
	  'bclass' => 'common',
	  'sclass' => '',
	  'datatype' => 'int',
	  # �Ƿ�Ϊ��ǰ�ӷ����¹���
	  'iscommon' => 1,
	  # �Ƿ�������
	  'maintable' => 1
	);
	public static $cms_statcode = array (
		'ename' => 'cms_statcode',//��ʶӢ������
		'cname' => '������ͳ�ƴ���',//��ʶ��������
		'bclass' => 'common',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $sn_row = array (
		'ename' => 'sn_row',//��ʶӢ������
		'cname' => '�б��е��б��',//��ʶ��������
		'bclass' => 'common',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $arcurl = array (
		'ename' => 'arcurl',//��ʶӢ������
		'cname' => '����ҳ_URL',//��ʶ��������
		'bclass' => 'archives',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $arcurl1 = array (
		'ename' => 'arcurl1',//��ʶӢ������
		'cname' => '����ҳ1_URL',//��ʶ��������
		'bclass' => 'archives',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $marcurl = array (
		'ename' => 'marcurl',//��ʶӢ������
		'cname' => '�ĵ��Ŀռ�����ҳ',//��ʶ��������
		'bclass' => 'archives',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mpnav = array (
		'ename' => 'mpnav',//��ʶӢ������
		'cname' => '��ҳ����',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mptitle = array (
		'ename' => 'mptitle',//��ʶӢ������
		'cname' => '(�ı�)��ҳ����',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mppage = array (
		'ename' => 'mppage',//��ʶӢ������
		'cname' => '��ҳ��ǰҳ',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mpcount = array (
		'ename' => 'mpcount',//��ʶӢ������
		'cname' => '��ҳ��ҳ��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mpstart = array (
		'ename' => 'mpstart',//��ʶӢ������
		'cname' => '��ҳ��ҳURL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mpend = array (
		'ename' => 'mpend',//��ʶӢ������
		'cname' => '��ҳβҳURL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mppre = array (
		'ename' => 'mppre',//��ʶӢ������
		'cname' => '��ҳ��ҳURL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mpnext = array (
		'ename' => 'mpnext',//��ʶӢ������
		'cname' => '��ҳ��ҳURL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mpacount = array (
		'ename' => 'mpacount',//��ʶӢ������
		'cname' => '��ҳ�ܼ�¼��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mp',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $url = array (
		'ename' => 'url',//��ʶӢ������
		'cname' => '����URL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'attachment',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $title = array (
		'ename' => 'title',//��ʶӢ������
		'cname' => '����˵��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'attachment',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $url_s = array (
		'ename' => 'url_s',//��ʶӢ������
		'cname' => 'ͼƬ����ͼURL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'attachment',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $width = array (
		'ename' => 'width',//��ʶӢ������
		'cname' => 'ͼƬ���',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'attachment',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $height = array (
		'ename' => 'height',//��ʶӢ������
		'cname' => 'ͼƬ�߶�',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'attachment',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
    	public static $link = array (
		'ename' => 'link',//��ʶӢ������
		'cname' => 'ͼƬ����2',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'attachment',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $vid = array (
		'ename' => 'vid',//��ʶӢ������
		'cname' => 'ͶƱ��ĿID',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $caid = array (
		'ename' => 'caid',//��ʶӢ������
		'cname' => 'ͶƱ����ID',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $subject = array (
		'ename' => 'subject',//��ʶӢ������
		'cname' => 'ͶƱ����',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $content = array (
		'ename' => 'content',//��ʶӢ������
		'cname' => 'ͶƱ˵��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $totalnum = array (
		'ename' => 'totalnum',//��ʶӢ������
		'cname' => '��Ʊ��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mid = array (
		'ename' => 'mid',//��ʶӢ������
		'cname' => '�����˻�ԱID',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mname = array (
		'ename' => 'mname',//��ʶӢ������
		'cname' => '�����˻�Ա',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $createdate = array (
		'ename' => 'createdate',//��ʶӢ������
		'cname' => 'ͶƱ���ʱ��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $vopid = array (
		'ename' => 'vopid',//��ʶӢ������
		'cname' => 'ͶƱѡ��ID',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $title_1 = array (
		'ename' => 'title',//��ʶӢ������
		'cname' => 'ͶƱѡ�����',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $votenum = array (
		'ename' => 'votenum',//��ʶӢ������
		'cname' => 'ͶƱѡ��Ʊ��',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $input = array (
		'ename' => 'input',//��ʶӢ������
		'cname' => 'ͶƱѡ��ؼ�',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $percent = array (
		'ename' => 'percent',//��ʶӢ������
		'cname' => 'ͶƱѡ��ٷֱ�',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'vote',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $indexurl = array (
		'ename' => 'indexurl',//��ʶӢ������
		'cname' => '��Ŀ�ڵ�_URL',//��ʶ��������
		'bclass' => 'catalogs',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $indexurl1 = array (
		'ename' => 'indexurl1',//��ʶӢ������
		'cname' => '�ڵ㸽��ҳ1_URL',//��ʶ��������
		'bclass' => 'catalogs',//��ʶ����
		'sclass' => '',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 1,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $word = array (
		'ename' => 'word',//��ʶӢ������
		'cname' => '�ؼ���',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'keywords',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $wordlink = array (
		'ename' => 'wordlink',//��ʶӢ������
		'cname' => '�ؼ��ʹ�����URL',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'keywords',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $mcaid = array (
		'ename' => 'mcaid',//��ʶӢ������
		'cname' => '�ռ���Ŀid',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mcatalogs',//��ʶ�ӷ���ID
		'datatype' => 'int',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $title_mc = array (
		'ename' => 'title',//��ʶӢ������
		'cname' => '�ռ���Ŀ����',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'mcatalogs',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $title0 = array (
		'ename' => 'title0',//��ʶӢ������
		'cname' => '��һ��ѡ������',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'texts',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $title1 = array (
		'ename' => 'title1',//��ʶӢ������
		'cname' => '�ڶ���ѡ������',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'texts',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	public static $title2 = array (
		'ename' => 'title2',//��ʶӢ������
		'cname' => '������ѡ������',//��ʶ��������
		'bclass' => 'others',//��ʶ����
		'sclass' => 'texts',//��ʶ�ӷ���ID
		'datatype' => 'text',//��������
		'iscommon' => 0,//�Ƿ�Ϊ��ǰ�ӷ����¹���
		'maintable' => 1,//�Ƿ�������
	);
	
}