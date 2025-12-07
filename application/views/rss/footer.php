        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const mainContent = document.getElementById('mainContent');
            
            // Toggle sidebar on mobile
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                    sidebarBackdrop.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
                });
            }
            
            // Close sidebar when clicking on backdrop
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }
            
            // Auto-close sidebar when clicking on nav item on mobile
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 992) {
                        sidebar.classList.remove('open');
                        sidebarBackdrop.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
            
            // Handle alert dismiss buttons
            const alertDismissButtons = document.querySelectorAll('.alert-dismiss');
            alertDismissButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert-custom');
                    if (alert) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 300);
                    }
                });
            });
            
            // Auto-dismiss alerts after 5 seconds
            const autoDismissAlerts = document.querySelectorAll('.alert-custom');
            autoDismissAlerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 300);
                    }
                }, 5000);
            });
            
            // Responsive sidebar handling
            function handleResize() {
                if (window.innerWidth > 992) {
                    // Desktop: ensure sidebar is open and backdrop is hidden
                    sidebar.classList.add('open');
                    sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                } else {
                    // Mobile: start with sidebar closed
                    sidebar.classList.remove('open');
                }
            }
            
            // Initial check
            handleResize();
            
            // Listen for window resize
            window.addEventListener('resize', handleResize);
        });
    </script>
</body>
</html>