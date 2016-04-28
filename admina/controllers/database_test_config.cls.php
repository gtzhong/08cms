<?php
/**
 * ���ݲ������ò���ҳ��
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
class cls_database_test_config extends cls_AdminHeader
{
    /**
     * �ĵ�ģ��������Ϣ
     *
     * @var array
     */
    private $channels = array();

    /**
     * �ĵ����Ե����û�����Ϣ
     *
     * @var array
     */
    private $arccache = array();

    /**
     * �������Ե����û�����Ϣ
     *
     * @var array
     */
    private $commucache = array();

    /**
     * ���ݲ��Թ�������
     *
     * @var object
     */
    private $test = null;

    /**
     * ��ǰ������ģ��ID
     *
     * @var int
     */
    private $chid = 0;

    /**
     * ��ǰ��������������
     *
     * @var array
     */
    private $config = array();

    public function __construct()
    {
        @set_time_limit(0);
        // ���嵱ǰʹ��Ȩ��
        parent::__construct('database');
        // �ĵ�ģ��
        $this->channels = cls_channel::InitialInfoArray();
        $this->arccache = cls_cache::Read('database_test_arc');
        $this->commucache = cls_cache::Read('database_test_commu');
        isset($this->_params['chid']) && $this->chid = intval($this->_params['chid']);
        if( submitcheck('saveconfig') )
        {
            $this->saveConfig();
            exit;
        }
        if( submitcheck('savecommuconfig') )
        {
            $this->saveCommuConfig();
            exit;
        }

        foreach(array('members', 'coclasses', 'compilation', 'commumembers') as $value)
        {
            if( submitcheck($value) )
            {
                $this->test = new cls_database_test();
                $function = 'action' . ucfirst($value);
                method_exists($this, $function) && $this->$function();
                exit;
            }
        }
    }

    /**
     * �������û�Աģ�ͽ���
     */
    public function configArc()
    {
        // ��Աģ��
        $members = $this->getMchannels();

        $mchannels = isset($this->arccache['config']['mchannels']) ? $this->arccache['config']['mchannels'] : array();

        $this->config['showdatas'] = array();
        foreach($this->channels as $channel)
        {
            // ����ʾ��ϵͳ���õ�δ����ģ��
            if( !$channel['cname'] || !$channel['available'] || (isset($channel['issystem']) && !$channel['issystem']) )
            {
                continue;
            }

            $member_str = makeselect(
                "mchannels[{$channel['chid']}][]",
                makeoption($members, isset($mchannels[$channel['chid']]) ? $mchannels[$channel['chid']] : 0),
                'multiple="multiple" class="w100"'
            );

            $this->config['showdatas'][$channel['chid']] = array(
                $channel['cname'],
                $member_str,
                '<a href="?entry=database_test_config&action=configcoclasses&chid=' . $channel['chid'] . '" onclick="return floatwin(\'open_generate_config\', this);" style="color:#134D9D">������ϵ</a>',
                '<a href="?entry=database_test_config&action=configcompilation&chid=' . $channel['chid'] . '" onclick="return floatwin(\'open_generate_config\', this);" style="color:#134D9D">���úϼ�</a>'
            );
        }

        // ��ȡ����
        $this->config['title'] = '�ĵ�ģ�Ͳ����������ã�ע��������ݱȽ϶����ʱ���Ƚϳ���';
        $this->config['tabletitle'] = array('ģ��ID', 'ģ������', '������Աģ��', '������ϵ', '�����ϼ�');
        $this->config['submits'] = array('saveconfig' => '��������', 'members' => '��ʼ������Աģ��');

        $this->_build->table( $this->config );
    }

    /**
     * ��ʼ������Աģ��
     */
    public function actionMembers()
    {
        /**
         * �����ĵ������Ļ�Աģ����Ϣ
         */
        if( isset($this->_params['mchannels']) )
        {
            $this->test->setArchiveMembers($this->_params['mchannels']);
        }
        cls_message::show('������ɣ�', axaction(2));
    }

    /**
     * �������úϼ�����
     */
    public function configCompilation()
    {
        $abrels = cls_cache::Read('abrels');
        if(empty($abrels) || !is_array($abrels)) cls_message::show('û�кϼ���Ŀ��δ����ϵͳ���棡', M_REFERER);

        $this->config['title'] = '�����ĵ�ģ�Ͳ���������ϼ����ã�ע��������ݱȽ϶����ʱ���Ƚϳ���';
        $this->config['tabletitle'] = array('�ϼ�ID', '�ϼ�����', '��Դ�ĵ�ģ��', '��������(��λ��%)');
        $this->config['submits'] = array('saveconfig' => '��������', 'compilation' => '��ʼ�����ϼ�');
        // ��ȡ���ò���
        if(isset($this->arccache['config']['compilation']))
        {
            if(isset($this->arccache['config']['compilation']['proportion']))
            {
                $proportion = $this->arccache['config']['compilation']['proportion'];
            }
            else
            {
                $proportion = array();
            }

            if(isset($this->arccache['config']['compilation']['channels']))
            {
                $channelses = $this->arccache['config']['compilation']['channels'];
            }
            else
            {
                $channelses = array();
            }
        }

        $channels = array('0' => '��ѡ����Դ�ĵ�ģ��');
        foreach( $this->channels as $channel)
        {
            // ����ʾ��ϵͳ���õ�δ����ģ��
            if( !$channel['cname'] || !$channel['available'] || (isset($channel['issystem']) && !$channel['issystem']) || $this->chid == $channel['chid'] )
            {
                continue;
            }
            $channels[$channel['chid']] = $channel['cname'];
        }

        foreach($abrels as $abrel)
        {
            if($abrel['available'])
            {
                if( isset($proportion[$this->chid][$abrel['arid']]) )
                {
                    $default_value = (int)$proportion[$this->chid][$abrel['arid']];
                }
                else
                {
                    $default_value = 100;
                }
                if( isset($channelses[$this->chid][$abrel['arid']]) )
                {
                    $channel = $channelses[$this->chid][$abrel['arid']];
                }
                else
                {
                    $channel = 0;
                }

                $input = '<input type="text" name="proportion['.$abrel['arid'].']" class="w80" value="'. $default_value. '" />';
                $this->config['showdatas'][$abrel['arid']] = array(
                    $abrel['cname'],
                    makeselect( "channels[{$abrel['arid']}]", makeoption($channels, $channel) ),
                    $input
                );
            }
        }

        $this->_build->table( $this->config );
    }

    /**
     * ��ʼ�����ϼ�ģ��
     */
    public function actionCompilation()
    {
        /**
         * �����ĵ������ĺϼ�ģ����Ϣ
         */
        if( !empty($this->_params['proportion']) )
        {
            $this->test->setArchiveCompilation(
                $this->chid,
                $this->_params['channels'],
                $this->_params['proportion']
            );
        }
        cls_message::show('������ɣ�', axaction(2));
    }

    /**
     * ����������ϵ����
     */
    public function configCoclasses()
    {
        if( empty($this->chid) ) cls_message::show('��ָ����ȷ�Ĳ�����', axaction(2));
        isset($this->channels[$this->chid]['stid']) && $stid = $this->channels[$this->chid]['stid'];
        if( empty($stid) ) cls_message::show('��ģ��û�й����κ���ϵ��', axaction(2));
        $cotypes = cls_cache::Read('cotypes');
        $splitbls = cls_cache::Read('splitbls');
        $arccache = $this->arccache['config']['co_proportion'];

        // ��ȡ������ʾ����
        foreach($cotypes as $k => $cotype)
        {
            if( isset($splitbls[$stid]['coids']) && in_array($k, $splitbls[$stid]['coids']) )
            {
                // �����ֵδ�������С�ڻ����0ʱ���Զ���Ϊ0
                if( !isset($arccache[$this->chid][$k]) || ((int)$arccache[$this->chid][$k] <= 0) )
                {
                    $default_value = 0;
                }
                // ���������ֵ���ڻ����100ʱ���Զ���Ϊ100
                else if( (int)$arccache[$this->chid][$k] >= 100 )
                {
                    $default_value = 100;
                }
                else
                {
                    $default_value = (int) $arccache[$this->chid][$k];
                }
                $input = '<input type="text" name="co_proportion['.$k.']" class="w80" value="'. $default_value. '" />';

                $this->config['showdatas'][$k] = array($cotype['cname'], $input);
            }
        }

        // ��ȡ����
        $this->config['title'] = '�����ĵ�ģ�Ͳ�����������ϵ���ã�ע��������ݱȽ϶����ʱ���Ƚϳ���';
        $this->config['tabletitle'] = array('��ϵID', '��ϵ����', '���ɱ���(��λ��%)');
        $this->config['submits'] = array('saveconfig' => '��������', 'coclasses' => '��ʼ������ϵ');

        $this->_build->table( $this->config );
    }

    /**
     * ��ʼ������ϵ
     */
    public function actionCoclasses()
    {
        /**
         * �����ĵ�������ϵ
         */
        if( isset($this->_params['co_proportion']) )
        {
            $this->test->setArchiveCotypes("#__archives{$this->chid}", $this->_params['co_proportion']);
        }
        cls_message::show('������ɣ�', axaction(2));
    }

    /**
     * ��ȡ���л�Աģ��
     *
     * @return array $members �������л�Աģ��ID������
     * @static
     */
    public function getMchannels()
    {
        // ��Աģ��
        $mchannels = cls_mchannel::InitialInfoArray();
        $members = array();
        foreach($mchannels as $mchannel)
        {
            $members[$mchannel['mchid']] = $mchannel['cname'];
        }
        return $members;
    }

    /**
     * ���콻�����ý���
     */
    public function configCommu()
    {
        $commus = cls_commu::InitialInfoArray();
        if( empty($commus) ) cls_message::show('û�н���ģ�ͣ�', axaction(2));

        $mchannels = isset($this->commucache['config']['mchannels']) ? $this->commucache['config']['mchannels'] : array();
        $members = $this->getMchannels();
        $chinnels = $this->getChannels();

        foreach($commus as $commu)
        {
            if( empty($commu['available']) ) continue;
            $member_str = $this->_build->select(
                array(
                    'selectname' => "mchannels[{$commu['cuid']}]",
                    'selectdatas' => $members,
                    'selectedkey' => (isset($mchannels[$commu['cuid']]) ? $mchannels[$commu['cuid']] : 0)
                )
            );
//            if(!empty($commu['cfgs']['chids']))
//            {
//                $chinnel_str = $this->_build->select(
//                    array(
//                        'selectname' => "channels[{$commu['cuid']}][]",
//                        'selectdatas' => $chinnels,
//                        'selectedkey' => isset($channels[$commu['cfgs']['chids']]) ? $channels[$commu['cfgs']['chids']] : 0
//                    )
//                );
//            }
//            else
//            {
//                $chinnel_str = '';
//            }

            $this->config['showdatas'][$commu['cuid']] = array(
                $commu['cname'],
                $member_str,
          //      $chinnel_str
            );
        }
        // ��ȡ����
        $this->config['title'] = '��������ģ�Ͳ����������ã�ע��������ݱȽ϶����ʱ���Ƚϳ���';
        $this->config['tabletitle'] = array('ģ��ID', 'ģ������', '������Աģ��'/*, '�����ĵ�ģ��'*/);
        $this->config['submits'] = array('savecommuconfig' => '��������', 'commumembers' => '��ʼ����ģ��');

        $this->_build->table( $this->config );
    }

    /**
     * ִ�н���ģ�����Աģ�͹���
     */
    public function actionCommuMembers()
    {
        if( isset($this->_params['mchannels']) )
        {
            $this->test->setCommuMembers($this->_params['mchannels']);
        }
        cls_message::show('������ɣ�', axaction(2));
    }

    /**
     * ���潻������
     */
    public function saveCommuConfig()
    {
        if( isset($this->_params['mchannels']) )
        {
            $this->_params['mchannels'] = array_map('intval', $this->_params['mchannels']);
            $this->commucache['config']['mchannels'] = $this->_params['mchannels'];
        }
        cls_CacheFile::Save($this->commucache, 'database_test_commu');
        cls_message::show('������ɣ�', M_REFERER);
    }

    /**
     * ��ȡ�ĵ�ģ��ID������
     *
     * @return array $channels �����ĵ�ģ����������
     */
    public function getChannels()
    {
        $channels = array();
        foreach($this->channels as $channel)
        {
            // ����ʾ��ϵͳ���õ�δ����ģ��
            if( !$channel['cname'] || !$channel['available'] || (isset($channel['issystem']) && !$channel['issystem']) )
            {
                continue;
            }
            $channels[$channel['chid']] = $channel['cname'];
        }
        return $channels;
    }

    /**
     * �������ò���
     */
    public function saveConfig()
    {
        /**
         * �����ĵ������ĺϼ���Ϣ
         */
        if( isset($this->_params['proportion']) && isset($this->_params['channels']) )
        {
            if( is_array($this->_params['proportion']) )
            {
                // �ñ���������0%-100%֮��
                foreach($this->_params['proportion'] as &$proportion)
                {
                    if($proportion < 0) {
                        $proportion = 0;
                    } else if($proportion > 100) {
                        $proportion = 100;
                    } else {
                        $proportion = (int) $proportion;
                    }
                }
            }
            $this->_params['channels'] = array_map('intval', array_unique($this->_params['channels']));
            $this->arccache['config']['compilation']['proportion'][$this->chid] = $this->_params['proportion'];
            $this->arccache['config']['compilation']['channels'][$this->chid] = $this->_params['channels'];
        }

        /**
         * �����ĵ������Ļ�Աģ����Ϣ
         */
        if( isset($this->_params['mchannels']) )
        {
            $this->arccache['config']['mchannels'] = $this->_params['mchannels'];
        }

        /**
         * ���������ϵ����
         */
        if( isset($this->_params['co_proportion']) )
        {
            if( empty($this->chid) ) cls_message::show('��ָ����ȷ�Ĳ�����', axaction(2));

            $this->_params['co_proportion'] = array_map('intval', $this->_params['co_proportion']);
            $sum = array_sum($this->_params['co_proportion']);

            #if( $sum < 0 || $sum > 100 ) cls_message::show('���б���֮��ֻ����0-100%���ڡ�', M_REFERER);
            $this->arccache['config']['co_proportion'][$this->chid] = $this->_params['co_proportion'];
        }
        cls_CacheFile::Save($this->arccache, 'database_test_arc');
        cls_message::show('������ɣ�', M_REFERER);
    }
}