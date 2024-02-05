<?php
function custom_settings_menu()
{
	add_submenu_page(
		'edit.php?post_type=tf_reviewsystem',
		'Custom Settings',
		'Settings',
		'manage_options',
		'custom-settings',
		'custom_settings_page'
	);
}
add_action('admin_menu', 'custom_settings_menu');
// Register settings
function custom_settings_init()
{
    register_setting('custom-settings-group', 'smtp_host');
    register_setting('custom-settings-group', 'smtp_port');
    register_setting('custom-settings-group', 'smtp_username');
    register_setting('custom-settings-group', 'smtp_password');
    register_setting('custom-settings-group', 'hr_name');
    register_setting('custom-settings-group', 'hr_email');
}
add_action('admin_init', 'custom_settings_init');
// Create the settings page
function custom_settings_page()
{
?>
    <div class="">
        <form class="smtp-hrform" method="post" action="options.php">
            <?php settings_fields('custom-settings-group'); ?>
            <?php do_settings_sections('custom-settings-group'); ?>
            <table class="smtp-settingsform">
            <tr style="font-size:24px;">
                <td style="font-size:24px;text-align: center;margin-bottom: 20px;width: 100%;color: #2271b1;font-weight: 700;">SMTP Settings</td>
            </tr>
                <tr valign="top">
                    <th scope="row">SMTP Host</th>
                    <td><input type="text" name="smtp_host" value="<?php echo esc_attr(get_option('smtp_host')); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Port</th>
                    <td><input type="text" name="smtp_port" value="<?php echo esc_attr(get_option('smtp_port')); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Username</th>
                    <td><input type="text" name="smtp_username" value="<?php echo esc_attr(get_option('smtp_username')); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Password</th>
                    <td><input type="password" name="smtp_password" value="<?php echo esc_attr(get_option('smtp_password')); ?>" required /></td>
                </tr>
            </table>            
            <table class="hr-settingsform">
                <tr style="font-size:24px;">
                    <td style="font-size:24px;text-align: center;margin-bottom: 20px;width: 100%;color: #2271b1;font-weight: 700;">HR Details</td>
                </tr>
                <tr valign="top">
                    <th scope="row">HR Name</th>
                    <td><input type="text" name="hr_name" value="<?php echo esc_attr(get_option('hr_name')); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">HR Email</th>
                    <td><input type="text" name="hr_email" value="<?php echo esc_attr(get_option('hr_email')); ?>" required /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}