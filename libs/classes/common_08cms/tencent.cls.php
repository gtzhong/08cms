<?php

/**
 * ��Ѷ�־���
 * @param array    $Map                 �־���ͼ����
 * @param float    $Map['lat']          ����γ�� 
 * @param float    $Map['lng']          ���꾭��
 * @param string   $Map['divId']        �־�����div��ID
 * @param int      $Map['type']         ������Դ�������������̡���ѡֵΪ :
                                            1:gps��γ�ȣ�2:�ѹ���γ�ȣ�3:�ٶȾ�γ��
                                            4:mapbar��γ�ȣ�5:google��γ�ȣ�6:�ѹ�ī���� 
 * @param int      $Map['heading']      ƫ���ǣ�����������ļнǣ�˳ʱ��һ��Ϊ360�ȣ�Ĭ��360��
 * @param int      $Map['pitch']        �����ǣ��򵥵�˵����̧ͷ���ͷ�ĽǶȡ�
                                        ˮƽΪ0�ȣ���ͷΪ0��90�ȣ�̧ͷΪ0��-90�ȡ���Ĭ��9��
 * @param int      $Map['zoom']         ���ţ���Ϊ1��4��������Զ��һ����4���ŵ���󣬿�����Զ��Ĭ��1��
 * @author lyq0328
 * @copyright 2014
 */

class cls_Tencent{
    /**
     *�־� 
     */
    public static function view($Map,$Mconfigs){
        //�־���Կ
        $streetViewKey = empty($Mconfigs['streetviewkey'])?'':trim($Mconfigs['streetviewkey']);
        if(empty($streetViewKey)) return;
        if(empty($Map) || empty($Map['divid'])) return false;
        //����Ƿ��������꣬���򷵻�
        if(false === self::isSetLatLng($Map)) return false;
        //���ý־�����JS����
        self::setViewJsParam($Map,$streetViewKey);
        //��ʾ�־�
        self::streetViewJs();        
    }
    
    /**
     * �ĵ�û�е�ͼ���꣬�򷵻أ�û��ҪĬ��һ�����꣩
     * @param float    $Map['lat']            ����γ�� 
     * @param float    $Map['lng']            ���꾭�� 
     * @param string   $Map['divId']          �־�����div��ID
     */
    protected function isSetLatLng($Map){
        echo "<script type='text/javascript'>var DivId = '".(empty($Map['divid'])?'':$Map['divid'])."';</script>";
        if(empty($Map['lat']) || empty($Map['lng'])) {
            echo "<script type=\"text/javascript\">$('#'+DivId).html('���ĵ�û�е�ͼ���ꡣ');</script>";     
            return false;      
        } 
    }
    
    /**
     * ����JS��Ҫ�ı��� �Լ���ʾdiv
     * @param array    $Map                   �־���ͼ����
     * @param float    $Map['lat']            ����γ�� 
     * @param float    $Map['lng']            ���꾭��
     * @param string   $Map['divId']          �־�����div��ID
     * @param int      $Map['type']           ������Դ�������������̡���ѡֵΪ :
                                                1:gps��γ�ȣ�2:�ѹ���γ�ȣ�3:�ٶȾ�γ��
                                                4:mapbar��γ�ȣ�5:google��γ�ȣ�6:�ѹ�ī���� 
     * @param int      $Map['heading']        ƫ���ǣ�����������ļнǣ�˳ʱ��һ��Ϊ360�ȣ�Ĭ��360��
     * @param int      $Map['pitch']          �����ǣ��򵥵�˵����̧ͷ���ͷ�ĽǶȡ�
                                              ˮƽΪ0�ȣ���ͷΪ0��90�ȣ�̧ͷΪ0��-90�ȡ���Ĭ��9��
     * @param int      $Map['zoom']           ���ţ���Ϊ1��4��������Զ��һ����4���ŵ���󣬿�����Զ��Ĭ��1��
     */
    protected function setViewJsParam($Map,$streetViewKey){
        echo    "<script src='http://map.qq.com/api/js?v=2.exp&key=".$streetViewKey."&libraries=convertor'></script>";
        echo    "<script type='text/javascript'>
                    var Lat = '".(empty($Map['lat'])?'':$Map['lat'])."';
                    var Lng = '".(empty($Map['lng'])?'':$Map['lng'])."';                    
                    var Type = ".(empty($Map['type'])?3:min(6,intval($Map['type'])))."; 
                    var Heading = ".(empty($Map['heading'])?360:min(360,intval($Map['heading'])))."; 
                    var Pitch = ".(empty($Map['pitch'])?9:max(-90,intval($Map['pitch'])))."; 
                    var Zoom = ".(empty($Map['zoom'])?1:max(1,intval($Map['zoom']))).";            
                    $('#'+DivId).css('display','block');           
                </script>";
    }
    
    
    /**
     * ��ָ����DIV����ʾ�־�
     * JS��
       translate(points:LatLng | Point | Array.<LatLng> | Array.<Point>, type:Number, callback:Function)
       ��������ͼ�����̵���������ת��������Ѷ��ͼ��γ������
     */    
    protected function streetViewJs(){
        echo <<<EOT
            <script type="text/javascript">
    			qq.maps.convertor.translate(new qq.maps.LatLng(Lat,Lng),Type,function(res){	
    				//ת��֮�������
    				var coordinates = String(res[0]);
    			 	coordinates = coordinates.split(',');
    				var	center = new qq.maps.LatLng(coordinates[0],coordinates[1]);	
    				
    				pano_service = new qq.maps.PanoramaService();			
    				var radius;
    				pano_service.getPano(center, radius, function (result){			
    					var pano = new qq.maps.Panorama(document.getElementById(DivId), {
    						pano: result.svid,
    						disableFullScreen: false,
    						disableMove: false,
    						pov:{
    							heading:Heading,
    							pitch:Pitch
    						},
    						zoom:Zoom
    					});
    				});
    		
    			})
            </script>
EOT;
    }
    
    
}


?>