<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	$htmlarr = array(
		'0' => '��������',
		'clearhtml' => '���Html��ǩ',
		'disablehtml' => '����ʾHtml��ǩ',
		'safehtml' => '�����Թ���Html',
#		'html_cleara' => '��ɾ��������',
		'html_decode' => 'HTML������',
		//'html_keepa' => '������������',
	);
	trbasic('* ָ��������Դ','mtagnew[setting][tname]',isset($mtag['setting']['tname']) ? $mtag['setting']['tname'] : '','text',array('guide' => '�����ʽ���ֶ���aa������$a[b]�ȡ�'));
    $tag_string = '';
    foreach (_08_HTML::getDealHtmlTagsMap() as $key => $tag)
    {
        if (isset($mtag['setting']['dealhtml_tags']) && is_string($mtag['setting']['dealhtml_tags']))
        {
            $mtag['setting']['dealhtml_tags'] = explode('|', $mtag['setting']['dealhtml_tags']);
            $mtag['setting']['dealhtml_tags'] = array_fill_keys($mtag['setting']['dealhtml_tags'], array('on'));
        }
        if (!empty($mtag['setting']['dealhtml_tags']) && array_key_exists($key, $mtag['setting']['dealhtml_tags']))
        {
            $checked = ' checked="checked"';
        }
        else
        {
        	$checked = '';
        }
        $tag_string .= ('<li style="width: 160px;display:block; float:left;"><input type="checkbox" id="mtagnew[setting][dealhtml_tags][' . $key . ']" name="mtagnew[setting][dealhtml_tags][' . $key . ']" style="vertical-align: middle;" ' . $checked . '/> <label for="mtagnew[setting][dealhtml_tags][' . $key . ']">' . htmlspecialchars($tag)) . '</label></li>';
    }
    $str_tags = <<<TAG
    <div id="_08_tags_box" style="border:1px #134d9d solid; float:left; padding: 10px; margin-top:10px; background-color:#f1f7fd; display:none">
        <ul>
            {$tag_string}
            <li style="width: 160px;display:block; float:left;"><input type="checkbox" id="checkedAll" name="" style="vertical-align: middle;" /> <label for="checkedAll" style="font-weight: bold; background-color:#134d9d; color:#FFF">ȫ ѡ</label></li>
        </ul>
    </div>
TAG;
	trbasic('����Html����','mtagnew[setting][dealhtml]',makeoption($htmlarr,empty($mtag['setting']['dealhtml']) ? '0' : $mtag['setting']['dealhtml']),'select', array('validate' => 'onchange="selectTags(this);"', 'addstr' => $str_tags));
	trbasic('�ı����ȼ���','mtagnew[setting][trim]',isset($mtag['setting']['trim']) ? $mtag['setting']['trim'] : 0,'text',array('guide' => '�����ֽڳ���,��Ϊ�ջ�0ֵ��ʾ�����ã����İ������ֽ�,utf-8Ҳ������'));
	trbasic('�ı�����ʡ�Է�','mtagnew[setting][ellip]',isset($mtag['setting']['ellip']) ? $mtag['setting']['ellip'] : '','text',array('guide' => '����ı������ã����ϴ��ַ���ʾʡ�ԡ�����Ϊ����'));
	trbasic('��ɫ������Դ','mtagnew[setting][color]',empty($mtag['setting']['color']) ? '' : $mtag['setting']['color'],'text',array('guide' => '�����ʽ���ֶ���aa������$a[b]����#FF6633����ɫֵ������Ϊ��������ɫ'));
	trbasic('���˲�����','mtagnew[setting][badword]',empty($mtag['setting']['badword']) ? '0' : $mtag['setting']['badword'],'radio',array('guide'=>'�滻����˺�̨���õĲ�����(���йؼ���)��'));
	trbasic('�����������','mtagnew[setting][wordlink]',empty($mtag['setting']['wordlink']) ? '0' : $mtag['setting']['wordlink'],'radio',array('guide'=>'���ı��г����˺�̨���õ����Źؼ��ʼ����������ӡ�'));
	trbasic('�����ı�����','mtagnew[setting][nl2br]',empty($mtag['setting']['nl2br']) ? '0' : $mtag['setting']['nl2br'],'radio',array('guide'=>'ֻ���ڶ����ı��ֶδ����Ѷ����ı��е�[�س�]�滻Ϊhtml��&lt;br&gt;��'));
	trbasic('��ӻ����ִ�','mtagnew[setting][randstr]',empty($mtag['setting']['randstr']) ? '0' : $mtag['setting']['randstr'],'radio',array('guide'=>'���ı��Ŀհ׻��д�������ص�������֣����ڷ��ɼ���'));
	trbasic('��js�����ʽ��','mtagnew[setting][injs]',empty($mtag['setting']['injs']) ? '0' : $mtag['setting']['injs'],'radio',array('guide' => '�����ı��еĵ�����\n\r���ַ���ʹ�������js������'));
	tabfooter();
	tabheader('ͼƬ��������');
	trbasic('�ֻ���ͼƬ���','mtagnew[setting][maxwidth]',isset($mtag['setting']['maxwidth']) ? $mtag['setting']['maxwidth'] : '','text',array('guide' => '���ֻ����У�������ô����html�ֶ��е�ͼƬ�����Զ���ü�������Ĭ��640��ȣ���'));
	trbasic('ȥ��ͼƬ�߿�����','mtagnew[setting][noimgwh]',empty($mtag['setting']['noimgwh']) ? '0' : $mtag['setting']['noimgwh'],'radio',array('guide' => '��Ҫ���ֻ����У�������ô��ȥ��ͼƬ��ǩ�е�width/height���ԣ�������ʾ����css���ƣ���'));	
	trbasic('����������','mtagnew[setting][face]',empty($mtag['setting']['face']) ? '0' : $mtag['setting']['face'],'radio',array('guide'=>'ֻ���ڶ����ı��ֶδ������{:face13:}�滻��һ��С����ͼƬ��'));
	tabfooter();
	if(empty($_infragment)){
		tabheader('��ʶ��ҳ����');
		trbasic('�����б��ҳ','mtagnew[setting][mp]',empty($mtag['setting']['mp']) ? 0 : $mtag['setting']['mp'],'radio');
		trbasic('�Ƿ���׵ķ�ҳ����','mtagnew[setting][simple]',empty($mtag['setting']['simple']) ? '0' : $mtag['setting']['simple'],'radio');
		trbasic('��ҳ������ҳ�볤��','mtagnew[setting][length]',isset($mtag['setting']['length']) ? $mtag['setting']['length'] : '');
		tabfooter();
	}
    echo <<<JS
    <script type="text/javascript">
        function selectTags(ele)
        {
            var _08_tags_box_obj = jQuery('#_08_tags_box');
            if (ele.value == 'clearhtml')
            {
                _08_tags_box_obj.show();
            }
            else
            {
                _08_tags_box_obj.hide();
            }
        }
    	
        selectTags(document.getElementById('mtagnew[setting][dealhtml]'));
        jQuery('#checkedAll').click(function(){
            var items = jQuery(this).parent().prevAll().find('input[type=checkbox]');
            items.prop('checked', jQuery(this).is(':checked'));
        })
    </script>
JS;
}else{
	$mtagnew['setting']['tname'] = trim($mtagnew['setting']['tname']);
	if(empty($mtagnew['setting']['tname']) || !preg_match("/^[a-zA-Z_\$][a-zA-Z0-9_\[\]]*$/",$mtagnew['setting']['tname'])){
		mtag_error('������Դ���ò��Ϲ淶');
	}
	$mtagnew['setting']['color'] = trim($mtagnew['setting']['color']);
	$mtagnew['setting']['trim'] = max(0,intval($mtagnew['setting']['trim']));
	$mtagnew['setting']['length'] = max(0,intval($mtagnew['setting']['length']));
}
?>
