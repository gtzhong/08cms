<?php
class cls_member extends cls_memberbase{
	
	// ��¥��˾-�����д��¥
	function user_xiezilou($key,$mode = 'init'){
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$xiezilou = isset($this->predata[$key]) ? $this->predata[$key] : ''; 
				trbasic('�����д��¥','',getArchives('115',$xiezilou,100,'xiezilou[]','д��¥'),'');
				//echo "xx1,";
			break;
			case 'sv'://���洦��
				global $xiezilou;//�ύ����������				
				$xiezilou = empty($xiezilou) ? "" : ",".implode(',',$xiezilou);
				$mchid = $this->mchid;
				$this->auser->updatefield('xiezilou',$xiezilou,"members_$mchid");
			break;
		}
	}
    
	// ��¥��˾-���������
	function user_shaopu($key,$mode = 'init'){
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$shaopu = isset($this->predata[$key]) ? $this->predata[$key] : ''; 
				trbasic('���������','',getArchives('116',$shaopu,100,'shaopu[]','����'),'');
				//echo "xx1,";
			break;
			case 'sv'://���洦��
				global $shaopu;//�ύ����������				
				$shaopu = empty($shaopu) ? "" : ",".implode(',',$shaopu);
				$mchid = $this->mchid;
				$this->auser->updatefield('shaopu',$shaopu,"members_$mchid");
			break;
		}
	}
    
	// ��¥��˾-�����¥��
	function user_loupan($key,$mode = 'init'){
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$loupan = isset($this->predata[$key]) ? $this->predata[$key] : ''; 
				trbasic('�����¥��','',getArchives('4',$loupan,100,'loupan[]','¥��'),'');
				//echo "xx1,";
			break;
			case 'sv'://���洦��
				global $loupan;//�ύ����������				
				$loupan = empty($loupan) ? "" : ",".implode(',',$loupan);
				$mchid = $this->mchid;
				$this->auser->updatefield('loupan',$loupan,"members_$mchid");
				//echo "xx2,$loupan,$mchid";
			break;
		}
	}
	
	// updatedb()-ǰ,����:�����¥��
	function sv_update(){
		$this->sv_items('loupan');
		$this->auser->updatedb();
	}    
    
    /**
     * �ϴ�ͷ�����ӻ��֡���Ա���ķ���ҳ����ת�����������Ϻ��Զ�����ԭ������ҳ��
     */
    function sv_all_common_ex($type=''){
        $curuser = cls_UserMain::CurUser();
        //ԭ��ͷ��Ϊ�յģ������ύ��Ϣͷ��Ϊ�յ�����£��ӷ�
        empty($curuser->info['image']) && $this->sv_upload_image_point('image',1,'uploadpicture','�ϴ�ͷ��');
        $jumpType = '';
        //����Ƕ��ַ������ⷿԴ��ת���������ӣ��ύ֮��ֱ�ӷ��ط���ҳ��
        !empty($type) && $jumpType = "?action=".$type;
        $this->sv_all_common(array('jumptype'=>$jumpType));
    }
    
    
    /**
     * ��ͷ��Ϊ��ʱ���ϴ�ͷ�񣬿ɻ�û��֣������Ƿ��ǣ���һ���ֶΣ������ǵ�һ���ϴ�ͷ�񣿣���ΪĿǰ���ֲ���ô��Ҫ��
     * @param string $currencyObj  �ӷ��ֶ������涨�����ĸ��ֶμӷ֣�
     * @param int    $currencyId   �������ID
     * @param int    $currencyType �ӻ������ͣ����緢���ĵ���ע���Ա����վͶƱ�ȣ� 
     * @param string $remark       �ӻ���˵��
     * @param int    @mode         �ֶ���/�ۻ���
     */
	function sv_upload_image_point($currencyObj,$currencyId,$currencyType,$remark,$mode=0){
		$db = _08_factory::getDBO();
        $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$curuser = cls_UserMain::CurUser();
		$currencys = cls_cache::Read('currencys');
        $timestamp = TIMESTAMP; 
		if(empty($curuser->info['mid']) || empty($currencys[$currencyId])) return;		
		$point = $currencys[$currencyId]['bases'][$currencyType];
        
		$db->query(" UPDATE {$tblprefix}members SET currency$currencyId = currency$currencyId + $point WHERE mid = ".$curuser->info['mid']);
		$db->query("INSERT INTO {$tblprefix}currency$currencyId SET
				value='$point',
				mid='".$curuser->info['mid']."',
				mname='".$curuser->info['mname']."',
				fromid='".$curuser->info['mid']."',
				fromname='".$curuser->info['mname']."',
				createdate='$timestamp',
				mode='$mode',
				remark='$remark'");
	}
}


