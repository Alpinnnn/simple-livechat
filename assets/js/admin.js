document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const chatMessages = document.getElementById('chat-messages');
    const displayedMessages = new Set(); // Menyimpan ID pesan yang sudah ditampilkan

    // Fungsi untuk scroll ke bawah - lebih robust
    function scrollToBottom() {
        // Gunakan timeout untuk memastikan DOM sudah diperbarui
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    }

    // Hapus pesan welcome saat percakapan dimulai
    function removeWelcomeMessage() {
        const welcomeMsg = chatMessages.querySelector('.text-center.text-muted');
        if (welcomeMsg) {
            chatMessages.removeChild(welcomeMsg);
        }
    }

    function addMessage(message, isUser = false, messageId) {
        if (displayedMessages.has(messageId)) return; // Cek apakah pesan sudah ditampilkan
        
        // Hapus pesan welcome jika masih ada
        removeWelcomeMessage();
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'admin'}`;
        messageDiv.textContent = message;
        messageDiv.setAttribute('data-message-id', messageId);
        chatMessages.appendChild(messageDiv);
        
        displayedMessages.add(messageId); // Tambahkan ID pesan ke set
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        
        if (message) {
            fetch('../api/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message, is_admin: true })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Kosongkan input, pesan akan ditampilkan dari fetch berikutnya
                    messageInput.value = '';
                    scrollToBottom();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    function fetchNewMessages() {
        fetch('../api/get_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.messages) {
                    let newMessages = false;
                    data.messages.forEach(msg => {
                        if (!displayedMessages.has(msg.id)) {
                            addMessage(msg.message, msg.is_user === '1', msg.id);
                            newMessages = true;
                        }
                    });
                    
                    // Scroll ke bawah jika ada pesan baru
                    if (newMessages) {
                        scrollToBottom();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Panggil scroll di awal untuk memastikan scroll sudah di bawah
    scrollToBottom();
    
    // Ambil pesan baru setiap 3 detik
    setInterval(fetchNewMessages, 3000);
    fetchNewMessages(); // Panggil fetch pertama kali
    
    // Pastikan form input selalu terlihat
    window.addEventListener('resize', scrollToBottom);
    
    // Tambahkan event listener untuk scroll manual
    chatMessages.addEventListener('scroll', function() {
        // Simpan posisi scroll untuk pemulihan nanti jika diperlukan
        const isScrolledToBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= chatMessages.scrollTop + 50;
        chatMessages.setAttribute('data-scrolled-to-bottom', isScrolledToBottom);
    });
}); 