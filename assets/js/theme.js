/**
 * Theme and responsive handling for WhatsApp Clone
 */
document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        // Check for saved theme preference or prefer-color-scheme
        const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.getItem('whatsapp-theme');
        
        // Apply theme based on saved preference or system preference
        if (savedTheme === 'dark' || (!savedTheme && prefersDarkMode)) {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }
        
        // Toggle dark mode when checkbox is clicked
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('whatsapp-theme', 'dark');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('whatsapp-theme', 'light');
            }
        });
    }
    
    // Mobile responsiveness
    setupMobileLayout();
    
    // Handle window resize events
    window.addEventListener('resize', function() {
        setupMobileLayout();
    });
});

/**
 * Set up mobile layout and event listeners
 */
function setupMobileLayout() {
    const isMobile = window.innerWidth <= 768;
    const sidebar = document.querySelector('.sidebar');
    const chatArea = document.querySelector('.chat-area');
    const backButton = document.querySelector('.back-button');
    const chatItems = document.querySelectorAll('.chat-item');
    
    if (isMobile && sidebar && chatArea) {
        // Add back button to chat header if it doesn't exist
        const chatHeader = document.querySelector('.chat-header-info');
        if (chatHeader && !backButton) {
            const backBtn = document.createElement('button');
            backBtn.className = 'back-button';
            backBtn.innerHTML = '<i class="fas fa-arrow-left"></i>';
            backBtn.addEventListener('click', function() {
                sidebar.classList.remove('hidden');
                chatArea.classList.remove('active');
            });
            chatHeader.parentNode.insertBefore(backBtn, chatHeader);
        }
        
        // Add click events to chat items to hide sidebar
        chatItems.forEach(item => {
            item.addEventListener('click', function() {
                if (isMobile) {
                    sidebar.classList.add('hidden');
                    chatArea.classList.add('active');
                }
            });
        });
        
        // Check if a chat is active on page load
        const activeChat = chatArea.querySelector('.chat-header');
        if (activeChat) {
            sidebar.classList.add('hidden');
            chatArea.classList.add('active');
        } else {
            sidebar.classList.remove('hidden');
            chatArea.classList.remove('active');
        }
    } else {
        // Remove mobile-specific classes and elements for desktop
        if (sidebar) sidebar.classList.remove('hidden');
        if (chatArea) chatArea.classList.remove('active');
        if (backButton) backButton.remove();
    }
}

/**
 * Format file size for uploads
 * @param {number} bytes - File size in bytes
 * @return {string} Formatted file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Create a typing indicator in the chat
 * @param {string} username - The username of the person typing
 */
function showTypingIndicator(username) {
    const chatMessages = document.getElementById('chat-messages');
    
    // Remove existing typing indicator
    const existingIndicator = document.querySelector('.typing-indicator');
    if (existingIndicator) {
        existingIndicator.remove();
    }
    
    // Create new typing indicator
    const indicator = document.createElement('div');
    indicator.className = 'typing-indicator';
    indicator.innerHTML = `
        <span>${username} is typing</span>
        <span class="typing-dots">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        </span>
    `;
    
    // Add to chat
    if (chatMessages) {
        chatMessages.appendChild(indicator);
        scrollToBottom();
    }
}

/**
 * Hide the typing indicator
 */
function hideTypingIndicator() {
    const indicator = document.querySelector('.typing-indicator');
    if (indicator) {
        indicator.remove();
    }
} 