<?php

/**
 * ��ҳ�������
 *
 * @author Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
class ckPagingManagement extends CkPublicClass
{
    /**
     * ��ǰ�������
     *
     * @var    object
     * @static
     * @since  1.0
     */
    private static $_instance = null;

    /**
     * ����CkPublicClass �����๹�캯��
     *
     * �������Ҫʹ�ù��췽����ʼ�����Ǹù��췽������Ҫ���û���Ĺ��췽��
     * ������๹�췽����鿴�ļ���ck_public_class.php
     *
     * @since 1.0
     */
    public function __construct($title = '')
    {
        // ���ò���������ڱ���
        $this->_title = $title;
        parent::__construct();
    }

    /**
     * ������
     *
     * @since nv50
     */
    public function init()
    {
		echo <<<EOT
        <div style="width:90%; margin:15px auto; text-align:left;" id="show_styles"></div>
		<script type="text/javascript">
            var parentObject = getParentObject();
            var editorContent = parentObject.getContent();
			var sourceDiv = document.getElementById('show_styles');
            var pageData, pageContents = '';
            var patt = /\[#(.*?)#\]/;
            var testString = editorContent.replace(/(\s|<.*?>|&nbsp;)/, '').search(patt);
            if (testString == -1)
            {
                pageContents = ('<div style="font-size:14px; text-align:center; width:100%; margin:50px 0;">����Ϣ�޷�ҳ��</div>');
            }
            else
            {
                if (testString == 0)
                {
                    var page = 1;
                }
                else
                {
                	var page = 2;
                }
                for(var i = 0; (pageData = editorContent.match(patt)) && (i < 100); ++i)
                {
                    pageContents += ('<div style="height:30px;">��' + page + 'ҳ���⣺[#<input type="text" name="ck_page' + i + '" value="' + pageData[1] + '" style="border:1px #ccc solid;" id="ck_page' + i + '"/>#]</div>');
                    editorContent = editorContent.replace(pageData[0], '<!--08_REPLACE_PAGE_' + i + '-->');
                    ++page;
                }
                pageContents = ('<div style="font-size:14px;">����' + (page - 1) + 'ҳ�� ( [#<font style="color:red;">��������Ƿ�ҳ����</font>#] )<br /><br />' + pageContents + '<div style="width:100%; text-align:center; margin-top:30px"><input type="button" value="���淵��" onclick="setValue();"/></div></div>');                
            }
            
            sourceDiv.innerHTML = pageContents;
			function setValue()
            {
				var str = '', newEditorContent = editorContent;
				var sourceDiv = document.getElementById('show_styles');
				var elements = sourceDiv.getElementsByTagName("input");
				for(i=0; i< elements.length - 1; i++)
				{
					if(elements[i].value == '')
                    {
						alert('��ҳ���ⲻ��Ϊ�գ�');
						return false;
					}
                    newEditorContent = newEditorContent.replace('<!--08_REPLACE_PAGE_' + i + '-->', '[#' + elements[i].value + '#]');
				}
                if ( newEditorContent )
                {
                    parentObject.setContent(newEditorContent);
                }
                
                winclose();
			}
		</script>
EOT;
    }

    /**
     * ��װ�ò������
     *
     * @param string $title ����������
     *
     * @static
     * @since 1.0
     */
    public static function Setup($title = '')
    {
        if(null == self::$_instance)
        {
            self::$_instance = new self($title);
        }
        self::$_instance->init();
    }
}

ckPagingManagement::Setup('��ҳ�������');