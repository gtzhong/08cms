<?PHP
!defined('M_COM') && exit('No Permisson');


/* ------------------------------------------------------------------ 
���º������Ѿ��ƶ���������У�
��ʱ����ֻΪ��ʱ���ݾɰ汾
��201308֮��Ŀ����У�����������ʹ�����¼��ݺ�����������һ�������������ȫ�滻����ɾ�����ű���
------------------------------------------------------------------ */


# �Զ����¾�̬����ͣʱ�εķ�������ʱ�����Լ��ݾɰ汾
function static_pause($val = ''){
	return cls_Static::InParsePeriod($val);
}
# �ַ���ת�ɱ�ǩ���飨����ת�ɷǷ�װ��ʶ���ݣ�����ʱ�����Լ��ݾɰ汾
function _08_code_to_tagarr($string){
	return cls_TagAdmin::CodeToTagArray($string);
}
# ����(����)ָ����Ա�ľ�̬�ռ䡣��ʱ�����Լ��ݾɰ汾
function mspace_static($mid = 0){
	return cls_Mspace::ToStatic($mid);
}
# ����ҳ��Url����ʱ�����Լ��ݾɰ汾
function _one_freeurl($fid = 0){
	return cls_FreeInfo::Url($fid);
}
# ������Ե�ַͼƬ������ָ����С������ͼ��������ʾurl����ʱ�����Լ��ݾɰ汾
function thumb($dbstr,$width = 0,$height = 0,$mode = 0){
	return cls_atm::thumb($dbstr,$width,$height,$mode);
}
# ����������ͼƬ��С����ʱ�����Լ��ݾɰ汾
function imagewh($width=0,$height=0,$maxwidth=0,$maxheight=0){
	return cls_atm::ImageSizeKeepScale($width,$height,$maxwidth,$maxheight);
}
# ���ػ�Ա��Ϣ�е�δ��֤�ֶ���Ϣ����ʱ�����Լ��ݾɰ汾
function hidden_uncheck_cert(&$info){
	cls_UserMain::HiddenUncheckCertField($info);
}
# ȡ��Ͷ���ֶε������Ϣ����ʱ�����Լ��ݾɰ汾
function field_votes($fname,$type,$id,$onlyvote = 1){
	return cls_field::field_votes($fname,$type,$id,$onlyvote);
}
# ��ȡdynamic/htmlcac�еĻ�����·������������ھʹ���Ŀ¼����ʱ�����Լ��ݾɰ汾
function htmlcac_dir($mode='arc',$spath=''){
	return cls_cache::HtmlcacDir($mode,$spath);
}
# ����Sitemap��̬����ʱ�����Լ��ݾɰ汾
function sitemap_static($map){
	return cls_SitemapPage::Create(array('map' => $map,'inStatic' => true));
}
# ��ǰ��Ա�Ƿ���Ȩ�����������ĵ��еĸ���//�鸽����ֵ�����˷�Χ����ʱ�����Լ��ݾɰ汾
function arc_allow_down($item){//��ǰ��Ա�Ƿ���Ȩ�����������ĵ��еĸ���//�鸽����ֵ�����˷�Χ
	return cls_ArcMain::AllowDown($item);
}
# �ĵ���ģ������д����ݿ�����ж�������Ҫ׷�Ӵ����������ʱ�����Լ��ݾɰ汾
function arc_parse(&$item,$inList = false){
	return cls_ArcMain::Parse($item,$inList);
}
# ����Ȩ�޷�����������Ȩ��ԭ����ʱ�����Լ��ݾɰ汾
function mem_noPm($info = array(),$pmid=0){
	return cls_Permission::noPmReason($info,$pmid);
}
# �ڵ�ҳ��(��ϵͳ��ҳ)���ɾ�̬����ʱ�����Լ��ݾɰ汾
function index_static($cnstr = '',$addno = 0){
	return cls_CnodePage::Create(array('cnstr' => $cnstr,'addno' => $addno,'inStatic' => true));
}
# ��ԱƵ��ҳ�����ɾ�̬����ʱ�����Լ��ݾɰ汾
function mindex_static($cnstr = '',$addno = 0){
	return cls_McnodePage::Create(array('cnstr' => $cnstr,'addno' => $addno,'inStatic' => true));
}

# ����ָ���ı����ļ�����ʱ�����Լ��ݾɰ汾
function file_down($file, $filename = ''){
	return cls_atm::Down($file, $filename);
}
# ģ�������жϺ�������ʱ�����Լ��ݾɰ汾
function tpl_exit($str = ''){
	return cls_Parse::Message($str);
}
# ģ�������������ʱ�����Լ��ݾɰ汾 xxxxxxxxxxxxxxxxxxxx
function _E($SourceArray,$init=0,$add=array()){
	return cls_Parse::Active($SourceArray,$init);
}
# ģ�������������ʱ�����Լ��ݾɰ汾 xxxxxxxxxxxxxxxxxxxx
function _X(){
	return cls_Parse::ActiveBack();
}
# ģ�������������ʱ�����Լ��ݾɰ汾 xxxxxxxxxxxxxxxxxxxx
function _T($tag=array()){
	return cls_Parse::Tag($tag);
}

# �ռ���Ŀҳ�в��������ԭʼ��ǩ���õ��������飬��ʱ�����Լ��ݾɰ汾
function mcn_parse($info = array(),$ps=array()){
	return cls_Mspace::IndexAddParseInfo($info,$ps);
}

# ��ȡָ����Ա��ģ�巽������,$Key��setting/arctpls����ʱ�����Լ��ݾɰ汾
function load_mtconfig($mid=0,$Key='setting'){
	return cls_mtconfig::ConfigByMid($mid,$Key);
}

# ��ȡָ����Ա�ռ����ϣ���ʱ�����Լ��ݾɰ汾
function load_member($mid = 0,$ttl = 0){
	$re = array();
	if(!($re[0] = cls_Mspace::LoadMember($mid,$ttl))){
		return array();
	}
	$re[1] = cls_Mspace::LoadUclasses($mid,$ttl);
	$re[2] = cls_mtconfig::ConfigByMid($mid,'setting');
	$re[3] = cls_Mspace::LoadMcatalogs($re[0]['mtcid']);
	return $re;
}
# ��ȡָ����Ա�ĸ��˷������ϣ���ʱ�����Լ��ݾɰ汾
function loaduclasses($mid,$ttl = 0){
	return cls_Mspace::LoadUclasses($mid,$ttl);
}

# ɾ���� addslashes() ������ӵķ�б�ܣ�֧�����飬��ʱ�����Լ��ݾɰ汾
function mstripslashes($s){
	cls_Array::array_stripslashes($s);
	return $s;
}

# ��ȡ�����ǩ�ڶ����ģ�壬��ʱ�����Լ��ݾɰ汾
function rtagval($tname,$rt=1){
	return cls_tpl::rtagval($tname,$rt);
}

# ȡ�����и������� ID=>���� ���б����飬��ʱ�����Լ��ݾɰ汾
function fcaidsarr($chid = 0){
	return cls_fcatalog::fcaidsarr($chid);
}

# ȡ���������ͷ��� ID=>���� ���б����飬��ʱ�����Լ��ݾɰ汾
function ptidsarr(){
	return cls_pushtype::ptidsarr();
}
# ��ȡ�����ܹ���������
function get_commus_info(){
	return cls_commu::InitialInfoArray();
}
# ������Ŀ����� ID=>���� ���б����飬��ԴΪ��Ŀ�������������
function caidsarr($SourceArray,$chid = 0,$nospace = 0){
	return cls_catalog::ccidsarrFromArray($SourceArray,$chid,$nospace);
}
# ������Ŀ����� ID=>���� ���б����飬��ԴΪָ������ϵID
function ccidsarr($coid,$chid = 0,$nospace = 0){
	return cls_catalog::ccidsarr($coid,$chid,$nospace);
}
//ȡ�ó���ģ����в�ͬ����ģ���ѡ�����飬��ʱ�����Լ��ݾɰ汾
function mtplsarr($tpclass = 'archive',$chid = 0){
	return cls_mtpl::mtplsarr($tpclass,$chid);
}
# ȡ�����и���ģ�� ID=>���� ���б����飬��ʱ�����Լ��ݾɰ汾
function fchidsarr(){
	return cls_fchannel::fchidsarr();
}
# ȡ�����л�Աģ�� ID=>���� ���б����飬��ʱ�����Լ��ݾɰ汾
function mchidsarr(){
	return cls_mchannel::mchidsarr();
}
# ȡ���ĵ�ģ�� ID=>���� ���б����飬��ʱ�����Լ��ݾɰ汾
function chidsarr($all = 0,$noViewID = 0){
	return cls_channel::chidsarr($all,$noViewID);
}
//����������$arr�е�����ID�������¼�Ƕ�׵Ĺ�ϵ(pid)�����������򣬷�����������ϸ��������
function order_arr($arr = array(),$pid = 0){
	return cls_catalog::OrderArrayByPid($arr,$pid);
}

// ����$arr�У�����pid�µķ���id(����id)����ʱ�����Լ��ݾɰ汾
function son_ids($arr = array(),$pid = 0){
	return cls_catalog::OrderArrayByPid($arr,$pid,1);
}

// ����$arr�У�����id�µ���id����ʱ�����Լ��ݾɰ汾
function cnsonids($id,$arr){
	return cls_catalog::cnsonids($id,$arr);
}

// ȡ��ָ����Ŀ�������¼�id(���¼�)����ʱ�����Լ��ݾɰ汾
function son_ccids($nowid,$coid = 0){
	return cls_catalog::son_ccids($nowid,$coid);
}

// ȡ��ĳ����Ŀ��ָ����(level)���ϼ���Ŀ����ʱ�����Լ��ݾɰ汾
function p_ccid($nowid,$coid = 0,$level = 0){
	return cls_catalog::p_ccid($nowid,$coid,$level);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function cn_upid($id,&$arr,$level=0){
	return cls_catalog::cn_upid($id,$arr,$level);
}

// ��ȡ������������ϵ/��Ŀ���飬��ʱ�����Լ��ݾɰ汾
function uccidsarr($coid,$chid = 0,$framein = 0,$nospace = 0,$viewp = 0,$id=0){
	return cls_catalog::uccidsarr($coid,$chid,$framein,$nospace,$viewp,$id);
}

// ��ȡ��ϵ���ƻ�ͼ�꣬��ʱ�����Լ��ݾɰ汾
function cnstitle($id,$mode,$sarr,$num=0,$showmode=0){
	return cls_catalog::cnstitle($id,$mode,$sarr,$num,$showmode);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function mcn_format($cnstr = '',$addno = 0){//��{$page}�Ľڵ��ļ�(���ϵͳ��Ŀ¼)
	return cls_node::mcn_format($cnstr,$addno);
}

// �г��ڵ����� ����/��ϵ ��Ŀ�����Ϣ����ʱ�����Լ��ݾɰ汾
function cn_parse($cnstr,$listby=-1){
	return cls_node::cn_parse($cnstr,$listby);
}

// �г���Ա�ڵ����� ����/��ϵ/��ϵ ��Ŀ�����Ϣ����ʱ�����Լ��ݾɰ汾
function m_cnparse($cnstr){
	return cls_node::m_cnparse($cnstr);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function re_cnode(&$item,&$cnstr,&$cnode){
	cls_node::re_cnode($item,$cnstr,$cnode);
}

// ���ݽڵ�$cnstr�����ؽڵ����ƣ���ʱ�����Լ��ݾɰ汾
function cnode_cname($cnstr){
	return cls_node::cnode_cname($cnstr);
}

// ���ݽڵ��ִ������ػ�Ա�ڵ���Ϣ����ʱ�����Լ��ݾɰ汾
function read_mcnode($cnstr){
	return cls_node::read_mcnode($cnstr);
}

// ���ݻ�Ա�ڵ���Ϣ���õ��ڵ��ִ�����ʱ�����Լ��ݾɰ汾
function mcnstr($temparr){
	return cls_node::mcnstr($temparr);
}

// ���ݽڵ��ִ������ؽڵ���Ϣ����ʱ�����Լ��ݾɰ汾
function cnodearr($cnstr,$NodeMode = 0){
	return cls_node::cnodearr($cnstr,$NodeMode);
}

// ���ݻ�Ա�ڵ��ִ������ؽڵ���Ϣ����ʱ�����Լ��ݾɰ汾
function mcnodearr($cnstr,$noauto=0){
	return cls_node::mcnodearr($cnstr,$noauto);
}

// ���ݽڵ��ִ������ؽڵ���Ϣ����ʱ�����Լ��ݾɰ汾
function read_cnode($cnstr,$NodeMode = 0){
	return cls_node::read_cnode($cnstr,$NodeMode);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function cn_format($cnstr,$addno,&$cnode){//��{$page}�Ľڵ��ļ���ʽ(���ϵͳ��Ŀ¼)
	return cls_node::cn_format($cnstr,$addno,$cnode);
}

// ���ݻ�Ա�ڵ��ִ����õ��ڵ�����(�������Զ���ڵ�)����ʱ�����Լ��ݾɰ汾
function mcnode_cname($cnstr){
	return cls_node::mcnode_cname($cnstr);
}

// ��url������������ʱ�����Լ��ݾɰ汾
function domain_bind($url){
	return cls_url::domain_bind($url);
}

// ����ϵͳ����[����]��url����ʱ�����Լ��ݾɰ汾
function remove_index($url){
	return cls_url::remove_index($url);
}

// ����html�ֶ�ʱ�����������url����ʱ�����Լ��ݾɰ汾
function html_atm2tag(&$str){
	cls_url::html_atm2tag($str);
}

// �������ݿⱣ��·�����ж��Ƿ�Ϊ ftp�������Ǳ��ظ���,ֻ�ܷ���������������ʱ�����Լ��ݾɰ汾
function is_remote_atm($str){
	return cls_url::is_remote_atm($str);
}

// ����url���ж��Ƿ�Ϊ�����ļ�(����)����ʱ�����Լ��ݾɰ汾
function islocal($url,$isatm=0){
	return cls_url::islocal($url,$isatm);
}

// �� ԭʼ�����url�ַ� ת��Ϊ ���������url����ʱ�����Լ��ݾɰ汾
function tag2atm($str,$ishtml=0){
	//ishtml:�����1�Ļ��������html�ı���Ҫ���������Ƕ�ĸ�������ʱ�����Լ��ݾɰ汾
	return cls_url::tag2atm($str,$ishtml);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function arr_tag2atm(&$item,$fmode=''){
	cls_url::arr_tag2atm($item,$fmode);
}

// ����ʱ�����Լ��ݾɰ汾
function local_file($url){
	return cls_url::local_file($url);
}

// ����url�õ�����·��//incftpͬʱ����ftp��url//����ǵ����������򷵻�ԭurl����ʱ�����Լ��ݾɰ汾
function local_atm($url,$incftp=0){
	return cls_url::local_atm($url,$incftp);
}

// ���ݱ���·�����õ�����ͼ�ı���·��������ʱ�����Լ��ݾɰ汾
function thumb_local($local,$width,$height){//���ݱ���·�����õ�����ͼ�ı���·����
	return cls_url::thumb_local($local,$width,$height);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function fetch_arc_tpl($chid,$caid = 0){
	return cls_tpl::arc_tpl($chid, $caid);
}

// ����ģ�棬��ʱ�����Լ��ݾɰ汾
function load_tpl($tplname,$rt=1){
	return cls_tpl::load($tplname, $rt);
}

// ��ȡģ�����ƣ���ʱ�����Լ��ݾɰ汾
function tplname($type,$id,$name){
	return cls_tpl::CommonTplname($type, $id, $name);
}

// ��ȡ��Ŀ�ڵ�ģ�����ƣ���ʱ�����Լ��ݾɰ汾
function cn_tplname($cnstr,&$cnode,$addno=0,$tn=''){
	return cls_tpl::cn_tplname($cnstr, $cnode, $addno, $tn);
}

// ��ȡ��Ա�ڵ�ģ�����ƣ���ʱ�����Լ��ݾɰ汾
function mcn_tplname($cnstr,$addno=0){
	return cls_tpl::mcn_tplname($cnstr, $addno);
}

// �������⾲̬��ַ����ʱ�����Լ��ݾɰ汾
function en_virtual($str,$suffix=0,$novu=0){
	return cls_url::en_virtual($str,$novu);
}

// ��Ե�������url�õ����浽���ݿ��еĸ�ʽ����ʱ�����Լ��ݾɰ汾
function save_atmurl($url){
	return cls_url::save_atmurl($url);
}

//�ο�tag2atm(�� tag2atm ���� ??? )����ʱ�����Լ��ݾɰ汾
function view_atmurl($url=''){
	return cls_url::view_atmurl($url);
}

// ��ȡ����url����ʱ�����Լ��ݾɰ汾
function view_farcurl($id,$url=''){
	return cls_url::view_farcurl($id,$url);
}


//���ݽڵ��ִ������½ڵ�������ӣ���ʱ�����Լ��ݾɰ汾
function view_cnurl($cnstr,&$cnode){
	cls_url::view_cnurl($cnstr,$cnode);
}

//��url��ʽ����ʾ������ ������,ϵͳ����[����]��url����ʱ�����Լ��ݾɰ汾
function view_url($url){
	return cls_url::view_url($url);
}

// ˵��������ʱ�����Լ��ݾɰ汾
function m_parseurl($u,$s = array()){
	return cls_url::m_parseurl($u,$s);
}

//��ȡ�ĵ�url����Ҫ���ֶΣ���ʱ�����Լ��ݾɰ汾
function view_arcurl(&$archive,$addno = 0){
	return cls_ArcMain::Url($archive,$addno);
}

//��ȡ�ĵ�url����ʱ�����Լ��ݾɰ汾
function view_mspcnurl(&$info,$params = array(),$dforce = 0){//$dforceǿ�ƶ�̬
	return cls_Mspace::IndexUrl($info, $params, $dforce);
}

//��ȡ��Ա�ռ�url����Ҫ���ֶΣ���ʱ�����Լ��ݾɰ汾
function view_mcnurl(&$cnstr,&$cnode){
	return cls_url::view_mcnurl($cnstr,$cnode);
}


//�ú����д�ɾ������ʱ�����Լ��ݾɰ汾
function str_js_src($val){
    return cls_phpToJavascript::str_js_src($val);
}

//��ȫ�ִ�����ʱ�����Լ��ݾɰ汾
function safestr($string){
	return cls_string::SafeStr($string);
}

//�����ȼ����ı�����ʱ�����Լ��ݾɰ汾
function cutstr($string, $length, $dot = ' ...') {
	return cls_string::CutStr($string, $length, $dot);
}

//����������ͳ����������ʱ�����Լ��ݾɰ汾
function ccstrlen($str){
	return cls_string::WordCount($str);
}

//����html�ı��е���ʽ��js�ȣ���ʱ�����Լ��ݾɰ汾
function html2text($str){
	return cls_string::HtmlClear($str);
}

//����ת������ʱ�����Լ��ݾɰ汾
function convert_encoding($from,$to,$source){
	return cls_string::iconv($from,$to,$source);
}

//���ص绰,�ֻ�,�ʼ�,qq,ip���м�һ����,��ʱ�����Լ��ݾɰ汾
function sub_replace($str,$char=''){
	return cls_string::SubReplace($str,$char);
}

//�и��ؼ��ʣ���ʱ�����Լ��ݾɰ汾
function keywords($nstr, $ostr=''){
	return cls_string::keywords($nstr, $ostr);
}

//�������㣬��ʱ�����Լ��ݾɰ汾
function wordcount($string, $flag = false){
	return cls_string::WordCount($string, $flag);
}

//�Ƿ�email����ʱ�����Լ��ݾɰ汾
function isemail($email){
	return cls_string::isEmail($email);
}

//�Ƿ����ڸ�ʽ����ʱ�����Լ��ݾɰ汾
function isdate($date, $mode = 0) {
	return cls_string::isDate($date, $mode);
}

# ��ʱ�����Լ��ݾɰ汾
function sys_cache2file($carr,$cname,$cacdir=''){
	return cls_CacheFile::cacSave($carr,$cname,$cacdir);
}

# ��ʱ�����Լ��ݾɰ汾(cecore)
function sys_cache($cname,$cacdir='',$noex = 0){
	return cls_cache::cacRead($cname,$cacdir,$noex);
}

//�ɷ���(�贫pname)����ʱ�����Լ��ݾɰ汾����ʹ�� mem_pmbypmid
function mem_permission($info = array(),$pname = '',$pmid=0){
	return _mem_noPm($info,$pmid) ? false : true;
}

# ��������ִ�����ʱ�����Լ��ݾɰ汾
function random($length, $onlynum = 0) {
	return cls_string::Random($length, $onlynum);
}

# ��ָ����ֵ���������������ʱ�����Լ��ݾɰ汾
function m_array_multisort(&$array,$orderkey = 'vieworder',$keepkey = false){
	cls_Array::_array_multisort($array,$orderkey,$keepkey);
}

# ׷��ָ������������ϼ�id����ʱ�����Լ��ݾɰ汾
function pccidsarr($ccid = 0,$coid = 0,$self = 0){
	return cls_catalog::Pccids($ccid,$coid,$self);
}

# ��������ȡ��һ�Σ���ʱ�����Լ��ݾɰ汾
function marray_slice($arr,$offset = 0,$length = 0){
	return array_slice($arr,$offset,$length,true);
}

# ��ʱ�����Լ��ݾɰ汾
function mmicrotime(){
	return microtime(TRUE);
}

//��ȡͨ�û��棬���ݾɰ汾(��Ϊʹ�úܹ㣬��Ҫ���ڱ���??)
function read_cache($CacheName,$BigClass = '',$SmallClass = '',$noExCache = 0){
	return cls_cache::Read($CacheName,$BigClass,$SmallClass,$noExCache);
}

//��ȫ�ֱ�����ʽ���ػ��棬���ݾɰ汾(��Ϊʹ�úܹ㣬��Ҫ���ڱ���??)
function load_cache($Keys = ''){
	return cls_cache::Load($Keys);
}

//ͨ�û�������·������ʱ�����Լ��ݾɰ汾
function cache_dir($CacheName=''){
	return cls_cache::CacheDir($CacheName);
}

//���ɻ����ֵ(�����ļ���)����ʱ�����Լ��ݾɰ汾
function cache_name($CacheName,$BigClass = '',$SmallClass = ''){
	return cls_cache::CacheKey($CacheName,$BigClass,$SmallClass);
}

//���ȶ�ȡ��չϵͳ�еĿ������û��棬��ʱ�����Լ��ݾɰ汾
function extend_cache($cname,$noex = 0){
	return cls_cache::exRead($cname,$noex);
}

//ǿ�ƴӻ����ļ��������뻺�棬��ʱ�����Լ��ݾɰ汾
function reload_cache($CacheName,$BigClass = '',$SmallClass = ''){
	return cls_cache::ReLoad($CacheName,$BigClass,$SmallClass);
}

//��ȡ����ģ���ʶ���棬��ʱ�����Լ��ݾɰ汾
function read_tag($TagType,$TagName){
	return cls_cache::ReadTag($TagType,$TagName);
}

//���������鱣�浽ͨ�û����ļ�����ʱ�����Լ��ݾɰ汾
function cache2file($carr,$cname,$ctype='',$noex = 0){
	return cls_CacheFile::Save($carr,$cname,$ctype,$noex);
}

//ɾ��ĳ��ͨ�û����Ӧ�Ļ����ļ�����ʱ�����Լ��ݾɰ汾
function del_cache($CacheName,$BigClass=''){
	return cls_CacheFile::Del($CacheName,$BigClass);
}

//ͨ���Զ�������ϵ ��sql�Ӿ䣬��ʱ�����Լ��ݾɰ汾
function self_sqlstr($coid,$ccids,$pre = ''){
	return cls_catalog::SelfClassSql($coid,$ccids,$pre);
}

//���վ�㼰�ֻ����Ƿ�رգ���ʱ�����Լ��ݾɰ汾
function if_siteclosed($noout = 0){
	cls_env::CheckSiteClosed($noout);
}

if ( !function_exists('sp_tplname') )
{//ȡ�ù���ҳ���ģ�壬��ʱ�����Լ��ݾɰ汾
    function sp_tplname($name,$NodeMode = 0){
		return cls_tpl::SpecialTplname($name,$NodeMode);
    }
}
function mapsql($x,$y,$diff = 0,$mode = 1,$fname){
	cls_dbother::MapSql($x,$y,$diff,$mode,$fname);
}

####################���º�����/include/common.fun.php��ֲ����##################
if ( !function_exists('message') )
{
    function message($str,$url = '', $mtime = 1250) {
        cls_message::show( $str, $url, $mtime );
    }
}

if ( !function_exists('ajax_info') )
{
    function ajax_info($str) {
    	cls_message::ajax_info($str);
    } 
}

if ( !function_exists('cumessage') )
{
    function cumessage($msg = '',$url=''){
    	cls_message::show($msg, $url);
    }
}
if ( !function_exists('template') )
{
   function template($spname='',$_da=array(),$NodeMode = 0){
	   return cls_tpl::SpecialHtml($spname,$_da,$NodeMode);
    } 
}

/**
 * ��������
 *
 * ͳһ��ע�ᡢ��¼��������м��ܣ������������㷨ʱ�ɼ���ά���ɱ�
 *
 * @param  string $passwrod ���ݼ���ǰ������
 * @return string $passwrod ���ؼ��ܺ������
 */
function encryptionPass($password) {
    return _08_Encryption::password($password);
}

#-------------------- ��include/admina.fun.php ��ֲ����
function amessage($str='', $url = '', $mtime = 1250) {
	cls_message::show($str, $url, $mtime);
}

#-------------------- ��adminm/func/main.php ��ֲ����
function mcmessage($str='', $url = '', $mtime = 1250){
	cls_message::show($str, $url, $mtime);
}
##############################################################################