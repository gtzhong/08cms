<?php

/* #################################################### 
#��Ŀ�˵� --- +����ĸ+����
$cocsmenus = array (
	#coid
	12 => array (
		'label' => '����',
		'first_letter' => '1', //��ʾ����ĸ,��Ҫ��Ч����Ҫ��һ��
		'letter_search' => '1', //����ĸ����,��Ҫ��Ч����Ҫ��һ��
		'items' => array(
			#ccid => aurl id
			12 => '1,2',
			13 => '1,3',
			14 => '2,3',
			15 => '1,2,3',
		),
		'aurls' => array(
			
			// ��ccid����
			1 => array(
				'name' => '��������',
				'link' => '?entry=home#extend&extend=cararchives',
			),
			2 => array(
				'name' => 'Ʒ�����',
				'link' => '?entry=extend&extend=coclass1&action=coclassadd&coid=1',
			),
			3=> array(
				'name' => '����ϲ�',
				'link' => '?entry=extend&extend=carunion&caid=32',
			),
			4=> array(
				'name' => '�ֶ�ά��', //����
				'link' => '?entry=extend&extend=updateautoparams',
			),
			5=> array(
				'name' => '�������',
				'link' => '?entry=autodatas',
			),
			
			6=> array(
				'name' => '����Ŀ����',
				'link' => '?entry=extend&extend=coclass1&action=coclassedit',
			),
			7=> array(
				'name' => '�Ƽ�����',
				'link' => '?entry=extend&extend=coclass1&action=coclass_hot',
			),
			
			// ccid����-Ʒ��
			11 => array(
				'name' => 'Ʒ�Ʊ༭',
				'link' => '?entry=extend&extend=coclass1&action=coclassdetail&coid=1',
			),
			12 => array(
				'name' => '�������',
				'link' => '?entry=extend&extend=coclass1&action=coclassadd&coid=1',
			),

			// ccid����-����
			21 => array(
				'name' => '���̱༭',
				'link' => '?entry=extend&extend=coclass1&action=coclassdetail&coid=1',
			),
			22 => array(
				'name' => '�������',
				'link' => '?entry=extend&extend=coclass1&action=coclassadd&coid=1',
			),
		),
	),
);

//��ʱ�趨����
$arr = cls_cache::Read('coclasses',12);
$char = array();
// 1~5, 11~12, 21~22, 31~38
$level = array(
	0=>'1,11,12', 
	1=>'1,21,22',
	2=>'1,31,32,33,34,35,36,37,38',
);
foreach($arr as $k=>$v){ 
	$items[$k] = $level[$v['level']];
	//������ĸ����,����4�пɲ�Ҫ
	if($v['level']==0){
		$ltter = $v['letter'];
		if(!empty($ltter)) $char[$ltter] = $ltter;
	}
}
$cocsmenus[12]['items'] = array(0=>'1,6,7,2,3,4,5')+$items; // 31,32, ,33,34,35,36,37,38
//print_r($cocsmenus);


//������ĸ����,����4�пɲ�Ҫ
asort($char); // for ��ĸͷ
global $cms_abs; $ccid12_letter = '';
foreach($char as $k=>$v){ 
	$ccid12_letter .= "<option value='$k'>$k</option>";
}
$ccid12_letter = '<script type="text/javascript" x_src="'.$cms_abs.'include/js/ccid12_select2.js"></script>
  <select name="c1s_Letter" id="c12s_Letter" style="width:40px" x_onchange="ccid12_leftMenuScrooll(1)">'.$ccid12_letter.'</select>
  <input type="text" name="c1i_word" id="c1i_word" style="width:40px;border-bottom:1px solid #999;" x_onkeydown="ccid12_leftMenuDown(this,event)" title="���س�ȷ��" />
  <input type="button" name="button" id="button" value=" �� " xonclick="ccid12_leftMenuScrooll(2)" />';
$cocsmenus[12]['search_item'] = "<li id='leftMenuLi_CcidSearch12'>$ccid12_letter</li>"; 
// ע�⣺li id='leftMenuLi_CcidSearch1' һ��Ҫ��[leftMenuLi_CcidSearch]��ͷ��id,��include/js/aframe.js�к���initaMenu(Ul,ck)��һ��
#################################################### */


// ======================================================================


/* #################################################### 
#��Ŀ�˵�
$cocsmenus = array (
	#coid
	2 => array (
		'label' => '��Ʒ����',
		'items' => array(
			#ccid => aurl id
			12 => '1,2',
			13 => '1,3',
			14 => '2,3',
			15 => '1,2,3',
		),
		'aurls' => array(
			#aurl id
			1 => array(
				'name' => '�����Ʒ',
				'link' => '?entry=extend&extend=news_s',
			),
			2 => array(
				'name' => '��Ӳ�Ʒ',
				'link' => '?entry=extend&extend=news_a',
			),
			3 => array(
				'name' => 'ɾ����Ʒ',
				'link' => '?entry=extend&extend=news_d',
			),
		),
	),
);
#################################################### */
