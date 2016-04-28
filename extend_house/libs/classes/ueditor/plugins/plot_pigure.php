<?php
/**
 * CK���������С��ͼƬ������
 *
 * ֻҪ��CK���������̳л��ࣺCkPublicClass������Ҫ�ڹ��췽�����Ǹ��࿪ʼ����ǰ����
 *
 * @author Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
class CkPlotPigure extends CkPublicClass {
    /**
     * ��ǰ�������
     *
     * @var object
     * @static
     * @since 1.0
     */
    private static $_instance = null;

    /**
     * ͼƬ��������ID
     *
     * @var   const
     * @since 1.0
     */
    const CAID = 7;

    /**
     * ͼƬ����ģ��ID
     *
     * @var   const
     * @since 1.0
     */
    const CHID = 7;

    /**
     * ����CkPublicClass �����๹�캯��
     *
     * �������Ҫʹ�ù��췽����ʼ�����Ǹù��췽������Ҫ���û���Ĺ��췽��
     * ������๹�췽����鿴�ļ���ck_public_class.php
     *
     * @since 1.0
     */
    public function __construct($title = '') {
        // ���ò���������ڱ���
        $this->_title = $title;
        parent::__construct();
    }

    /**
     * ������
     *
     * �ò��ʹ����COOKIE��ʽ��ȡֵ�������ڵ���ҳ�������������COOKIE
     * ��ԴС��ID��fyid��С��¥�����ƣ�lpmc
     *
     * @since 1.0
     */
    public function init() {
        global $m_cookie, $db, $tblprefix, $mcharset;
        $count = 0;
        $handlekey = (int)@$this->_get['parent_wid'];
        $fyid = empty($m_cookie['fyid' . $handlekey]) ? 0 : (int)$m_cookie['fyid' . $handlekey];
        $lpmc = empty($m_cookie['lpmc' . $handlekey]) ? '' : urldecode(trim($m_cookie['lpmc' . $handlekey]));
        if ( false === stripos($mcharset, 'UTF') )
        {
            $lpmc = cls_string::iconv('UTF-8', $mcharset, $lpmc);
        }
        tabheader($lpmc . ' >>> С��ͼƬ', 'tu_list', _08_Http_Request::uri2MVC("editor=plot_pigure&action=search{$this->_uri}"));
        if(empty($fyid) || empty($lpmc)) {
            cls_message::show('��ָ����Դ������С����');
        }
        $query = $db->query("SELECT a.aid, a.subject, a.thumb, a.caid, a.chid,  a.chid, a.initdate, a.customurl, a.nowurl, a.jumpurl FROM {$tblprefix}".atbl(self::CHID)." a WHERE a.pid3={$fyid} AND a.chid = " . self::CHID);
        $str = '<tr><td align="center"><div style="width:100%;">';
        while($row = $db->fetch_array($query)) {
            $count = 1;
            $str .= $this->showImgStyle($row, 'thumb');
        }
        $str.= '</div></td></tr>';
        if($count == 0) cls_message::show('��С����ͼƬ��');
        $str .= <<<HTML
            <tr><td height="50" align="center"><input type="button" value="��ɲ���" onclick="winclose();" /></td></tr>
HTML;
        echo $str;
        tabfooter();
        $this->SetReturnFunctionStrTu();
    }

    /**
     * ��װ�ò������
     *
     * @param string $title ����������
     *
     * @static
     * @since 1.0
     */
    public static function Setup($title = '') {
        if(null == self::$_instance) {
            self::$_instance = new self($title);
        }
        self::$_instance->init();
    }
}

CkPlotPigure::Setup('����С��ͼƬ >> (��ʾ��ֱ�ӵ��ͼƬ���ɲ���)');