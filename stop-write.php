<?php
/*
Plugin Name: Stop Write
Plugin URI: http://wordpress.org/extend/plugins/stop-write/
Description: A wordpress plugin that allows you to secure your site from changes. You can prevent changes to your wp-admin, wp-content, wp-includes directories. Use this plugin if you got a nasty virus making changes to your site, cos it won't be able to make changes if the site is non writable. There are some cons though, you won't be able to upgrade if the site is completely non writable. When you do want to upgrade, make everything writable beforehand.

Installation:

1) Install WordPress 3.5.1 or higher

2) Download the latest from:

http://wordpress.org/extend/plugins/stop-write

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 1.0
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/

add_action('admin_menu', 'register_stop_write_page');
function register_stop_write_page() {
  add_menu_page('Stop Write', 'Stop Write', 'manage_options', 'stop-write/stop-write.php', 'stop_write_initial_page');
}

//call register settings function
add_action( 'admin_init', 'register_stop_write_settings' );
function register_stop_write_settings() {
  register_setting( 'stop-write-settings-group', 'stop_write_wp_admin' );
  register_setting( 'stop-write-settings-group', 'stop_write_wp_content' );
  register_setting( 'stop-write-settings-group', 'stop_write_wp_includes' );
}

function stop_write_initial_page() { ?>
	<div class="wrap">
	  <h2>Stop Write</h2>
	  <?php
	  if (isset($_POST["action"]) && $_POST["action"] == "Update Permissions") {

	  	$permission_array = array(
	  		"stop_write_wp_admin" => "/wp-admin", 
	  		"stop_write_wp_content" => "/wp-content", 
	  		"stop_write_wp_includes" => "/wp-includes"
	  	);

	  	foreach ($permission_array as $key => $value) {
				update_option($key, $_POST[$key]);
				if ($_POST[$key] == "writable") {
					stop_write_change_permissions(ABSPATH . $value, 0755);
				} else {
					stop_write_change_permissions(ABSPATH . $value, 0555);
				}
	  	}

			echo("<div class='updated below-h2'><p>Permissions Updated.</p></div>");
		}
	  ?>
	  <p>Please be aware that you will not be able to upgrade Wordpress or any of your plugins if you make everything unwritable. The best thing to do is before upgrading return write permissions to the directories.</p>
    <div class="postbox " style="display: block; ">
    <div class="inside">
      <form action="" method="post">
        <table>
        	<tbody>
        		<?php stop_write_admin_row("WP Admin", "stop_write_wp_admin"); ?>
        		<?php stop_write_admin_row("WP Content", "stop_write_wp_content"); ?>
        		<?php stop_write_admin_row("WP Includes", "stop_write_wp_includes"); ?>
        	</tbody>

        </table>
        <p><input type="submit" name="action" value="Update Permissions"/></p>
      </form>
    </div>
    </div>
<?php
}

function stop_write_admin_row($label, $option_name) { ?>
  <tr>
		<th scope="row"><?php echo($label); ?></th>
		<td>
			<select name="<?php echo($option_name); ?>">
				<option value="writable" <?php 
					if (get_option($option_name) == "writable") {
						echo "selected";
					}
				?>>Writable</option>
				<option value="non-writable" <?php 
					if (get_option($option_name) == "non-writable") {
						echo "selected";
					}
				?>>Non-Writable</option>
			</select>
		</td>
	</tr>
<?php
}

function stop_write_change_permissions($src, $permission) { 
    $dir = opendir($src); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
              stop_write_change_permissions($src . '/' . $file, $permission);
              chmod($src . '/' . $file, $permission); 
            }
            chmod($src . '/' . $file, $permission);
        }   
    }
    closedir($dir); 
}

?>