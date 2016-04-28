<?php
    // ���ݵ�����Excel�ļ�,�ݲ�֧�������ļ����ơ���������UTF-8�����excel�����������ݿ�ȡ����������ת��UTF-8��,����ʱĳЩWPS�汾ֻ֧��UTF-8�ı���
	/* @example 
		$xls = new cls_XmlExcelExport($charset);           //Ĭ��UTF-8����
		$xls->generateXMLHeader('zhaobiao_'.date('Y-md-His',$timestamp));  //excel�ļ���
		$xls->worksheetStart('�б���Ϣ');
		$xls->setTableHeader($_value);  //���ֶ���
		$xls->setTableRows($data); //�����ֶ�
		$xls->worksheetEnd();
		$xls->generateXMLFoot();
	*/

    /**
    * ���� XML��ʽ�� Excel ����
	*
    */
!defined('M_COM') && exit('No Permisson');
    class cls_XmlExcelExport
    {

        /**
         * �ĵ�ͷ��ǩ
         *
         * @var string
         */
        private $header = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";

        /**
         * �ĵ�β��ǩ
         *
         * @var string
         */
        private $footer = "</Workbook>";

        /**
         * ���ݱ���
         * @var string
         */
        private $sEncoding;

        /**
         * �Ƿ�ת���ض��ֶ�ֵ������
         *
         * @var boolean
         */
        private $bConvertTypes;
       
        /**
         * ���ɵ�Excel�ڹ������ĸ���
         *
         * @var int
         */
        private $dWorksheetCount = 0;

        /**
         * ���캯��
         *
         * ʹ������ת��ʱҪȷ��:ҳ����ʱ����'0'��ͷ
         *
         * @param string $sEncoding ���ݱ���
         * @param boolean $bConvertTypes �Ƿ�ת���ض��ֶ�ֵ������
         */
        function __construct($sEncoding = 'UTF-8', $bConvertTypes = false)
        {
            $this->bConvertTypes = $bConvertTypes;
            $this->sEncoding = $sEncoding;
        }

        /**
         * ���ع���������,��� �ַ���Ϊ 31
         *
         * @param string $title ����������
         * @return string
         */
        function getWorksheetTitle($title = 'Table1')
        {
            $title = preg_replace("/[\\\|:|\/|\?|\*|\[|\]]/", "", empty($title) ? 'Table' . ($this->dWorksheetCount + 1) : $title);
            return substr($title, 0, 31);
        }
       
        /**
         * ��ͻ��˷���Excelͷ��Ϣ
         *
         * @param string $filename �ļ�����,����������
         */
        function generateXMLHeader($filename){
           
            $filename = preg_replace('/[^aA-zZ0-9\_\-]/', '', $filename);
            $filename = urlencode($filename);
           
            // ��������ʹ��urlencode�������IE�д��ܱ�����������Ƶ��ļ�,������FF��ȴ������
            header("Pragma: public");   header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/vnd.ms-excel; charset={$this->sEncoding}");
            header("Content-Transfer-Encoding: binary");
            header("Content-Disposition: attachment; filename={$filename}.xls");
           
            echo stripslashes(sprintf($this->header, $this->sEncoding));
        }
       
        /**
         * ��ͻ��˷���Excel������ǩ
         *
         * @param string $filename �ļ�����,����������
         */
        function generateXMLFoot(){
            echo $this->footer;
        }
       
        /**
         * ����������
         *
         * @param string $title
         */
        function worksheetStart($title){
            $this->dWorksheetCount ++;
            echo "\n<Worksheet ss:Name=\"" . $this->getWorksheetTitle($title) . "\">\n<Table>\n";
        }
       
        /**
         * ����������
         */
        function worksheetEnd(){
            echo "</Table>\n</Worksheet>\n";
        }
       
        /**
         * ���ñ�ͷ��Ϣ
         *
         * @param array $header
         */
        function setTableHeader($header=array()){
            echo $this->_parseRow($header);
        }
       
        /**
         * ���ñ����м�¼����
         *
         * @param array $rows ���м�¼
         */
        function setTableRows($rows=array()){
            foreach ($rows as $row) echo $this->_parseRow($row);
        }
       
        /**
         * �����˵ĵ��м�¼����ת���� xml ��ǩ��ʽ
         *
         * @param array $array ���м�¼����
         */
        private function _parseRow($row=array())
        {
            $cells = "";
            foreach ($row as $k => $v){
                $type = 'String';
                if ($this->bConvertTypes === true && is_numeric($v))
                    $type = 'Number';
                   
                $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
                $cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n";
            }
            return "<Row>\n" . $cells . "</Row>\n";
        }

    }