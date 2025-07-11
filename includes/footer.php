<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar
    // document.getElementById('toggleSidebar').addEventListener('click', function() {
    //     document.body.classList.toggle('sidebar-collapsed');
    // });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Drag and drop functionality for image upload
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        if (!dropZone) return;
        
        const fileInput = document.getElementById('fileInput');
        const dropZoneThumb = document.getElementById('dropZoneThumb');
        const dropZonePrompt = document.querySelector('.drop-zone-prompt');
        
        // Click to browse files
        dropZone.addEventListener('click', function(e) {
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });
        
        // Handle file selection via input
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length) {
                updateThumbnail(fileInput.files[0]);
            }
        });
        
        // Handle drag events
        ['dragover', 'dragenter'].forEach(type => {
            dropZone.addEventListener(type, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('drop-zone-over');
            }, false);
        });
        
        ['dragleave', 'dragend', 'drop'].forEach(type => {
            dropZone.addEventListener(type, function(e) {
                dropZone.classList.remove('drop-zone-over');
            }, false);
        });
        
        // Handle drop event
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateThumbnail(e.dataTransfer.files[0]);
            }
        }, false);
        
        // Function to update thumbnail
        function updateThumbnail(file) {
            // First time - hide the prompt
            if (dropZonePrompt) {
                dropZonePrompt.style.display = 'none';
            }
            
            // Show thumbnail for image files
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function() {
                    dropZoneThumb.style.backgroundImage = `url('${reader.result}')`;
                    dropZoneThumb.style.display = 'block';
                    
                    // Add filename
                    if (dropZoneThumb.querySelector('.file-name')) {
                        dropZoneThumb.querySelector('.file-name').textContent = file.name;
                    } else {
                        const fileNameElement = document.createElement('span');
                        fileNameElement.className = 'file-name';
                        fileNameElement.textContent = file.name;
                        dropZoneThumb.appendChild(fileNameElement);
                    }
                };
                
                reader.readAsDataURL(file);
            }
        }
        
        // Initialize with existing image if present
        if (dropZoneThumb && dropZoneThumb.style.backgroundImage && dropZonePrompt) {
            dropZonePrompt.style.display = 'none';
        }
    });
</script>
<style>
    /* Drag & Drop Zone Styles */
    .drop-zone {
        max-width: 100%;
        height: 200px;
        padding: 25px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        color: #666;
        border: 2px dashed #ccc;
        border-radius: 10px;
        position: relative;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .drop-zone:hover {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .drop-zone-over {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.1);
    }
    
    .drop-zone-prompt {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #666;
        z-index: 1;
    }
    
    .drop-zone-thumb {
        width: 100%;
        height: 100%;
        border-radius: 8px;
        overflow: hidden;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
    }
    
    .drop-zone-input {
        position: absolute;
        left: -9999px;
        opacity: 0;
        visibility: hidden;
    }
    
    .file-name, .current-image-label {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 5px 0;
        color: white;
        background: rgba(0, 0, 0, 0.5);
        font-size: 14px;
        text-align: center;
    }
</style>
</body>

</html>