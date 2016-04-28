<?php
/**
 * ��洦����
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */

class _08_Advertising
{
    /**
     * ˢ�¹��ģ���������
     *
     * @param  string	$fcaid ���λID
     * @return bool 	��ճɹ�����TRUE�����򷵻�FALSE
     * @static
     * @since  1.0
     */
    public static function cleanTag($fcaid)
    {
		$tpl_cache = cls_Parse::TplCacheDirFile('adv_' . $fcaid . '.php');
		$file = _08_FilesystemFile::getInstance();
        $file->delFile($tpl_cache);
        $content_cache = M_ROOT . 'dynamic.adv_cache.adv_' . $fcaid;
        _08_FileSystemPath::checkPath($content_cache, true);
        return $file->cleanPathFile($content_cache, 'php');
    }

    /**
     * ˢ�����й���ǩ����
     *
     * @static
     * @since  1.0
     */
    public static function cheanAllCache()
    {
		$fcatalogs = cls_fcatalog::InitialInfoArray();
		foreach($fcatalogs as $k => $v){
			self::cleanTag($k);
		}
    }

    /**
     * ���ù��չʾ��
     *
     * @param string $content Ҫ��ȡ�Ĺ������ģ��
     * @since 1.0
     */
    public function setViews($viewscachefile)
    {
    	global $db,$tblprefix;
        if(empty($viewscachefile) || !is_file($viewscachefile)) return false;
        $file = _08_FilesystemFile::getInstance();
        $file->_fopen($viewscachefile, 'rb');
        if( $file->_flock(LOCK_SH) )
        {
            $ids = $file->_fread();
            $file->_flock(LOCK_UN);
        }
        if(empty($ids)) return false;
        // �������ļ���ĵ�һ������ǰ�����������ļ�����ʱ�䣬���Դ������ݿ�ʱ��Ҫ���˸�ֵ
        $ids = array_reverse(array_count_values(explode(',', $ids)), true);
        array_pop($ids);
        if(is_array($ids))
        {
            foreach($ids as $aid => $count)
            {
                $aid = (int)$aid;
                $db->query("
                    UPDATE `{$tblprefix}farchives`
                    SET `views` = `views` + {$count}
                    WHERE `aid` = {$aid}"
                );
            }
        }
    }

    /**
     * ��ȡ���չʾID
     *
     * @param  string $content Ҫ��ȡ�Ĺ��ģ��
     * @return array           ���ع��ID����
     * @static
     * @since  1.0
     */
    public static function getViews($content)
    {
        if ( preg_match_all('@<!--(\d+)-->@isU', $content, $views) )
        {
            return $views[1];
        }
        else {
            return array();
        }
    }

    /**
     * ����ĳ������������Ϣ
     *
     * @param  array $adv_config Ҫ���õĲ���
     * @return bool              ���óɹ�����TRUE�����򷵻�FALSE
     */
    public static function setFcatalog(array $adv_config, $fcaid)
    {
		return cls_fcatalog::ModifyOneConfig($fcaid,$adv_config,false) ? true : false;
    }

    /**
     * ��ȡ�������ԭʼ��Ϣ(������Դ��ȡ)
     *
     * @param  int   $fcaid ���ID
     * @return array        ���ػ�ȡ���Ĺ����Ϣ���飬�����ȡ���ɹ��򷵻�FALSE
     * @since  1.0
     */
    public static function getAdvConfig($fcaid)
    {
		$re = cls_fcatalog::InitialOneInfo($fcaid);
		if(empty($re) || empty($re['ftype'])){
			return false;
		}else return $re;
    }
	
    /**
     * ɾ��һ����������ʱ��������λ���������
     *
     * @param  int		$fcaid		���λID
     * @return bool		���óɹ�����TRUE�����򷵻�FALSE
     */
    public static function DelOneAdv($fcaid){
		$fcaid = cls_fcatalog::InitID($fcaid);
		self::cleanTag($fcaid);
		cls_CacheFile::Del('advtag',"adv_$fcaid");
		return true;
    }
    /**
     * ���ƹ��λ��ģ���ǩ����һ���λ
     *
     * @param  int		$fromID ��Դ���λID
     * @param  int		$toID Ŀ�Ĺ��λID
     * @return bool		���óɹ�����TRUE�����򷵻�FALSE
     */
    public static function AdvTagCopy($fromID,$toID){
		if(!(self::getAdvConfig($fromID))){
			throw new Exception('���ģ���ǩ����ʧ�ܣ���Դ���λ�����ڡ�');
		}
		if(!(self::getAdvConfig($toID))){
			throw new Exception('���ģ���ǩ����ʧ�ܣ�Ŀ�Ĺ��λ�����ڡ�');
		}
		if($tag = cls_cache::Read('advtag',"adv_$fromID")){
			$tag['ename'] = "adv_$toID";
			$tag['setting']['casource'] = $toID;
			cls_CacheFile::Save($tag,cls_cache::CacheKey('advtag',$tag['ename']),'advtag');
		}else{
			throw new Exception('���ģ���ǩ����ʧ�ܣ�δ�ҵ���Դ��ǩ��');
		}
		return true;
    }

    /**
     * ���ù��ģ�����û���
     *
     * @param  array $mtagnew �������
     * @return bool           ���óɹ�����TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public static function setAdvCache(array $mtagnew)
    {
        global $unsetvars, $unsetvars1, $fcaid, $ttype, $tclass, $iscopy;
        if(!is_array($unsetvars) || !is_array($unsetvars1)) return false;
		$fcaid = cls_fcatalog::InitID($fcaid);
        try {
    		$mtagnew['setting'] = empty($mtagnew['setting']) ? array() : $mtagnew['setting'];
    		if(!empty($mtagnew['setting'])){
    			foreach($mtagnew['setting'] as $key => $val){
    				if(in_array($key,$unsetvars) && empty($val)) unset($mtagnew['setting'][$key]);
    				if(!empty($unsetvars1[$key]) && in_array($val,$unsetvars1[$key])) unset($mtagnew['setting'][$key]);
    			}
    		}
    		$mtagnew['template'] = empty($mtagnew['template']) ? '' : stripslashes($mtagnew['template']);
    		$mtagnew['disabled'] = $iscopy || empty($mtag['disabled']) ? 0 : 1;
    		$mtag = array(
        		'ename' => $mtagnew['ename'],
        		'tclass' => $tclass,
        		'template' => $mtagnew['template'],
        		'setting' => $mtagnew['setting']
    		);
    		cls_CacheFile::Save($mtag,cls_cache::CacheKey($ttype,$mtagnew['ename']),$ttype);
            self::cleanTag($fcaid);
#            cls_CacheFile::Update('fcatalogs');
            return true;
        } catch (Exception $error) {
            return false;
        }
    }

    /**
     * ��ʾ���ư�ť
     * ���ø÷����������JQ�⣬���Ҷ���һ�����غ��� ��$.closeClipBoard�������ڣ�
     * $.closeClipBoard = function() {
     *     alert('���Ƴɹ���');
     * }
     *
     * @param  string $value  Ҫ���Ƶ�ֵ���ⲿ����ʱ����base64_encode(rawurlencode($value))�����£�������ʹ��POST����
     * @param  bool   $type   �����ʾ�����İ�ť������ΪTRUE������ֻ��ʾ�����ơ���ť
     * @return string $string ���ش�ֵ�ĸ��ư�ťHTML����
     *
     * @since  1.0
     */
    public static function showCopyCode($value, $id = 'flashcopier')
    {
        global $cms_abs, $mcharset;
        $value = base64_decode($value);
        // �����ǰ�༭����UTF����ʱת����UTF8
        if(false === stripos($mcharset, 'UTF'))
        {
            $value = rawurlencode(cls_string::iconv($mcharset, 'UTF-8', rawurldecode($value)));
        }
        $string = '
            <div id="' . $id . '" class="flashcopier">
                <span style="float:left;">(</span> <div class="flashcopier_div">
                    <object id="' . $id . '_flash" height="120" width="166" type="application/x-shockwave-flash" data="' . $cms_abs . 'images/common/copy.swf" class="flashcopier_flash">
                        <param value="always" name="allowScriptAccess">
                        <param value="url=' . $value . '" name="flashvars">
                        <param value="' . $cms_abs . 'images/common/copy.swf" name="movie">
                        <param value="opaque" name="wmode">
                        <param value="high" name="quality">
                        <div>
                            <h4>ҳ����Ҫ�°�Adobe Flash Player.</h4>
                            <p>
                                <a target="_blank" href="http://www.adobe.com/go/getflashplayer">
                                    <img height="33" width="112" src="' . $cms_abs . 'images/common/get_flash_player.gif" alt="��ȡ�°�Flash">
                                </a>
                            </p>
                        </div>
                    </object>
                </div> <span style="float:left;">)</span>
            </div>
        ';
        return $string;
    }
}