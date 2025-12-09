<?php
if (! defined('WPINC')) {
    die;
}
?>


<div class="ace_live_wrap">
    <h1>Ace Live Chat Settings</h1>
    
    <div class="nav-tab-wrapper ace-tab-wrapper">
          <a href="#tab-settings" class="nav-tab nav-tab-active">Settings</a>
            <a href="#tab-smtp" class="nav-tab ">SMTP</a>
         <a href="#tab-advance" class="nav-tab ">Advance</a>
    </div>

    <form method="post" action="options.php" enctype="multipart/form-data">
        <?php
        settings_fields('ace_live_chat_settings_group');
        do_settings_sections('ace-live-chat-settings');
        ?>
        <div class="form_outer">
        <div id="tab-settings" class="ace-tab-content active">
            <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Enable Chat Support</th>
                        <td>
                            <label class="ace-switch">
                                <input type="checkbox" name="ace_enable_chat" value="1" <?php $enable_chat = get_option('ace_enable_chat', 0);
                                 checked(1, $enable_chat, true); ?> />
                                <span class="ace-slider round"></span>
                            </label>

                        </td>
                    </tr>
                    <!-- Pusher App ID -->
                    <?php $enable_chat = get_option('ace_enable_chat', 0); ?>
                    <tr class="ace_enable_chat_credentials" style="display: <?php echo $enable_chat ? 'table-row' : 'none'; ?>;">
                    <th scope="row">Timezone</th>
                        <td>
                            <?php  $saved_tz = get_option('ace_timezone','Asia/Kolkata'); ?>
                            <select class="ace_timezone" name="ace_timezone">
                                <?php foreach (timezone_identifiers_list() as $tz): ?>
                                    <option value="<?php echo esc_attr($tz); ?>"
                                        <?php selected($saved_tz, $tz); ?>>
                                        <?php echo esc_html($tz); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="ace_enable_chat_credentials" style="display: <?php echo $enable_chat ? 'table-row' : 'none'; ?>;">
                        <th scope="row">Pusher App ID</th>
                        <td>
                            <input type="text" name="ace_pusher_app_id" value="<?php echo esc_attr(get_option('ace_pusher_app_id', '')); ?>" style="width:60%;">
                        </td>
                    </tr>

                    <tr class="ace_enable_chat_credentials" style="display: <?php echo $enable_chat ? 'table-row' : 'none'; ?>;">
                        <th scope="row">Pusher Key</th>
                        <td>
                            <input type="text" name="ace_pusher_key" value="<?php echo esc_attr(get_option('ace_pusher_key', '')); ?>" style="width:60%;">
                        </td>
                    </tr>

                    <tr class="ace_enable_chat_credentials" style="display: <?php echo $enable_chat ? 'table-row' : 'none'; ?>;">
                        <th scope="row">Pusher Secret</th>
                        <td>
                            <input type="text" name="ace_pusher_secret" value="<?php echo esc_attr(get_option('ace_pusher_secret', '')); ?>" style="width:60%;">
                            <p class="description">Keep this private. Do not share publicly.</p>
                        </td>
                    </tr>

                    <tr class="ace_enable_chat_credentials" style="display: <?php echo $enable_chat ? 'table-row' : 'none'; ?>;">
                        <th scope="row">Pusher Cluster</th>
                        <td>
                            <input type="text" name="ace_pusher_cluster" value="<?php echo esc_attr(get_option('ace_pusher_cluster', '')); ?>" style="width:60%;"><br>
                            <a href="https://dashboard.pusher.com/" target="_blank">https://dashboard.pusher.com/</a>
                        </td>
                    </tr>
                </table>
            </div>
                    <!-- <tr valign="top">
                        <th scope="row">SMTP Test</th>
                            <td>
                                <button type="button" class="button button-primary" id="ace_smtp_test_btn">
                                    Send Test Email
                                </button>
                            <p id="ace_smtp_test_result"></p>
                        </td>
                    </tr> -->
                
                    <!-- SMTP Settings Section -->
                <div id="tab-smtp" class="ace-tab-content">
                    <table class="form-table">
                    <tr valign="top">
                        <th colspan="2"><h2>SMTP Settings</h2></th>
                    </tr>
                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">SMTP Host</th>
                        <td>
                            <input type="text" name="ace_smtp_host" value="<?php echo esc_attr(get_option('ace_smtp_host', '')); ?>" style="width:60%;" placeholder="smtp.example.com">
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">SMTP Port</th>
                        <td>
                            <input type="number" name="ace_smtp_port" value="<?php echo esc_attr(get_option('ace_smtp_port', 587)); ?>" style="width:60%;" placeholder="587">
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">SMTP Username</th>
                        <td>
                            <input type="text" name="ace_smtp_username" value="<?php echo esc_attr(get_option('ace_smtp_username', '')); ?>" style="width:60%;" placeholder="username@example.com">
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">SMTP Password</th>
                        <td>
                            <input type="password" name="ace_smtp_password" value="<?php echo esc_attr(get_option('ace_smtp_password', '')); ?>" style="width:60%;" placeholder="password">
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">SMTP Encryption</th>
                        <td>
                            <select name="ace_smtp_encryption" style="width:60%;">
                                <option value="none" <?php selected(get_option('ace_smtp_encryption'), 'none'); ?>>None</option>
                                <option value="ssl" <?php selected(get_option('ace_smtp_encryption'), 'ssl'); ?>>SSL</option>
                                <option value="tls" <?php selected(get_option('ace_smtp_encryption'), 'tls'); ?>>TLS</option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">From Email</th>
                        <td>
                            <input type="email" name="ace_smtp_from_email" value="<?php echo esc_attr(get_option('ace_smtp_from_email', get_bloginfo('admin_email'))); ?>" style="width:60%;" placeholder="from@example.com">
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">From Name</th>
                        <td>
                            <input type="text" name="ace_smtp_from_name" value="<?php echo esc_attr(get_option('ace_smtp_from_name', get_bloginfo('name'))); ?>" style="width:60%;" placeholder="Your Site Name">
                                </br><a href="https://myaccount.google.com/apppasswords"target="_blank">https://myaccount.google.com/apppasswords</a>
                        </td>
                    </tr>

                    <tr valign="top" class="ace_enable_smtp_credentials">
                        <th scope="row">Test Email</th>
                        <td>
                            <button type="button" class="button button-primary" id="ace_smtp_test_btn">Send Test Email</button>
                            <p id="ace_smtp_test_result"></p>
                        </td>
                    </tr>
                </table>
            </div>
                <div id="tab-advance" class="ace-tab-content">
                <table class="form-table">
                    <!-- Upload Support Icon -->
                    <tr valign="top">
                        <th scope="row">Support Icon</th>
                     <td class="ace_support_icon_upload">
                        <!-- Upload Wrapper -->
                          <?php $icon = get_option('ace_support_icon'); 
                          $imagename= explode('/', $icon);
                            $imagename = end($imagename); 
                          ?>
                        <div class="ace-upload-wrapper">
                            <label class="ace-upload-box">
                               <div class=" ace-upload-box-content">
                                <h2>Drop files to upload</h2>
                                <p>or</p>
                                </div>
                                <span class="ace-upload-btn">Select Files</span>
                                <input type="file" name="ace_support_icon_file" id="ace_upload_input" accept="image/png, image/jpeg, image/webp">
                                <p>Maximum upload file size: 512 MB.</p>
                            </label>
                        </div>
                        <!-- Hidden field storing image URL -->
                       
                       <input type="hidden" id="ace_support_icon" name="ace_support_icon" value="<?php echo esc_attr($icon); ?>">
                        <!-- Preview Wrapper -->
                        <?php $default_icon = plugin_dir_url(dirname(__FILE__)) . 'images/ace_live_chat.png'; ?>

                        <div class="ace_support_icon_preview_wrapper">
                            <img id="ace_support_icon_preview"
                                src="<?php echo $icon ? esc_url($icon) : esc_url($default_icon); ?>"
                                data-default="<?php echo esc_url($default_icon); ?>" alt="Icon Preview">
                            <span class="ace-remove-icon"
                                style="display: <?php echo $icon ? 'inline-block' : 'none'; ?>;">&times;</span>
                        </div>
                        <br><br>
                    </td>
                    </tr>

                    <!-- Chat Widget Text -->
                    <tr valign="top">
                        <th scope="row">Widget Text</th>
                        <td>
                            <input type="text" name="ace_widget_text" value="<?php echo esc_attr(get_option('ace_widget_text', 'Ace Live Support')); ?>" class="input_style">
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Chat Header/Footer Background Color</th>
                        <td class="chat_box_header_footer_background">

                            <input type="color"
                                name="ace_chatbox_header_bg_color"
                                value="<?php echo esc_attr(get_option('ace_chatbox_header_bg_color', '#ffffff')); ?>">
                            <input type="text" id="ace_chatbox_header_bg_color_text" name="ace_chatbox_header_bg_color" value="<?php echo esc_attr(get_option('ace_chatbox_header_bg_color', '#ffffff')); ?>"style="width:80px; margin-left:10px;">
                            <!-- Toggle Switch -->
                            <label class="ace-switch">
                                <input type="checkbox"
                                    name="ace_chatbox_header_only"
                                    value="1"
                                    <?php checked(1, get_option('ace_chatbox_header_only', '#000000')); ?>>
                                <span class="ace-slider"></span>
                            </label>
                            <span style="margin-left:10px;">Enable only for header</span>

                        </td>
                    </tr>


                    <tr valign="top">
                        <th scope="row">Chat Header/Footer Text Color</th>
                        <td>
                            <input type="color" name="ace_chatbox_header_color" value="<?php echo esc_attr(get_option('ace_chatbox_header_color', '#000000')); ?>">
                            <input type="text" id="ace_chatbox_header_color_text" name="ace_chatbox_header_color" value="<?php echo esc_attr(get_option('ace_chatbox_header_color', '#000000')); ?>"class="input_color_style">
                            
                        </td>
                    </tr>


                    <!-- Button Background Color -->
                    <tr valign="top">
                        <th scope="row">Button Background Color</th>
                        <td>
                            <input type="color" id= "ace_btn_bg_color" name="ace_btn_bg_color" value="<?php echo esc_attr(get_option('ace_btn_bg_color', '#ff0000')); ?>">
                            <input type="text" id="ace_btn_bg_color_text" name="ace_btn_bg_color" value="<?php echo esc_attr(get_option('ace_btn_bg_color', '#ff0000')); ?>"class="input_color_style">
                        </td>
                    </tr>

                    <!-- Button Text Color -->
                    <tr valign="top">
                        <th scope="row">Button Text Color</th>
                        <td>
                            <input type="color" name="ace_btn_txt_color" value="<?php echo esc_attr(get_option('ace_btn_txt_color', '#ffffff')); ?>">
                             <input type="text" id="ace_btn_txt_color_text" name="ace_btn_txt_color" value="<?php echo esc_attr(get_option('ace_btn_txt_color', '#ffffff')); ?>"class="input_color_style">
                        </td>
                    </tr>

                    <!-- Chatbox Background Color -->
                    <tr valign="top">
                        <th scope="row">Chatbox Background Color</th>
                        <td>
                            <input type="color" name="ace_chatbox_bg_color" value="<?php echo esc_attr(get_option('ace_chatbox_bg_color', '#ffffff')); ?>">
                             <input type="text" id="ace_chatbox_bg_color_text" name="ace_chatbox_bg_color" value="<?php echo esc_attr(get_option('ace_chatbox_bg_color', '#ffffff')); ?>"class="input_color_style">
                        </td>
                    </tr>

                    <!-- Chatbox Text Color -->
                    <tr valign="top">
                        <th scope="row">Chatbox Text Color</th>
                        <td>
                            <input type="color" name="ace_chatbox_txt_color" value="<?php echo esc_attr(get_option('ace_chatbox_txt_color', '#000000')); ?>">
                                <input type="text" id="ace_chatbox_txt_color_text" name="ace_chatbox_txt_color" value="<?php echo esc_attr(get_option('ace_chatbox_txt_color', '#000000')); ?>"class="input_color_style">
                        </td>
                    </tr>
                </div>
            </table>
        </div>
        <?php submit_button(); ?>
    </form>
</div>