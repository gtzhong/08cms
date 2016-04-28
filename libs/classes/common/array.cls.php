<?php
/**
 * Array���Ĺ�����
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_Array
{
     /**
     * �� haystack ������ needle�����û������ strict ��ʹ�ÿ��ɵıȽϡ�
     * �ú������Լ���ά���ϵ����飬�������һά������ʹ��ԭ����in_array����
     * @link http://docs.php.net/manual/zh/function.in-array.php
     *
     * @param mixed $needle   ��������ֵ������
     * @param array $haystack Ҫ����������
     * @param int   $strict   ������������� strict ��ֵΪ TRUE ��Ϊ 1 �� in_array()
     *                        ���������� needle �������Ƿ�� haystack �е���ͬ,
     *                        �����ֵΪ2ʱ����strpos��ʽ�����ж����Ƶ�ֵ��
     *
     * @static
     * @since 1.0
     */
    public static function _in_array( $needle, array $haystack, $strict = 0 )
    {
        # ��strpos��ʽ�����ж�����1���Ƿ��������2���Ƶ�ֵ
        if ( is_array($needle) && $strict === 2 )
        {
            foreach ($needle as $need) 
            {                
                if($return = self::_in_array($need, $haystack, $strict))
                {
                    return $return;
                }
            }
        }
        
        # �������� $haystack
        foreach($haystack as $value)
        {
            if( is_array($value) )
            {
                if($return = self::_in_array($needle, $value, $strict))
                {
                    return $return;
                }
            }

            if ( $strict === 1 || $strict === true )
            {
                if( $value === $needle ) return true;
            }
            else if ($strict === 2)
            {
            	if( false !== @strpos($value, $needle) ) return true;
            }
            else
            {
                if( $value == $needle ) return true;
            }
        }

        return false;
    }

    /**
     * ��һ������������һ��Ԫ�أ����������Ϊ��ά�����ÿһά����
     * 
     * @param array  $array Ҫ���ӵ��������
     * @param string $key   Ҫ���ӵļ�
     * @param string $value Ҫ���ӵ�ֵ
     * @param int    $Layer Ҫ�����ά��
     *                      ���$Layer = 0 ����$Layer��������ά��ʱ���������鶼�ᴦ��
     *                      �����ֵΪ��ʱ���磺$Layer = 2ʱֻ�����2ά��
     *                      �����ֵΪ��ʱ���磺$Layer = -2ʱ����˵�2ά������
     * 
     * @since 1.0
     */ 
	public static function _array_push( array &$array, $key = '', $value, $Layer = 0 )
    {
        static $num = 1;
        $LayerAbs = abs($Layer);

		if(
			!$Layer || 
			(($Layer < 0) && ($LayerAbs != $num)) || 
			(($Layer > 0) && ($LayerAbs == $num))
		) 
		{
			empty($key) ? ($array[] = $value) : ($array[$key] = $value);
		}
		++$num; 
        foreach($array as &$v)
        {
            if(is_array($v))
			{
				self::_array_push($v, $key, $value, $Layer);
				--$num;
			}
        }  
    }
    
    /**
     * ��������������ָ���ļ�ֵ������ü����������Զ�����
     * 
     * @param object $object Ҫ���ӵĶ���
     * @param string $key    Ԫ�ؼ���
     * @param mexid  $value  Ԫ��ֵ
     */ 
    public static function setObjectDOM(&$object, $key, $value)
    {
        foreach($object as &$v)
        {
            $v->$key = $value;
        }
    }
	
    /**
     * ��������ָ������ֵ���������������
     * 
     * @param array  $array 	Ҫ���������
     * @param string $orderkey  ָ������ļ�������ֵ����
     * @param bool   $keepkey 	���ּ����Ƿ���Ҫ���ֲ���
     */ 
    public static function _array_multisort(array &$array,$orderkey = 'vieworder',$keepkey = false){
        if(!is_array($array) || empty($array) || !function_exists('array_multisort')) return;
        foreach($array as $k => $v){
            $vorder[$k] = $array[$k][$orderkey] = empty($v[$orderkey]) ? 0 : $v[$orderkey];
            $eorder[$k] = $k;
            if($keepkey) $array[$k]['_key'] = $k;
        }
        array_multisort($vorder,SORT_ASC,$eorder,SORT_ASC,$array);
        if($keepkey){
            $na = array();
            foreach($array as $k => $v){
                $key = $v['_key'];
                unset($v['_key']);
                $na[$key] = $v;
            }
            $array = $na;
        }
    }
      
    /**
     * �����鰴�ַ�������ע������������֮�䲻����
     * 
     * @param array $array Ҫ��������飨֧�ֶ�ά��
     */
    public static function _array_uasort( array &$array, $function = '__numberOfCharactersCmp' )
    {
        uasort($array, array(__CLASS__, $function));
    }
    
    /**
     * ���ַ����Ƚ�
     */
    private static function __numberOfCharactersCmp( &$a, &$b )
    {
        if ( is_array($a) )
        {
            return self::_array_uasort($a);
        }
        
        if ( is_array($b) )
        {
            return self::_array_uasort($b);
        }
        
        $lenA = strlen( (string) $a );
        $lenB = strlen( (string) $b );
        if ($lenA == $lenB)
        {
            return 0;
        }
        
        return ($lenA > $lenB) ? -1 : 1;
    }
    
    /**
     * ��һ����ά����ת��һά
     * 
     * @param  array $array        Ҫת���Ķ�ά����
     * @param  bool  $retentionKey �Ƿ�����ֵ��TRUEΪ������FALSEΪ������
     * @return array               �����Ѿ�ת����һά����
     * 
     * @since  nv50
     */
    public static function _array_multi_to_one ( array $arrays, $retentionKey = false )
    {
        static $_one_array = array();
        foreach($arrays as $key => $array)
        {
            if ( is_array($array) )
            {
                self::_array_multi_to_one($array);
            }
            else
            {
                if ( $retentionKey )
                {
                    $_one_array[$key] = $array;
                }
                else
                {
                	$_one_array[] = $array;
                }
            	
            }
        }
        
        return $_one_array;
    }
      
    /**
    * ��һ�����鷴����
    * �����ڣ� array_map('stripslashes', $array);
    *  
    * @param array $array  Ҫ�����õ�����
    * 
    * @since nv50
    */ 
    public static function array_stripslashes(&$array)
    {
        if(is_array($array))
        {
            foreach($array as &$value)
            {
                self::array_stripslashes($value);
            }
        }
        else 
        {
            $array = stripslashes($array);
        }
    }
	
    /**
     * ��ȡ�����ά��
     * 
     * @param  array $array Ҫ��ȡ������
     * @return int          ���������ά��
     * @since  nv50
     */
    public static function array_dimension(array $array)
    {
        $dimension = 0;
        if ( empty($array) )
        {
            return $dimension;
        }
        
        foreach ($array as $value) 
        {
            if ( is_array($value) )
            {
                # �ж�������ά��
                $sonArrayDimension = self::array_dimension($value);
                if ( $sonArrayDimension > $dimension )
                {
                    $dimension = $sonArrayDimension;
                }
            }
        }
        
        return $dimension + 1;
    }
    
    /**
     * ��ȡ����ƫ��Ԫ��ֵ����SQL��Limit���ƣ�ע����ȡ��Key����0��ʼ�����ּ�����
     *
     * @param  array $array Ҫ��ȡ������
     * @param  int   $limit Ҫ��ȡ�Ŀ�ʼλ��
     * @param  int   offset Ҫȡ�ĸ���
     * @return array $IteratorParams ���ػ�ȡ���Ԫ��ֵ�����ƫ����������ʱ����null
     *
     * @since  nv50
     */
    public static function limit( array $array, $limit, $offset = 0, $reservedKEY = false)
    {
        if ( empty($offset) )
        {
        	$offset = $limit;
            $limit = 0;
        }

        try
        {
            $arrayIterator = new ArrayIterator($array);
            $paramsIterator = new LimitIterator($arrayIterator, (int) $limit, (int) $offset);
            $IteratorParams = array();
            foreach ( $paramsIterator as $key => $param )
            {
                if ($reservedKEY)
                {
                    $IteratorParams[$key] = $param;
                }
                else
                {
                	$IteratorParams[] = $param;
                }            	
            }
        }
        catch (OutOfBoundsException $error)
        {
            $IteratorParams = array();
        }

        return $IteratorParams;
    }
	
	/**
     * ����$Key��ȡ�����е�ֵ			
     * @param  array  		$array 				Դ����
     * @param  string  		$Key 				������֧��'xx.kk.dd'�õ�$array['xx']['kk']['dd']
     */
	public static function Get($array = array(),$Key = ''){
		if(!is_array($array)) return NULL;
		if(!($KeyArray = self::ParseKey($Key))){
			return NULL;
		}
		foreach($KeyArray as $k){
			$array = isset($array[$k]) ? $array[$k] : NULL;
		}
		return $array;
    }
	
	/**
     * ����$Key���������е�ֵ
     * @param  string  		$Key 				������֧��'xx.kk.dd'Ϊ$array['xx']['kk']['dd']��ֵ
     * @param  string  		$value ֵ			��Ҫ���õ�ֵ
     * @param  array  		$array 				Դ����
    */
	public static function Set(&$array,$Key = '',$Value = 0){
		if(!is_array($array)) return;
		if(!($KeyArray = self::ParseKey($Key))) return;
		$level = count($KeyArray) - 1;
		foreach($KeyArray as $k => $v){
			if($k < $level){
				$array[$v] = isset($array[$v]) && is_array($array[$v]) ? $array[$v] : array();
				$array = &$array[$v];
			}else{
				$array[$v] = $Value;
			}
		}
    }
	
	/**
     * ����ϼ���(��'.'����)�ֽ�Ϊ����
     * @param  string  $key			�����ִ�
     * @param  string  $AllowDot	�Ƿ�����������ӷ�'.'
     */
    public static function ParseKey($Key = ''){
        $Key = preg_replace('/[^\w\.]/', '', (string)$Key);
		if($Key === '') return false;
		return explode('.',$Key);
    }
	
    
    /**
     * ���ص��������õ���������ĵ�Ԫ��
     * ���������ڣ�array_map ������鿴��{@link http://www.php.net/manual/zh/function.array-map.php}
     * ��������{@see self::map}֧�ֵ�Ԫ��Ҳ������
     * 
     * @param  mixed $params   ���ݸ��ص������Ĳ���
     * @return mixed           ���ػص����������ķ���ֵ
     * 
     * @since  nv50
     */
    public static function map()
    {
        $params = func_get_args();
        if ( is_array($params[1]) )
        {
            $_params = $params;
            foreach ( $params[1] as &$param ) 
            {
                $_params[1] = $param;
                $param = call_user_func_array( array('self', 'map'), $_params );
            }
        
            $params = $params[1];
        }
        else
        {
            $function = $params[0];
            unset($params[0]);
        	$params = call_user_func_array( $function, $params );
        }
        
        return $params;
    }
}