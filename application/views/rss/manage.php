<?php if ($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $this->session->flashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title mb-4">Manage Posts (<?= $total_posts ?> total)</h2>
        
        <?php if (empty($posts)): ?>
            <p class="text-center text-muted py-5">No posts yet. Import an RSS feed to get started.</p>
        <?php else: ?>
            <div id="posts-container">
                <?php foreach ($posts as $post): ?>
                    <?php 
                    $has_twitter = in_array('X (Twitter)', $post['platforms']);
                    $exceeds_limit = $has_twitter && $post['char_count'] > 280;
                    ?>
                   <div class="post-item p-3 mb-3 rounded <?= $exceeds_limit ? 'twitter-exceed' : '' ?>" 
     draggable="true" 
     data-id="<?= $post['id'] ?>"
     data-priority="<?= $post['priority'] ?>">
    <div class="d-flex">
        <div class="me-3">
            <i class="bi bi-grip-vertical text-muted" style="font-size: 1.5rem;"></i>
        </div>
        
        <?php if (!empty($post['image_url'])): ?>
        <div class="me-3">
            <img src="<?= htmlspecialchars($post['image_url']) ?>" 
                 alt="Post image" 
                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                 onerror="this.style.display='none'">
        </div>
        <?php endif; ?>
        
        <div class="flex-grow-1">                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-primary me-2">Priority <?= $post['priority'] ?></span>
                                        <span class="badge bg-secondary me-2"><?= date('M d, Y', strtotime($post['pub_date'])) ?></span>
                                        <span class="badge <?= $exceeds_limit ? 'bg-danger' : 'bg-info' ?>"><?= $post['char_count'] ?> chars</span>
                                    </div>
                                    <a href="<?= base_url('rss/delete/' . $post['id']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                                
                                <h5 class="mb-2"><?= htmlspecialchars($post['title']) ?></h5>
                                <p class="text-muted mb-2"><?= htmlspecialchars(substr($post['content'], 0, 200)) ?><?= strlen($post['content']) > 200 ? '...' : '' ?></p>
                                
                                <?php if ($exceeds_limit): ?>
                                <div class="alert alert-danger py-2 mb-2">
                                    <i class="bi bi-exclamation-triangle"></i> Post exceeds X (Twitter) 280 character limit!
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($platforms as $platform): ?>
                                        <span class="badge platform-badge <?= in_array($platform, $post['platforms']) ? 'active' : 'bg-light text-dark border' ?>"
                                              data-post-id="<?= $post['id'] ?>"
                                              data-platform="<?= $platform ?>">
                                            <?= $platform ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_url('rss/manage?page=' . ($current_page - 1)) ?>">Previous</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Page <?= $current_page ?> of <?= $total_pages ?></span>
                    </li>
                    <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= base_url('rss/manage?page=' . ($current_page + 1)) ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Drag and Drop
let draggedElement = null;

document.querySelectorAll('.post-item').forEach(item => {
    item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.style.opacity = '0.5';
    });
    
    item.addEventListener('dragend', function(e) {
        this.style.opacity = '1';
        document.querySelectorAll('.post-item').forEach(el => el.classList.remove('drag-over'));
    });
    
    item.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });
    
    item.addEventListener('dragleave', function(e) {
        this.classList.remove('drag-over');
    });
    
    item.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        if (draggedElement !== this) {
            const draggedId = draggedElement.dataset.id;
            const targetPriority = this.dataset.priority;
            
            fetch('<?= base_url('rss/update_priority') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `post_id=${draggedId}&new_priority=${targetPriority}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
});

// Platform Toggle
document.querySelectorAll('.platform-badge').forEach(badge => {
    badge.addEventListener('click', function() {
        const postId = this.dataset.postId;
        const platform = this.dataset.platform;
        
        fetch('<?= base_url('rss/toggle_platform') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `post_id=${postId}&platform=${encodeURIComponent(platform)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.classList.toggle('active');
                this.classList.toggle('bg-light');
                this.classList.toggle('text-dark');
                this.classList.toggle('border');
            }
        });
    });
});
</script>