<?php
/*
Plugin Core: Before deleting the posts.
Plugin URI: http://asumaru.com/plugins/asm-B4DelPosts/
Description: This plugin backs up before deleting posts.
Author: Masarki Kondo @ Asumaru Corp.
Version: 0.2
Author URI: http://asumaru.com/
Created: 2014.10.23
Updated: 2014.12.01 (0.1)   We registed to Wordpress.org.
Updated: 2014.12.02 (0.1.1) Erase bug code.("print_r")
Updated: 2014.12.03 (0.2)   We changed plugin-file-name from "asm-B4DelPosts" to "before-deleting-the-posts".
                            Because there was an installation error "The plugin does not have a valid header.".
*/

/**
    @class : Base Class
**/
class asm_B4DelPosts_Class{

	var $textdomain = 'asm-B4DelPosts';
	var $funcs = array();
	var $cache = array();
	var $name = 'deleted_posts';
	var $rgx =	array(
		'func'			=> '/([ \t]*)function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(.*\)\s*{/m',
		'post_types'	=> '/if\s*\(\s*\$args\[\'status\'\]\s*&&\s*\(\s*(.*)\s*\)\s*\)/m',
	);
	var $limit = 1000;
	var $excludes = array(
		'post',
		'page',
		'attachment',
		'revision',
	);

	/**
		@function : Constractor for PHP 4
		@param : void
		@return : void
	 */
	function asm_B4DelPosts_Class(){
		$this->cache['export.php'] = ABSPATH . 'wp-admin/includes/export.php';

		register_activation_hook(__FILE__, 'active');
		register_deactivation_hook(__FILE__, 'deactive');
		
		// Initialize
		add_action( 'init', array( &$this, 'init'), 20 );

	}


	/**
		@function : Constractor
		@param : void
		@return : void
	**/
	function __construct(){
		return $this->asm_B4DelPosts_Class();
	}

	/**
		@function : call method in this class.
		@param : $name function name
		@param : $args argments
		@return : (mixed) resalt/null
	**/
	function __call( $name, $args){
		if( method_exists( $this, $name)){
			return call_user_func_array( array( &$this, $name), $args);
		}
		if( is_array( $this->funcs) && array_key_exists( $name, $this->funcs)){
			if( is_scalar($this->funcs[$name])){
				$func = create_function( '', $this->funcs[$name]);
			}
			else
			{
				$func = $this->funcs[$name];
			}
			return call_user_func_array( $func, $args);
		}
		return null;
	}

	/**
		@function : Action Hook. plugin active
		@param : void
		@return : void
	**/
	function active() {
	}

	/**
		@function : Action Hook. plugin deactive
		@param : void
		@return : void
	**/
	function deactive() {
	}

	/**
		@function : Action Hook. Template Include
		@param : $template
		@return : void
	**/
	function init(){
		$this->new_export_php();
		$this->cache['bkdir'] = $this->get_dir();
		add_action( 'before_delete_post', array( &$this, 'b4delpost'));
	}

	/**
		@function : Create export.php
		@param : void
		@return : void
	**/
	function new_export_php(){
		$pts = get_post_types();
		$pts = array_keys( $pts );
		$pts = apply_filters( 'B5DelPosts/excludes', $pts );
		$this->cache['post_types'] = array_diff( $pts, $this->excludes );

		$src = $this->cache['export.php'];
		if( !file_exists( $src ) ){
			return false;
		}
		$src_time = filemtime( $src );
		$this->cache['original']['export.php'] = $src;
		$dest = dirname(__FILE__) . '/export.php';
		$dest = apply_filters( 'B5DelPosts/export.php', $dest );
		$dest_time = 0;
		if( file_exists( $dest ) ){
			$dest_time = filemtime( $dest );
		}
		if( $src_time >= $dest_time ){
			$now = date_i18n('Y-m-d H:i');
			$php = file_get_contents( $src );
			$res = $php;
			$res = preg_replace('/^([\040\t]*)header\s*\((.*)\);$/m','//\1Erase \2',$res);
			$res = $this->replace_funcs($res);
			$res = preg_replace_callback( $this->rgx['post_types'], array(&$this,'cb_replace_post_types'), $res );
			$res = <<<EOS
<?php
/**
 * Headers is removed by original WP-Export({$src})
 * new file : {$dest} at {$now}
**/
?>

{$res}
EOS;
			if( ! file_put_contents($dest,$res) ){
				return false;
			}
		}
		$this->cache['export.php'] = $dest;
		return $dest;
	}

	/**
		@function : Replace function statement with func_exists.
		@param : $src original source code
		@return : replaced code
	**/
	function replace_funcs($src){
		$this->cache['funcs'] = array();
		$this->cache['read'] = $src;
		$temp = preg_replace_callback( $this->rgx['func'], array(&$this,'cb_replace_funcs'), $this->cache['read'],1);
		$cnt = count( $this->cache['funcs'] );
		$res = '';
		$last = '';
		for( $i = 0; $i < $cnt; $i++ ){
			$cache = $this->cache['funcs'][$i];
			if( $cache[3] > 0 ) {
				$endmark = "/* ### END {$cache[0]} ### */";
				$cache[1] = preg_replace( $this->rgx['func'], '\1if( ! function_exists(\'\2\') ){\0', $cache[1] );
				$cache[1] = str_replace( $endmark, '} ' . $endmark, $cache[1] );
			}
			$res .= $cache[1];
			if( isset( $cache[2] ) )	$last = $cache[2];
		}
		$res .= $last;
		return $res;
	}

	/**
		@function : Callback. Replace function statement with func_exists.
		@param : $m matched code
		@return : replaced code
	**/
	function cb_replace_funcs($m){
		$num = count( (array) $this->cache['funcs'] );
		if( $num > $this->limit ) return $res;
		$this->cache['loop'] = $num;
		$res = $m[0];
		$cache = array();
		$read = $this->cache['read'];
		$this->cache['level'] = strlen($m[1]);
		$cache[3] = $this->cache['level'];
		$arr = explode( $res, (string) $read, 2);
		$cache[0] = $m[2];
		$cache[1] = (string) array_shift( $arr );
		$cache[1] .= $res;
		$rest = (string) $arr[0];
		$glue = $m[1] . '}';
		$arr = explode( "\n" . $glue, $rest, 2);
		if( $cache[3] > 0 ){
			$cache[1] .= $arr[0] . "\n" . $glue . " /* ### END {$cache[0]} ### */";
			$rest = $arr[1];
		}
		else
		{
			$cache[2] = "\n" . $glue . " /* ### END {$cache[0]} ### */" . $arr[1];
			$rest = $arr[0];
		}
		if( preg_match($this->rgx['func'], $rest, $m1 ) ){
			$cache[4] = $m1[2];
			$arr = explode( $m1[0], (string) $rest, 2);
			$cache[1] .= (string) $arr[0];
			$rest = $m1[0] . $arr[1];
		}
		if( empty( $cache[4] ) ){
			$cache[5] = $rest;
		}
		$this->cache['read'] = $rest;
		$this->cache['funcs'][$num] = $cache;
		$temp = preg_replace_callback( $this->rgx['func'], array(&$this,'cb_replace_funcs'), $this->cache['read'],1);
		if( !empty( $cache[2] ) ){
			$pre = $this->cache['loop'];
			$this->cache['funcs'][$num][2] = $this->cache['funcs'][$pre][5] . $this->cache['funcs'][$num][2];
		}
		return $res;
	}

	function cb_replace_post_types( $m ){
		$res = $m[0];
		$temp = trim( (string) $m[1] );
		$cond = preg_split( '/\s*\|\|\s*/m', (string) $m[1] );
		$includes = $this->cache['post_types'];
		$tmpl = $cond[0];
		$target = null;
		foreach( (array) $this->excludes as $ptype ){
			$ps = strpos( $tmpl, "'{$ptype}'" );
			if( $ps === false || $ps === null ) continue;
			$target = $ptype;
			break;
		}
		if( !empty( $includes ) && !empty( $target ) ){
			foreach( (array) $includes as $ptype ){
				$cond[] = str_replace( "'{$target}'", "'{$ptype}'", $tmpl );
			}
		}
		$rep = ' ' . implode( ' || ', (array) $cond ) . ' ';
		$res = str_replace( $m[1], $rep, $res );
		return $res;
	}

	/**
		@function : Get the directory for backup.
		@param : void
		@return : directory path
	**/
	function get_dir(){
//		$updir = wp_upload_dir();
//		$uppath = $updir['path'];
//		$bpath = dirname($uppath);
		$bpath = WP_CONTENT_DIR;
		$res = $bpath . '/' . $this->name;
		if ( function_exists('is_multisite') && is_multisite() && function_exists('get_current_blog_id') ){
			$sid = get_current_blog_id();
			$sid = empty($sid) ? 1 : $sid;
			$res .= '/' . $sid;
		}
		$res = apply_filters( 'B5DelPosts/export_dir', $res );
		if( !empty( $res ) && !file_exists( $res ) ){
			mkdir($res,0755,true);
		}
		return $res;
	}

	/**
		@function : Action Hook. Backup for export file before delete post.
		@param : $pid post ID.
		@return : void
	**/
	function b4delpost($pid=0){
		$path = $this->cache['export.php'];
		$ptypes = array_keys(get_post_types());
		$type = get_post_type($pid);
		$status = get_post_status($pid);
		$file = $this->cache['bkdir'];
		$file = $file . '/deleted_post-' . $pid . '.xml';
		$file = apply_filters( 'B5DelPosts/export_file', $file, $pid );
		wp_update_post(array('ID'=>$pid,'post_status'=>'deleting_' . $pid));
		$res = '';
		if(file_exists($path)){
			if(!function_exists('export_wp')){
				include $path;
			}
			if(function_exists('export_wp')){
				$args = array( 'status' => 'deleting_' . $pid, 'content' => $type );
				ob_start();
				export_wp($args);
				$res = ob_get_contents();
				ob_end_clean();
				$res = str_replace('<wp:status>deleting_' . $pid . '</wp:status>', '<wp:status>' . $status . '</wp:status>', $res);
				file_put_contents($file,$res);
			}
		}
		wp_update_post(array('ID'=>$pid,'post_status'=>$status));
	}

	function err_hndr( $errno=0, $errstr=''){
		echo "[$errno] $errstr\n";
	}

}

if( class_exists('asm_B4DelPosts_Class') && !is_object( $GLOBALS['asm_B4DelPosts_Class'])){
	$GLOBALS['asm_B4DelPosts_Class'] = new asm_B4DelPosts_Class();
}

?>