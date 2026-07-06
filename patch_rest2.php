<?php
$file = __DIR__ . '/app/Http/Controllers/Admin/ServiceController.php';
$content = file_get_contents($file);

// Fix 1: change addActSecImages to addRestNewImages
$content = str_replace('id="editRestImageInput" accept="image/*" multiple style="display:none" onchange="addActSecImages(this)">', 'id="editRestImageInput" accept="image/*" multiple style="display:none" onchange="addRestNewImages(this)">', $content);

// Fix 2: fix quickAddRestSubEdit and append photo JS logic
// First let's locate the block that starts with `var desc = document.getElementById("newRestDescEdit").value;` and ends with `</script>';`
$search = <<<EOT
            var desc = document.getElementById("newRestDescEdit").value;
        window.quickAddRestSubEdit = function(cat, token, country, vender) {
            var sel = document.getElementById("edit_modal_vender_select");
            var actCat = sel ? sel.value : cat;
            if (!actCat) actCat = cat;
            var cost = document.getElementById("newRestCostEdit").value || 0;
            if(!desc) { alert("Please enter description"); return; }
            var btn = event.target;
            btn.innerHTML = "Saving...";
            btn.disabled = true;
            $.ajax({
                url: "/admin/services",
                type: "POST",
                data: {
                    _token: token,
                    service_type: "service",
                    description: desc,
                    cost: cost,
                    category: actCat,
                    country: country,
                    vender: vender
                },
                success: function(res) {
                    btn.innerHTML = "Save"; btn.disabled = false;
                    document.getElementById("newRestDescEdit").value = "";
                    document.getElementById("newRestCostEdit").value = "0";
                    window.currentRestServices.unshift(res.data);
                    renderRestSvcTable();
                    if(typeof showToast==="function") showToast("Service added!", "success");
                },
                error: function(x) {
                    btn.innerHTML = "Save"; btn.disabled = false;
                    alert("Error saving service");
                }
            });
        };
        
        setTimeout(function(){
            if (typeof renderRestSvcTable === "function") {
                renderRestSvcTable();
            }
        }, 300);
        </script>';
EOT;

$replace = <<<'EOT'
        window.quickAddRestSubEdit = function(cat, token, country, vender) {
            var sel = document.getElementById("edit_modal_vender_select");
            var actCat = sel ? sel.value : cat;
            if (!actCat) actCat = cat;
            var desc = document.getElementById("newRestDescEdit").value;
            var cost = document.getElementById("newRestCostEdit").value || 0;
            if(!desc) { alert("Please enter description"); return; }
            var btn = event.target;
            btn.innerHTML = "Saving...";
            btn.disabled = true;
            $.ajax({
                url: "/admin/services",
                type: "POST",
                data: {
                    _token: token,
                    service_type: "service",
                    description: desc,
                    cost: cost,
                    category: actCat,
                    country: country,
                    vender: vender
                },
                success: function(res) {
                    btn.innerHTML = "Save"; btn.disabled = false;
                    document.getElementById("newRestDescEdit").value = "";
                    document.getElementById("newRestCostEdit").value = "0";
                    window.currentRestServices.unshift(res.data);
                    if (typeof window.processRestEditChange === "function") window.processRestEditChange(actCat);
                    if (typeof showToast==="function") showToast("Service added!", "success");
                },
                error: function(x) {
                    btn.innerHTML = "Save"; btn.disabled = false;
                    alert("Error saving service");
                }
            });
        };
        
        window.restEditDt = new DataTransfer();
        
        window.addRestNewImages = function(input) {
            if(input.files && input.files.length > 0){
                for(var i=0; i<input.files.length; i++){
                    window.restEditDt.items.add(input.files[i]);
                }
            }
            input.value = "";
            window.renderRestNewImages();
        };
        
        window.renderRestNewImages = function() {
            var row = document.getElementById("restPhotosRow");
            if(!row) return;
            var addBtn = row.lastElementChild;
            var existingNew = row.querySelectorAll(".new-rest-photo-wrap");
            existingNew.forEach(function(e) { e.remove(); });
            
            for(let i=0; i<window.restEditDt.files.length; i++){
                (function(idx) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var div = document.createElement("div");
                        div.className = "acc-photo-wrap new-rest-photo-wrap";
                        div.style.cssText = "position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;";
                        div.innerHTML = "<img src='" + e.target.result + "' style='width:100%;height:100%;border-radius:4px;object-fit:cover;'>" +
                                        "<button type='button' onclick='removeRestNewImg(" + idx + ")' style='position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;'>✕</button>";
                        row.insertBefore(div, addBtn);
                    };
                    reader.readAsDataURL(window.restEditDt.files[idx]);
                })(i);
            }
        };
        
        window.removeRestNewImg = function(idx) {
            var newDt = new DataTransfer();
            for(var i=0; i<window.restEditDt.files.length; i++){
                if(i !== idx) newDt.items.add(window.restEditDt.files[i]);
            }
            window.restEditDt = newDt;
            window.renderRestNewImages();
        };

        window.submitEditRestaurant = function(id) {
            var form = document.getElementById("editRestForm");
            var fd = new FormData(form);
            fd.append("_method","PUT");
            fd.append("_token","' . csrf_token() . '");
            fd.append("service_type","restaurant");
            
            fd.delete("new_images[]");
            if (window.restEditDt) {
                for(var i=0; i<window.restEditDt.files.length; i++){
                    fd.append("new_images[]", window.restEditDt.files[i]);
                }
            }
            
            var btn = form.querySelector("button[type=submit]");
            if(btn) { btn.disabled = true; btn.innerText = "Saving..."; }

            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (typeof closeCatModal === "function") closeCatModal();
                    else if (typeof closeModal === "function") closeModal();
                    if (typeof showToast === "function") showToast("Restaurant updated", "success");
                    if (typeof refreshData === "function") refreshData(); // Or reload window
                    else window.location.reload();
                },
                error: function(x) {
                    if(btn) { btn.disabled = false; btn.innerText = "Save"; }
                    var msg = "Error updating restaurant";
                    if(x.responseJSON && x.responseJSON.message) msg = x.responseJSON.message;
                    alert(msg);
                }
            });
        };
        
        setTimeout(function(){
            var sel = document.getElementById("edit_modal_vender_select");
            if (sel && typeof window.processRestEditChange === "function") {
                window.processRestEditChange(sel.value);
            }
        }, 100);
        </script>';
EOT;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Replaced successfully\n";
