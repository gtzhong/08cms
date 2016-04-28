<?php
/**
 * ���ݿ���Խ��湹��ű�
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
class _08_databaseTest extends cls_AdminHeader
{
    public function __construct()
    {
        @set_time_limit(0);
        parent::__construct('database');
        backnav('data','database_test');
        if( submitcheck('save_config') )
        {
            $this->saveConfig();
            exit;
        }
        // �ύ���ɲ�������
        if( submitcheck('generate_submit') || isset($this->_params['generate']) )
        {
            $this->generateSubmit();
            exit;
        }
    }

    public function init()
    {

        tabheader(
            '���ɲ�������'.'<!--<input class="checkbox" type="checkbox" name="chkall" onclick="checkall(this.form)" checked="checked">ȫѡ&nbsp;&nbsp;&nbsp;&nbsp;-->����ʼID<input type="text" name="begin_id" value="1" class="w50" /> >> <a href="?entry='. $this->_params['entry'] . '&action=clean_all_file">�����������</a>',
            'generate',
            "?entry={$this->_params['entry']}&action={$this->_params['action']}"
        );
        // ��Աģ��
        $mchannels = cls_mchannel::InitialInfoArray();
        $this->_view->assign('member_model', '��Աģ��');
        $this->_view->assign('member_str', self::getShowString('member', $mchannels));

        // �ĵ�ģ��
        $channels = cls_channel::InitialInfoArray();
        // ����ʾδ���õ�ģ��
        foreach($channels as $k => $channel)
        {
            if(empty($channel['available']))
            {
                unset($channels[$k]);
            }
        }
        $this->_view->assign('arc_model', '�ĵ�ģ�� ��<a href="?entry=database_test_config&action=configarc" onclick="return floatwin(\'open_generate_config\', this);" style="font-weight:bold; color:#134D9D">����</a>��');
        $this->_view->assign('arc_str', self::getShowString('arc', $channels));

        // ����ģ��
        $commus = cls_commu::InitialInfoArray();
        $this->_view->assign('commu_model', '����ģ�ͣ�<a href="?entry=database_test_config&action=configcommu" onclick="return floatwin(\'open_generate_config\', this);" style="font-weight:bold; color:#134D9D">����</a>��');
        $this->_view->assign('commu_str', self::getShowString('commu', $commus));

        $this->_view->display('databasetest.tpl');
        echo <<<EOT
        </table><br />
        <input class="btn" type="submit" name="generate_submit" value="��ʼ����">&nbsp;&nbsp;&nbsp;&nbsp;
        <input class="btn" type="submit" name="save_config" value="��������">
        </form>
EOT;
        a_guide('databasetest');
    }

    /**
     * �ύ��ת����
     */
    public function generateSubmit()
    {
        if( !empty($this->_params['generate']) )
        {
            if( !is_array($this->_params['generate']) )
            {
                $this->_params['generate'] = array_flip(explode(',', $this->_params['generate']));
            }
            $test = new cls_database_test();
            $test->setURL("?entry={$this->_params['entry']}&action={$this->_params['action']}");

            /* ���ò������ɻ�Ա�������ݲ��� */
            if( isset($this->_params['generate']) )
            {
                $config = array(
                    'begin_id' => (isset($this->_params['begin_id']) ? (int)$this->_params['begin_id'] : 1),
                    'current_num' => (!empty($this->_params['current_num']) ? explode(',', $this->_params['current_num']) : array()),
                    'generate' => array_keys($this->_params['generate'])
                );
                $this->getURIConfig( 'members', 'member', 'mchids', $config );
                $this->getURIConfig( 'archives', 'arc', 'chids', $config );
                $this->getURIConfig( 'commus', 'commu', 'cuids', $config );

                if( isset($this->_params['generate']['members']) )
                {
                   $test->generateJumpLogic(
                        'members',
                        $config,
                        'member',
                        'mchids',
                        '��Ա����������ɣ�'
                    );
                }

                if( isset($this->_params['generate']['archives']) )
                {
                   $test->generateJumpLogic(
                        'archives',
                        $config,
                        'arc',
                        'chids',
                        '�ĵ�����������ɣ�'
                    );
                }

                if( isset($this->_params['generate']['commus']) )
                {
                    $test->generateJumpLogic(
                        'commus',
                        $config,
                        'commu',
                        'cuids',
                        '��������������ɣ�'
                    );
                }
            }
        }
        cls_message::show('���в����Ѿ���ɣ�', "?entry={$this->_params['entry']}");
    }

    /**
     * ��ȡURI���ò���
     *
     * @param string $type      ��ȡ����
     * @param string $data_name �����±�����
     * @param string $chid_name ����ģ��ID����
     * @param array  $config    ���ò����洢��
     *
     * @since 1.0
     */
    public function getURIConfig( $type, $data_name, $chid_name, &$config )
    {
        if( !isset($this->_params['generate'][$type]) ) return false;
        // ����ģ��ID
        if( isset($this->_params[$chid_name]) )
        {
            $config[$type][$chid_name] = explode(',', $this->_params[$chid_name]);
        }
        else
        {
            $config[$type][$chid_name] = array_keys($this->_params[$data_name]);
        }
        // ��ȡ��������ģ����������
        if( isset($this->_params[$data_name]) )
        {
            if( is_array($this->_params[$data_name]) )
            {
                $config[$type][$data_name] = $this->_params[$data_name];
            }
            else
            {
                $config[$type][$data_name] = explode(',', $this->_params[$data_name]);
            }
        }
    }

    /**
     * ��������
     */
    public function saveConfig()
    {
        $arccache = cls_cache::Read('database_test_arc');
        $membercache = cls_cache::Read('database_test_member');
        $commucache = cls_cache::Read('database_test_commu');

        $members = @$this->_params['member'];
        $archives = @$this->_params['arc'];
        $commus = @$this->_params['commu'];

        $arccache['archives'] = $archives;
        $membercache['members'] = $members;
        $commucache['commus'] = $commus;

        cls_CacheFile::Save($membercache, 'database_test_member');
        cls_CacheFile::Save($arccache, 'database_test_arc');
        cls_CacheFile::Save($commucache, 'database_test_commu');
        cls_message::show('����ɹ���', "?entry={$this->_params['entry']}");
    }

    /**
     * ��ȡСģ����ʾ��ʽ�ַ���
     *
     * @param  string $name  INPUT������
     * @param  array  $value INPUTҪ����������
     *
     * @return string        ������ʽ�ַ���
     * @since  1.0
     */
    public static function getShowString($name, array $array)
    {
        $string = '';
        foreach($array as $value)
        {
            switch($name)
            {
                case 'member' :
                    $g_member = cls_cache::Read('database_test_member');
                    $index = 'mchid';
                    $val = isset($g_member['members'][$value[$index]]) ? $g_member['members'][$value[$index]] : 0;
                break;
                case 'arc' :
                    $g_arc = cls_cache::Read('database_test_arc');
                    $index = 'chid';
                    $val = isset($g_arc['archives'][$value['chid']]) ? $g_arc['archives'][$value['chid']] : 0;
                break;
                case 'commu' :
                    $g_commu = cls_cache::Read('database_test_commu');
                    $index = 'cuid';
                    $val = isset($g_commu['commus'][$value['cuid']]) ? $g_commu['commus'][$value['cuid']] : 0;
                break;
            }

            if( $value['cname'] && ($value['available'] || (isset($value['issystem']) && $value['issystem'])) )
            {
                $string .= <<<EOT
                    <div style="width:210px; float:left;">
                        <span style="font-weight: bold;">{$value['cname']}</span>������
                        <input type="text" name="{$name}[{$value[$index]}]" value="{$val}" class="w70" style="margin-right:20px;" />
                    </div>
EOT;
            }
        }

        return $string;
    }

    /**
     * �����Ŀ¼�����в��������ļ�
     *
     * @since 1.0
     */
    public function clean_all_file()
    {
        $file = _08_FilesystemFile::getInstance();
        $path = M_ROOT . 'dynamic' . DS . 'test_data_cache';
        _08_FileSystemPath::checkPath($path, true);
        $file->cleanPathFile($path, 'txt');
        cls_message::show('�����ɣ�', M_REFERER);
    }
}