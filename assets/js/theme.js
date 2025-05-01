document.addEventListener('DOMContentLoaded', function() {
    const settingsBtn = document.getElementById('settingsBtn');
    const bottomBar = document.getElementById('bottomBar');
    const darkModeToggle = document.getElementById('darkModeToggle');

    // Toggle bottom bar
    settingsBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        bottomBar.classList.toggle('active');
    });

    // Close bottom bar when clicking outside
    document.addEventListener('click', function(event) {
        if (!bottomBar.contains(event.target) && !settingsBtn.contains(event.target)) {
            bottomBar.classList.remove('active');
        }
    });

    // Deteksi apakah kita berada di halaman admin
    const isAdminPage = window.location.pathname.includes('/admin/');
    
    // Set path API berdasarkan lokasi halaman
    const apiPath = isAdminPage ? '../api/toggle_dark_mode.php' : 'api/toggle_dark_mode.php';

    // Dark mode toggle
    darkModeToggle.addEventListener('change', function() {
        const isDarkMode = this.checked;
        document.documentElement.classList.toggle('dark-mode', isDarkMode);
        document.body.classList.toggle('dark-mode', isDarkMode);
        
        // Send request to update dark mode
        fetch(apiPath, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'dark_mode=' + isDarkMode
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Gagal mengubah mode gelap');
                // Kembalikan toggle ke posisi semula jika gagal
                this.checked = !isDarkMode;
                document.documentElement.classList.toggle('dark-mode');
                document.body.classList.toggle('dark-mode');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Kembalikan toggle ke posisi semula jika error
            this.checked = !isDarkMode;
            document.documentElement.classList.toggle('dark-mode');
            document.body.classList.toggle('dark-mode');
        });
    });
}); 