<?php
/*-----------------------------------------------------------------------------
cheetan is licensed under the MIT license.
copyright (c) 2006 cheetan all right reserved.
http://php.cheetan.net/
modified by akira 2011
-----------------------------------------------------------------------------*/
class CController extends CObject
{
	var	$template		= null;
	var $viewfile		= null;
	var $viewpath		= null;
	var $viewfile_ext	= ".html";
	var $variables		= array();
	var	$db;
	var $sanitize;
	var $s;
	var	$validate;
	var	$v;
	//	Models Array
	var $m				= array();
	//	Components Array
	var $c				= array();
	var	$post			= array();
	var	$get			= array();
	var	$request		= array();
	var	$data			= array();
	var $debug			= false;
	var $sessionstart	= false;	//akira add

	function CController()
	{
	}


	function AddModel( $path, $name = "" )
	{
		$cname	= basename( $path, ".php" );
		$cname	= strtolower( $cname );
		if( !$name )	$name = $cname;
		$cname	= "C" . ucfirst( $name );
		if( !file_exists( $path ) )
		{
			return FALSE;
		}
		else
		{
			require_once( $path );
//modified by akira			$class = &new $cname();
			$class = new $cname();
			if( !$class->table )	$class->table = $name;
			$class->SetController( $this );
			$this->m[$name]	= &$class;
			if( empty( $this->{$name} ) )	$this->{$name} = &$this->m[$name];
		}
		return TRUE;
	}


	function AddComponent( $path, $cname = '', $name = '' )
	{
		if( !$cname )
		{
			$cname	= basename( $path, '.php' );
			$cname	= strtolower( $cname );
			if( !$name )	$name = $cname;
			$cname	= 'C' . ucfirst( $name );
		}
		else
		{
			$name	= basename( $path, '.php' );
			$name	= strtolower( $name );
		}
		if( !file_exists( $path ) )
		{
			print 'Component file $path is not exist.';
			return FALSE;
		}
		else
		{
			require_once( $path );
//modified by akira			$class = &new $cname();
			$class = new $cname();
			$this->c[$name]	= $class;
			if( empty( $this->{$name} ) )	$this->{$name} = &$this->c[$name];
		}
		return TRUE;
	}


	function SetTemplateFile( $template )
	{
		$this->template	= $template;
	}


	function SetViewFile( $viewfile )
	{
		$this->viewfile	= $viewfile;
	}


	function SetViewPath( $viewpath )
	{
		$this->viewpath	= $viewpath;
	}


	function SetViewExt( $ext )
	{
		if( $ext{0} != '.' )	$ext = '.' . $ext;
		$this->viewfile_ext	= $ext;
	}


	function GetTemplateFile()
	{
		return $this->template;
	}


	function GetViewFile()
	{
		if( $this->viewfile )
		{
			return $this->viewfile;
		}

		$pos	= strpos( SCRIPTFILE, "." );
		if( $pos === FALSE )	return SCRIPTFILE . $this->viewfile_ext;
		if( !$pos )				return $this->viewfile_ext;

		list( $title, $ext )	= explode( ".", SCRIPTFILE );
		if( $this->viewpath )
		{
			$path	= $this->viewpath;
			switch( $this->viewpath[strlen($this->viewpath)-1] )
			{
			case '/';
			case "\\";
				$path	= $this->viewpath . $title . $this->viewfile_ext;
				break;
			default:
				$path	= $this->viewpath . DIRECTORY_SEPARATOR . $title . $this->viewfile_ext;
				break;
			}
		}
		else
		{
			$path	= $title . $this->viewfile_ext;
		}
		return $path;
	}


	function set( $name, $value )
	{
		$this->variables[$name]	= $value;
	}


	function setarray( $datas )
	{
		foreach( $datas as $key => $data )
		{
			$this->set( $key, $data );
		}
	}


	function redirect( $url, $is301 = FALSE )
	{
		if( $is301 )
		{
			header( "HTTP/1.1 301 Moved Permanently" );
		}
		header( "Location: " . $url );
		exit();
	}


	function RequestHandle()
	{
		if( count( $_GET ) )		$this->get = $_GET;
		if( count( $_POST ) )		$this->post = $_POST;
		if( count( $_REQUEST ) )	$this->request = $_REQUEST;
		$this->ModelItemHandle( $_GET );
		$this->ModelItemHandle( $_POST );
	}


	function ModelItemHandle( $requests )
	{
		foreach( $requests as $key => $request )
		{
			if( strpos( $key, "/" ) !== FALSE )
			{
				list( $model, $element )		= explode( "/", $key );
				$this->data[$model][$element]	= $request;
			}
		}
	}


	function GetVariable()
	{
		return $this->variables;
	}


	function &GetDatabase()
	{
		return $this->db;
	}


	function SetDatabase( &$db )
	{
		$this->db	= $db;
	}


	function SetSanitize( &$sanitize )
	{
		$this->sanitize	= $sanitize;
		$this->s		= &$this->sanitize;
	}


	function SetValidate( &$validate )
	{
		$this->validate	= $validate;
		$this->v		= &$this->validate;
	}


	function SetDebug( $debug )
	{
		$this->debug	= $debug;
	}


	function GetDebug()
	{
		return $this->debug;
	}


	function GetSqlLog()
	{
		return $this->db->GetSqlLog();
	}
//akira add start
	function SetSessionstart($value = TRUE)
	{
		$this->sessionstart = $value;
	}
	function GetSessionstart()
	{
		return $this->sessionstart;
	}
//akira add end

}
?>
