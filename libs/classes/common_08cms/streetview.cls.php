<?php

/**
 * �־������ 
 * @example 
        <div id="streetview" style="display:none;width:400px; height:300px; "></div>
        <?php
            cls_StreetView::StreetView(array('lat'=>'23.003088673571295','lng'=>'113.72987204787934','divid'=>'streetview'));
        ?> 
        ע�⣺
	       1.div��id�뺯���д��ݽ�ȥ��dividҪһ�¡�
	       2.Ҫȷ��ʹ�øô���ǰ�Ѿ�������jquery�ļ�
 * @author lyq0328
 * @copyright 2014
 */

class cls_StreetView{   
    /**
     * ���ݺ�̨�趨�Ľ־�������ʾ�־�
       Tencent����Ѷ�־�
       noview���رս־�        
     */
    public static function view($Map){
        //��ȡ��̨�־�����
        $mconfigs = cls_cache::Read('mconfigs');
        //�־�����
        $streetViewType = empty($mconfigs['streetviewtype'])?'':trim($mconfigs['streetviewtype']);
        //�رս־�
        if($streetViewType == 'noview') return false;
        $className = "cls_".$streetViewType;         
        if(class_exists($className)){            
            $className::view($Map,$mconfigs);
        }    
    }
}

?>