<?php
if (! defined('WPINC')) {
    die;
}
?>


<div class="ace_live_wrap">
    <h1>Ace Live Chat Settings</h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('ace_live_chat_settings_group');
        do_settings_sections('ace-live-chat-settings');
        ?>
        <div class="form_outer">
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
                    <tr valign="top">
                        <th scope="row">SMTP Test</th>
                            <td>
                                <button type="button" class="button button-primary" id="ace_smtp_test_btn">
                                    Send Test Email
                                </button>
                            <p id="ace_smtp_test_result"></p>
                        </td>
                    </tr>

                    <!-- Upload Support Icon -->
                    <tr valign="top">
                        <th scope="row">Support Icon</th>
                        <td>
                            <?php
                            $icon = get_option('ace_support_icon');
                            ?>
                            <input type="text" id="ace_support_icon" name="ace_support_icon" value="<?php echo esc_attr($icon); ?>" class="input_style">
                            <input type="button" class="button-primary" value="Upload Icon" id="ace_upload_button" />
                            <br><br>
                            <?php if ($icon): ?>
                                <img src="<?php echo esc_url($icon); ?>" width="80" height="80" style="border:1px solid #ccc;">
                            <?php endif; ?>
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
        
            </table>
        </div>
        <?php submit_button(); ?>
    </form>
</div>