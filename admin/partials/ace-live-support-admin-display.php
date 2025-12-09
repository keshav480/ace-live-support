<?php
if (! defined('WPINC')) {
    die;
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Live_Support
 * @subpackage Ace_Live_Support/admin/partials
 */
$saved_tz = get_option('ace_timezone', 'Asia/Kolkata');
$dt = new DateTime('now', new DateTimeZone($saved_tz));
$current = $dt->format('Y-m-d H:i:s');
?>
<div class="wrap ace-chat-wrapper">

    <!-- USERS LIST -->
    <div id="ace-user-list">
        <h2>Users</h2>

        <?php
        if ($users) {
            foreach ($users as $row) {
                if ($row->message_update) {
                    $updated = strtotime($row->message_update);
                    $currenttime = strtotime($current);
                    $diff = $currenttime - $updated;
                    if ($diff > 300) {
                       $this->update_user_status($table ,$row->id);
                    }
                }
                $status = 'Offline';
                if (!empty($row->status)) {
                    if ($row->status === 'active') {
                        $status = 'Online';
                        $status_class = 'ace-status-online';
                    } else {
                        $status = 'Offline';
                        $status_class = 'ace-status-offline';
                    }
                }
                $active = '';
                if (isset($_GET['user'], $_GET['_wpnonce'])) {

                    $user_id = intval($_GET['user']); // sanitize user ID
                    $nonce   = sanitize_text_field(wp_unslash($_GET['_wpnonce'])); // sanitize nonce

                    if (wp_verify_nonce($nonce, 'select_user_' . $user_id)) {
                        $active = ($user_id === intval($row->user_id)) ? ' active' : '';
                    }
                }
                $new_message = $row->unread_count ? $row->unread_count : 0;
                $status_class = "ace-status-offline";
                if ($status === "Online") $status_class = "ace-status-online";
                elseif (strpos($status, "Last seen") !== false) $status_class = "ace-status-lastseen";

                $nonce = wp_create_nonce('select_user_' . $row->user_id);
                $user_url = add_query_arg(
                    [
                        'page'    => 'ace-live-chat',
                        'user'    => $row->user_id,
                        '_wpnonce' => $nonce
                    ],
                    admin_url('admin.php')
                );
        ?>
                <div class="ace-user-item-outer ace-user-item<?php echo esc_attr($active); ?>" data-userid="<?php echo esc_html($row->user_id); ?>" data-username="<?php echo esc_html($row->name); ?>">
                    <a href="<?php echo esc_url($user_url); ?>" class="user_detail">
                        <div class="name_status_outer">
                            <div class="ace-user-name">
                                <?php echo esc_html($row->name); ?>
                            </div>
                            <div class="ace-user-status <?php echo esc_attr($status_class); ?>">
                                
                            </div>
                            <?php if ($new_message):
                                $show_bell = empty($active) ? 'block' : 'none';
                            ?>
                                <span class="ace-unread-bell">
                                    <!-- <i class="fa fa-bell"></i> -->
                                </span>
                                <span class="ace-unread-count"><?php echo esc_html($new_message); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="ace-user-actions-dropdown">
                        <span class="ace-menu-toggle">⋮</span>
                        <ul class="ace-actions-menu">
                            <li class="ace-clear-chat" data-userid="<?php echo esc_attr($row->user_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('ace_clear_chat')); ?>">Clear Chat</li>
                            <li class="ace-delete-user" data-userid="<?php echo esc_attr($row->user_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('ace_delete_user')); ?>">Delete User</li>
                        </ul>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>

    <!-- CHAT AREA -->
    <div class="ace-chat-container">

        <div id="ace-chat-header">Chat</div>

        <div id="ace-admin-chat">
            <?php
            $user_selected = false;
            if (isset($_GET['user'], $_GET['_wpnonce'])) {

                $user_id = intval($_GET['user']); // sanitize user ID
                $nonce   = sanitize_text_field(wp_unslash($_GET['_wpnonce']));

                if (wp_verify_nonce($nonce, 'select_user_' . $user_id)) {
                    $user_selected = true;
                }
            }
            if (!$user_selected):  ?>
                <div class="ace-no-chat-selected">
                    <i class="fa fa-comments"></i>
                    <h3>Select a User to Start Chat</h3>
                    <p>Choose a user from the left panel to view or reply to messages.</p>
                </div>
            <?php else: ?>
                <div class="ace-admin-chat-message"></div>
            <?php endif; ?>
        </div>

        <div class="ace-chat-input-area">
            <input type="text" id="ace-admin-input" placeholder="Type reply..." />
            <button id="ace-admin-send">Send <div id="ace-loader" class="ace-loader loader" style="display:none;"></div></button>
        </div>

    </div>
</div>