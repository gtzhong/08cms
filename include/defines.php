<?php


function crack_this( $content )
{
		$content = str_replace( $_SERVER['HTTP_HOST'], $_SERVER['_HOST_'], $content );
		foreach ( headers_list( ) as $v )
		{
				if ( strpos( $v, $_SERVER['HTTP_HOST'] ) !== FALSE )
				{
						$v = str_replace( $_SERVER['HTTP_HOST'], $_SERVER['_HOST_'], $v );
						@header( $v );
				}
		}
		return $content;
}

$GLOBALS['_SERVER']['_HOST_'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
$GLOBALS['_SERVER']['SERVER_NAME'] = $GLOBALS['_SERVER']['HTTP_HOST'] = "127.0.0.1";
$crackParams = array( "_POST", "_GET", "_COOKIE", "_SERVER",);
foreach ( $crackParams as $__key )
{
		if ( empty( $__key ) || !is_array( $$__key ) )
		{
		}
		else
		{
				$tmp = $$__key;
				foreach ( $tmp as $k => $v )
				{
						if ( !( $k == "_HOST_" ) )
						{
								if ( is_array( $v ) )
								{
										foreach ( $v as $kk => $vv )
										{
												if ( !is_array( $vv ) )
												{
														$tmp[$k][$kk] = str_replace( $_SERVER['_HOST_'], $_SERVER['HTTP_HOST'], $vv );
												}
										}
								}
								else
								{
										$tmp[$k] = str_replace( $_SERVER['_HOST_'], $_SERVER['HTTP_HOST'], $v );
								}
						}
				}
				$$__key = $tmp;
		}
}
if ( function_exists( "apache_setenv" ) )
{
		@apache_setenv( "HTTP_HOST", $_SERVER['SERVER_NAME'] );
		@apache_setenv( "SERVER_NAME", $_SERVER['SERVER_NAME'] );
}
if ( function_exists( "putenv" ) )
{
		@putenv( "HTTP_HOST=".$_SERVER['SERVER_NAME'] );
		@putenv( "SERVER_NAME=".$_SERVER['SERVER_NAME'] );
}
ob_start( "crack_this" );
define( "M_COM", TRUE );
if ( !defined( "DS" ) )
{
		define( "DS", DIRECTORY_SEPARATOR );
}
if ( !defined( "TIMESTAMP" ) )
{
		define( "TIMESTAMP", time( ) );
}
define( "_08_CACHE_DIR", "dynamic" );
define( "_08_USERCACHE_DIR", "cache" );
define( "_08_SYSCACHE_DIR", "syscache" );
define( "_08_CACHE_PATH", M_ROOT._08_CACHE_DIR.DS );
define( "_08_USERCACHE_PATH", _08_CACHE_PATH._08_USERCACHE_DIR.DS );
define( "_08_SYSCACHE_PATH", _08_CACHE_PATH._08_SYSCACHE_DIR.DS );
define( "_08_LIBS_DIR", "libs" );
define( "_08_LIBS_PATH", M_ROOT._08_LIBS_DIR.DS );
define( "_08_EXTEND_DIR", empty( $_08_extend_dir ) ? "extend_sample" : $_08_extend_dir );
define( "_08_EXTEND_PATH", M_ROOT._08_EXTEND_DIR.DS );
define( "_08_EXTEND_LIBS_PATH", _08_EXTEND_PATH._08_LIBS_DIR.DS );
define( "_08_EXTEND_CACHE_PATH", _08_EXTEND_PATH._08_CACHE_DIR.DS );
define( "_08_EXTEND_SYSCACHE_PATH", _08_EXTEND_CACHE_PATH._08_SYSCACHE_DIR.DS );
define( "_08_TPL_CACHE", _08_CACHE_PATH."tplcache".DS );
define( "_08_TEMP_TAG_CACHE", _08_CACHE_PATH."temp_tag_cache".DS );
define( "_08_ADMIN", "admina" );
define( "_08_ADMIN_PATH", M_ROOT._08_ADMIN.DS );
define( "_08_TEMPLATE_DIR", "template" );
define( "_08_TEMPLATE_PATH", M_ROOT._08_TEMPLATE_DIR.DS );
define( "_08_INCLUDE_DIR", "include" );
define( "_08_INCLUDE_PATH", M_ROOT._08_INCLUDE_DIR.DS );
define( "_08_CORE_API_PATH", _08_INCLUDE_PATH."core_api".DS );
define( "_08_INCLUDE_EX_PATH", _08_INCLUDE_PATH."extends".DS );
define( "_08_APPLICATION_PATH", _08_INCLUDE_PATH."application".DS );
define('_08_EXTEND_APPLICATION_PATH', _08_EXTEND_PATH . _08_INCLUDE_DIR . DS . 'application' . DS);
define('_08_PLUGINS_PATH', _08_APPLICATION_PATH . 'plugins' . DS);
define('_08_EXTEND_PLUGINS_PATH', _08_EXTEND_APPLICATION_PATH . 'plugins' . DS);
define( "_08CMS_APP_EXEC", true );
define( "_08_V_PATH", _08_APPLICATION_PATH."views".DS );
define( "M_TOOLS_PATH", M_ROOT."tools".DS );
define( "M_REFERER", isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : "" );
define( "M_URI", isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( $_SERVER['REQUEST_URI'] ) : "" );
define( "M_SERVER", strtolower( $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'] ) );
define( "QUOTES_GPC", function_exists( "get_magic_quotes_gpc" ) ? get_magic_quotes_gpc( ) : FALSE );
$os = strtoupper( substr( PHP_OS, 0, 3 ) );
if ( !defined( "IS_WIN" ) )
{
		define( "IS_WIN", $os === "WIN" ? true : false );
}
if ( !defined( "IS_MAC" ) )
{
		define( "IS_MAC", $os === "MAC" ? true : false );
}
if ( !defined( "IS_UNIX" ) )
{
		define( "IS_UNIX", $os !== "MAC" && $os !== "WIN" ? true : false );
}
?>
