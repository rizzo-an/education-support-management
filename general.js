document.addEventListener('DOMContentLoaded', function() {
    

    const profileBtn = document.getElementById('userProfileBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(event) {
            profileDropdown.classList.toggle('show');
            event.stopPropagation(); 
        });

        document.addEventListener('click', function(event) {
            if (!profileBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleMenu() {
        if(sidebar && overlay) { 
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleMenu);
    }

});