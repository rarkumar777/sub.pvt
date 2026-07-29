<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\SliderImage;
use App\Models\Page;
use App\Models\TopNav;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    // ===== Sliders =====
    public function sliders()
    {
        $sliders = Slider::with('images')->paginate(20);
        return view('admin.cms.sliders', compact('sliders'));
    }

    public function storeSlider(Request $request)
    {
        $request->validate(['name' => 'required']);
        Slider::create($request->only('name'));
        return redirect()->route('admin.sliders.index')->with('success', 'Slider created');
    }

    public function editSlider($id)
    {
        $slider = Slider::findOrFail($id);
        $sliders = Slider::with('images')->paginate(20);
        return view('admin.cms.sliders', compact('sliders', 'slider'));
    }

    public function updateSlider(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        $slider = Slider::findOrFail($id);
        $slider->update($request->only('name'));
        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully');
    }

    public function deleteSlider($id)
    {
        $slider = Slider::findOrFail($id);
        $slider->images()->delete();
        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully');
    }

    public function sliderImages($sliderId)
    {
        $slider = Slider::with('images')->findOrFail($sliderId);
        $images = $slider->images;
        $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];

        // Build imageContents data for the edit modal, auto-create missing content rows
        $imageContents = [];
        foreach ($images as $img) {
            $contents = \DB::table('en33_slider_contents')->where('image_id', $img->id)->get();
            $langData = [];
            foreach ($contents as $c) {
                $langData[$c->lang] = ['text' => $c->text, 'text2' => $c->text2, 'text3' => $c->text3 ?? '', 'link' => $c->link];
            }
            // Auto-create missing content rows for languages that don't have one
            foreach ($langs as $l) {
                if (!isset($langData[$l])) {
                    \DB::table('en33_slider_contents')->insert([
                        'image_id' => $img->id, 'lang' => $l,
                        'text' => '', 'text2' => '', 'text3' => '', 'link' => '',
                    ]);
                    $langData[$l] = ['text' => '', 'text2' => '', 'text3' => '', 'link' => ''];
                }
            }
            $imageContents[$img->id] = ['price' => $img->price, 'langs' => $langData];
        }

        return view('admin.cms.slider-images', compact('sliderId', 'slider', 'images', 'imageContents'));
    }

    public function storeSliderImage(Request $request, $sliderId)
    {
        $slider = Slider::findOrFail($sliderId);


        // Accept either file upload or filename
        $imageName = null;
        $allowedMimes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'mp4', 'webm', 'ogg'];

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedMimes)) {
                return redirect()->back()->with('error', 'Only image or video files (jpg, png, mp4, webm, ogg, etc.) are allowed.');
            }
            $imageName = time() . '.' . $ext;
            $file->move(public_path('uploads/sliders'), $imageName);
        } elseif ($request->filled('image')) {
            // Use provided filename (should already exist in uploads/sliders)
            $imageName = $request->input('image');
            $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedMimes)) {
                return redirect()->back()->with('error', 'Only image or video files (jpg, png, mp4, webm, ogg, etc.) are allowed.');
            }
        } else {
            return redirect()->back()->with('error', 'Image or video file is required.');
        }

        $newImage = SliderImage::create([
            'slider_id' => $sliderId,
            'image' => $imageName,
            'price' => $request->price !== null && $request->price !== '' ? $request->price : 0,
        ]);

        // Create content rows for all languages with provided inputs
        $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];
        foreach ($langs as $lang) {
            \DB::table('en33_slider_contents')->insert([
                'image_id' => $newImage->id,
                'lang' => $lang,
                'text' => strval($request->input('edit_' . $lang)),
                'text2' => strval($request->input('edit_2' . $lang)),
                'text3' => strval($request->input('edit_3' . $lang)),
                'link' => strval($request->input('link_' . $lang)),
            ]);
        }

        return redirect()->route('admin.sliders.images', $sliderId)->with('success', 'Image added');
    }

    public function deleteSliderImage($sliderId, $imageId)
    {
        $image = SliderImage::where('slider_id', $sliderId)->where('id', $imageId)->firstOrFail();
        \DB::table('en33_slider_contents')->where('image_id', $imageId)->delete();
        $image->delete();
        return redirect()->route('admin.sliders.images', $sliderId)->with('success', 'Image deleted');
    }

    public function editSliderImage($sliderId, $imageId)
    {
        $slider = Slider::findOrFail($sliderId);
        $image = SliderImage::where('slider_id', $sliderId)->where('id', $imageId)->firstOrFail();
        $images = $slider->images;
        $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];
        $imageContents = [];
        foreach ($images as $img) {
            $contents = \DB::table('en33_slider_contents')->where('image_id', $img->id)->get();
            $langData = [];
            foreach ($contents as $c) {
                $langData[$c->lang] = ['text' => $c->text, 'text2' => $c->text2, 'text3' => $c->text3 ?? '', 'link' => $c->link];
            }
            // Auto-create missing content rows
            foreach ($langs as $l) {
                if (!isset($langData[$l])) {
                    \DB::table('en33_slider_contents')->insert([
                        'image_id' => $img->id, 'lang' => $l,
                        'text' => '', 'text2' => '', 'text3' => '', 'link' => '',
                    ]);
                    $langData[$l] = ['text' => '', 'text2' => '', 'text3' => '', 'link' => ''];
                }
            }
            $imageContents[$img->id] = ['price' => $img->price, 'langs' => $langData];
        }
        return view('admin.cms.slider-images', compact('sliderId', 'slider', 'images', 'image', 'imageContents'));
    }

    public function updateSliderImage(Request $request, $sliderId, $imageId)
    {
        $image = SliderImage::where('slider_id', $sliderId)->where('id', $imageId)->firstOrFail();

        // Handle image file upload
        if ($request->hasFile('change_image')) {
            $file = $request->file('change_image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sliders'), $imageName);
            $image->image = $imageName;
        }

        $image->price = $request->price !== null && $request->price !== '' ? $request->price : 0;
        $image->save();

        // Update multilingual contents using updateOrInsert to prevent duplicates
        $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];
        foreach ($langs as $lang) {
            \DB::table('en33_slider_contents')->updateOrInsert(
                ['image_id' => $imageId, 'lang' => $lang],
                [
                    'text' => strval($request->input('edit_' . $lang)),
                    'text2' => strval($request->input('edit_2' . $lang)),
                    'text3' => strval($request->input('edit_3' . $lang)),
                    'link' => strval($request->input('link_' . $lang)),
                ]
            );
        }

        return redirect()->route('admin.sliders.images', $sliderId)->with('success', 'Image updated');
    }

    // ===== Pages =====
    public function pages(Request $request)
    {
        $query = Page::where('lang', 'en');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', $search);
                }
                $q->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('published', $request->input('status'));
        }

        $pages = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.cms.pages', compact('pages'));
    }

    public function createPage()
    {
        return view('admin.cms.create-page');
    }

    public function storePage(Request $request)
    {
        $name = $request->input('pname') ?? '';
        $title = $request->input('ptitle') ?? '';
        $url = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        $page = Page::create([
            'name' => $name,
            'title' => $title,
            'url' => $url,
            'meta_desc' => $request->input('pmeta_desc') ?? '',
            'meta_key' => $request->input('pmeta_keywords') ?? '',
            'contents' => $request->input('pcontents') ?? '',
            'published' => $request->input('published') ?? 0,
            'layout' => $request->input('playout') ?? 'one col',
            'icon' => $request->input('selected_icon') ?? '',
            'lang' => 'en',
            'lang_id' => 0,
        ]);

        // Set lang_id = id (same as reference)
        $page->update(['lang_id' => $page->id]);

        return redirect()->route('admin.pages.index')->with('success', 'Success');
    }

    public function editPage(Request $request, $pageId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $currentLang = $request->query('PL', 'en');
        if (!in_array($currentLang, $langs)) {
            $currentLang = 'en';
        }

        // Get the English page to find lang_id
        $enPage = Page::findOrFail($pageId);
        $langId = $enPage->lang_id ?? $pageId;

        // Get page for selected language
        $page = Page::where('lang_id', $langId)->where('lang', $currentLang)->first();
        $insertNew = false;
        if (!$page) {
            $insertNew = true;
            // Show empty form but keep reference data
            $page = new Page();
            $page->name = $enPage->name;
            $page->icon = $enPage->icon;
            $page->layout = $enPage->layout;
            $page->published = $enPage->published;
        }

        return view('admin.cms.edit-page', compact('pageId', 'page', 'langs', 'currentLang', 'insertNew'));
    }

    public function updatePage(Request $request, $pageId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $pageLang = $request->input('page_lang', 'en');
        $insertNew = $request->input('insert_new') == '1';
        
        $enPage = Page::findOrFail($pageId);
        $langId = $enPage->lang_id ?? $pageId;

        $url = $request->input('url') ?? '';
        // Generate SEO URL
        $url = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $url), '-'));

        $data = [
            'name' => $request->input('pname') ?? '',
            'title' => $request->input('ptitle') ?? '',
            'url' => $url,
            'meta_desc' => $request->input('pmeta_desc') ?? '',
            'meta_key' => $request->input('pmeta_keywords') ?? '',
            'contents' => $request->input('pcontents') ?? '',
            'published' => $request->input('published') ?? 0,
            'layout' => $request->input('playout') ?? 'one col',
            'icon' => $request->input('selected_icon') ?? '',
        ];

        if ($insertNew) {
            $data['lang_id'] = $langId;
            $data['lang'] = $pageLang;
            Page::create($data);
        } else {
            Page::where('lang_id', $langId)->where('lang', $pageLang)->update($data);
        }

        return redirect(route('admin.pages.edit', $pageId) . '?PL=' . $pageLang)->with('success', 'Success');
    }

    public function destroyPage($pageId)
    {
        $page = Page::findOrFail($pageId);
        // Delete all language variants using lang_id (same as reference)
        Page::where('lang_id', $page->lang_id)->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted');
    }

    public function togglePageStatus(Request $request, $pageId)
    {
        $page = Page::findOrFail($pageId);
        $newStatus = ($page->published == 1) ? 0 : 1;
        
        // Update all language variants using lang_id
        Page::where('lang_id', $page->lang_id)->update(['published' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'label' => $newStatus == 1 ? 'Active' : 'Inactive',
            'class' => $newStatus == 1 ? 'label green' : 'label red'
        ]);
    }

    // ===== Navigation =====
    // ===== Navigation =====
    public function nav()
    {
        $items = TopNav::where('lang', 'en')->orderBy('link_order')->get();
        // Group by parent_id for easier tree rendering in blade
        $navItems = $items->groupBy('parent_id');
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        return view('admin.cms.nav', compact('navItems', 'items', 'langs'));
    }

    public function storeNavLink(Request $request)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        
        // Find max lang_id to increment
        $maxLangId = TopNav::max('lang_id') ?? 100;
        $newLangId = $maxLangId + 1;

        foreach ($langs as $lang) {
            $label = $request->input('link_label' . $lang);
            $link = $request->input('link_url' . $lang);
            
            TopNav::create([
                'lang_id' => $newLangId,
                'lang' => $lang,
                'label' => $label,
                'link' => $link,
                'parent_id' => 0,
                'target' => $request->input('link_target'),
                'icon' => $request->input('selected_icon'),
                'link_order' => $newLangId, // Default order to ID
            ]);
        }

        return redirect()->route('admin.nav.index')->with('success', 'Nav link added');
    }

    public function saveNavOrder(Request $request)
    {
        $itemsRaw = $request->input('items');
        if (!$itemsRaw) return response('No data', 400);

        $items = explode('items:', $itemsRaw);
        array_shift($items);

        foreach ($items as $item) {
            $parts = explode(',', $item);
            if (count($parts) == 3) {
                $order = $parts[0];
                $parent = $parts[1];
                $langId = $parts[2];

                TopNav::where('lang_id', $langId)->update([
                    'link_order' => $order,
                    'parent_id' => $parent
                ]);
            }
        }

        return '<div class="row cell"><div class="sd-12 green-bg white pad">Success</div></div><script>setTimeout(function(){ $("#ajax").fadeOut(); }, 2000);</script>';
    }

    public function editNavLink($navLinkId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $navLinks = TopNav::where('lang_id', $navLinkId)->get()->keyBy('lang');
        $navLink = $navLinks['en'] ?? $navLinks->first(); // Default to 'en' or first available
        return view('admin.cms.edit-nav-link', compact('navLinkId', 'navLinks', 'navLink', 'langs'));
    }

    public function updateNavLink(Request $request, $navLinkId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        
        foreach ($langs as $lang) {
            $action = $request->input('action_' . $lang);
            
            $data = [
                'parent_id' => $request->input('parent') ?? 0,
                'target' => $request->input('link_target') ?? '_self',
                'icon' => $request->input('selected_icon') ?? '',
                'label' => $request->input('link_label' . $lang) ?? '',
                'link' => $request->input('link_url' . $lang) ?? '',
            ];

            if ($action == 'edit') {
                TopNav::where('lang_id', $navLinkId)->where('lang', $lang)->update($data);
            } elseif ($action == 'insert') {
                $data['lang_id'] = $navLinkId;
                $data['lang'] = $lang;
                TopNav::create($data);
            }
        }

        return redirect()->route('admin.nav.index')->with('success', 'Nav link updated');
    }

    public function deleteNavLink($id)
    {
        TopNav::where('parent_id', $id)->update(['parent_id' => 0]);
        TopNav::where('lang_id', $id)->delete();
        return redirect()->route('admin.nav.index')->with('success', 'Nav link deleted');
    }

    // ===== Blocks =====
    public function blocks()
    {
        return view('admin.cms.blocks');
    }

    public function editBlock($blockId)
    {
        return view('admin.cms.edit-block', compact('blockId'));
    }

    // ===== Custom Blocks =====
    private function loadBlocks()
    {
        $blocksFile = storage_path('app/blocks/blocks.php');
        if (!file_exists($blocksFile)) return [];

        $content = file_get_contents($blocksFile);
        $blocks = [];
        // Parse: 'key'=>['name'=>'value','desc'=>'value'],
        if (preg_match_all("/'([^']+)'=>\['name'=>'([^']*)','desc'=>'([^']*)'\]/", $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $blocks[$m[1]] = ['name' => $m[2], 'desc' => $m[3]];
            }
        }
        return $blocks;
    }

    private function saveBlocksConfig($blocks)
    {
        $data = '<?php if (!defined(\'gogies\')){ exit;} $GOGIES[\'custom_blocks\']=[';
        foreach ($blocks as $k => $v) {
            $data .= '\'' . $k . '\'=>[\'name\'=>\'' . $v['name'] . '\',\'desc\'=>\'' . $v['desc'] . '\'],';
        }
        $data .= ']; ?>';
        file_put_contents(storage_path('app/blocks/blocks.php'), $data);
    }

    public function customBlocks()
    {
        $blocks = $this->loadBlocks();
        return view('admin.cms.custom-blocks', compact('blocks'));
    }

    public function storeCustomBlock(Request $request)
    {
        $bname = preg_replace('/[^a-zA-Z0-9_]/', '', $request->bname);
        $bdesc = $request->bdesc ?? '';
        if (empty($bname)) {
            return redirect()->route('admin.customblocks.index')->with('error', 'Name may contain only letters, numbers and underscore');
        }
        $blocks = $this->loadBlocks();
        if (isset($blocks[$bname])) {
            return redirect()->route('admin.customblocks.index')->with('error', 'Name already in use');
        }
        $blocks[$bname] = ['name' => $bname, 'desc' => $bdesc];
        $this->saveBlocksConfig($blocks);
        // Create empty block file for default language
        file_put_contents(storage_path('app/blocks/en' . $bname . '.php'), '');
        return redirect()->route('admin.customblocks.index')->with('success', 'Block created');
    }

    public function deleteCustomBlock($name)
    {
        $blocks = $this->loadBlocks();
        if (isset($blocks[$name])) {
            unset($blocks[$name]);
            $this->saveBlocksConfig($blocks);
            // Delete all language files
            $langs = ['en', 'fr', 'ar', 'it', 'es', 'ge', 'pt'];
            foreach ($langs as $l) {
                $file = storage_path('app/blocks/' . $l . $name . '.php');
                if (file_exists($file)) @unlink($file);
                $file2 = storage_path('app/blocks/' . ucfirst($l) . $name . '.php');
                if (file_exists($file2)) @unlink($file2);
            }
            return redirect()->route('admin.customblocks.index')->with('success', 'Block deleted');
        }
        return redirect()->route('admin.customblocks.index')->with('error', 'Block not found');
    }

    public function editCustomBlock(Request $request, $name)
    {
        $blocks = $this->loadBlocks();
        if (!isset($blocks[$name])) {
            return redirect()->route('admin.customblocks.index')->with('error', 'Block not found');
        }
        $bname = $name;
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $blang = $request->query('lang', 'en');
        if (!in_array($blang, $langs)) $blang = 'en';

        $blockFile = storage_path('app/blocks/' . $blang . $bname . '.php');
        $blockContent = '';
        if (file_exists($blockFile)) {
            $blockContent = file_get_contents($blockFile);
        }
        return view('admin.cms.edit-block', compact('bname', 'blang', 'langs', 'blockContent'));
    }

    public function updateCustomBlock(Request $request, $name)
    {
        $blocks = $this->loadBlocks();
        if (!isset($blocks[$name])) {
            return redirect()->route('admin.customblocks.index')->with('error', 'Block not found');
        }
        $blang = $request->input('blang', 'en');
        $blockCode = $request->input('block_code', '');
        $blockFile = storage_path('app/blocks/' . $blang . $name . '.php');
        file_put_contents($blockFile, html_entity_decode(strval($blockCode)));
        return redirect()->route('admin.customblocks.edit', $name)->with('success', 'Block saved')->withInput(['lang' => $blang]);
    }


    // ===== Footer =====
    public function footer(Request $request)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $currentLang = $request->query('lang', 'en');
        if (!in_array($currentLang, $langs)) {
            $currentLang = 'en';
        }
        
        $footerFile = storage_path('app/footer/' . $currentLang . '.php');
        $footerContent = '';
        if (file_exists($footerFile)) {
            $footerContent = file_get_contents($footerFile);
        }
        
        return view('admin.cms.footer', compact('langs', 'currentLang', 'footerContent'));
    }

    public function updateFooter(Request $request)
    {
        $lang = $request->input('lang', 'en');
        $content = $request->input('footer_contents') ?? '';
        
        $footerDir = storage_path('app/footer');
        if (!is_dir($footerDir)) {
            mkdir($footerDir, 0755, true);
        }
        
        file_put_contents($footerDir . '/' . $lang . '.php', html_entity_decode($content));
        
        return redirect()->route('admin.footer.index', ['lang' => $lang])->with('success', 'Success');
    }
}
