<div class="card">
    <div class="card-body">
        <h2 class="card-title mb-4">Import RSS Feed</h2>
        
        <div id="alert-container"></div>
        
        <form id="import-form">
            <div class="mb-3">
                <label for="feed_url" class="form-label">RSS Feed URL</label>
                <input type="url" class="form-control" id="feed_url" name="feed_url" 
                       placeholder="https://example.com/feed.xml" required>
            </div>
            
            <div class="mb-3">
                <label for="sort_mode" class="form-label">Sort Mode</label>
                <select class="form-select" id="sort_mode" name="sort_mode">
                    <option value="DESC">DESC (Newest ? Oldest)</option>
                    <option value="ASC">ASC (Oldest ? Newest)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary w-100" id="import-btn">
                <span id="btn-text">Import Feed</span>
                <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
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