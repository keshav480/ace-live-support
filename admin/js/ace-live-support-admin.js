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
		function appendMessage(msg, callback) {
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
			if(msg.type === 'file'){
				$.post(ace_chat_admin.ajax_url, {
					action: 'ace_get_file',
					user_id: selectedUser,
					message: msg.message,
					nonce: ace_chat_admin.nonce
				}, function (res) {
						
					if (!res.success || !res.data.files.length) return;
					let fileHtml = '';
				
					res.data.files.forEach(function (file) {

					if (file.type && file.type.startsWith('image/')) {
						fileHtml += `
							<div class="ace-file-preview image">
								<a href="${file.url}" target="_blank">
									<img src="${file.url}" width="120" alt="${file.name}"class="ace-file-preview" />
								</a>`;
						// if(msg.sender == 'user'){
						fileHtml +=`<div class="ace-file-actions">
									<span class="ace-actions-toggle">
										<i class="fa-solid fa-ellipsis-vertical"></i>
									</span>

									<div class="ace-actions-dropdown">
										<a href="${file.url}" 
										download="${file.name}" 
										class="ace-download-btn">
											<i class="fa-regular fa-circle-down"></i> Download
										</a>

										<a href="javascript:void(0)" 
										class="ace-delete-btn" 
										data-file="${file.name}">
											<i class="fa-regular fa-trash-can"></i> Delete
										</a>
									</div>
								</div>
									`;
								// }
						fileHtml +=`</div>`;
					} else if (file.type === 'application/pdf') {
						fileHtml += `
							<div class="ace-file-preview file application/pdf">
							<a href="${file.url}" target="_blank">📄 ${file.name}</a>`;
								// if(msg.sender == 'user'){
								fileHtml +=`<div class="ace-file-actions">
									<span class="ace-actions-toggle">
										<i class="fa-solid fa-ellipsis-vertical"></i>
									</span>

									<div class="ace-actions-dropdown">
										<a href="${file.url}" 
										download="${file.name}" 
										class="ace-download-btn">
											<i class="fa-regular fa-circle-down"></i> Download
										</a>

										<a href="javascript:void(0)" 
										class="ace-delete-btn" 
										data-file="${file.name}">
											<i class="fa-regular fa-trash-can"></i> Delete
										</a>
									</div>
								</div>
									`;
								// }
						fileHtml +=`</div>`;
					} else {
						fileHtml += `
							<div class="ace-file-preview file application/pdf">
								<a href="${file.url}" target="_blank">📎 ${file.name}</a>`;
							// if(msg.sender == 'user'){
								fileHtml +=`
								<div class="ace-file-actions">
									<span class="ace-actions-toggle">
										<i class="fa-solid fa-ellipsis-vertical"></i>
									</span>

									<div class="ace-actions-dropdown">
										<a href="${file.url}" 
										download="${file.name}" 
										class="ace-download-btn">
											<i class="fa-regular fa-circle-down"></i> Download
										</a>

										<a href="javascript:void(0)" 
										class="ace-delete-btn" 
										data-file="${file.name}">
											<i class="fa-regular fa-trash-can"></i> Delete
										</a>
									</div>
								</div>`;
							// }
						fileHtml += `</div>`;
					}
					});
					const msgHtml = `
						<div class="${cls} file">
							${fileHtml}
							<div class="ace-msg-time">${timeText}</div>
						</div>
					`;
					$("#ace-admin-chat").append(dateSeparator + msgHtml);
					$("#ace-admin-chat").scrollTop($("#ace-admin-chat")[0].scrollHeight);
					if (callback) callback();
				});
			}else{						
				var msgHtml = `<div class="${cls}_outer"><div class="${cls}">${msg.message}<div class="ace-msg-time">${timeText}</div></div>
								<div class="ace-file-actions">
									<span class="ace-actions-toggle">
										<i class="fa-solid fa-ellipsis-vertical"></i>
									</span>

									<div class="ace-actions-dropdown">
										<a href="javascript:void(0)" 
										class="ace-delete-btn" 
										data-file="${msg.message}">
											<i class="fa-regular fa-trash-can"></i> Delete
										</a>
									</div>
								</div></outer>`;
				$("#ace-admin-chat").append(dateSeparator+msgHtml);
				$("#ace-admin-chat").scrollTop($("#ace-admin-chat")[0].scrollHeight);
				if (callback) callback();
			}		
		}
		
		//   SEND ADMIN MESSAGE
		// -----------------------------
		function sendAdminMessage() {
			var message = $('#ace-admin-input').val().trim();
			if (!message || selectedUser == 0) return;
			$('#ace-admin-input').val('');
			// $('#ace-loader').show();
			$.post(ace_chat_admin.ajax_url, {
				action: 'ace_chat_send_admin_user',
				user_id: selectedUser,
				message: message,
				nonce: ace_chat_admin.nonce
			}, function (res) {
				// $('#ace-loader').hide(); 
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
						let messageQueue = res.data;
						let currentIndex = 0;
						
						function processNextMessage() {
							if (currentIndex < messageQueue.length) {
								let msg = messageQueue[currentIndex];
								currentIndex++;
								appendMessage(msg, processNextMessage);
							}
						}
						
						processNextMessage();
					}
				});
				subscribeToChannel(selectedUser);
			}
		}else{
			let firstUser = $('.ace-user-item').first();
			if (firstUser.length) {
				let userId = firstUser.data('userid');
				let userUrl = firstUser.find("a.user_detail").attr("href");
				window.location.href = userUrl;
			}			
		}

	// jquery for ace live support setting page 
		jQuery(document).ready(function($){
			$('input[name="ace_enable_chat"]').on('change', function(){
				if ($(this).is(':checked')) {
					$('.ace_enable_chat_credentials').fadeIn();
					$('.ace_enable_chat_credentials td input').prop('required', true);
					$('.notice.notice-warning.is-dismissible').hide()
				} else {
					$('.ace_enable_chat_credentials').fadeOut();
					 $('.ace_enable_chat_credentials td input').prop('required', false);
					 $('.notice.notice-warning.is-dismissible.onload').show()
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
							let firstUser = $('.ace-user-item').first();
							if (firstUser.length) {
								let userId = firstUser.data('userid');
								let userUrl = firstUser.find("a.user_detail").attr("href");
								window.location.href = userUrl;
							}		
						}
					});

				}
			});

			$('.ace-remove-icon').on('click', function() {
				var $wrapper = $(this).closest('.ace_support_icon_preview_wrapper');
				var $img = $wrapper.find('#ace_support_icon_preview');
				var defaultSrc = $img.data('default');
				$img.attr('src', defaultSrc);
				$('#ace_support_icon').val('');
				$('.ace-remove-icon').hide();
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

		jQuery(document).ready(function($){
			$('input[type="color"]').each(function () {
				let colorPicker = $(this);
				let name = colorPicker.attr('name'); 
				let textField = $('#' + name + '_text'); 
				colorPicker.on('input', function () {
					textField.val($(this).val());
				});
				textField.on('input', function () {
					let val = $(this).val();
					let isHex = /^#([0-9A-F]{3}){1,2}$/i.test(val);
					if (isHex) {
						colorPicker.val(val);
					}
				});
			});
		});

jQuery(document).ready(function ($) {

    const fileInput = $("#ace_upload_input");
    const previewImg = $("#ace_support_icon_preview");
    const hiddenInput = $("#ace_support_icon"); 
    const removeBtn = $(".ace-remove-icon");
    const defaultImg = previewImg.data("default");
    const dropArea = $(".ace-upload-box");
	const uploadText = $(".ace-upload-text");
	const maxSize = 10 * 1024 * 1024; // 10 MB
    // Drag & drop events
    dropArea.on("dragover", function (e) {
        e.preventDefault();
        dropArea.addClass("dragover-border");
    });
    dropArea.on("dragleave", function (e) {
        dropArea.removeClass("dragover-border");
    });
    dropArea.on("drop", function (e) {
        e.preventDefault();
        dropArea.removeClass("dragover-border");
        let files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
			if (files[0].size > maxSize) {
                alert("File too large! Max size allowed is 10 MB.");
                fileInput.val("");
                uploadText.text("No file chosen");
                return;	
            }
            fileInput[0].files = files;  
            handleFile(files[0]);
        }
    });

    // File input change
    fileInput.on("change", function () {
        if (this.files.length > 0) {
			if (this.files[0].size > maxSize) {
                alert("File too large! Max size allowed is 10 MB.");
                fileInput.val("");
                uploadText.text("No file chosen");
                return;	
            }
            handleFile(this.files[0]);
        }
    });

    // Remove button click
    removeBtn.on("click", function () {
        previewImg.hide();
        $("#ace_support_icon_font").show();
        hiddenInput.val("");
        fileInput.val(""); 
        $(this).hide();
		uploadText.text("No file chosen");
    });

    function handleFile(file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            previewImg.attr("src", e.target.result).show();
            $("#ace_support_icon_font").hide();
            hiddenInput.val(""); 
            removeBtn.show();
			uploadText.text(file.name)
        };
        reader.readAsDataURL(file);
    }
});
jQuery(document).ready(function () {
    $(".ace-tab-wrapper .nav-tab").on("click", function (e) {
        e.preventDefault();
        $(".nav-tab").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");
        $(".ace-tab-content").removeClass("active");
        let target = $(this).attr("href");
        $(target).addClass("active");
    });
	$(".ace-bulk-menu-toggle").on("click", function (e) {
        e.stopPropagation(); 
        $("#ace-user-list .ace-bulk-actions-menu").toggle();
    });

	
	function toggleBulkClearMode(activeitem, nonce, messsage){ 
		$(".ace-bulk-actions-menu").hide();
		$(".checkbox_update_option_outer").show();
		var allUserIds = [];
		$(".ace-bulk-user-checkbox:checked").each(function() {
			allUserIds.push($(this).data("userid"));
		});
		if(activeitem){
			$.post(ace_chat_admin.ajax_url, {
				action: 'ace_bulk_user_action',
				user_ids: allUserIds,
				bulk_action: activeitem,
				nonce: nonce
			}, function (res) {
				if (res.success) {
					location.reload();
				} else {
					alert("An error occurred while performing the bulk action.");
				}
			});
		}
	}

// Click handler
$("#ace-user-list .ace-bulk-actions-menu li").on("click", function(e) {
    e.stopPropagation();
    var $this = $(this);
    $this.siblings().removeClass("active");
    $this.addClass("active");
	var action = $this.data("action");
	if ($(".ace-bulk-user-checkbox:checked").length > 0) {
		if (confirm("Are you sure you want to " + $this.text() + "?")) {
            toggleBulkClearMode(action , $this.data("nonce"), $this.text());
        }
	} else {
		alert("Please select at least one user.");
	}
});

});
$(document).on("click", function (e) {
	if (!$(e.target).closest(".ace-bulk-actions-menu").length &&
		!$(e.target).closest(".ace-bulk-menu-toggle").length) {
		$(".ace-bulk-actions-menu").hide();
	}
});

$("#ace-bulk-select-all-users").change(function() {
	var isChecked = $(this).is(":checked");
	if(isChecked){
	$("#ace-user-list .ace-bulk-user-checkbox").prop("checked", isChecked).show();
	}else{
	$("#ace-user-list .ace-bulk-user-checkbox").prop("checked", isChecked).hide();
	}
});

jQuery(document).ready(function($) {
    $('#ace-user-search-input').on('input', function() {
        var query = $(this).val().toLowerCase();
        $('.ace-user-item-outer').each(function() {
            var username = $(this).data('username').toLowerCase();
	         var name = $(this).data('username').toLowerCase();
            if (username.indexOf(query) !== -1 || name.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

$(document).ready(function() {
    let selectedFiles = [];
    $('#ace-admin-upload-btn').on('click', function() {
        $('#ace-admin-files').click();
    });
    $('#ace-admin-files').on('change', function() {
		let urlParams = new URLSearchParams(window.location.search);
		var selectedUser = '';
		if (urlParams.has("user")) {
			let uid = urlParams.get("user");
			let userItem = $('.ace-user-item[data-userid="' + uid + '"]');
			selectedUser = $(userItem).data('userid');
		}
		
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
		$('#ace-admin-send').on('click', function() {
			if(selectedFiles .length === 0){
				return;
			}
			const message = $('#ace-admin-input').val();
			$('#ace-loader').show();
			const formData = new FormData();
			formData.append('action', 'upload_chat_message'); 
			formData.append('user_id', selectedUser);
			formData.append('message', message);
			formData.append('nonce', ace_chat_admin.nonce);
			formData.append('message', message);
			
			selectedFiles.forEach(file => formData.append('files[]', file));
			$.ajax({
			url: ace_chat_admin.ajax_url,
			type: 'POST',
			data: formData,
			processData: false, // Important!
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
// show message option 
$(document).on('click', '.ace-actions-toggle', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('.ace-actions-dropdown').not($(this).next()).hide();
    $(this).next('.ace-actions-dropdown').toggle();
});

$(document).on('click', function () {
    $('.ace-actions-dropdown').hide();
});
// delete the message 
$(document).on('click', '.ace-delete-btn', function () {
    if (!confirm('Delete this file?')) return;

    const fileName = $(this).data('file');
    const wrapper = $(this).closest('.ace-file-preview');

    $.post(ace_chat_admin.ajax_url, {
        action: 'ace_delete_message_file',
        user_id: selectedUser,
        file: fileName,
        nonce: ace_chat_admin.nonce
    }, function (res) {
        if (res.success) {
            wrapper.fadeOut(300, function () {
                $(this).remove();
            });
        }
    });
});



})( jQuery );
