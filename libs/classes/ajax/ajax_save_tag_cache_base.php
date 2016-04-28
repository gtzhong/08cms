<?php
/**
 * �����ǩ���棨�ڱ�ʶ��ԭʱ�õ���
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/save_tag_cache/createrange/ddddd/fn/ddd/
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 * @since     nv50
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Save_Tag_Cache_Base extends _08_Models_Base
{
    public function __toString()
    {    	
        if($re = $this->_curuser->NoBackFunc('tpl')) cls_message::show($re);
        $fn = preg_replace('/[^\w]/', '', trim(@$this->_get['fn']));
        $createrange = @$this->_get['createrange'];
        if(in_array(true, array(empty($createrange), empty($fn)))) {
            exit('����ѡ�����ݣ�');
        }
        
        _08_FileSystemPath::checkPath(_08_TEMP_TAG_CACHE, true);
        try {
            // ��ճ���һСʱ�Ļ����ļ�
            $iterator = new DirectoryIterator(_08_TEMP_TAG_CACHE);
            $_file = _08_FilesystemFile::getInstance();
            foreach ($iterator as $file)
            {
                if(@$iterator->isFile($file) && ((time() - $iterator->getMTime()) >= 3600)) {
                    $_file->delFile($iterator->getPathname());
                }
            }
        } catch (RuntimeException $e) {
            die($e->getMessage());
        }
    
        $createrange = (array)cls_TagAdmin::CodeToTagArray($createrange);
    	cls_Array::array_stripslashes($createrange);//�������ݿ⣬��ת��ȡ��
    	
        // ���浱ǰѡ���ı��������ļ�
        cls_CacheFile::cacSave($createrange, $fn, _08_TEMP_TAG_CACHE);
    }
}