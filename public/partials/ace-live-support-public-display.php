<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Live_Support
 * @subpackage Ace_Live_Support/public/partials
 */
if (! defined('WPINC')) {
    die;
}
// Get chat settings
$chat_icon                  = esc_url(get_option('ace_support_icon', '')); // fallback icon
$chat_widget_txt            = esc_html(get_option('ace_widget_text', 'Ace Live Support'));
$btn_bg_color               = esc_attr(get_option('ace_btn_bg_color', '#ff0000'));
$btn_txt_color              = esc_attr(get_option('ace_btn_txt_color', '#fff'));
$chatbox_bg                 = esc_attr(get_option('ace_chatbox_bg_color', '#f5f5f5'));
$chatbox_txt                = esc_attr(get_option('ace_chatbox_txt_color', '#000'));

$header_bg_color            = esc_attr(get_option('ace_chatbox_header_bg_color', '#fffff'));
$header_color               = esc_attr(get_option('ace_chatbox_header_color', '#000'));
$enable_chat                = get_option('ace_enable_chat', 0);
$ace_chatbox_header_only    = get_option('ace_chatbox_header_only', 0);
$ace_pusher_app_id    = get_option('ace_pusher_app_id', '');



echo "<style>
    .ace-date-separator{
      color:" . esc_attr($chatbox_txt) . ";
    }
    #ace-save-email, #ace-verify-otp{
        background-color:" . esc_attr($btn_bg_color) . ";
        color:" . esc_attr($btn_txt_color) . ";
        margin-top:10px;
        width:100%;
        padding:10px;
        color:white;
        border-radius:5px;
    }
</style>";

$session_id = false;
if (session_id() && !empty($_SESSION['ace_guest_id'])) {
    $session_id = true;
}
?>
<?php if ($enable_chat && $ace_pusher_app_id): ?>
    <div id="chatPage" class="chat_page">
        <div class="chat_button" style="background-color: <?php echo esc_attr($btn_bg_color); ?>; color: <?php echo esc_attr($btn_txt_color); ?>;">
            <?php if ($chat_icon): ?>
                <img src="<?php echo esc_url($chat_icon); ?>" alt="Site Logo" class="chat_logo" />
            <?php else: ?>
                <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__)) . 'images/ace_live_chat.png'); ?>" class="chat_logo" style="width:100%" alt="">
            <?php endif; ?>
            <span class="chat_close_icon" style="display:none;">X</span>
        </div>

        <div id="ace-live-chat">
            <div id="ace-chat-header" style="background-color: <?php echo esc_attr($header_bg_color); ?>; color: <?php echo esc_attr($header_color); ?>;">
                <!-- New clean SVG arrows -->
                <?php if (! is_user_logged_in() && $session_id === false) : ?>
                    <a href="javascript:void(0);" class="ace-arrow-svg backToEmail">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M15 6l-6 6 6 6" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                <?php endif; ?>
                <?php echo esc_html($chat_widget_txt); ?>
                <?php if (! is_user_logged_in() && $session_id === false) : ?>
                    <a href="javascript:void(0);" class="ace-arrow-svg forwardToOtp">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M9 6l6 6-6 6" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            <?php if (! is_user_logged_in()): ?>
                <div class="guest_user_login_form" style="display:none;">
                    <form id="ace-guest-email-form" style="padding:15px;">
                        <div id="ace-email-box" style="padding:15px;">
                            <div class="guest_login_field_outer">
                                <h2 class="guest_form_heading">Enter your email to start chat</h2>
                                <div class="input_outer">
                                    <input type="email" name="ace_guest_email" id="ace-guest-email" placeholder="Enter your email" style="width: 100%;padding: 11px 0;text-align: center;border-radius: 6px;" />
                                    <button id="ace-save-email" type="submit">Continue
                                        <div id="ace-loader" style="display:none;">
                                            <span class="spinner"></span>
                                        </div>
                                    </button>
                                </div>
                                <p id="ace-email-error" style="color:red; display:none; margin-top:5px;">
                                    Please enter a valid email.
                                </p>
                                <input type="hidden" name="ace_guest_email_nonce" value="<?php echo esc_attr($email_nonce); ?>">
                            </div>
                        </div>
                    </form>
                    <form id="ace-guest-otp-form" style="display:none; padding:15px;">
                        <div id="ace-email-box" style="padding:15px;">
                            <div class="guest_login_field_outer">
                                <h2 class="guest_form_heading">Enter OTP</h2>
                                <div class="input_outer">
                                    <input type="number" id="ace-otp-input" name="ace-otp-input" maxlength="6" placeholder="Enter OTP" style="width: 100%;padding: 11px 0;text-align: center;border-radius: 6px;" />
                                    <button id="ace-verify-otp" type="submit">Verify
                                        <div id="ace-otp-loader" style="display:none;">
                                            <span class="spinner"></span>
                                        </div>
                                    </button>
                                    <span class="resendOtpBtn outer"> Didn’t get the code? <a href="javascript:void(0);" id="resendOtpBtn">Resend</a></span>
                                    <span id="countdown" style="margin-left: 10px; font-weight: bold;"></span>
                                </div>
                                <input type="hidden" name="ace_otp_nonce" value="<?php echo esc_attr($otp_nonce); ?>">
                            </div>
                            <p id="ace-otp-error" style="color:red; display:none; margin-top:5px;">
                                Please enter a valid OTP.
                            </p>
                        </div>
                    </form>
                </div>
            <?php endif; 
            ?>
            <div id="ace-chat-messages" style="background-color: <?php echo esc_attr($chatbox_bg); ?>; color: <?php echo esc_attr($chatbox_txt); ?>;"></div>
            <div class="ace-chat-send_input" style="background-color: <?php echo $ace_chatbox_header_only === '0' ? esc_attr($header_bg_color) : ''; ?>; color: <?php echo $ace_chatbox_header_only ? esc_attr($header_color) : ''; ?>;">
                <input type="text" id="ace-chat-input" placeholder="Type your message..." />
                <button id="ace-chat-send" style="background-color: <?php echo esc_attr($btn_bg_color); ?>; color: <?php echo esc_attr($btn_txt_color); ?>;">Send
                    <div id="ace-loader-send-button" style="display:none;">
                        <span class="spinner"></span>
                    </div>
                </button>
            </div>
        </div>
    </div>

<?php endif; ?>