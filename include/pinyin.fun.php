<?php
/**
* �й�ƴ���Ĵ�����ʱ�����Լ��ݾɰ汾
*/

!defined('M_COM') && exit('No Permission');

function Pinyin($_String){	
	return cls_string::Pinyin($_String);
}
function py__One($chr, $tab=''){
	return cls_string::py__One($chr, $tab);
}
function FirstLetter($string, $number=0, $first=1){
	return cls_string::FirstLetter($string, $number, $first);
}
?>