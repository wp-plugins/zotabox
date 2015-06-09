<?php
/*
/**
 * Plugin Name: Zotabox
 * Plugin URI: http://zotabox.com/plugin_wp/add_more
 * Description: Free marketing tools to promote your site
 * Version: 1.0
 * Author: Zotabox
 * Author URI: http://zotabox.com/
 * License: SMB 1.0
 */
add_action( 'admin_init', 'zotabox_admin_init' );
function zotabox_admin_init(){
	/* Register stylesheet. */
	wp_register_style( 'css_main', plugins_url('assets/css/style.css', __FILE__) );
	wp_enqueue_style('css_main');
    /* Register js. */
	wp_register_script( 'main_js', plugins_url('assets/js/main.js', __FILE__) );
	wp_enqueue_script('main_js');

    //Create options
    add_option( 'ztb_source', '', '', 'yes' );
    add_option( 'ztb_id', '', '', 'yes' );
    add_option( 'ztb_domainid', '', '', 'yes' );
    add_option( 'access_key', '', '', 'yes' );
    add_option( 'ztb_status_message', 1, '', 'yes' );
    add_option( 'ztb_status_disconnect', 2, '', 'yes' );
    add_option( 'ztb_token_key', generate_token(), '', 'yes' );
}

register_deactivation_hook( __FILE__, 'zotabox_deactivate' );
function zotabox_deactivate(){
	update_option( 'ztb_status_message', 2 );
}

register_activation_hook( __FILE__, 'zotabox_activate' );
function zotabox_activate() {
	update_option( 'ztb_status_message', 1 );
}


function generate_token() {
    $str = "mQWEqweRTYvbnUI89rtOASDFG034opasdfHJKL12MZghjkXNC567yuiBlzxcVdswerwfWERdsdKLui";
    return substr($str, rand(0, 55), 32);
}
add_action('admin_notices', 'show_admin_messages');
function show_admin_messages()
{
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$domain_action = 'http://widgets.zotabox.com';
	$token_key = get_option( 'ztb_token_key', '' );
	$public_key = get_option( 'access_key', '' );
	$ztb_status_message = get_option( 'ztb_status_message', '' );
	$ztb_status_disconnect = get_option( 'ztb_status_disconnect', '' );
	$ztb_id = get_option( 'ztb_id', '' );
	$message_intall = '<div id="message" class="updated fade">
		    <p>Thanks for installing <strong>Zotabox plugin!</strong>  
		    <a href="/wp-admin/admin.php?page=zotabox">
		    <strong>Click to configure.</strong></a></p>
	    </div>';
	$message_disconnect = '<div id="message" class="updated fade">
			    <p>Disconnected from <strong>Zotabox </strong>successfully! </p>
		    </div>';
	if ( is_plugin_active( 'zotabox/zotabox.php') ) {
		if($ztb_status_message == 1){
	  		echo $message_intall;
		   if($ztb_status_message == 1){
		   		update_option( 'ztb_status_message', 2 );
		   }
		}

		if($ztb_status_disconnect == 1){
			echo $message_disconnect;
			if($ztb_status_disconnect == 1){
		   		update_option( 'ztb_status_disconnect', 2 );
		   }
		}
	} 
    
    
}

add_action('admin_menu', 'ztb_setting');
function ztb_setting() {
	add_menu_page('Zotabox', 'Zotabox', 'administrator', 'zotabox', 'zotabox_setting',plugins_url( 'zotabox.png', __FILE__ ));
}

function zotabox_setting(){
	$domain_action = 'http://widgets.zotabox.com';
	$token_key = get_option( 'ztb_token_key', '' );
	if(empty($token_key) || strlen($token_key) == 0){
		update_option( 'ztb_token_key', generate_token());
		$token_key = get_option( 'ztb_token_key', '' );
	}
	$access_key = get_option( 'access_key', '' );
	$ztb_id = get_option( 'ztb_id', '' );
	$domain = get_option('ztb_domainid','');
	$ztb_source = get_option('ztb_source','');
	$button = '';
	$button_active = '';
	$embed_code = '';
	if(isset($access_key) && !empty($access_key) && strlen($access_key) > 0){
	
		$button = '<a  target="zotabox" href="'.$domain_action.'/customer/access/?redirect='.get_option('home').'/wp-admin/admin-ajax.php?action=update_ztbcode&token='.$token_key.'&customer='.$ztb_id.'&access='.$access_key.'">
			Configure your widgets
		</a>
		<div class="disconect-ztb-wrapper">
			<span>
				<a href="javascript:void(0);" id="disconnect-ztb">Disconnect</a>
			</span>
			<div class="loading">
				<img src="'.plugins_url( 'assets/images/loading.gif', __FILE__ ).'">
			</div>
		</div>';
	}else{

		$button = '<a id="connect-ztb" target="zotaboxsignin" href="'.$domain_action.'/customer/access/?redirect='.get_option('home').'/wp-admin/admin-ajax.php?action=update_ztbcode&token='.$token_key.'">
			Sign in Zotabox
		</a>';
	} 

	if(isset($domain) && !empty($domain)){
		$button_active = '<span>(</span><span class="check-actived"></span> <span>activated)</span>';
	}else if(isset($ztb_source) && !empty($ztb_source)){
		$button_active = '<span>(</span><span class="check-actived"></span> <span>activated)</span>';
	}

	if(!empty($domain) || strlen($domain) > 0){ 
		$embed_code = printCode($domain); 
	} else {
		$embed_code = $ztb_source;
	}  
	$html = '
	<div class="ztb-wrapper">
		<div class="ztb-logo">
			<a href="http://zotabox.com" title="Zotabox" target="zotabox">
				<img title="Zotabox" alt="zotabox" src="'.plugins_url( 'assets/images/logo-zotabox.png', __FILE__ ).'">
			</a>
		</div>
		<div class="ztb-code-wrapper wrap">
			<div class="ztb-title">
				Configure your Zotabox widgets
			</div>
			
			<div class="ztb-button">'.$button.'</div>
			<div class="insert-code-wrapper">
				<div>
					<a id="button-open-insert-code" href="javascript:void(0);">Save your embed code manually</a>'.$button_active.'
						
				</div>
				<div class="wrap insert-code-content">
					<form id="insert-ztbcode-form" action="options.php" method="post" name="options">
					'.wp_nonce_field("update-options").'
						<textarea class="ztb-source" style="" name="ztb_source" rows="8" cols="50">'.$embed_code.'</textarea><br/>
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="ztb_id" id="ztb_id" value="'.$ztb_id.'" />
						<input type="hidden" name="page_options" value="ztb_source" />
						<input class="button button-primary insert-code" type="submit" name="Submit" value="Save" />
					</form>
				</div>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>';
	echo $html;
	
	?>
	
	<?php	
}

function get_url() {
    $url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    return $url;
}

function insert_ztb_code(){
	if(!is_admin()){
		$domain = get_option( 'ztb_domainid', '' );
		$ztb_source = get_option('ztb_source','');
		if(!empty($domain) || strlen($domain)>0){
			print_r(html_entity_decode(printCode($domain)));
		}else{
			print_r(html_entity_decode($ztb_source));
		}
		
	}
}
add_action( 'get_footer', 'insert_ztb_code' );

add_action("wp_ajax_update_ztbcode", "update_ztbcode");
add_action("wp_ajax_nopriv_update_ztbcode", "update_ztbcode");
function update_ztbcode(){
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
	$domain = addslashes($_REQUEST['domain']);
	$public_key = addslashes($_REQUEST['access']);
	$token = addslashes($_REQUEST['token']);
	$id = intval($_REQUEST['customer']);
	if(!isset($domain) || empty($domain)){
		header("Location: /wp-admin/admin.php?page=zotabox");
	}else{
		if($token == get_option( 'ztb_token_key', '' )){
			update_option( 'ztb_domainid', $domain );
			update_option( 'ztb_token_key', '');
			update_option( 'access_key', $public_key );
			update_option( 'ztb_id', $id );
			wp_send_json( array(
				'error' => false,
				'message' => 'Update Zotabox embedded code successful !' 
				)
			);
		}else{
			wp_send_json( array(
				'error' => true,
				'message' => 'Wrong token key !' 
				)
			);
		}
	}
}

add_action("wp_ajax_disconnect_ztb", "disconnect_ztb");
add_action("wp_ajax_nopriv_disconnect_ztb", "disconnect_ztb");
function disconnect_ztb(){
	update_option( 'ztb_source', '' );
	update_option( 'ztb_domainid', '' );
	update_option( 'access_key', '' );
	update_option( 'ztb_status_disconnect', 1 );
	wp_send_json( array(
		'error' => false,
		'message' => 'Disconnected to zotabox !' 
		)
	);
}

add_action("wp_ajax_clear_account", "clear_account");
add_action("wp_ajax_nopriv_clear_account", "clear_account");
function clear_account(){
	update_option( 'ztb_domainid', '' );
	update_option( 'access_key', '' );
	update_option( 'ztb_status_disconnect', 1 );
	wp_send_json( array(
		'error' => false,
		'message' => 'Disconnected to zotabox !' 
		)
	);
}

function printCode($domainSecureID = "", $isHtml = false) {

	$ds1 = substr($domainSecureID, 0, 1);
	$ds2 = substr($domainSecureID, 1, 1);
	$baseUrl = '//static.zotabox.com';
	$code = <<<STRING
<script type="text/javascript">
(function(d,s,id){var z=d.createElement(s);z.type="text/javascript";z.id=id;z.async=true;z.src="{$baseUrl}/{$ds1}/{$ds2}/{$domainSecureID}/widgets.js";var sz=d.getElementsByTagName(s)[0];sz.parentNode.insertBefore(z,sz)}(document,"script","zb-embed-code"));
</script>
STRING;
	return $code;
}

add_action("wp_ajax_create_ztbtoken", "create_ztbtoken");
add_action("wp_ajax_nopriv_create_ztbtoken", "create_ztbtoken");
function create_ztbtoken(){
	$token = get_option( 'ztb_token_key', '' );
	wp_send_json( array(
		'error' => false,
		'message' => 'Created token !'
		)
	);
}

add_action("wp_ajax_check_ztbtoken", "check_ztbtoken");
add_action("wp_ajax_nopriv_check_ztbtoken", "check_ztbtoken");
function check_ztbtoken(){
	$token = get_option( 'ztb_token_key', '' );
	if(empty($token) || strlen($token) == 0 || !isset($token)){
		wp_send_json( array(
			'error' => false,
			'message' => 'Disconnected to zotabox !' 
			)
		);
	}else{
		wp_send_json( array(
			'error' => true,
			'message' => 'Connect to zotabox !' 
			)
		);
	}
}
?>