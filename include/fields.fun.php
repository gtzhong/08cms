<?php
!defined('M_COM') && exit('No Permission');
empty($sysparams) && $sysparams = cls_cache::cacRead('sysparams');
$datatypearr = array(
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
);
$limitarr = array(
	'' => '���޸�ʽ',
	'int' => '����',
	'number' => '����',
	'letter' => '��ĸ',
	'numberletter' => '��ĸ������',
	'tagtype' => '��ĸ��ʼ����ĸ�����»���',
	'date' => '����',
	'email' => 'E-mail',
);
$rpidsarr = array('0' => '������Զ�̸���');
$rprojects = cls_cache::Read('rprojects');
foreach($rprojects as $k => $v) $rpidsarr[$k] = $v['cname'];
$wmidsarr = array('0' => 'ͼƬ����ˮӡ');
$watermarks = cls_cache::Read('watermarks');
foreach($watermarks as $k => $v) $v['Available'] && $wmidsarr[$k] = $v['cname'];