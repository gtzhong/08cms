<?php
/**
 * CacheLock ������,��Ҫ��������cacheʧЧʱ�ĵ�����cache��ȡ����ֹ�����SQL����͸�����ݿ�
 * ���ڽ��PHP�ڲ���ʱ���������,ͨ���ļ�/eaccelerator���н��̼�����
 * ���û��ʹ��eaccelerator����н����ļ�������������ӦĿ¼�²�����Ӧ���ȵ���
 * ʹ����eaccelerator�����ڴ��д���������Խϸ�
 * ��ͬ����֮�䲢��ִ�У�����mysql innodb���м���
 * ������sunli��phplock�Ļ��������������޸�  http://code.google.com/p/phplock 
 * @author yangxinqi
 *
 */
class CacheLock
{
	//�ļ������·��
	private $path = null;
	//�ļ����
	private $fp = null;
	//������,����Խ������ԽС
	private $hashNum = 256;
	//cache key 
	private $name;
	//�Ƿ����eaccelerator��־
	private  $eAccelerator = false;
	
	/**
	 * ���캯��
	 * �������Ĵ��·������cache key�����ƣ��������Խ��в���
	 * @param string $path ���Ĵ��Ŀ¼����"/"��β
	 * @param string $name cache key
	 */
	public function __construct($name, $path = ''){
		//�ж��Ƿ����eAccelerator,����������eAccelerator֮����Խ����ڴ������Ч��
		$this->eAccelerator = function_exists("eaccelerator_lock");
		if(!$this->eAccelerator){
			if(!$path)$this->mkdir($path = M_ROOT . 'dynamic/cachelock');
			$this->path = "$path/" . ($this->_mycrc32($name) % $this->hashNum) . '.lock';
		}
		$this->name = $name;
	}
	
	/**
	 * crc32
	 * crc32��װ
	 * @param int $string
	 * @return int
	 */
	private function _mycrc32($string){
		$crc = abs(crc32($string));
		if($crc & 0x80000000){
			$crc ^= 0xffffffff;
			$crc += 1;
		}
		return $crc;
	}
	/**
	 * mkdir
	 * ·������
	 * @param string $path
	 * @return int
	 */
	private function mkdir($path){
		if(is_dir($path))return true;
		if(!$this->mkdir(dirname($path)) || @!mkdir($path,0777))return false;
		return true;
	}
	/**
	 * ����
	 * Enter description here ...
	 */
	public function lock(){
		//����޷�����ea�ڴ����������ļ���
		if(!$this->eAccelerator){
			//����Ŀ¼Ȩ�޿�д
			$this->fp = fopen($this->path, 'w+');
			if($this->fp === false)return false;
			return flock($this->fp, LOCK_EX);
		}else{
			return eaccelerator_lock($this->name);
		}
	}
	
	/**
	 * ����
	 * Enter description here ...
	 */
	public function unlock(){
		if(!$this->eAccelerator){
			if($this->fp !== false){
				flock($this->fp, LOCK_UN);
				clearstatcache();
				//���йر�
				fclose($this->fp);
#				unlink($this->path);
				return true;
			}
		}else{
			return eaccelerator_unlock($this->name);
		}
	}
}
?>
