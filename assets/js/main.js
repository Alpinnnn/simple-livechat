$(document).ready(function() {
    // Dropdown toggle
    $('.dropdown-toggle').click(function(e) {
        e.preventDefault();
        $(this).siblings('.dropdown-menu').toggleClass('show');
    });

    // Close dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });

    // New chat button
    $('#new-chat-btn, #start-chat-btn').click(function(e) {
        e.preventDefault();
        $('#new-chat-modal').css('display', 'block');
        $('#user-search').focus();
    });

    // Global chat button
    $('#global-chat-btn').click(function(e) {
        e.preventDefault();
        window.location.href = 'chat.php?chat=global';
    });

    // Close modal
    $('.close-modal').click(function() {
        $('#new-chat-modal').css('display', 'none');
    });

    // Close modal when clicking outside
    $(window).click(function(e) {
        if ($(e.target).is('#new-chat-modal')) {
            $('#new-chat-modal').css('display', 'none');
        }
    });

    // User search
    let searchTimeout;
    $('#user-search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        
        if (query.length < 1) {
            $('#search-results').html('');
            return;
        }
        
        searchTimeout = setTimeout(function() {
            searchUsers(query);
        }, 500);
    });

    // Message form submission
    $('#messageForm').submit(function(e) {
        e.preventDefault();
        
        const message = $('#message-input').val().trim();
        const chatId = $('input[name="chat_id"]').val();
        
        if (message === '') return;
        
        // Send message using Ajax
        $.ajax({
            url: 'functions/send_message.php',
            type: 'POST',
            data: {
                chat_id: chatId,
                message: message
            },
            success: function(response) {
                // Clear input
                $('#message-input').val('');
                
                // Append message to chat (optional, can also wait for the refresh)
                const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const newMessage = `
                    <div class="message sent">
                        <div class="message-content">
                            ${message}
                            <div class="message-time">
                                ${currentTime}
                                <span class="message-status">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#chat-messages').append(newMessage);
                scrollToBottom();
            },
            error: function(xhr, status, error) {
                console.error("Error sending message:", error);
            }
        });
    });

    // Chat list search
    $('#search-input').on('input', function() {
        const query = $(this).val().toLowerCase();
        
        $('.chat-item').each(function() {
            const name = $(this).find('.chat-item-name').text().toLowerCase();
            
            if (name.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Initialize scroll to bottom
    scrollToBottom();
    
    // Real-time updates with polling
    if ($('#chat-messages').length) {
        setInterval(refreshChat, 3000);
    }
});

// Function to search users
function searchUsers(query) {
    $.ajax({
        url: 'functions/search_users.php',
        type: 'GET',
        data: { query: query },
        success: function(data) {
            $('#search-results').html(data);
            
            // Add click event for user items
            $('.user-item').click(function() {
                const userId = $(this).data('id');
                startChat(userId);
            });
        },
        error: function(xhr, status, error) {
            console.error("Error searching users:", error);
        }
    });
}

// Function to start a new chat
function startChat(userId) {
    $.ajax({
        url: 'functions/start_chat.php',
        type: 'POST',
        data: { user_id: userId },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success && data.chat_id) {
                    $('#new-chat-modal').hide();
                    window.location.href = 'chat.php?chat=' + data.chat_id;
                } else {
                    alert(data.message || 'Failed to start chat');
                }
            } catch (e) {
                console.error("Error parsing response:", e);
                alert('An error occurred');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error starting chat:", error);
            alert('Failed to start chat');
        }
    });
}

// Function to refresh chat messages
function refreshChat() {
    const chatId = $('input[name="chat_id"]').val();
    const lastMessageId = $('.message').last().data('id') || 0;
    
    $.ajax({
        url: 'functions/get_new_messages.php',
        type: 'GET',
        data: {
            chat_id: chatId,
            last_id: lastMessageId
        },
        success: function(data) {
            if (data.trim() !== '') {
                $('#chat-messages').append(data);
                scrollToBottom();
            }
        }
    });
}

// Function to scroll to bottom of chat
function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
} 