<?php
/**
* ���ǩ�йص��ⲿ��������
* 
*/
defined('M_COM') || exit('No Permission');
abstract class cls_TagBase{
	
	# �ڼ򻯱�ǩ����ʱ��ֻҪΪ�ջ�Ϊ0�Ϳ�������ı�����
	protected static $UnsetVars = array();
	
	public static function BadWord(&$source){
		$badwords = cls_cache::Read('badwords');
        // preg_replace�����ı���������UTF8�������ƥ��
        if (!empty($badwords['wsearch']))
        {
            $mcharset = cls_env::getBaseIncConfigs('mcharset');
            $badwords['wsearch'] = cls_string::iconv($mcharset, 'UTF-8', $badwords['wsearch']);
            $source = cls_string::iconv($mcharset, 'UTF-8', $source);
            $source = preg_replace($badwords['wsearch'],$badwords['wreplace'],$source);
            $source = cls_string::iconv('UTF-8', $mcharset, $source);
        }
	}
	public static function WordLink(&$source){
		$wordlinks = cls_cache::Read('wordlinks');
		if(!empty($wordlinks['swords'])){
			if(preg_match_all("/<.*?>/s", $source, $matchs)){
				$matchs = array_unique($matchs[0]);
				foreach($matchs as $k => $v) $source = str_replace($v,":::$k:::", $source);
				$source = preg_replace($wordlinks['swords'],$wordlinks['rwords'],$source,1);
				$source = preg_replace("/:::(\d+):::/se", '$matchs[$1]', $source);
			}
		}
		return $source;
	}
	public static function Face(&$source){
		$faceicons = cls_cache::Read('faceicons');
		if(!empty($faceicons['from'])){
			$tos = array();
			foreach($faceicons['to'] as $v) $tos[] = '<img src="'.cls_env::mconfig('cms_abs').$v.'">';
			$source = str_replace($faceicons['from'],$tos,$source);
			unset($tos);
		}
		return $source;
	}
	// type: 0-br, 1-</p>, 2-<p style='XXXX'>
	public static function RandStr($type=0){
		$str = ''; 
		for($i = 0;$i < mt_rand(5,15);$i++)  $str .= chr(mt_rand(0,59)).chr(mt_rand(63,126));
		$tags = array('a','b','i','em','span'); 
		$tag = $tags[mt_rand(0, 4)];
		$str = "<$tag style='display:none'>$str</$tag>"; //font��������
		if($type==1){
			return mt_rand(0, 1) ? '</p>' : $str.'</p>'; //һ���ּ�,һ���ֲ���
		}elseif($type==2){
			return mt_rand(0, 1) ? '<p>' : '<p>'.$str; //һ���ּ�,һ���ֲ���
		}else{
			return mt_rand(0, 1) ? '<br />'.$str : $str.'<br />'; //һ���ּ���ǰ,һ���ֲ����ں�
		}
	}
		
	
	# ��������(�б��Զ�ά������list/��ά������single/�ִ����string/��ҳmp/�ɶ���Ȩ�޷���pmid)ȡ����Ӧ�ı�ǩ����
	public static function TagClassByType($Type = 'list'){
		$ClassArray = array();
		switch($Type){
			case 'list':
				$ClassArray = array(
					'archives','outinfos','functions','members','searchs','msearchs','commus','catalogs','mccatalogs','mcatalogs','pushs',
					'farchives','fromids','keyword','votes','vote','nownav','mnownav','images','files','medias','flashs','texts','advertising',
				);
			break;
			case 'single':
				$ClassArray = array('archive','member','farchive','commu','cnode','mcnode','acount','mcount','image','file','flash','media','fromid',);
			break;
			case 'string':
				$ClassArray = array('freeurl','fragment','date','text','field','regcode',);
			break;
			case 'mp':
				$ClassArray = array('archives','catalogs','commus','farchives','functions','images','mccatalogs','members','searchs','msearchs','outinfos','text','votes',);
			break;
			case 'pmid':
				$ClassArray = array('archive','member','farchive','commu',);
			break;
		}
		return $ClassArray;
	}
	
	# �ڼ򻯱�ǩ����ʱ��ֻҪΪ�ջ�Ϊ0�Ϳ��������Key
	public static function UnsetVars(){
		if(empty(self::$UnsetVars)){
			self::$UnsetVars = array(
				'casource','cainherit','caidson','urlmode','chsource','space','ucsource','detail','rec','orderby','orderby1','orderstr','startno','wherestr','simple','alimits',
				'fmode','date','time','tmode','width','height','maxwidth','maxheight','expand','emptyurl','emptytitle','dealhtml','trim','badword','wordlink','nl2br','randstr',
				'next','chid','caid','mid','aid','func','mpfunc','sqlstr','vid','vsource','vids','chdata','js','checked','cnid','cnsource','level','caids','limits','letter','ids',
				'validperiod','thumb','asc','chids','nochids','val','tname','disabled','face','source','pmid','isfunc','isall','type','id','coids','idsource','mode','arid','mp',
				'length','ttl','timeout','fee','color','ellip','vieworder','classid1','classid2','injs',
			);
			foreach(array(0,1,2) as $k){
				self::$UnsetVars[] = 'source'.$k;
				self::$UnsetVars[] = 'ids'.$k;
			}
			$cotypes = cls_cache::Read('cotypes');
			foreach($cotypes as $k => $v){
				self::$UnsetVars[] = 'cosource'.$k;
				self::$UnsetVars[] = 'coinherit'.$k;
				self::$UnsetVars[] = 'ccid'.$k;
				self::$UnsetVars[] = 'ccidson'.$k;
				self::$UnsetVars[] = 'ccids'.$k;
			}
			$grouptypes = cls_cache::Read('grouptypes');
			foreach($grouptypes as $k => $v){
				self::$UnsetVars[] = 'source'.(10+$k);
				self::$UnsetVars[] = 'ids'.(10+$k);
				self::$UnsetVars[] = 'ugid'.$k;
			}
		}
		return self::$UnsetVars;
	}
	
	# �ڼ򻯱�ǩ����ʱ��ֻҪֵΪ��Ӧ�����ڵ�ֵ�Ϳ��������Key
	public static function UnsetVars1(){
		$UnsetVars1 = array(
			'val' => array('v',),
			'limits' => array('10',),
		);
		return $UnsetVars1;
	}
	
	# ȡ�ñ�ǩ�������ͣ�$isFragment�Ƿ���Ƭ��Ҫ�ı�ǩ����
	public static function TagClass($isFragment = false){
		$ClassArray = array(
			'archives' => '�ĵ��б�',
			'catalogs' => '��Ŀ�б�',
			'members' => '��Ա�б�',
			'commus' => '�����б�',
			'farchives' => '�����б�',
			'pushs' => '�����б�',
			'mccatalogs' => '��Ա�ڵ��б�',
			'outinfos' => '���ɵ����б�',
			'functions' => '�Զ������б�',
			'searchs' => '�ĵ������б�',
			'msearchs' => '��Ա�����б�',
			'keyword' => '�ؼ����б�',
			'fromids' => '�ܹ������б�',
			'nownav' => '��Ŀ����',
			'mcatalogs' => '�ռ���Ŀ�б�',
			'mnownav' => '�ռ���Ŀ����',
			'fragment' => '��Ƭ����',
			'archive' => '�����ĵ�',
			'member' => '������Ա',
			'farchive' => '��������',
			'commu' => '��������',
			'cnode' => '��Ŀ�ڵ�',
			'mcnode' => '��Ա�ڵ�',
			'acount' => '�ĵ�����',
			'mcount' => '��Ա����',
			'text' => '�ı�����',
			'images' => 'ͼ���б�',
			'image' => 'ͼƬģ��',
			'files' => '�����б�',
			'file' => '����ģ��',
			'flashs' => 'Flash�б�',
			'flash' => 'Flashģ��',
			'medias' => '��Ƶ�б�',
			'media' => '��Ƶģ��',
			'texts' => '�ı����б�',
			'fromid' => 'ָ��ID����',
			'date' => 'ʱ������',
			'field' => '�ֶα���ֵ',
			'regcode' => '��֤����',
			'freeurl' => '����ҳURL',
			'votes' => 'ͶƱ�б�',
			'vote' => 'ͶƱѡ���б�',
		);
		if($isFragment){
			$ClassArray = array('' => '�Զ�ģ��') + $ClassArray;
			foreach(array('searchs','msearchs','nownav','mcatalogs','mnownav','fragment',) as $Key) unset($ClassArray[$Key]);
		}
		return $ClassArray;
	}
	
	
}
