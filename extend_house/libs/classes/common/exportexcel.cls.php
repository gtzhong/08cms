<?php
/**
 * 数据导出到excel类
 * 链接上传递chid/cuid/aid  
   eg:?entry=$entry$extend_str&chid=$chid&cuid=$cuid&aid=$aid
 *
 * 根据传参，可显示文档模型字段以及交互字段 或  模型字段  或 交互字段(字段中不含datatype为image、images、map、htmltext的字段)
 *
 * 处理数据，导出excel文件 
 */
defined('M_COM') || exit('No Permission');
class cls_exportexcel extends cls_exportexcels{	

}
