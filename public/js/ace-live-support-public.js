(function( $ ) {
	'use strict';
	function initializeChat(userid) {
		var pusher = new Pusher(ace_chat_ajax.pusher_key, {
			cluster: ace_chat_ajax.pusher_cluster,
			forceTLS: true
		});
		var channel = pusher.subscribe('live-chat-' + userid);
		channel.bind('new-message', function(data){
			appendMessage(data);
		});
	}
	var $messages = $('#ace-chat-messages');
	var  input = $('#ace-chat-input');
	var userid= ace_chat_ajax.user_id; 
	var isGuest = (!userid || userid == 0);
	var lastMessageDate = '';


		function appendMessage(msg, callback) {

			const cls = msg.sender === 'user' ? 'ace-msg-user' : 'ace-msg-admin';
			let timeText = '';
			let dateSeparator = '';

			/* ---------- TIME + DATE ---------- */
			if (msg.time) {
				const date = new Date(msg.time);

				let hours = date.getHours();
				let minutes = date.getMinutes();
				const ampm = hours >= 12 ? 'PM' : 'AM';
				hours = hours % 12 || 12;
				minutes = minutes < 10 ? '0' + minutes : minutes;
				timeText = `${hours}:${minutes} ${ampm}`;

				const options = { day: '2-digit', month: 'short', year: 'numeric' };
				const formattedDate = date.toLocaleDateString('en-US', options);

				const today = new Date().toLocaleDateString('en-US', options);
				const yesterday = new Date(Date.now() - 86400000).toLocaleDateString('en-US', options);

				let displayDate = formattedDate;
				if (formattedDate === today) displayDate = 'Today';
				else if (formattedDate === yesterday) displayDate = 'Yesterday';

				if (lastMessageDate !== formattedDate) {
					dateSeparator = `<div class="ace-date-separator">${displayDate}</div>`;
					lastMessageDate = formattedDate;
				}
			}

			/* ---------- FILE MESSAGE ---------- */
			if (msg.type === 'file') {

				// Placeholder (prevents UI jump)
				const placeholderId = `file-${Date.now()}`;
				$('#ace-chat-messages').append(
					dateSeparator +
					`<div class="${cls} file" id="${placeholderId}">
						<div class="ace-file-loading">Loading files…</div>
						<div class="ace-msg-time">${timeText}</div>
					</div>`
				);

				$.post(ace_chat_ajax.ajax_url, {
					action: 'ace_get_file',
					user_id: ace_chat_ajax.user_id,
					message: msg.message,
					nonce: ace_chat_ajax.nonce
				}, function (res) {
					if (!res.success || !Array.isArray(res.data.files)) return;
					let fileHtml = '';
					res.data.files.forEach(file => {
						if (file.type && file.type.startsWith('image/')) {
							fileHtml += `
								<div class="ace-file-preview image">
									<a href="${file.url}" target="_blank">
										<img src="${file.url}" alt="${file.name}" /></a>`;
									fileHtml += `<div class="ace-file-actions">
										<span class="ace-actions-toggle">
										<i class="fa-solid fa-ellipsis-vertical"></i>
										</span>
									<div class="ace-actions-dropdown">`;
										if(msg.sender != 'user'){
									fileHtml +=`	<a href="${file.url}" 
										download="${file.name}" 
										class="ace-download-btn">
										<i class="fa-regular fa-circle-down"></i> Download
										</a>`;
										}
									if(msg.sender == 'user'){
										fileHtml +=`<a href="javascript:void(0)" 
										class="ace-delete-msg-btn" 
										data-file="${file.name}">
											<i class="fa-regular fa-trash-can"></i> Delete
										</a>`;
									}
									fileHtml +=`</div></div></div>`;
									
						} else if (file.type === 'application/pdf') {
							fileHtml += `
								<div class="ace-file-preview file">
									<a href="${file.url}" target="_blank">📄 ${file.name}</a>`;
									if(msg.sender != 'user'){
											fileHtml +=`
											<a href="${file.url}" download="${file.name}" class="ace-download-btn">
											<i class="fa-regular fa-circle-down"></i>
											</a>`;
										}
							fileHtml += `</div>`;
						} else {
							fileHtml += `
								<div class="ace-file-preview file">
									<a href="${file.url}" target="_blank">📎 ${file.name}</a>`
									if(msg.sender != 'user'){
											fileHtml +=`
											<a href="${file.url}" download="${file.name}" class="ace-download-btn">
											<i class="fa-regular fa-circle-down"></i>
											</a>`;
										}
								fileHtml += `</div>`;
						}
					});

					$(`#${placeholderId}`).html(`
						${fileHtml}
						<div class="ace-msg-time">${timeText}</div>
					`);
					$('#ace-chat-messages').scrollTop($('#ace-chat-messages')[0].scrollHeight);
					if (callback) callback();
				});

				return;
			}

			/* ---------- TEXT MESSAGE ---------- */
		let textMessageHtml = dateSeparator +
			`<div class="${cls}-outer"><div class="${cls}">
				${msg.message}
				<div class="ace-msg-time">${timeText}</div></div>`;
		
		if (msg.sender === 'user') {
			textMessageHtml += `
				<div class="ace-msg-actions">
					<span class="ace-actions-toggle">
						<i class="fa-solid fa-ellipsis-vertical"></i>
					</span>
					<div class="ace-actions-dropdown">
						<a href="javascript:void(0)" class="ace-delete-msg-btn" data-file="${msg.message || ''}">
							<i class="fa-regular fa-trash-can"></i> Delete
						</a>
					</div>
				</div>`;
		}
		
		textMessageHtml += `</div>`;
		$('#ace-chat-messages').append(textMessageHtml);
		$('#ace-chat-messages').scrollTop($('#ace-chat-messages')[0].scrollHeight);

			// if (callback) callback();
	}
	$(document).ready(function(){
		$('#ace-chat-messages').on('click', '.ace-actions-toggle', function (e) {
			e.stopPropagation();
			const $dropdown = $(this).siblings('.ace-actions-dropdown');
			$('.ace-actions-dropdown').not($dropdown).hide();
			$dropdown.toggle();
		});
		$('#ace-chat-messages').on('click', '.ace-delete-msg-btn', function (e) {
			e.stopPropagation();
		
			const fileName = $(this).data('file');
			if (!fileName) return;
			if (!confirm('Are you sure you want to delete this message?')) return;
			$.post(ace_chat_ajax.ajax_url, {
				action: 'ace_delete_message_public',
				user_id: ace_chat_ajax.user_id,
				file: fileName,
				nonce: ace_chat_ajax.nonce_delete_message
			}, function (res) {
				if (res.success) {
					$(`.ace-delete-msg-btn[data-file="${fileName}"]`).closest('.ace-msg-user-outer , .ace-msg-user.file').remove();
				} else {
					// alert('Error deleting message: ' + res.data);
				}
				$('.ace-actions-dropdown').hide();
				});
			});
	})
	$('#ace-chat-messages').on('click', function () {
		$('.ace-actions-dropdown').hide();
	});

		// Load previous messages
		function loadMessages(){
			$.post(ace_chat_ajax.ajax_url, {action: 'ace_chat_get', nonce: ace_chat_ajax.nonce_get_chat}, function(res){
				if(res.success){
					$messages.html('');
					res.data.forEach(function(msg){
						appendMessage(msg);
					});
				}
			});
		}
		// loadMessages(); 
		
		// Send message
		function sendMessage() {
			var message =  $('#ace-chat-input').val().trim();
			if (message === '') return;
			 $('#ace-chat-input').val('');
			// $('#ace-loader-send-button').show();
			$.post(
				ace_chat_ajax.ajax_url,
				{
					action: 'ace_chat_send',
					message: message,
					nonce: ace_chat_ajax.nonce
				},
				function(res) {
					if (res.success) {
						// $('#ace-loader-send-button').hide();
						var user_id = res.data.user_id;
						if (res.data.register === true) {
							initializeChat(user_id);
						}
						// loadMessages();
					}
				}
			)
		}
		
	
		$('#ace-chat-send').click(sendMessage);
	
		 input.keypress(function(e){
			if(e.which === 13){ sendMessage(); return false; }
		});
		$(document).on('click', function () {
			$('#ace-live-chat').hide();
			$('.chat_logo').show();
			$('.chat_close_icon').hide();
			$('.ace-actions-dropdown').hide();
		});
		$('#chatPage').on('click', function (e) {
			e.stopPropagation();
		});
		function toggleChatBox() {
			if(userid){
				initializeChat(userid)
				loadMessages()
			}
			$('#ace-live-chat').toggle(); 
			$('.chat_logo').toggle();     
			$('.chat_close_icon').toggle()
		}
		$('.chat_page .chat_button').click(toggleChatBox)
		if (isGuest) {
			$('#ace-chat-messages').hide();
			$('.ace-chat-send_input').hide();
			$('.guest_user_login_form').show();
		}

		function toggleForms(showEmailForm) {
			if (showEmailForm) {
				$('#ace-guest-email-form').show();
				$('#ace-guest-otp-form').hide();
				$('.forwardToOtp').addClass('active');
				$('.backToEmail').removeClass('active');
			} else {
				$('#ace-guest-email-form').hide();
				$('#ace-guest-otp-form').show();
				$('.forwardToOtp').removeClass('active');
				$('.backToEmail').addClass('active');
			}
		}
		function get_otp_byemail() {
			var email = $('#ace-guest-email').val();
			if (!email) {
				$('#ace-email-error').show();
				return;
			}
			var nonce = $('input[name="ace_guest_email_nonce"]').val();
			$('#ace-loader').show();
			startCountdown(60);
			$('#resendOtpBtn').off('click').on('click', get_otp_byemail);
			$('.backToEmail').addClass('active');
			$.post(ace_chat_ajax.ajax_url, {
				action: 'ace_save_guest_email',
				ace_guest_email: email,
				ace_guest_email_nonce: nonce
			}, function (res) {
				$('#ace-loader').hide();
				if (res.success) {
					toggleForms(false); 
				} else {
					$('#ace-email-error').text(res.data.message).show();
				}
			});
		}
		
		// Toggle Buttons — only bind once
		$('.backToEmail').on('click', function () {
			if ($(this).hasClass('active')) {
				toggleForms(true);
			}
		});
		$('.forwardToOtp').on('click', function () {
			if ($(this).hasClass('active')) {
				toggleForms(false);
			}
		});
		
		// Form Submit
		$('#ace-guest-email-form').on('submit', function (e) {
			e.preventDefault();
			get_otp_byemail();
		});
		
		$('#ace-guest-otp-form').on('submit', function(e){
			e.preventDefault();	
			var email = $('#ace-guest-email').val();
			var nonce = $('input[name="ace_otp_nonce"]').val();
			var otp = $('input[name="ace-otp-input"]').val();
			$('#ace-otp-loader').show();
			$.post(ace_chat_ajax.ajax_url, {
				action: 'ace_save_guest_email',
				ace_guest_email: email,
				ace_otp_input:otp,
				ace_otp_nonce: nonce
				
			}, function(res){
				$('#ace-otp-loader').hide();
				if(res.success){
					var user_id=res.data.ace_guest_id
					initializeChat(user_id);
					loadMessages();
					$('#ace-chat-messages').show();
					$('.ace-chat-send_input').show();
					$('.guest_user_login_form').hide();
					$('.guest_user_login_form').remove();
					$('.backToEmail , .forwardToOtp').remove();
				} else {
					$('#ace-otp-error').html(res.data.message).show();
				}
			});
		});		
		// count down show when user 
		function startCountdown(seconds = 60) {
			const btn = document.getElementById('resendOtpBtn');
			const timer = document.getElementById('countdown');
		
			btn.style.pointerEvents = 'none';
			btn.style.opacity = '0.5';
		
			let timeLeft = seconds;
			timer.textContent = `(${timeLeft}s)`;
		
			const interval = setInterval(() => {
				timeLeft--;
				timer.textContent = `(${timeLeft}s)`;
		
				if (timeLeft <= 0) {
					clearInterval(interval);
					timer.textContent = "";
					btn.style.pointerEvents = 'auto';
					btn.style.opacity = '1';
				}
			}, 1000);
		}
	
	// multiple file upload for public 
	$(document).ready(function() {
		let selectedFiles = [];
		$('#ace-public-upload-btn').on('click', function() {
			$('#ace-public-files').click();
		});
		var selectedUser = ace_chat_ajax.user_id;
		$('#ace-public-files').on('change', function() {
			
			const newFiles = Array.from(this.files); 
			const previewDiv = $('#ace-file-preview');	
			selectedFiles = selectedFiles.concat(newFiles);
			previewDiv.empty();
			selectedFiles.forEach((file, index) => {
				let fileDiv = $('<div>', {
					css: {
						padding: '5px',
						border: '1px solid #ccc',
						borderRadius: '4px',
						display: 'flex',
						alignItems: 'center',
						gap: '5px'
					}
				});
				if (file.type.startsWith('image/')) {
					const reader = new FileReader();
					reader.onload = function(e) {
						const img = $('<img>', {
							src: e.target.result,
							width: 50,
							height: 50,
							css: { objectFit: 'cover', borderRadius: '4px' }
						});
						fileDiv.prepend(img);
						fileDiv.append(`<button data-index="${index}" style="border:none; background:none; cursor:pointer; color:red;">&times;</button>`);
					};
					reader.readAsDataURL(file);
				} else if (file.type === 'application/pdf') {
					const pdfIcon = $('<span>').text('📄 ' + file.name);
					fileDiv.append(pdfIcon);
					fileDiv.append(`<button data-index="${index}" style="border:none; background:none; cursor:pointer; color:red;">&times;</button>`);
				} else {
					const fileSpan = $('<span>').text('📎 ' + file.name);
					fileDiv.append(fileSpan);
					fileDiv.append(`<button data-index="${index}" style="border:none; background:none; cursor:pointer; color:red;">&times;</button>`);
				}
				previewDiv.append(fileDiv);
			});
			$('#ace-admin-files').val('');
		});
		$('#ace-file-preview').on('click', 'button', function() {
				const index = $(this).data('index');
				selectedFiles.splice(index, 1);
				$(this).parent().remove();
				$('#ace-file-preview div button').each(function(i){
					$(this).data('index', i);
				});
			});
			$('#ace-chat-send').on('click', function() {
				if(selectedFiles .length === 0){
					return;
				}
				const message = $('#ace-chat-input').val();
				$('#ace-loader').show();
				const formData = new FormData();
				formData.append('action', 'upload_chat_message_public'); 
				formData.append('user_id', selectedUser);
				formData.append('message', message);
				formData.append('nonce', ace_chat_ajax.public_upload_file);
				formData.append('message', message);
				
				selectedFiles.forEach(file => formData.append('files[]', file));
				$.ajax({
				url: ace_chat_ajax.ajax_url,
				type: 'POST',
				data: formData,
				processData: false, 
				contentType: false,    
				success: function(res) {
					$('#ace-loader').hide();
					if (res.success) {
						$('#ace-admin-input').val('');
						selectedFiles = [];
						$('#ace-file-preview').empty();
					} else {
						alert('Error: ' + res.data);
					}
				},
				error: function(err) {
					$('#ace-loader').hide();
					console.error(err);
					alert('Error sending message');
				}
			});
		});
	});


})( jQuery );
