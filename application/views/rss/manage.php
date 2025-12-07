<?php if ($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-4">
    <?= $this->session->flashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="mb-5">
        <h1 class="display-6 fw-bold mb-2">Manage Posts</h1>
        <p class="text-muted">Organize and manage your imported RSS feed posts (<?= $total_posts ?> total)</p>
    </div>

    <?php if (empty($posts)): ?>
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-newspaper text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="card-title mb-3">No posts yet</h4>
                <p class="card-text text-muted mb-4">Import an RSS feed to get started</p>
                <a href="<?= base_url('rss/import') ?>" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i> Import RSS Feed
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Posts List -->
        <div id="posts-container" class="mb-5">
            <?php foreach ($posts as $post): ?>
                <?php 
                $has_twitter = in_array('X (Twitter)', $post['platforms']);
                $exceeds_limit = $has_twitter && $post['char_count'] > 280;
                ?>
                <div class="card mb-3 border <?= $exceeds_limit ? 'border-danger border-2' : 'border-light' ?> post-item" 
                     draggable="true" 
                     data-id="<?= $post['id'] ?>"
                     data-priority="<?= $post['priority'] ?>">
                    
                    <!-- Drag Handle -->
                    <div class="drag-handle position-absolute top-50 start-0 translate-middle-y ms-3">
                        <i class="bi bi-grip-vertical text-muted"></i>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Post Image -->
                            <?php if (!empty($post['image_url'])): ?>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <img src="<?= htmlspecialchars($post['image_url']) ?>" 
                                     alt="Post image" 
                                     class="img-fluid rounded"
                                     style="height: 160px; width: 100%; object-fit: cover;"
                                     onerror="this.style.display='none'">
                            </div>
                            <?php endif; ?>
                            
                            <!-- Post Content -->
                            <div class="<?= !empty($post['image_url']) ? 'col-md-9' : 'col-12' ?>">
                                <!-- Post Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                            Priority <?= $post['priority'] ?>
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            <?= date('M d, Y', strtotime($post['pub_date'])) ?>
                                        </span>
                                        <span class="badge <?= $exceeds_limit ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : 'bg-info bg-opacity-10 text-info' ?>">
                                            <?= $post['char_count'] ?> chars
                                        </span>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="<?= base_url('rss/delete/' . $post['id']) ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this post?')">
                                                   <i class="bi bi-trash me-2"></i> Delete
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- Post Title -->
                                <h5 class="card-title mb-3"><?= htmlspecialchars($post['title']) ?></h5>
                                
                                <!-- Post Excerpt -->
                                <p class="card-text text-muted mb-4">
                                    <?= htmlspecialchars(substr($post['content'], 0, 200)) ?><?= strlen($post['content']) > 200 ? '...' : '' ?>
                                </p>
                                
                                <!-- Twitter Warning -->
                                <?php if ($exceeds_limit): ?>
                                <div class="alert alert-danger d-flex align-items-center mb-3 py-2">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>Post exceeds X (Twitter) 280 character limit!</div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Platform Selection -->
                                <div>
                                    <p class="text-muted mb-2 small">Select platforms for this post:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($platforms as $platform): ?>
                                            <?php 
                                            $platform_icons = [
                                                'X (Twitter)' => 'bi bi-twitter-x',
                                                'Facebook' => 'bi bi-facebook',
                                                'LinkedIn' => 'bi bi-linkedin',
                                                'Instagram' => 'bi bi-instagram'
                                            ];
                                            $icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
                                            ?>
                                            <button type="button" 
                                                    class="btn btn-sm platform-badge <?= in_array($platform, $post['platforms']) ? 'btn-primary' : 'btn-outline-primary' ?>"
                                                    data-post-id="<?= $post['id'] ?>"
                                                    data-platform="<?= $platform ?>">
                                                <i class="<?= $icon ?> me-1"></i> <?= $platform ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('rss/manage?page=' . ($current_page - 1)) ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                
                <!-- Page Numbers -->
                <?php 
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('rss/manage?page=' . $i) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('rss/manage?page=' . ($current_page + 1)) ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .card {
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .drag-handle {
        cursor: move;
        opacity: 0;
        transition: opacity 0.2s ease;
        background: white;
        padding: 4px 6px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .card:hover .drag-handle {
        opacity: 1;
    }
    
    .post-item {
        position: relative;
        cursor: move;
    }
    
    .post-item.dragging {
        opacity: 0.5;
        transform: rotate(1deg);
    }
    
    .post-item.drag-over {
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    
    .platform-badge {
        border-radius: 20px;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .platform-badge:hover {
        transform: translateY(-1px);
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

<script>
// Drag and Drop
let draggedElement = null;

document.querySelectorAll('.post-item').forEach(item => {
    item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.dataset.id);
    });
    
    item.addEventListener('dragend', function(e) {
        this.classList.remove('dragging');
        document.querySelectorAll('.post-item').forEach(el => el.classList.remove('drag-over'));
    });
    
    item.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
        e.dataTransfer.dropEffect = 'move';
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
        
        // Toggle visual state immediately for better UX
        const isActive = this.classList.contains('btn-primary');
        this.classList.toggle('btn-primary', !isActive);
        this.classList.toggle('btn-outline-primary', isActive);
        
        fetch('<?= base_url('rss/toggle_platform') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `post_id=${postId}&platform=${encodeURIComponent(platform)}`
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert if API call fails
                this.classList.toggle('btn-primary', isActive);
                this.classList.toggle('btn-outline-primary', !isActive);
            }
        })
        .catch(error => {
            // Revert if network error
            this.classList.toggle('btn-primary', isActive);
            this.classList.toggle('btn-outline-primary', !isActive);
        });
    });
});

// Make whole card draggable (except the buttons)
document.querySelectorAll('.post-item').forEach(item => {
    const buttons = item.querySelectorAll('button, a, .dropdown, .dropdown-toggle, .platform-badge');
    buttons.forEach(button => {
        button.addEventListener('mousedown', function(e) {
            e.stopPropagation(); // Prevent drag when clicking buttons
        });
    });
});
</script>