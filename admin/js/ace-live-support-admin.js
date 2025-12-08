(function( $ ) {
	'use strict';

		var selectedUser = 0;
		var currentChannel = null;
		var pusher = new Pusher(ace_chat_admin.pusher_key, {
			cluster: ace_chat_admin.pusher_cluster,
			forceTLS: true
		});
		function subscribeToChannel(user_id) {
			if (currentChannel) {
				pusher.unsubscribe(currentChannel);
			}
			currentChannel = "live-chat-" + user_id;
			var channel = pusher.subscribe(currentChannel);
			channel.bind("new-message", function (data) {
				if (selectedUser == data.user_id) {
					appendMessage(data);
				} else if (data.sender === "user") {
					let item = $('.ace-user-item[data-userid="' + data.user_id + '"]');
					item.addClass("incoming-highlight");
					setTimeout(() => item.removeClass("incoming-highlight"), 2000);
				}
			});
		}

		var lastMessageDate = ""; 
		function appendMessage(msg) {
			var cls = (msg.sender == 'user') ? 'ace-msg-user' : 'ace-msg-admin';
			var timeText = '';
			var dateSeparator = '';
		
			if (msg.time) {
				var d = new Date(msg.time);
				var hrs = d.getHours();
				var mins = d.getMinutes();
				var ampm = hrs >= 12 ? 'PM' : 'AM';
				hrs = hrs % 12 || 12;
				mins = mins < 10 ? '0' + mins : mins;
				timeText = hrs + ':' + mins + ' ' + ampm;
				var today = new Date();
				var yesterday = new Date();
				yesterday.setDate(today.getDate() - 1);
				var msgDateString = d.toDateString();
				var todayString = today.toDateString();
				var yesterdayString = yesterday.toDateString();
				let finalDateLabel = "";
				if (msgDateString === todayString) {
					finalDateLabel = "Today";
				} else if (msgDateString === yesterdayString) {
					finalDateLabel = "Yesterday";
				} else {
					var options = { day: '2-digit', month: 'short', year: 'numeric' };
					finalDateLabel = d.toLocaleDateString('en-US', options);
				}
				if (lastMessageDate !== msgDateString) {
					dateSeparator = `<div class="ace-date-separator">${finalDateLabel}</div>`;
					lastMessageDate = msgDateString;
				}
			}
		
			$("#ace-admin-chat").append(
				dateSeparator +
				`<div class="${cls}">
					${msg.message}
					<div class="ace-msg-time">${timeText}</div>
				</div>`
			);
		
			$("#ace-admin-chat").scrollTop($("#ace-admin-chat")[0].scrollHeight);
		}
		
		//   SEND ADMIN MESSAGE
		// -----------------------------
		function sendAdminMessage() {
			var message = $('#ace-admin-input').val().trim();
			if (!message || selectedUser == 0) return;
			$('#ace-admin-input').val('');
			$('#ace-loader').show();
			$.post(ace_chat_admin.ajax_url, {
				action: 'ace_chat_send_admin_user',
				user_id: selectedUser,
				message: message,
				nonce: ace_chat_admin.nonce
			}, function (res) {
				$('#ace-loader').hide();
				if (res.success) {
					$('#ace-admin-input').val('');
				}
			});
		}
	
		$('#ace-admin-send').click(sendAdminMessage);
		$('#ace-admin-input').keypress(function (e) {
			if (e.which == 13) {
				sendAdminMessage();
			}
		});
		// ------------------------------------
		//   AUTO-LOAD USER FROM URL
		// ------------------------------------
		let urlParams = new URLSearchParams(window.location.search);
		if (urlParams.has("user")) {
			let uid = urlParams.get("user");
			let userItem = $('.ace-user-item[data-userid="' + uid + '"]');
			if (userItem.length) {
				$('.ace-user-item').removeClass('active');
				$(userItem).addClass('active');
				$('.ace-user-item.active .ace-unread-count').hide();
				selectedUser = $(userItem).data('userid');
				$('#ace-chat-header').text($(userItem).find('.ace-user-name').text().trim())
				$('#ace-admin-chat').html('');
				$('.ace-chat-input-area').css('display', 'flex');
				$.post(ace_chat_admin.ajax_url, {
					action: 'ace_chat_get_user',
					user_id: selectedUser,
					nonce: ace_chat_admin.nonce
				}, function (res) {
					if (res.success) {
						$.each(res.data, function (i, msg) {
							appendMessage(msg);
						});
					}
				});
				subscribeToChannel(selectedUser);
			}
		}

	// jquery for ace live support setting page 
		jQuery(document).ready(function($){
			var mediaUploader;
			$('#ace_upload_button').click(function(e){
				e.preventDefault();
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}
				mediaUploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Support Icon',
					button: {
						text: 'Choose Icon'
					},
					multiple: false
				});
				mediaUploader.on('select', function() {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					$('#ace_support_icon').val(attachment.url);
				});
				mediaUploader.open();
			});
			
			$('input[name="ace_enable_chat"]').on('change', function(){
				if ($(this).is(':checked')) {
					$('.ace_enable_chat_credentials').fadeIn();
				} else {
					$('.ace_enable_chat_credentials').fadeOut();
				}
			});
			$(document).on('click', '.ace-menu-toggle', function(e){
				e.stopPropagation();
				$(this).siblings('.ace-actions-menu').toggle();
			});
		
			// Close dropdown when clicking outside
			$(document).click(function(){
				$('.ace-actions-menu').hide();
			});
		
			// Handle actions
			$(document).on('click', '.ace-clear-chat', function(){
				if(confirm('Are you sure you want to Delete chat?')){
						var userId = $(this).data('userid');
						var nonce = $(this).data('nonce');
						$.post(ace_chat_admin.ajax_url, {
							action: 'ace_clear_chat',
							user_id: userId,
							nonce: nonce
						}, function(response){
							location.reload();
						});
				}
			});
		
			$(document).on('click', '.ace-delete-user', function(){
				var userId = $(this).data('userid');
				if(confirm('Do you really want to remove this user?')){
					var userId = $(this).data('userid');
					var nonce = $(this).data('nonce');
					$.post(ace_chat_admin.ajax_url, {
						action: 'ace_delete_user',
						user_id: userId,
						nonce: nonce
					}, function(response){
						if(response.success){
							location.reload();
						}
					});

				}
			});
		});

		$("#ace_smtp_test_btn").click(function () {
			$("#ace_smtp_test_result").text("Testing SMTP...");
			$.post(ajaxurl, {
				action: "ace_test_smtp"
			}, function (response) {
				$("#ace_smtp_test_result").html(response.data);
			});
		});
})( jQuery );
