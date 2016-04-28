<?PHP
/**
* ��ǩ������࣬�������ͱ�ǩ������Ļ��࣬
* 
* ֻ��cls_Parse��ʹ�ã��̳�cls_Parse�ķ��������о�̬�������磺$_mp��$G(����)��$_da(����)��ģ������߲���ֱ�ӽӴ�����(��ν�һЩ�������ⲿ����?????)
* ��cls_Parse�����ⲿģ�岻��ֱ�ӽӴ�����
*/

defined('M_COM') || exit('No Permission');
abstract class cls_TagParseBase extends cls_BasePage{
	
    const TAG_MP_OFFSET = 2;					# ��ҳ�����е�ƫ����
	protected $tag = array();					# ��ǰ��ǩ����
	protected $_TagSqlBaseStr = NULL;			# �ݴ洦��õı�ǩSQL���Ա����ã�ֻ�ڲ������ͱ�ǩ����Ч
	
	abstract protected function TagReSult();	# �������͵ı�ǩ����ͳһ�Ľ�����ط���
	
	# ��ȡһ����ǩ�����ݽ����Ψһ�ⲿ���
	public static function OneTag($tag){
		# �������
		if(!($TagClass = @$tag['tclass'])){
			throw new cls_ParseException('��ǩ'.@$tag['ename'].'�ķ���δ����');
		}
		
		# ĳЩ���͵ı�ǩ�����๲��
		$_TagClassTrans = array( # ��ǩ����ת����ǰ��ʹ�ú��ߵĴ�����
			'advertising' => 'farchives',
			'acount' => 'archives',
			'mcount' => 'members',
			'flashs' => 'medias',
			'flash' => 'medias',
			'files' => 'medias',
			'file' => 'medias',
			'media' => 'medias',
			'image' => 'images',
			'vote' => 'votes',
		);
		if(isset($_TagClassTrans[$TagClass])) $TagClass = $_TagClassTrans[$TagClass];
		
		$TagClassName = "cls_Tag_$TagClass";
		if(!$TagClassName || !class_exists($TagClassName)){
			throw new cls_ParseException("��ǩ������[$TagClassName]δ����");
		}
		
		$_TagInstance = new $TagClassName($tag);
		try{
			$Return = $_TagInstance->TagFetch();
		}catch(cls_ParseException $e){ # �����Ժ������������ǩ������Ϣ��Ŀǰ�Ǻ��Է��ؿ�ֵ
			$Return = $_TagInstance->_TagError($e->getMessage(),$TagClass);
		}
		return $Return;
/*
		if(!empty($tag['ename']) && $_mdebug = cls_env::GetG('_mdebug')) $_mdebug->setvar('tag',$tag['ename']); # ����SQL����
		$this->_Set('G.tag',$tag); # ��$G���ݴ浱ǰ��ǩ��ģ������Ҫ��????
		
		# ���ݱ�ǩ���ͣ�������Ӧ�Ĵ�����
		cls_env::SetG('_sqlintag',true); # ��ǩSQL�������ã���Ҫɾ����
		if(self::_GetTagInstance($tag)){
			$re = self::$_TagInstance->TagFetch();
		}else $re = '';
		cls_env::SetG('_sqlintag',false); # ��ǩSQL�������ã���Ҫɾ����
		return $re;
*/		
	}
	
    function __construct($tag = array()){
		$this->tag = $tag;
    }
	
	# ��ǩ�����������ȡ���
	protected function TagFetch(){
		$this->_TagInit(); # ��ʼ����ǰ��ǩ
		$this->TagMpInfo(); # �ȷ�����ҳ���������
		$Return = $this->TagReSult();
		return $Return;
	}
	
	# ���½ӿڣ��Ժ������Ҫ��׽��ǩ�����Ĵ�����Ϣ����չʾ��Ŀǰֻ�ǽ������Ϊ��ֵ
	protected function _TagError($Msg,$TagClass){
		#if(_08_DEBUGTAG) throw new cls_ParseException($Msg);	
		return in_array($TagClass,cls_Tag::TagClassByType('string')) ? '' : array();
	}
	
	# ��Sql�ĸ�ʽ������
	protected function TagResultBySql(){
		$ReturnArray = array();
		if($sqlstr = $this->TagSqlStr()){
			$ReturnArray = self::$db->ex_fetch_array($sqlstr,intval(@$this->tag['ttl']));
			foreach($ReturnArray as $k => &$v){
				$v = $this->TagOneRecord($v); # ���ؽ���ĵ�����¼����
				$v['sn_row'] = $k + 1;
			}
		}
		return $ReturnArray;
	}
	
	# �����ǩ��SQL��䣬$IsCount:ͳ�Ʋ�ѯ(true)/���ݲ�ѯ(false)
	protected function TagSqlStr($IsCount = false){
		# ����SqlStr������
		if(is_null($this->_TagSqlBaseStr)){ # ֮ǰδ�����
			$SqlStr = $this->iTagSqlBaseStr();
			$this->_TagSqlBaseStr = $SqlStr;
		}else{ # ����֮ǰ�����˵Ľ��
			$SqlStr = $this->_TagSqlBaseStr;
		}
		
		if($SqlStr){
			# ���봦�� ������ѯ/����ͳ�Ʋ�ѯ ���ֽ��
			if(!$IsCount){ # �������ݲ�ѯ
				$SqlStr .= $this->iTagOrderStr();
				$SqlStr .= $this->iTagLimitStr();
			}else{
				$SqlStr = $this->SqlStrTransToCount($SqlStr);
			}
		}
		return $SqlStr;
	}
	
	# ����SQL����Ҫ����(select��from��where)
	protected function iTagSqlBaseStr(){
		if(!empty($this->tag['isall'])){ #�û���ȫ����sqlstr
			$sqlstr = $this->TagHandWherestr();
		}else{ # ���ݱ�ǩ����ƴ��sqlstr
			$sqlstr = $this->CreateTagSqlBaseStr();
		}
		return $sqlstr;
	}
	
	# ��ǩ�������ֶ������wherestr����
	protected function TagHandWherestr(){
		$wherestr = '';
		if(!empty($this->tag['wherestr'])){
			$wherestr = empty($this->tag['isfunc']) ? $this->tag['wherestr'] :  @EvalFuncInTag($this->tag['wherestr']);
		}
		return $wherestr ? $wherestr : '';
	}
	
	# ����ORDER BY�����ִ�
	protected function iTagOrderStr(){
		$OrderStr = empty($this->tag['orderstr']) ? '' : trim($this->tag['orderstr']);
		$OrderStr = $this->TagCustomOrderStr($OrderStr);
		$OrderStr = $OrderStr ? " ORDER BY $OrderStr" : $this->TagDefaultOrderStr();
		$OrderStr = preg_replace('/[^ \w\.\,]/', '', $OrderStr); # ��ʱ�����Ժ�Ҫʹ������ƴװ�õ�SQL
		return $OrderStr;
	}
	
	# ����LIMIT�ִ�
	protected function iTagLimitStr(){
		$Limits = $this->TagInitLimits();
		$Start = $this->TagInitStart();
		return " LIMIT $Start,$Limits";
	}
	
	# ����ʼλ��
	protected function TagInitStart(){
		$Limits = $this->TagInitLimits();
		$Start = empty($this->tag['mp']) ? 0 : ((int)cls_Parse::Get('_mp.nowpage') - 1) * $Limits;
		if(!empty($this->tag['startno'])) $Start += (int)$this->tag['startno'];
		return $Start;
	}
	
	# �����б���������
	protected function TagInitLimits(){
		return empty($this->tag['limits']) ? 10 : (int)$this->tag['limits'];
	}
	
	# �����ҳ��������ʾҳ��
	protected function TagInitLength(){
		return empty($this->tag['length']) ? 10 : (int)$this->tag['length'];
	}
	
	
	# ������SQL���תΪSELECT COUNT��SQL���
	protected function SqlStrTransToCount($SqlStr){
		if(!($SqlStr = (string)$SqlStr)) return '';
		if(preg_match('/^(.+?)\s+GROUP\s+BY(.+)$/is',$SqlStr,$matches)){
			return "SELECT COUNT(DISTINCT $matches[2]) ".stristr($matches[1],'FROM');
		}else{
			return 'SELECT COUNT(*) '.stristr($SqlStr,'FROM');
		}
	}

	# ǿ����������
	protected function ForceIndexSql($tbl = ''){
		$Return = '';
		if(empty($this->tag['forceindex'])) return $Return;
		$na = array_filter(explode('.',$this->tag['forceindex']));
		if(empty($tbl) && count($na)  == 1){
			$Return = $na[0];
		}elseif(count($na)  == 2){
			$Return =$tbl == $na[0] ? $na[1] : '';
		}
		return $Return ? " FORCE INDEX ($Return)" : '';
	}
	
	# �׳�һ����ǩ��������
	protected function TagThrowException($Msg){
		throw new cls_ParseException("��ǩ[����:{$this->tag['ename']}][����:{$this->tag['tclass']}]��������$Msg");	
	}
	
	protected function TagMpInfo(){		
		if(!empty(self::$_mp['_MpDone']) || empty($this->tag['mp'])) return;
		
		if(!in_array($this->tag['tclass'],cls_Tag::TagClassByType('mp'))) return;
		
		#��ʼ�� $_mp
		self::$_mp['limits'] = $this->TagInitLimits();
		self::$_mp['length'] = $this->TagInitLength();
		self::$_mp['simple'] = empty($this->tag['simple']) ? 0 : 1;
		
		# ��ҳ����self::$_mp['acount']�Ȳ�ͬ���ͱ�ǩ�Ĳ��컯����
		$this->TagCustomMpInfo();
		
		# ��ҳͳ����Ϣ
		if(!empty($this->tag['alimits'])){
			self::$_mp['acount'] = min(self::$_mp['acount'],$this->tag['alimits']);
		}
		if(self::$_mp['acount']){
			self::$_mp['pcount'] = ceil(self::$_mp['acount'] / self::$_mp['limits']);
		}
		self::$_mp['nowpage'] = max(1,min(self::$_mp['nowpage'],self::$_mp['pcount']));

		# Ϊ����Ӧԭʼ��ǩ��������Ӧ��ĸ�ֵ��ͬʱ��ȫself::$_mp�Ļ�������
		self::$_mp['s_num'] = empty(self::$_mp['s_num']) ? self::$_mp['pcount'] : min(self::$_mp['pcount'],self::$_mp['s_num']);
		self::$_mp['mppage'] = self::$_mp['nowpage'];
		self::$_mp['mpcount'] = self::$_mp['pcount'];
		self::$_mp['mpacount'] = self::$_mp['acount'];

		# ȡ�÷�ҳ����
		self::$_mp['mpnav'] = $this->TagMpNav();
		
		# �����Ѵ����ҳ�ı��
		self::$_mp['_MpDone'] = true;	
	}
	
	# �������ͣ�'archives','catalogs','mccatalogs','farchives','commus','members','votes','searchs','msearchs','pushs',��Ĭ��ʹ�ñ�������������Ҫ�ھ������ж���
	protected function TagCustomMpInfo(){
		if($sqlstr = $this->TagSqlStr(true)){
			if($num = self::$db->result_one($sqlstr,intval(@$this->tag['ttl']))){
				self::$_mp['acount'] = $num;
			}
		}
	}
	
	protected function TagMpNav(){
		
		# �����ڷ�ҳ
		if(self::$_mp['pcount'] == 1) return '';
	
		# ����ҳ�뷶Χ
		list($from,$to) = $this->MpPageFromTo();
		
		// ����ı���ҳ����Ҫȫ��ҳ��
		if($this->tag['tclass']=='text'){ 
			self::$_mp['mpurls'] = $this->MpUrls(); //����:0,0
		}else{ //���ı���ҳ����from,to����
			self::$_mp['mpurls'] = $this->MpUrls($from,$to);	
		} 
		
		foreach(array('mpstart' => 1,'mpend' => self::$_mp['pcount'],'mppre' => self::$_mp['nowpage'] - 1,'mpnext' => self::$_mp['nowpage'] + 1,) as $k => $v){
			self::$_mp[$k] = $this->MpLink($v);
		}
		
		# ��ҳ��������		
		$_NavCode = '';
		if(self::$_mp['nowpage'] - self::TAG_MP_OFFSET > 1 && self::$_mp['pcount'] > self::$_mp['length']){
			$_NavCode .= '<a href="'.self::$_mp['mpstart'].'" class="p_redirect" target="_self">|<</a>';
		}
		if(self::$_mp['nowpage'] > 1 && !self::$_mp['simple']){
			$_NavCode .= '<a href="'.self::$_mp['mppre'].'" class="p_redirect" target="_self"><<</a>';
		}
		for($i = $from; $i <= $to; $i++){
			if($i == self::$_mp['nowpage']){
				$_NavCode .= '<a class="p_curpage">'.$i.'</a>';
			}else{
				$_NavCode .= '<a href="'.self::$_mp['mpurls'][$i].'" class="p_num" target="_self">'.$i.'</a>';
			}
		}
		if(self::$_mp['nowpage'] < self::$_mp['pcount'] && !self::$_mp['simple']){
			$_NavCode .= '<a href="'.self::$_mp['mpnext'].'" class="p_redirect" target="_self">>></a>';
		}
		if($to < self::$_mp['pcount']){
			$_NavCode .= '<a href="'.self::$_mp['mpend'].'" class="p_redirect" target="_self">>|</a>';
		}
		
		if($_NavCode){
			if(!empty(self::$_mp['simple'])){ # ��ʾ��¼������ҳ��
				$_TotalCode = '<a class="p_total">&nbsp;'.self::$_mp['acount'].'&nbsp;</a><a class="p_pages">&nbsp;'.self::$_mp['nowpage'].'/'.self::$_mp['pcount'].'&nbsp;</a>';
				$_NavCode = $_TotalCode.$_NavCode;
			}
			$_NavCode = '<div class="p_bar">'.$_NavCode.'</div>';
		}
		return $_NavCode;
	}
	
	# ����ָ��ҳ�뷶Χ�����ط�ҳ����Url���顣��Ҫ��MpInfo֮�����ִ��
	# $from = 0���ӵ�1ҳ��ʼ��$to = 0������ĩҳΪֹ��
	protected function MpUrls($from = 0,$to = 0){
		$from = empty($from) ? 1 : max(1,(int)$from);
		$to = empty($to) ? self::$_mp['pcount'] : min(self::$_mp['pcount'],(int)$to);
		$Urls = array();
		for($i = $from; $i <= $to; $i++){
			$Urls[$i] = $this->MpLink($i);
		}
		return $Urls;
	}
	# ����ָ��ҳ�룬�������ҳ����Url����Ҫ��MpInfo֮�����ִ��
	protected function MpLink($PageNo = 0){
		$PageNo = min(self::$_mp['pcount'],max(1,(int)$PageNo)); #ֻ�����0->��ҳ��֮��ѡ��
		if(self::$_mp['static'] && $PageNo <= self::$_mp['s_num']){
			$UrlPre = self::$_mp['surlpre'];
		}else{
			$UrlPre = self::$_mp['durlpre'];
		}
		$Url = cls_url::m_parseurl($UrlPre,array('page' => $PageNo));
		return $Url;
	}
	
	# ����ҳ�뷶Χ
	protected function MpPageFromTo(){
		if(self::$_mp['length'] > self::$_mp['pcount']){
			$from = 1;
			$to = self::$_mp['pcount'];
		}else{
			$from = self::$_mp['nowpage'] - self::TAG_MP_OFFSET;
			$to = $from + self::$_mp['length'] - 1;
			if($from < 1){
				$to = self::$_mp['nowpage'] + 1 - $from;
				$from = 1;
				if($to - $from < self::$_mp['length']) $to = self::$_mp['length'];
			}elseif($to > self::$_mp['pcount']){
				$from = self::$_mp['pcount'] - self::$_mp['length'] + 1;
				$to = self::$_mp['pcount'];
			}
		}
		return array($from,$to);
	}
	
	# Ϊ�˼���������δ���庯�������¿յĽӿڷ��� *****************************************************
	
	# ��ʼ����ǰ��ǩ
	protected function _TagInit(){}
		
	# ��ͬ���ͱ�ǩ�ķ��ؽ���ĵ�����¼����
	protected function TagOneRecord($OneRecord){
		return $OneRecord;
	}
	
	# ��ͬ���ͱ�ǩ�������ִ�����
	protected function TagCustomOrderStr($OrderStr){
		return $OrderStr;
	}
	
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return '';
	}
	
	
}
