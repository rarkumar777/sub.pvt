/**
 * Image Selector - File Manager Modal
 * Opens a modal to browse /uploads/filemanager/ and select images.
 * Works with elements having class "image_selector" and data-input-name attribute.
 */
(function() {
    'use strict';

    var currentInputName = '';
    var currentDir = '';
    var modalEl = null;

    // Create the modal HTML
    function createModal() {
        if (document.getElementById('image_selector_modal')) return;

        var overlay = document.createElement('div');
        overlay.id = 'image_selector_modal';
        overlay.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; justify-content:center; align-items:center;';
        overlay.innerHTML =
            '<div style="background:#fff; width:800px; max-width:95%; max-height:90vh; border-radius:4px; overflow:hidden; display:flex; flex-direction:column;">' +
                '<div style="display:flex; justify-content:space-between; align-items:center; padding:10px 15px; background:#f5f5f5; border-bottom:1px solid #ddd;">' +
                    '<h3 style="margin:0; font-size:15px;"><i class="fa-folder-open"></i> Select Image</h3>' +
                    '<span id="img_sel_close" style="cursor:pointer; font-size:22px; color:#888; line-height:1;">&times;</span>' +
                '</div>' +
                '<div id="img_sel_breadcrumb" style="padding:6px 15px; background:#fafafa; border-bottom:1px solid #eee; font-size:12px;"></div>' +
                '<div id="img_sel_content" style="padding:15px; overflow-y:auto; flex:1;"></div>' +
            '</div>';

        document.body.appendChild(overlay);
        modalEl = overlay;

        // Close handlers
        document.getElementById('img_sel_close').addEventListener('click', closeModal);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
        });
    }

    function openModal(inputName) {
        createModal();
        currentInputName = inputName;
        currentDir = '';
        modalEl.style.display = 'flex';
        loadDirectory('');
    }

    function closeModal() {
        if (modalEl) modalEl.style.display = 'none';
    }

    function loadDirectory(dir) {
        currentDir = dir;
        var content = document.getElementById('img_sel_content');
        content.innerHTML = '<div style="text-align:center; padding:30px; color:#888;">Loading...</div>';
        updateBreadcrumb(dir);

        $.ajax({
            url: '/admin/ajax/file-manager-browse',
            type: 'GET',
            data: { dir: dir },
            success: function(data) {
                renderContent(data);
            },
            error: function() {
                content.innerHTML = '<div style="text-align:center; padding:30px; color:#d32f2f;">Error loading files</div>';
            }
        });
    }

    function updateBreadcrumb(dir) {
        var bc = document.getElementById('img_sel_breadcrumb');
        var html = '<a href="javascript:void(0);" onclick="window._imgSelGoDir(\'\')" style="color:#1976D2; text-decoration:none;">Root</a>';

        if (dir) {
            var parts = dir.split('/');
            var path = '';
            for (var i = 0; i < parts.length; i++) {
                path += (i > 0 ? '/' : '') + parts[i];
                html += ' / <a href="javascript:void(0);" onclick="window._imgSelGoDir(\'' + path.replace(/'/g, "\\'") + '\')" style="color:#1976D2; text-decoration:none;">' + parts[i] + '</a>';
            }
        }
        bc.innerHTML = html;
    }

    function renderContent(data) {
        var content = document.getElementById('img_sel_content');
        var html = '';

        // Back button
        if (data.current_dir) {
            var parentDir = data.current_dir.split('/').slice(0, -1).join('/');
            html += '<div onclick="window._imgSelGoDir(\'' + parentDir.replace(/'/g, "\\'") + '\')" style="display:inline-flex; align-items:center; padding:8px 12px; margin:4px; background:#f0f0f0; border:1px solid #ddd; border-radius:3px; cursor:pointer; font-size:13px;">' +
                '<i class="fa-arrow-left" style="margin-right:6px; color:#555;"></i> Back' +
                '</div>';
        }

        // Folders
        for (var i = 0; i < data.folders.length; i++) {
            var folder = data.folders[i];
            var folderPath = data.current_dir ? data.current_dir + '/' + folder : folder;
            html += '<div onclick="window._imgSelGoDir(\'' + folderPath.replace(/'/g, "\\'") + '\')" style="display:inline-flex; align-items:center; padding:8px 12px; margin:4px; background:#FFF9C4; border:1px solid #F9A825; border-radius:3px; cursor:pointer; font-size:13px;">' +
                '<i class="fa-folder" style="margin-right:6px; color:#F9A825;"></i> ' + folder +
                '</div>';
        }

        if (data.folders.length > 0 && data.files.length > 0) {
            html += '<hr style="border:none; border-top:1px solid #eee; margin:10px 0;">';
        }

        // Files grid
        if (data.files.length > 0) {
            html += '<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:10px; margin-top:5px;">';
            for (var j = 0; j < data.files.length; j++) {
                var file = data.files[j];
                html += '<div onclick="window._imgSelPick(\'' + file.url.replace(/'/g, "\\'") + '\')" ' +
                    'style="cursor:pointer; border:2px solid #eee; border-radius:4px; overflow:hidden; transition:border-color 0.2s;" ' +
                    'onmouseover="this.style.borderColor=\'#1976D2\'" onmouseout="this.style.borderColor=\'#eee\'">' +
                    '<div style="height:120px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; overflow:hidden;">' +
                    '<img src="' + file.url + '" style="max-width:100%; max-height:100%; object-fit:cover;" onerror="this.src=\'data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;60&quot; height=&quot;60&quot;><text x=&quot;10&quot; y=&quot;35&quot; fill=&quot;%23999&quot;>IMG</text></svg>\'">' +
                    '</div>' +
                    '<div style="padding:4px 6px; font-size:11px; color:#555; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="' + file.name + '">' + file.name + '</div>' +
                    '</div>';
            }
            html += '</div>';
        }

        if (data.folders.length === 0 && data.files.length === 0) {
            html = '<div style="text-align:center; padding:30px; color:#888;">No images found in this folder</div>';
        }

        content.innerHTML = html;
    }

    // Select an image
    window._imgSelPick = function(url) {
        // Find the images container
        var inputName = currentInputName;
        // The target is: images container inside day_images_X
        // or any container matching the input name pattern
        var dayMatch = inputName.match(/day_images_(\d+)/);
        if (dayMatch) {
            var dayNum = dayMatch[1];
            var imagesContainer = document.getElementById('images_' + dayNum);
            if (!imagesContainer) {
                // try alternate container
                imagesContainer = document.getElementById('day_images_' + dayNum);
            }
            if (imagesContainer) {
                var imgDiv = document.createElement('div');
                imgDiv.style.cssText = 'width:200px; float:left; position:relative; margin:5px; border:1px solid #ddd;';
                imgDiv.className = 'pull-left box relative';
                imgDiv.innerHTML =
                    '<input type="hidden" name="' + inputName + '[]" value="' + url + '">' +
                    '<img src="' + url + '" width="100%" style="display:block;">' +
                    '<span onclick="$(this).parent(\'div:first\').remove();" class="h-pad absolute top right btn red" style="cursor:pointer;"><i class="fa-close"></i></span>';
                imagesContainer.appendChild(imgDiv);
            }
        } else {
            // Generic: look for container with matching input name
            var container = document.querySelector('[data-input-name="' + inputName + '"]');
            if (container) {
                var parent = container.closest('fieldset') || container.parentElement;
                var target = parent.querySelector('.row') || parent;
                var imgDiv = document.createElement('div');
                imgDiv.style.cssText = 'width:200px; float:left; position:relative; margin:5px; border:1px solid #ddd;';
                imgDiv.innerHTML =
                    '<input type="hidden" name="' + inputName + '[]" value="' + url + '">' +
                    '<img src="' + url + '" width="100%">' +
                    '<span onclick="$(this).parent(\'div:first\').remove();" class="h-pad absolute top right btn red" style="cursor:pointer;"><i class="fa-close"></i></span>';
                target.appendChild(imgDiv);
            }
        }
        closeModal();
    };

    // Navigate to directory
    window._imgSelGoDir = function(dir) {
        loadDirectory(dir);
    };

    // Initialize: attach click handlers to .image_selector buttons
    function initImageSelector() {
        document.querySelectorAll('.image_selector').forEach(function(btn) {
            // Avoid double binding
            if (btn.dataset.imgSelBound) return;
            btn.dataset.imgSelBound = '1';
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var inputName = this.getAttribute('data-input-name');
                openModal(inputName);
            });
        });
    }

    // Expose globally for dynamic content (create.blade.php rebuilds day sections)
    window.initImageSelector = initImageSelector;

    // Auto-init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImageSelector);
    } else {
        initImageSelector();
    }

    // Also observe for dynamically added buttons (MutationObserver)
    var observer = new MutationObserver(function() {
        initImageSelector();
    });
    observer.observe(document.body, { childList: true, subtree: true });

})();
