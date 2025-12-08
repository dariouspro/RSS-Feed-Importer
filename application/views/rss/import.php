
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import RSS Feed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --light-bg: #f8f9fa;
            --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .rss-container {
            max-width: 800px;
            width: 100%;
        }
        
        .header-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            border: none;
            text-align: center;
        }
        
        .header-card h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .header-card p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .main-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: var(--card-shadow);
            border: none;
        }
        
        .input-group-icon {
            background-color: #f1f3f9;
            border: 1px solid #dee2e6;
            border-right: none;
            padding: 12px 16px;
            border-radius: 10px 0 0 10px;
            color: var(--primary-color);
        }
        
        .form-control-custom {
            padding: 14px 16px;
            border-radius: 0 10px 10px 0;
            border: 1px solid #dee2e6;
            border-left: none;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control-custom:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            border-color: var(--primary-color);
        }
        
        .input-label {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        
        .sort-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        
        .sort-option {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            background: #f8f9fa;
        }
        
        .sort-option:hover {
            border-color: #bdc6ff;
            background: #f1f3ff;
        }
        
        .sort-option.active {
            border-color: var(--primary-color);
            background: rgba(67, 97, 238, 0.05);
        }
        
        .sort-option i {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: var(--primary-color);
        }
        
        .sort-option-title {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 4px;
        }
        
        .sort-option-desc {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .btn-import {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
            transition: var(--transition);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .btn-import:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(67, 97, 238, 0.2);
        }
        
        .btn-import:active {
            transform: translateY(0);
        }
        
        .btn-import:disabled {
            opacity: 0.7;
            transform: none !important;
        }
        
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .alert-custom {
            border-radius: 10px;
            padding: 16px 20px;
            border: none;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .rss-icon {
            color: #ff6600;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .examples {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .examples h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .example-item {
            padding: 8px 12px;
            background: white;
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 0.9rem;
            border-left: 3px solid var(--success-color);
            color: #495057;
        }
        
        .example-item:hover {
            background-color: #f1f3ff;
            cursor: pointer;
        }
        
        .progress-container {
            margin-top: 20px;
            display: none;
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, var(--success-color), #4895ef);
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .progress-text {
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 8px;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .main-card {
                padding: 25px;
            }
            
            .sort-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="rss-container">
        <div class="header-card">
            <div class="rss-icon">
                <i class="fas fa-rss"></i>
            </div>
            <h1>Import RSS Feed</h1>
            <p>Enter a feed URL to import content into your application</p>
        </div>
        
        <div class="main-card">
            <div id="alert-container"></div>
            
            <form id="import-form">
                <div class="mb-4">
                    <div class="input-label">
                        <i class="fas fa-link me-2"></i> RSS Feed URL
                    </div>
                    <div class="input-group">
                        <span class="input-group-icon">
                            <i class="fas fa-rss"></i>
                        </span>
                        <input type="url" class="form-control form-control-custom" id="feed_url" name="feed_url" 
                               placeholder="https://example.com/feed.cms" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="input-label">
                        <i class="fas fa-sort me-2"></i> Sort Articles By
                    </div>
                    <div class="sort-options">
                        <div class="sort-option active" data-value="DESC">
                            <i class="fas fa-arrow-down-wide-short"></i>
                            <div class="sort-option-title">Newest First</div>
                            <div class="sort-option-desc">Most recent articles first</div>
                        </div>
                        <div class="sort-option" data-value="ASC">
                            <i class="fas fa-arrow-up-wide-short"></i>
                            <div class="sort-option-title">Oldest First</div>
                            <div class="sort-option-desc">Oldest articles first</div>
                        </div>
                    </div>
                    <input type="hidden" id="sort_mode" name="sort_mode" value="DESC">
                </div>
                
                <div class="progress-container" id="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div class="progress-text" id="progress-text">Starting import...</div>
                </div>
                
                <button type="submit" class="btn btn-import" id="import-btn">
                    <span id="btn-text">Import RSS Feed</span>
                    <span id="btn-spinner" class="spinner d-none"></span>
                </button>
            </form>
            
           
          
        </div>
    </div>
<script>
document.getElementById('import-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('import-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const alertContainer = document.getElementById('alert-container');
    
    btn.disabled = true;
    btnText.textContent = 'Importing...';
    btnSpinner.classList.remove('d-none');
    alertContainer.innerHTML = '';
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('rss/fetch_feed') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            setTimeout(() => {
                window.location.href = '<?= base_url('rss/manage') ?>';
            }, 1500);
        } else {
            alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        alertContainer.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
    })
    .finally(() => {
        btn.disabled = false;
        btnText.textContent = 'Import Feed';
        btnSpinner.classList.add('d-none');
    });
});
</script>
<script>
        // DOM elements
        const importForm = document.getElementById('import-form');
        const importBtn = document.getElementById('import-btn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const alertContainer = document.getElementById('alert-container');
        const sortOptions = document.querySelectorAll('.sort-option');
        const sortModeInput = document.getElementById('sort_mode');
        const feedUrlInput = document.getElementById('feed_url');
        const progressContainer = document.getElementById('progress-container');
        const progressFill = document.getElementById('progress-fill');
        const progressText = document.getElementById('progress-text');
        const exampleItems = document.querySelectorAll('.example-item');
        
        // Sort mode selection
        sortOptions.forEach(option => {
            option.addEventListener('click', () => {
                sortOptions.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                sortModeInput.value = option.getAttribute('data-value');
            });
        });
        
        // Example feed selection
        exampleItems.forEach(item => {
            item.addEventListener('click', () => {
                const url = item.getAttribute('data-url');
                feedUrlInput.value = url;
                
                // Highlight the selected example
                exampleItems.forEach(i => i.style.backgroundColor = 'white');
                item.style.backgroundColor = '#f1f3ff';
            });
        });
        
        // Form submission
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate URL
            if (!feedUrlInput.value) {
                showAlert('Please enter a valid RSS feed URL', 'danger');
                return;
            }
            
            // Show loading state
            importBtn.disabled = true;
            btnText.textContent = 'Importing...';
            btnSpinner.classList.remove('d-none');
            
            // Show progress bar
            progressContainer.style.display = 'block';
            simulateProgress();
            
            // Clear any previous alerts
            alertContainer.innerHTML = '';
            
            // In a real implementation, this would be a fetch call to your server
            // For demo purposes, we'll simulate the API call
            simulateImportRequest();
        });
        
        // Function to simulate progress
        function simulateProgress() {
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) {
                    clearInterval(interval);
                    return;
                }
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `Fetching feed... ${Math.round(progress)}%`;
            }, 300);
        }
        
        // Function to simulate API request
        function simulateImportRequest() {
            // Simulate network delay
            setTimeout(() => {
                // Randomly determine success/failure for demo
                const isSuccess = Math.random() > 0.2;
                
                if (isSuccess) {
                    // Success response
                    progressFill.style.width = '100%';
                    progressText.textContent = 'Import complete!';
                    
                    showAlert('RSS feed imported successfully! Redirecting to feed manager...', 'success');
                    
                    // Simulate redirect after delay
                    setTimeout(() => {
                        // In real implementation, this would redirect to your feed manager
                        // window.location.href = '<?= base_url('rss/manage') ?>';
                        
                        // For demo, show success message and reset form
                        showAlert('Success! In a real application, you would be redirected to the feed manager.', 'success');
                        resetForm();
                    }, 2000);
                } else {
                    // Error response
                    showAlert('Failed to import RSS feed. Please check the URL and try again.', 'danger');
                    resetForm();
                }
            }, 2500);
        }
        
        // Function to show alert message
        function showAlert(message, type) {
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert-custom ${alertClass}`;
            alertDiv.innerHTML = `
                <div class="alert-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div>${message}</div>
            `;
            
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alertDiv);
        }
        
        // Function to reset form
        function resetForm() {
            importBtn.disabled = false;
            btnText.textContent = 'Import RSS Feed';
            btnSpinner.classList.add('d-none');
            
            // Hide progress bar after a delay
            setTimeout(() => {
                progressContainer.style.display = 'none';
                progressFill.style.width = '0%';
                progressText.textContent = 'Starting import...';
            }, 1000);
        }
        
        // Add some interactivity to the input field
        feedUrlInput.addEventListener('focus', function() {
            this.parentElement.parentElement.style.transform = 'translateY(-2px)';
        });
        
        feedUrlInput.addEventListener('blur', function() {
            this.parentElement.parentElement.style.transform = 'translateY(0)';
        });
    </script>
</body>
</html>
