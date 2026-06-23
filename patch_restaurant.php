<?php
$content = file_get_contents('/home/rar/Desktop/NEW_VERSION/V10/app/Http/Controllers/Admin/ServiceController.php');
$search = 'private function editTransportModal($service)';
$insert = <<<HTML
    private function editRestaurantModal(\$service)
    {
        \$flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        \$imgPath = \$service->image ?? '';
        \$desc = htmlspecialchars(\$service->description ?? '');
        \$sid = \$service->id;
        \$notes = htmlspecialchars(\$service->notes ?? '');
        \$arrival = htmlspecialchars(\$service->arrival ?? '');

        \$html = '<script>';
        \$html .= 'document.getElementById("libModalHead").innerHTML=\'';
        \$html .= '<h3>Modify restaurant</h3>';
        \$html .= '<div style="display:flex;gap:10px;align-items:center">';
        \$html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#1a6b54;text-decoration:none">Cancel</a>';
        \$html .= '<button form="editRestForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#1a6b54;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        \$html .= '</div>\';';
        \$html .= '</script>';

        \$html .= '<form id="editRestForm" onsubmit="submitEditService(' . \$sid . '); return false;">';
        \$html .= csrf_field();
        \$html .= '<input type="hidden" name="service_type" value="restaurant">';

        // Flags
        \$html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach (\$flags as \$f) {
            \$active = (\$f['code'] === 'en');
            \$bg = \$active ? '#1a6b54' : 'transparent';
            \$border = \$active ? '2px solid #1a6b54' : '2px solid transparent';
            \$html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . \$border . ';background:' . \$bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . \$f['emoji'] . '</div>';
        }
        \$html .= '</div>';

        // Photos
        \$existingImages = [];
        if (\$imgPath) { \$d = @json_decode(\$imgPath, true); \$existingImages = is_array(\$d) ? \$d : [\$imgPath]; }
        \$html .= '<div style="margin-bottom:16px;">';
        \$html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        \$html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        \$html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#1a6b54;text-decoration:none;">How to choose the right photos?</a>';
        \$html .= '</div>';
        \$html .= '<input type="file" name="new_images[]" id="editRestImageInput" accept="image/*" multiple style="display:none" onchange="addActSecImages(this)">';
        \$html .= '<div id="restPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach (\$existingImages as \$img) {
            \$imgUrl = (str_starts_with(\$img, 'http')) ? \$img : '/' . ltrim(\$img, '/');
            \$html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            \$html .= '<img src="' . \$imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
            \$html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars(\$img) . '">';
            \$html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            \$html .= '</div>';
        }
        \$html .= '<div onclick="document.getElementById(\'editRestImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        \$html .= '</div></div>';

        \$html .= '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;">';
        \$html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Restaurant name</legend>';
        \$html .= '<input type="text" name="description" required style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="'.\$desc.'">';
        \$html .= '</fieldset>';

        \$html .= '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;">';
        \$html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        \$html .= '<input type="text" name="arrival" style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="'.\$arrival.'">';
        \$html .= '</fieldset>';

        \$html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;">';
        \$html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        \$html .= '<textarea name="notes" style="width:100%;min-height:250px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">'.\$notes.'</textarea>';
        \$html .= '</fieldset>';

        \$html .= '<input type="hidden" name="cost" value="' . (\$service->cost ?? 0) . '">';
        \$html .= '<input type="hidden" name="category" value="' . (\$service->category ?? '') . '">';
        \$html .= '</form>';

        return response()->json(['html' => \$html]);
    }

HTML;

$content = str_replace($search, $insert . "\n" . $search, $content);
file_put_contents('/home/rar/Desktop/NEW_VERSION/V10/app/Http/Controllers/Admin/ServiceController.php', $content);
echo "Patched successfully!";
?>
