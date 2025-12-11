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
        
        <!-- Debug Info (optional, remove in production) -->
        <div class="alert alert-info small">
            <strong>Page Info:</strong> Showing <?= count($posts) ?> posts on page <?= $current_page ?> of <?= $total_pages ?> (<?= $per_page ?> per page)
        </div>
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
            <?php 
            $current_page = $current_page;
            $per_page = $per_page;
            $start_priority = (($current_page - 1) * $per_page) + 1;
            
            foreach ($posts as $index => $post): 
                $has_twitter = in_array('X (Twitter)', $post['platforms']);
                $exceeds_limit = $has_twitter && $post['char_count'] > 280;
                $display_priority = $start_priority + $index;
            ?>
                <div class="card mb-3 border <?= $exceeds_limit ? 'border-danger border-2' : 'border-light' ?> post-item" 
                     draggable="true" 
                     data-id="<?= $post['id'] ?>"
                     data-original-priority="<?= $post['priority'] ?>"
                     data-display-priority="<?= $display_priority ?>"
                     data-index="<?= $index ?>">
                    
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
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 priority-badge">
                                            <i class="bi bi-flag me-1"></i> Priority <?= $display_priority ?>
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            <?= date('M d, Y', strtotime($post['pub_date'])) ?>
                                        </span>
                                        <span class="badge <?= $exceeds_limit ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : 'bg-info bg-opacity-10 text-info' ?>">
                                            <?= $post['char_count'] ?> chars
                                        </span>
                                        <span class="badge bg-warning bg-opacity-10 text-warning small">
                                            DB Priority: <?= $post['priority'] ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu"> 
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
                                
                                <!-- Platform Badges (Clickable) -->
                                <div class="mb-3">
                                    <p class="text-muted mb-1 small">Assigned Platforms:</p>
                                    <div class="d-flex flex-wrap gap-1 current-platforms" id="current-platforms-<?= $post['id'] ?>">
                                        <?php if (!empty($post['platforms'])): ?>
                                            <?php foreach ($post['platforms'] as $platform): ?>
                                                <?php 
                                                $platform_icons = [
                                                    'X (Twitter)' => 'bi bi-twitter-x text-dark',
                                                    'Facebook' => 'bi bi-facebook text-primary',
                                                    'LinkedIn' => 'bi bi-linkedin text-info',
                                                    'Instagram' => 'bi bi-instagram text-danger',
                                                    'TikTok' => 'bi bi-tiktok text-dark',
                                                    'Threads' => 'bi bi-threads text-purple'
                                                ];
                                                $icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
                                                ?>
                                                <span class="badge bg-primary d-flex align-items-center">
                                                    <i class="<?= $icon ?> me-1"></i> <?= $platform ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">No platforms selected</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Assign Platforms Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary mt-2 assign-platforms-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#platformModal"
                                            data-post-id="<?= $post['id'] ?>"
                                            data-current-platforms='<?= json_encode($post['platforms']) ?>'>
                                        <i class="bi bi-plus-circle me-1"></i> Assign Platforms
                                    </button>
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

<!-- Platform Assignment Modal -->
<div class="modal fade" id="platformModal" tabindex="-1" aria-labelledby="platformModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="platformModalLabel">
                    <i class="bi bi-share me-2"></i> Assign Social Media Platforms
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="text-muted small mb-2">Select platforms for this post:</p>
                    <div class="platform-checkboxes">
                        <?php foreach ($platforms as $platform): ?>
                            <?php 
                            $platform_icons = [
                                'X (Twitter)' => 'bi bi-twitter-x',
                                'Facebook' => 'bi bi-facebook text-primary',
                                'LinkedIn' => 'bi bi-linkedin text-info',
                                'Instagram' => 'bi bi-instagram text-danger',
                                'TikTok' => 'bi bi-tiktok',
                                'Threads' => 'bi bi-threads text-purple'
                            ];
                            $icon = isset($platform_icons[$platform]) ? $platform_icons[$platform] : 'bi bi-share';
                            ?>
                            <div class="form-check mb-2 platform-checkbox-item">
                                <input class="form-check-input platform-checkbox" 
                                       type="checkbox" 
                                       value="<?= $platform ?>" 
                                       id="platform_<?= str_replace([' ', '(', ')'], ['', '', ''], $platform) ?>">
                                <label class="form-check-label d-flex align-items-center" for="platform_<?= str_replace([' ', '(', ')'], ['', '', ''], $platform) ?>">
                                    <i class="<?= $icon ?> me-2"></i>
                                    <span><?= $platform ?></span>
                                    <?php if ($platform === 'X (Twitter)'): ?>
                                        <small class="text-muted ms-2">(280 char limit)</small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="selected-platforms-preview mb-3">
                    <p class="text-muted small mb-2">Selected platforms:</p>
                    <div class="d-flex flex-wrap gap-2" id="selectedPlatformsPreview">
                        <span class="text-muted small">None selected</span>
                    </div>
                </div>
                
                <!-- Twitter Character Warning -->
                <div class="alert alert-warning d-none" id="twitterWarning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <span>This post has <span id="charCount">0</span> characters. 
                    X (Twitter) has a 280 character limit.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="savePlatformsModal">
                    <i class="bi bi-save me-1"></i> Save Platforms
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px;
        transition: all 0.2s ease;
        position: relative;
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
        z-index: 10;
    }
    
    .card:hover .drag-handle {
        opacity: 1;
    }
    
    .post-item.dragging {
        opacity: 0.5;
        transform: rotate(2deg) scale(0.98);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
        z-index: 1000;
        position: relative;
    }
    
    .post-item.drag-over {
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05);
        transition: transform 0.2s ease;
    }
    
    .post-item.drag-over-above {
        border-top: 3px solid #0d6efd !important;
        transform: translateY(-10px);
    }
    
    .post-item.drag-over-below {
        border-bottom: 3px solid #0d6efd !important;
        transform: translateY(10px);
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .drag-feedback {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1001;
        animation: fadeInOut 1.5s ease;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: scale(0.8); }
        20% { opacity: 1; transform: scale(1); }
        80% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.8); }
    }
    
    /* Platform Modal Styles */
    .platform-checkbox-item {
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .platform-checkbox-item:hover {
        background-color: rgba(0, 123, 255, 0.05);
        border-color: rgba(0, 123, 255, 0.2);
    }
    
    .platform-checkbox-item .form-check-input:checked ~ .form-check-label {
        font-weight: 600;
        color: #0d6efd;
    }
    
    .platform-checkbox-item .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Selected platforms preview */
    #selectedPlatformsPreview .badge {
        padding: 6px 10px;
        font-size: 0.875rem;
    }
    
    /* Modal animations */
    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        transition: transform 0.3s ease-out;
    }
    
    .modal.show .modal-dialog {
        transform: none;
    }
    
    /* Platform icons */
    .bi-twitter-x { color: #000000; }
    .bi-facebook { color: #1877F2; }
    .bi-linkedin { color: #0A66C2; }
    .bi-instagram { color: #E4405F; }
    .bi-tiktok { color: #000000; }
    .bi-threads { color: #000000; }
    
    /* Toast Notifications */
    .toast-container {
        z-index: 9999;
    }
    
    .toast {
        backdrop-filter: blur(10px);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .toast-body {
        padding: 12px 16px;
        font-weight: 500;
    }
    
    /* Modal z-index fix */
    .modal-backdrop {
        z-index: 1055;
    }
    
    .modal {
        z-index: 1060;
    }
    
    /* Assign button */
    .assign-platforms-btn {
        transition: all 0.2s ease;
    }
    
    .assign-platforms-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
    }
</style>

<script>
// Global variables
let draggedItem = null;
let draggedItemIndex = null;
let originalPosition = null;

// Initialize drag and drop
function initializeDragAndDrop() {
    const postItems = document.querySelectorAll('.post-item');
    
    postItems.forEach((item, index) => {
        setupDragEvents(item, index);
    });
}

// Setup drag events for a single post item
function setupDragEvents(item, index) {
    item.addEventListener('dragstart', handleDragStart);
    item.addEventListener('dragend', handleDragEnd);
    item.addEventListener('dragover', handleDragOver);
    item.addEventListener('dragenter', handleDragEnter);
    item.addEventListener('dragleave', handleDragLeave);
    item.addEventListener('drop', handleDrop);
    
    // Prevent buttons from triggering drag
    const buttons = item.querySelectorAll('button, a, .dropdown-toggle');
    buttons.forEach(button => {
        button.addEventListener('mousedown', (e) => e.stopPropagation());
        button.addEventListener('dragstart', (e) => e.stopPropagation());
    });
}

// Event Handlers
function handleDragStart(e) {
    draggedItem = this;
    draggedItemIndex = Array.from(this.parentNode.children).indexOf(this);
    
    // Store original position
    originalPosition = {
        parent: this.parentNode,
        index: draggedItemIndex
    };
    
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.id);
    
    setTimeout(() => {
        this.style.opacity = '0.4';
    }, 0);
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    this.style.opacity = '1';
    
    // Remove all drag-over classes
    document.querySelectorAll('.post-item').forEach(item => {
        item.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
        item.style.transform = '';
    });
    
    draggedItem = null;
    draggedItemIndex = null;
    originalPosition = null;
}

function handleDragOver(e) {
    e.preventDefault();
    
    if (draggedItem !== this) {
        const rect = this.getBoundingClientRect();
        const y = e.clientY - rect.top;
        const height = rect.height;
        const threshold = height / 2;
        
        // Remove all drag-over classes first
        this.classList.remove('drag-over-above', 'drag-over-below');
        
        if (y < threshold) {
            this.classList.add('drag-over-above');
        } else {
            this.classList.add('drag-over-below');
        }
        
        this.classList.add('drag-over');
    }
}

function handleDragEnter(e) {
    e.preventDefault();
}

function handleDragLeave(e) {
    this.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
    this.style.transform = '';
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    if (draggedItem !== this) {
        // Get drop position
        const rect = this.getBoundingClientRect();
        const y = e.clientY - rect.top;
        const height = rect.height;
        const threshold = height / 2;
        
        // Remove visual feedback
        this.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
        this.style.transform = '';
        
        // Determine where to insert
        let targetIndex;
        if (y < threshold) {
            // Insert above
            targetIndex = Array.from(this.parentNode.children).indexOf(this);
            this.parentNode.insertBefore(draggedItem, this);
        } else {
            // Insert below
            targetIndex = Array.from(this.parentNode.children).indexOf(this) + 1;
            if (this.nextSibling) {
                this.parentNode.insertBefore(draggedItem, this.nextSibling);
            } else {
                this.parentNode.appendChild(draggedItem);
            }
        }
        
        // Calculate new priority based on page position
        const posts = Array.from(document.querySelectorAll('.post-item'));
        const currentPage = <?= $current_page ?>;
        const perPage = <?= $per_page ?>;
        const newPosition = posts.indexOf(draggedItem);
        const newGlobalPriority = ((currentPage - 1) * perPage) + newPosition + 1;
        
        // Update visual display
        updateVisualPriorities();
        
        // Send update to server with GLOBAL priority
        updatePriorityOnServer(draggedItem.dataset.id, newGlobalPriority);
    }
}

// Update priority badges visually
function updateVisualPriorities() {
    const posts = document.querySelectorAll('.post-item');
    const currentPage = <?= $current_page ?>;
    const perPage = <?= $per_page ?>;
    
    posts.forEach((post, index) => {
        const priorityBadge = post.querySelector('.priority-badge');
        if (priorityBadge) {
            const globalPriority = ((currentPage - 1) * perPage) + index + 1;
            priorityBadge.innerHTML = `<i class="bi bi-flag me-1"></i> Priority ${globalPriority}`;
            post.dataset.displayPriority = globalPriority;
        }
    });
}

// Update priority on server
function updatePriorityOnServer(postId, newGlobalPriority) {
    // Show loading
    const dragHandle = draggedItem.querySelector('.drag-handle');
    const originalHandleContent = dragHandle.innerHTML;
    dragHandle.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
    
    // Send AJAX request
    fetch('<?= base_url('rss/update_priority') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `post_id=${postId}&new_priority=${newGlobalPriority}`
    })
    .then(response => response.json())
    .then(data => {
        showFeedback(draggedItem, data.success ? 'success' : 'error');
        
        if (data.success) {
            // Update the original priority attribute
            draggedItem.dataset.originalPriority = newGlobalPriority;
        } else {
            // Revert on failure
            revertPosition();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showFeedback(draggedItem, 'error');
        revertPosition();
    })
    .finally(() => {
        setTimeout(() => {
            dragHandle.innerHTML = originalHandleContent;
        }, 500);
    });
}

// Show visual feedback
function showFeedback(element, type) {
    const existingFeedback = element.querySelector('.drag-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    const feedback = document.createElement('div');
    feedback.className = `drag-feedback badge bg-${type === 'success' ? 'success' : 'danger'}`;
    feedback.innerHTML = type === 'success' ? 
        '<i class="bi bi-check-lg me-1"></i> Updated' : 
        '<i class="bi bi-x-lg me-1"></i> Failed';
    
    element.appendChild(feedback);
    
    setTimeout(() => {
        if (feedback.parentNode) {
            feedback.remove();
        }
    }, 1500);
}

// Revert to original position
function revertPosition() {
    if (originalPosition && originalPosition.parent) {
        const posts = Array.from(originalPosition.parent.children);
        if (posts[originalPosition.index]) {
            originalPosition.parent.insertBefore(draggedItem, posts[originalPosition.index]);
        } else {
            originalPosition.parent.appendChild(draggedItem);
        }
        updateVisualPriorities();
    }
}

// Platform Modal Functions
function initializePlatformModal() {
    const modal = document.getElementById('platformModal');
    let currentPostId = null;
    let currentCharCount = 0;
    
    // When modal is about to be shown
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        currentPostId = button.getAttribute('data-post-id');
        const currentPlatforms = JSON.parse(button.getAttribute('data-current-platforms') || '[]');
        
        // Update modal title with post ID
        document.getElementById('platformModalLabel').innerHTML = 
            `<i class="bi bi-share me-2"></i> Assign Platforms `;
        
        // Reset all checkboxes
        document.querySelectorAll('.platform-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Check the platforms this post already has
        currentPlatforms.forEach(platform => {
            const checkbox = document.querySelector(`.platform-checkbox[value="${platform}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        
        // Update preview
        updateSelectedPlatformsPreview();
        
        // Get character count for Twitter warning
        const postCard = document.querySelector(`.post-item[data-id="${currentPostId}"]`);
        const charBadge = postCard.querySelector('.badge.bg-info');
        if (charBadge) {
            currentCharCount = parseInt(charBadge.textContent.match(/\d+/)[0]) || 0;
        }
        
        // Show/hide Twitter warning
        const twitterWarning = document.getElementById('twitterWarning');
        const twitterCheckbox = document.querySelector('.platform-checkbox[value="X (Twitter)"]');
        
        if (twitterCheckbox && currentCharCount > 280) {
            document.getElementById('charCount').textContent = currentCharCount;
            twitterWarning.classList.remove('d-none');
        } else {
            twitterWarning.classList.add('d-none');
        }
    });
    
    // When checkboxes change
    document.querySelectorAll('.platform-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedPlatformsPreview);
    });
    
    // Save button click
    document.getElementById('savePlatformsModal').addEventListener('click', function() {
        if (!currentPostId) return;
        
        // Get selected platforms
        const selectedPlatforms = [];
        document.querySelectorAll('.platform-checkbox:checked').forEach(checkbox => {
            selectedPlatforms.push(checkbox.value);
        });
        
        // Show loading on save button
        const saveBtn = this;
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
        saveBtn.disabled = true;
        
        // Save platforms
        savePlatformsToServer(currentPostId, selectedPlatforms, saveBtn, originalText);
    });
    
    // Update preview function
    function updateSelectedPlatformsPreview() {
        const previewDiv = document.getElementById('selectedPlatformsPreview');
        const selectedPlatforms = [];
        
        document.querySelectorAll('.platform-checkbox:checked').forEach(checkbox => {
            selectedPlatforms.push(checkbox.value);
        });
        
        previewDiv.innerHTML = '';
        
        if (selectedPlatforms.length === 0) {
            previewDiv.innerHTML = '<span class="text-muted small">None selected</span>';
            return;
        }
        
        selectedPlatforms.forEach(platform => {
            const platform_icons = {
                'X (Twitter)': 'bi bi-twitter-x text-dark',
                'Facebook': 'bi bi-facebook text-primary',
                'LinkedIn': 'bi bi-linkedin text-info',
                'Instagram': 'bi bi-instagram text-danger',
                'TikTok': 'bi bi-tiktok text-dark',
                'Threads': 'bi bi-threads text-purple'
            };
            
            const icon = platform_icons[platform] || 'bi bi-share';
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary d-flex align-items-center';
            badge.innerHTML = `<i class="${icon} me-1"></i> ${platform}`;
            previewDiv.appendChild(badge);
        });
    }
}

// Save platforms from modal
function savePlatformsToServer(postId, platforms, saveButton, originalButtonText) {
    fetch('<?= base_url('rss/update_platforms') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            post_id: postId,
            platforms: platforms
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI
            updatePostPlatformsUI(postId, platforms);
            
            // Show success message
            showToast('success', 'Platforms updated successfully!');
            
            // Close modal after delay
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('platformModal'));
                modal.hide();
            }, 1000);
        } else {
            showToast('error', 'Failed to update platforms');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error saving platforms');
    })
    .finally(() => {
        // Restore button
        setTimeout(() => {
            saveButton.innerHTML = originalButtonText;
            saveButton.disabled = false;
        }, 500);
    });
}

// Update post UI after saving
function updatePostPlatformsUI(postId, platforms) {
    const currentPlatformsDiv = document.getElementById(`current-platforms-${postId}`);
    const assignButton = document.querySelector(`.assign-platforms-btn[data-post-id="${postId}"]`);
    
    // Update badges
    currentPlatformsDiv.innerHTML = '';
    
    if (platforms.length === 0) {
        currentPlatformsDiv.innerHTML = '<span class="text-muted small">No platforms selected</span>';
    } else {
        const platform_icons = {
            'X (Twitter)': 'bi bi-twitter-x text-dark',
            'Facebook': 'bi bi-facebook text-primary',
            'LinkedIn': 'bi bi-linkedin text-info',
            'Instagram': 'bi bi-instagram text-danger',
            'TikTok': 'bi bi-tiktok text-dark',
            'Threads': 'bi bi-threads text-purple'
        };
        
        platforms.forEach(platform => {
            const icon = platform_icons[platform] || 'bi bi-share';
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary d-flex align-items-center me-1 mb-1';
            badge.innerHTML = `<i class="${icon} me-1"></i> ${platform}`;
            currentPlatformsDiv.appendChild(badge);
        });
    }
    
    // Update button data attribute
    if (assignButton) {
        assignButton.setAttribute('data-current-platforms', JSON.stringify(platforms));
    }
}

// Toast notification function
function showToast(type, message) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast
    const toastHtml = `
        <div class="toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0" 
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Add to page
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
    toastContainer.innerHTML = toastHtml;
    document.body.appendChild(toastContainer);
    
    // Show toast
    const toastElement = toastContainer.querySelector('.toast');
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Remove after hiding
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastContainer.remove();
    });
}

// Initialize everything
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
    initializePlatformModal();
});
</script>