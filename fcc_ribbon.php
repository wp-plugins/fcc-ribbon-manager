<?php
/**
Plugin Name: FCC Ribbon 
Plugin URI: http://www.fullo.net
Description: Add a barcamp ribbon on your blog without touching the theme<br/>Changelog: 0.1 initial release<br/> 0.2 ie6.0 hack, height and width of the ribbon are now changeable
Version: 0.2
Author: Francesco Fullone
Author URI: http://www.fullo.net/
 */

add_action('wp_head', 'fcc_ribbon_head');
function fcc_ribbon_head()
{	
	
	$ribbon = (array) get_option('fcc_ribbon');
	$ribbon = array_merge(fcc_ribbon_getDefault(),$ribbon);
	
	$css = "\n<style type='text/css'>";
	
	$css .= "a#ribbon { display:block; position:fixed; top:0; ";
	$css .= ($ribbon['align']=='left')?"left:0; ":"right:0; ";
	$css .= "background:url(".$ribbon['image'].") no-repeat top left; ";
	$css .= "height: ".$ribbon['height']."px; width: ".$ribbon['width']."px;";
	$css .= "text-indent:-999px; }\n";

	//ie 6.0 hack
	$css .= "* html a#ribbon { display: block; position: absolute; top: 0;";
	$css .= ($ribbon['align']=='left')?"left:0; ":"right:0; ";
	$css .= "background:url(".$ribbon['image'].") no-repeat top left; background-attachment: fixed; ";
	$css .= "height: ".$ribbon['height']."px; width: ".$ribbon['width']."px;";
	$css .= "text-indent: -999px z-index: 100; }\n";
	
	$css .= "</style>\n";
	
	echo $css;
}

add_action('wp_footer','fcc_ribbon_footer');
function fcc_ribbon_footer()
{	
	$ribbon = get_option('fcc_ribbon');
	echo '<a id="ribbon" href="'.$ribbon['url'].'" title="'.$ribbon['text'].'">'.$ribbon['text'].'</a>';
}

add_action('admin_menu', 'fcc_ribbon_admin');
function fcc_ribbon_admin()
{	
	if ( function_exists('add_submenu_page') )
	add_options_page(__('Ribbon Manager','fcc'),__('Ribbon Manager','fcc'), 'manage_options', 'fcc_ribbon', 'fcc_ribbon_page');
}

if ( ! function_exists('wp_nonce_field') )
{
	function fcc_ribbon_nonce_field($action = -1) { return; }
	$fcc_ribbon_nonce = -1;
}
else
{
	function fcc_ribbon_nonce_field($action = -1) { return wp_nonce_field($action); }
	$fcc_ribbon_nonce = 'fcc_ribbon_save';
}

/**
 * default ribbon properties for first install and upgrade issue
 *
 * @return mixed array default option
 */
function fcc_ribbon_getDefault()
{
		return array(
						'url'=> 'http://www.fullo.net/', 
						'align'=>'left',
						'image' => 'http://www.fullo.net/ilovefullo.gif',
						'text' => 'Fullo.net',
						'width' => 85,
						'height' => 85
						);
}

/**
 * ribbon admin interface
 *
 */
function fcc_ribbon_page()
{
	global $fcc_ribbon_nonce;
	
	if ( isset($_POST['submit']) )
	{
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		check_admin_referer($fcc_ribbon_nonce);
		update_option('fcc_ribbon', array(
											'url' => $_POST['fcc_ribbon_url'],
											'align' => (($_POST['fcc_ribbon_align']=='left')?'left':'right'),
											'image' => strip_tags($_POST['fcc_ribbon_img']),
											'text' => strip_tags($_POST['fcc_ribbon_text']),
											'width' => (($_POST['fcc_ribbon_width']=='')?85:intval($_POST['fcc_ribbon_width'])),
											'height' => (($_POST['fcc_ribbon_height']=='')?85:intval($_POST['fcc_ribbon_height']))
											)
					);
			
	}	
	
	$ribbon_code = (array) get_option('fcc_ribbon');
	$ribbon_code = array_merge(fcc_ribbon_getDefault(),$ribbon_code);
?>

	<div class="wrap">
		<h2>Fcc Ribbon manager</h2>
		<div class="fcc_ribbon_instruction">
		<p>
			This plugin add a ribbon on your template without touching its code.<br/>
			To use it simply upload the ribbon image, put the image url in the image location 
			and add the destination url for the ribbon himself.
		</p>
		<p>
			You can also specify a different width and height for your ribbon 
			(if leaved blank those will be set to 85px).
		</p>
		<p>
			Note that Internet Explorer doesn't support .PNG transparency. 
			So you have to use .GIF image for better results.
		</p>
		</div>
		<form name="form1" method="post" action="">
			<?php fcc_ribbon_nonce_field($fcc_ribbon_nonce) ?>
			<fieldset class="options">
		
			<legend>Add a CSS Ribbon to your WP Theme</legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
				<th width="33%" scope="row">Image Location Url:</th>
				<td><input type="text" size="40" name="fcc_ribbon_img" style="font-family: 'Courier New', Courier, mono; font-size: 0.9em;" value="<?php echo $ribbon_code['image']; ?>"/></td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row">Ribbon size:</th>
				<td>
					width: <input type="text" size="4" name="fcc_ribbon_width"  maxlength="3" style="text-align:right; font-family: 'Courier New', Courier, mono; font-size: 0.9em;" value="<?php echo $ribbon_code['width']; ?>"/>px X 
					height:	<input type="text" size="4" name="fcc_ribbon_height" maxlength="3" style="text-align:right; font-family: 'Courier New', Courier, mono; font-size: 0.9em;" value="<?php echo $ribbon_code['height']; ?>"/>px
				</td>
			</tr>
			
			<tr valign="top">
				<th width="33%" scope="row">Destination Url:</th>
				<td><input type="text" size="40" name="fcc_ribbon_url" style="font-family: 'Courier New', Courier, mono; font-size: 0.9em;" value="<?php echo $ribbon_code['url']; ?>"/></td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row">Title Url:</th>
				<td><input type="text" size="40" name="fcc_ribbon_text" style="font-family: 'Courier New', Courier, mono; font-size: 0.9em;" value="<?php echo $ribbon_code['text']; ?>"/></td>
			</tr>
			<tr valign="top">
				<th width="33%" scope="row">Align:</th>
				<td>
					<select name="fcc_ribbon_align">
					<?php if ($ribbon_code['align'] == 'left') { $left = 'selected="selected"'; $right = '';} else {$right = 'selected="selected"'; $left = '';} ?>
					<option value="left" <?=$left?> ><?php echo __('Left');?></option>				
					<option value="right" <?=$right?> ><?php echo __('Right');?></option>				
					</select>
				</td>
			</tr>
			</table>
			
			</fieldset>
			<p><input type="submit" name="submit" value="<?php _e('update');?>" /></p>
		</form>
	</div>

<?php
}

?>