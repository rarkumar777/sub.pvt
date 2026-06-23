<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\City;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function global()
    {
        if (!defined('gogies')) define('gogies', true);
        $configPath = base_path('../pvt.jo/config/global.php');
        $settings = $this->parseGlobalConfig($configPath);

        // Time zones
        $timezones = timezone_identifiers_list();

        // Themes
        $themePath = base_path('../pvt.jo/theme/');
        $themes = [];
        if (is_dir($themePath)) {
            foreach (scandir($themePath) as $i) {
                if (file_exists($themePath . $i . '/1_layout.php')) {
                    $themes[] = $i;
                }
            }
        }
        if (empty($themes)) $themes = ['pvt', 'default', 'modern'];

        // Layouts
        $layoutsPath = base_path('../pvt.jo/config/layouts/layouts.php');
        if (!file_exists($layoutsPath)) {
            $layoutsPath = storage_path('app/layouts/layouts.php');
        }
        $layouts = [];
        if (file_exists($layoutsPath)) {
            if (!defined('gogies')) define('gogies', true);
            $GOGIES = [];
            include $layoutsPath;
            $layouts = $GOGIES['layouts'] ?? [];
        }
        if (empty($layouts)) {
            $layouts = [
                'Homepage layout' => '1_layout',
                'Innerpage layout' => 'other_layout'
            ];
        }

        // Languages
        $langPath = base_path('../pvt.jo/lang/');
        $langs = [];
        if (is_dir($langPath)) {
            foreach (scandir($langPath) as $i) {
                if ($i != '.' && $i != '..' && file_exists($langPath . $i . '/lang_data.php')) {
                    $langs[] = $i;
                }
            }
        }
        if (empty($langs)) {
            $localLangs = storage_path('app/languages.json');
            if (file_exists($localLangs)) {
                $j = json_decode(file_get_contents($localLangs), true);
                foreach ($j['languages'] ?? [] as $l) {
                    if ($l['active']) $langs[] = $l['code'];
                }
            }
        }
        if (empty($langs)) $langs = ['en', 'Ar'];

        // Currencies
        $currencies = \DB::table('en33_currency')->where('lang', 'en')->pluck('name', 'lang_id')->toArray();
        if (empty($currencies)) {
            $currencies = ['1' => 'JOD', '2' => 'USD', '3' => 'EUR'];
        }

        // User groups
        $ugPath = base_path('../pvt.jo/config/users/user_groups.php');
        $userGroups = [];
        if (file_exists($ugPath)) {
            $GOGIES = [];
            include $ugPath;
            $userGroups = array_keys($GOGIES['user_groups'] ?? []);
        }
        if (empty($userGroups)) $userGroups = ['clients', 'agents'];

        return view('admin.settings.global', compact('settings', 'timezones', 'themes', 'layouts', 'langs', 'currencies', 'userGroups'));
    }

    public function updateGlobal(Request $request)
    {
        $configPath = base_path('../pvt.jo/config/global.php');
        $oldSettings = $this->parseGlobalConfig($configPath);

        $data = '<?php if (!defined(\'gogies\')){ exit;}
$GOGIES[\'system_mail\']=\'' . $request->input('systemmail', '') . '\';
$GOGIES[\'time_zone\']=\'' . $request->input('time_zone', 'Asia/Amman') . '\';
$GOGIES[\'theme\']=\'' . $request->input('defaulttheme', 'pvt') . '\';
$GOGIES[\'debug\']=\'' . $request->input('debug', 'off') . '\';
$GOGIES[\'session_life_time\']=' . ($oldSettings['session_life_time'] ?? 0) . ';
$GOGIES[\'adminfolder\']=\'' . ($oldSettings['adminfolder'] ?? 'admin') . '\';
$GOGIES[\'lang\']=\'' . $request->input('defaultlang', 'en') . '\';
$GOGIES[\'admin_lang\']=\'' . $request->input('defaultlangadmin', 'en') . '\';
$GOGIES[\'defaultlayout\']=\'' . $request->input('defaultlayout', 'Homepage layout') . '\';
$GOGIES[\'recored_per_page\']=' . intval($request->input('recored_per_page', 100)) . ';
$GOGIES[\'WEB_SITE_OFFLINE\']=\'' . $request->input('turnoffwebsite', 'on') . '\';
$GOGIES[\'currency\']=\'' . $request->input('default_currency', '') . '\';
$GOGIES[\'check_out_currency\']=\'' . $request->input('check_out_currency', '') . '\';
$GOGIES[\'front_currency\']=\'' . $request->input('front_currency', '') . '\';
$GOGIES[\'def_user_group\']=\'' . $request->input('def_user_group', 'clients') . '\';
$GOGIES[\'smtp_server\']=\'' . $request->input('smtp_server', '') . '\';
$GOGIES[\'smtp_port\']=\'' . $request->input('smtp_port', '') . '\';
$GOGIES[\'smtp_username\']=\'' . $request->input('smtp_username', '') . '\';
$GOGIES[\'smtp_password\']=\'' . $request->input('smtp_password', '') . '\';
$GOGIES[\'smtp_secure\']=\'' . $request->input('smtp_secure', 'none') . '\';
$GOGIES[\'google_analytics\']=\'' . $request->input('google_analytics', '') . '\';
$GOGIES[\'fb_status\']=\'' . $request->input('fb_status', '0') . '\';
$GOGIES[\'fb_app_id\']=\'' . $request->input('fb_app_id', '') . '\';
$GOGIES[\'fb_app_secret\']=\'' . $request->input('fb_app_secret', '') . '\';
$GOGIES[\'fb_graph_version\']=\'' . $request->input('fb_graph_version', '') . '\';

 ?>';
        file_put_contents($configPath, $data);
        return redirect()->route('admin.settings.global')->with('success', 'Global settings saved successfully');
    }

    private function parseGlobalConfig($configPath)
    {
        $settings = [];
        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            if (preg_match_all("/\\$" . "GOGIES\\['([^']+)'\\]='([^']*)'/", $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $settings[$m[1]] = $m[2];
                }
            }
            if (preg_match_all("/\\$" . "GOGIES\\['([^']+)'\\]=([0-9]+)/", $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $settings[$m[1]] = $m[2];
                }
            }
        }
        return $settings;
    }

    public function countries()
    {
        $countries = Country::where('lang', 'en')->orderBy('name')->paginate(20);
        return view('admin.settings.countries', compact('countries'));
    }

    public function storeCountry(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        Country::create(['name' => $request->name, 'lang' => 'en', 'lang_id' => 0]);
        return redirect()->route('admin.settings.countries')->with('success', 'Country added');
    }

    public function updateCountry(Request $request, $id)
    {
        $request->validate(['name' => 'required|string']);
        $country = Country::findOrFail($id);
        $country->update(['name' => $request->name]);
        return redirect()->route('admin.settings.countries')->with('success', 'Country updated');
    }

    public function deleteCountry($id)
    {
        Country::destroy($id);
        return redirect()->route('admin.settings.countries')->with('success', 'Country deleted');
    }

    public function currency()
    {
        $currencies = Currency::where('lang', 'en')->paginate(20);
        return view('admin.settings.currency', compact('currencies'));
    }

    public function storeCurrency(Request $request)
    {
        $request->validate(['name' => 'required|string', 'symbol' => 'required|string', 'rate' => 'required|numeric']);
        Currency::create(['name' => $request->name, 'symbol' => $request->symbol, 'rate' => $request->rate, 'lang' => 'en', 'lang_id' => 0]);
        return redirect()->route('admin.settings.currency')->with('success', 'Currency added');
    }

    public function updateCurrency(Request $request, $id)
    {
        $request->validate(['name' => 'required|string', 'symbol' => 'required|string', 'rate' => 'required|numeric']);
        $currency = Currency::findOrFail($id);
        $currency->update(['name' => $request->name, 'symbol' => $request->symbol, 'rate' => $request->rate]);
        return redirect()->route('admin.settings.currency')->with('success', 'Currency updated');
    }

    public function deleteCurrency($id)
    {
        Currency::destroy($id);
        return redirect()->route('admin.settings.currency')->with('success', 'Currency deleted');
    }

    public function companyProfile()
    {
        $profilePath = storage_path('app/company_profile.json');

        if (!file_exists($profilePath)) {
            // Import from reference config on first load
            $refConfigPath = base_path('../pvt.jo/config/company_profile.php');
            if (file_exists($refConfigPath)) {
                $GOGIES = [];
                defined('gogies') || define('gogies', true);
                @include $refConfigPath;
                $profile = [
                    'telephone' => $GOGIES['company_telephone'] ?? '',
                    'fax' => $GOGIES['company_fax'] ?? '',
                    'email' => $GOGIES['company_email'] ?? '',
                    'national_number' => $GOGIES['company_national_number'] ?? '',
                    'google_map' => $GOGIES['company_google_map'] ?? '',
                    'logo' => $GOGIES['company_logo'] ?? '',
                    'fav_icon' => $GOGIES['company_fav_icon'] ?? '',
                    'langs' => [],
                ];
                $langCodes = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
                foreach ($langCodes as $lc) {
                    if (isset($GOGIES['company_name'][$lc])) {
                        $profile['langs'][$lc] = [
                            'name' => html_entity_decode($GOGIES['company_name'][$lc] ?? '', ENT_QUOTES, 'UTF-8'),
                            'address' => html_entity_decode($GOGIES['company_address'][$lc] ?? '', ENT_QUOTES, 'UTF-8'),
                            'opening_hours' => html_entity_decode($GOGIES['company_opening_hours'][$lc] ?? '', ENT_QUOTES, 'UTF-8'),
                            'mail_signature' => html_entity_decode($GOGIES['company_mail_signture'][$lc] ?? '', ENT_QUOTES, 'UTF-8'),
                        ];
                    }
                }
                file_put_contents($profilePath, json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $profile = [];
            }
        } else {
            $profile = json_decode(file_get_contents($profilePath), true) ?: [];
        }

        // Ensure langs exist even if empty
        if (empty($profile['langs'])) {
            $profile['langs'] = ['en' => ['name' => '', 'address' => '', 'opening_hours' => '', 'mail_signature' => '']];
        }

        return view('admin.settings.company-profile', compact('profile'));
    }

    public function updateCompanyProfile(Request $request)
    {
        $profilePath = storage_path('app/company_profile.json');
        $profile = file_exists($profilePath) ? json_decode(file_get_contents($profilePath), true) : [];

        $profile['telephone'] = $request->input('telephone', '');
        $profile['fax'] = $request->input('fax', '');
        $profile['email'] = $request->input('email', '');
        $profile['national_number'] = $request->input('national_number', '');
        $profile['google_map'] = $request->input('google_map', '');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $profile['logo'] = $filename;
        }

        // Handle favicon upload
        if ($request->hasFile('fav_icon')) {
            $file = $request->file('fav_icon');
            $filename = 'fav' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $profile['fav_icon'] = $filename;
        }

        // Handle per-language fields
        $langs = $request->input('langs', []);
        foreach ($langs as $lang => $data) {
            $profile['langs'][$lang]['name'] = $data['name'] ?? '';
            $profile['langs'][$lang]['address'] = $data['address'] ?? '';
            $profile['langs'][$lang]['opening_hours'] = $data['opening_hours'] ?? '';
            $profile['langs'][$lang]['mail_signature'] = $data['mail_signature'] ?? '';
        }

        file_put_contents($profilePath, json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return redirect()->route('admin.settings.company-profile')->with('success', 'Company profile updated successfully');
    }

    public function languages()
    {
        $langPath = storage_path('app/languages.json');
        $config = file_exists($langPath) ? json_decode(file_get_contents($langPath), true) : ['default' => 'en', 'languages' => []];
        $languages = $config['languages'] ?? [];
        $defaultLang = $config['default'] ?? 'en';
        return view('admin.settings.languages', compact('languages', 'defaultLang'));
    }

    public function toggleLanguage(Request $request, $code)
    {
        $langPath = storage_path('app/languages.json');
        $config = file_exists($langPath) ? json_decode(file_get_contents($langPath), true) : [];
        foreach ($config['languages'] as &$lang) {
            if ($lang['code'] === $code) {
                $lang['active'] = !$lang['active'];
                break;
            }
        }
        file_put_contents($langPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return redirect()->route('admin.settings.languages')->with('success', 'Language status updated');
    }

    public function deleteLanguage($code)
    {
        $langPath = storage_path('app/languages.json');
        $config = file_exists($langPath) ? json_decode(file_get_contents($langPath), true) : [];
        $config['languages'] = array_values(array_filter($config['languages'], fn($l) => $l['code'] !== $code));
        file_put_contents($langPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return redirect()->route('admin.settings.languages')->with('success', 'Language deleted');
    }

    public function storeLanguage(Request $request)
    {
        $request->validate(['code' => 'required|string|max:5', 'name' => 'required|string']);
        $langPath = storage_path('app/languages.json');
        $config = file_exists($langPath) ? json_decode(file_get_contents($langPath), true) : ['default' => 'en', 'languages' => []];
        $config['languages'][] = [
            'code' => $request->code,
            'name' => $request->name,
            'flag' => 'flag.png',
            'dir' => $request->input('dir', 'ltr'),
            'active' => true,
        ];
        file_put_contents($langPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return redirect()->route('admin.settings.languages')->with('success', 'Language added');
    }

    public function emailTemplates($mod = null)
    {
        $templatesPath = storage_path('app/email_templates.json');
        
        // Initialize default templates if not exist
        if (!file_exists($templatesPath)) {
            $defaultTemplates = [
                'tours' => [
                    'name' => 'Tours Booking',
                    'emails' => [
                        'thank_you' => ['title' => 'Thank You For Your Booking', 'subject' => 'Booking Confirmation', 'body' => 'Dear [NAME], Thank you for your booking...'],
                        'admin_notice' => ['title' => 'Admin Please Note New Booking', 'subject' => 'New Tour Booking Received', 'body' => 'A new booking has been made...']
                    ]
                ],
                'users' => [
                    'name' => 'Users',
                    'emails' => [
                        'reset_password' => ['title' => 'Reset Your Password', 'subject' => 'Password Reset Template', 'body' => 'Click here to reset your password: [LINK]']
                    ]
                ],
                'invoice' => [
                    'name' => 'Invoice',
                    'emails' => [
                        'invoice_pdf' => ['title' => 'Invoice PDF generation', 'subject' => 'Your Invoice', 'body' => 'Please find your invoice attached.']
                    ]
                ]
            ];
            file_put_contents($templatesPath, json_encode($defaultTemplates, JSON_PRETTY_PRINT));
        }

        $templates = json_decode(file_get_contents($templatesPath), true);

        return view('admin.settings.email-templates', compact('mod', 'templates'));
    }

    public function editEmailTemplate($mod, $key)
    {
        $templatesPath = storage_path('app/email_templates.json');
        $templates = json_decode(file_get_contents($templatesPath), true);

        if (!isset($templates[$mod]['emails'][$key])) {
            return redirect()->route('admin.settings.email-templates')->with('error', 'Template not found');
        }

        $template = $templates[$mod]['emails'][$key];
        $templateTitle = $template['title'];

        return view('admin.settings.email-templates-edit', compact('mod', 'key', 'template', 'templateTitle'));
    }

    public function updateEmailTemplate(Request $request, $mod, $key)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string'
        ]);

        $templatesPath = storage_path('app/email_templates.json');
        $templates = json_decode(file_get_contents($templatesPath), true);

        if (isset($templates[$mod]['emails'][$key])) {
            $templates[$mod]['emails'][$key]['subject'] = $request->subject;
            $templates[$mod]['emails'][$key]['body'] = $request->body;
            file_put_contents($templatesPath, json_encode($templates, JSON_PRETTY_PRINT));
            return redirect()->route('admin.settings.email-templates', $mod)->with('success', 'Email template updated successfully!');
        }

        return redirect()->route('admin.settings.email-templates', $mod)->with('error', 'Template not found');
    }

    // ===== NEW SETTINGS PAGES =====

    public function seo()
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $seoData = [];

        foreach ($langs as $L) {
            $file = storage_path('app/seo/' . $L . '.php');
            $seoData[$L] = [
                'title' => '',
                'name' => '',
                'keywords' => '',
                'description' => '',
                'other_head_tags' => '',
            ];
            if (file_exists($file)) {
                $content = file_get_contents($file);
                // Parse the PHP variables from file
                if (preg_match("/\\$" . "lang\\['" . $L . "_TITLE'\\]='(.*?)';/", $content, $m)) {
                    $seoData[$L]['title'] = $m[1];
                }
                if (preg_match("/\\$" . "lang\\['" . $L . "_NAME'\\]='(.*?)';/", $content, $m)) {
                    $seoData[$L]['name'] = $m[1];
                }
                if (preg_match("/\\$" . "lang\\['" . $L . "_KEY_WORDS'\\]='(.*?)';/", $content, $m)) {
                    $seoData[$L]['keywords'] = $m[1];
                }
                if (preg_match("/\\$" . "lang\\['" . $L . "_DESCRIPTION'\\]='(.*?)';/", $content, $m)) {
                    $seoData[$L]['description'] = $m[1];
                }
                if (preg_match("/\\$" . "lang\\['" . $L . "_OTHER_HEAD_TAGS'\\]='(.*)';/s", $content, $m)) {
                    $seoData[$L]['other_head_tags'] = $m[1];
                }
            }
        }

        return view('admin.settings.seo', compact('langs', 'seoData'));
    }

    public function updateSeo(Request $request)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $seoDir = storage_path('app/seo');
        if (!is_dir($seoDir)) {
            mkdir($seoDir, 0755, true);
        }

        foreach ($langs as $L) {
            $title = str_replace("'", "\\'", $request->input('title' . $L) ?? '');
            $name = str_replace("'", "\\'", $request->input('websitename' . $L) ?? '');
            $keywords = str_replace("'", "\\'", $request->input('websitekeywords' . $L) ?? '');
            $description = str_replace("'", "\\'", $request->input('websitemetadescription' . $L) ?? '');
            $otherTags = str_replace("'", "\\'", $request->input('extra_head_tags' . $L) ?? '');

            $data = "<?php\nif (!defined('gogies')){ exit;}\n";
            $data .= "\$lang['" . $L . "_TITLE']='" . $title . "';\n";
            $data .= "\$lang['" . $L . "_NAME']='" . $name . "';\n";
            $data .= "\$lang['" . $L . "_KEY_WORDS']='" . $keywords . "';\n";
            $data .= "\$lang['" . $L . "_DESCRIPTION']='" . $description . "';\n";
            $data .= "\$lang['" . $L . "_OTHER_HEAD_TAGS']='" . $otherTags . "';\n";
            $data .= "?>";

            file_put_contents($seoDir . '/' . $L . '.php', $data);
        }

        return redirect()->route('admin.settings.seo')->with('success', 'Success');
    }

    public function onPageSeo()
    {
        return view('admin.settings.on-page-seo');
    }

    public function sitemap()
    {
        return view('admin.settings.sitemap');
    }

    public function links()
    {
        return view('admin.settings.links');
    }

    public function payments(Request $request)
    {
        $gatewaysPath = storage_path('app/payment_gate_ways.json');
        
        // Default gateway config if file doesn't exist
        if (!file_exists($gatewaysPath)) {
            $defaultGateways = [
                'status' => [
                    'migs' => 0,
                    'paypal' => 0,
                    'offline_payments' => 0,
                    'paytabs' => 1,
                ],
                'configs' => [
                    'migs' => ['mid' => '', 'access_code' => '', 'secret_hash' => '', 'handle_fee' => '0', 'test_mode' => 0],
                    'paypal' => ['id' => '', 'sand_box' => 0],
                    'offline_payments' => ['en' => 'Please pay via bank transfer.'],
                    'paytabs' => ['profile_id' => '', 'server_key' => '', 'client_key' => '', 'url' => '', 'handle_fee' => '0'],
                ]
            ];
            file_put_contents($gatewaysPath, json_encode($defaultGateways, JSON_PRETTY_PRINT));
        }

        $allData = json_decode(file_get_contents($gatewaysPath), true);
        $gateways = $allData['status'];
        $configs = $allData['configs'];

        // Handle enable
        if ($request->has('enable') && array_key_exists($request->enable, $gateways)) {
            $allData['status'][$request->enable] = 1;
            file_put_contents($gatewaysPath, json_encode($allData, JSON_PRETTY_PRINT));
            return redirect()->route('admin.settings.payments')->with('success', 'Enabled successfully');
        }

        // Handle disable
        if ($request->has('disable') && array_key_exists($request->disable, $gateways)) {
            $allData['status'][$request->disable] = 0;
            file_put_contents($gatewaysPath, json_encode($allData, JSON_PRETTY_PRINT));
            return redirect()->route('admin.settings.payments')->with('success', 'Disabled successfully');
        }

        $gatewayNames = [
            'migs' => 'Migs',
            'paypal' => 'Paypal',
            'offline_payments' => 'Offline Payments',
            'paytabs' => 'PayTabs',
        ];

        // Language config for offline
        $langPath = storage_path('app/languages.json');
        $langConfig = file_exists($langPath) ? json_decode(file_get_contents($langPath), true) : ['languages' => []];
        $langs = [];
        foreach ($langConfig['languages'] ?? [] as $lang) {
            if ($lang['active']) {
                $langs[] = $lang['code'];
            }
        }
        if (empty($langs)) $langs = ['en'];

        $offlineDetails = $configs['offline_payments'] ?? [];

        return view('admin.settings.payments', compact('gateways', 'gatewayNames', 'configs', 'langs', 'offlineDetails'));
    }

    public function updatePaymentConfig(Request $request, $gateway)
    {
        $gatewaysPath = storage_path('app/payment_gate_ways.json');
        $allData = file_exists($gatewaysPath) ? json_decode(file_get_contents($gatewaysPath), true) : ['status' => [], 'configs' => []];
        
        $data = $request->except('_token');
        
        if ($gateway === 'offline_payments') {
            $offline = [];
            foreach ($data as $k => $v) {
                if (str_starts_with($k, 'details')) {
                    $lang = str_replace('details', '', $k);
                    $offline[$lang] = $v;
                }
            }
            $allData['configs'][$gateway] = $offline;
        } else {
            // Checkboxes might be absent if unchecked
            if ($gateway === 'migs' && !isset($data['test_mode'])) $data['test_mode'] = 0;
            if ($gateway === 'paypal' && !isset($data['sand_box'])) $data['sand_box'] = 0;
            
            $allData['configs'][$gateway] = $data;
        }
        
        file_put_contents($gatewaysPath, json_encode($allData, JSON_PRETTY_PRINT));
        return redirect()->route('admin.settings.payments')->with('success', 'Configuration updated successfully');
    }

    // ===== Layouts =====
    private function loadLayouts()
    {
        $file = storage_path('app/layouts/layouts.php');
        if (!file_exists($file)) return [];
        $content = file_get_contents($file);
        $layouts = [];
        if (preg_match_all("/'([^']+)'=>'([^']*)'/", $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $layouts[$m[1]] = $m[2];
            }
        }
        return $layouts;
    }

    private function saveLayoutsConfig($layouts)
    {
        $data = '<?php if (!defined(\'gogies\')){ exit;} $GOGIES[\'layouts\']=[';
        foreach ($layouts as $k => $v) {
            $data .= '\'' . $k . '\'=>\'' . $v . '\',';
        }
        $data .= ']; ?>';
        file_put_contents(storage_path('app/layouts/layouts.php'), $data);
    }

    public function layouts()
    {
        $layouts = $this->loadLayouts();
        $defaultLayout = 'Homepage layout';
        return view('admin.settings.layouts', compact('layouts', 'defaultLayout'));
    }

    public function storeLayout(Request $request)
    {
        $name = preg_replace('/[^a-zA-Z0-9_ ]/', '', $request->layoutname);
        $colType = $request->columns_num;
        if (empty($name) || !in_array($colType, ['1', '2l', '2r', '3'])) {
            return redirect()->route('admin.settings.layouts')->with('error', 'Invalid name or column type');
        }
        $layouts = $this->loadLayouts();
        if (isset($layouts[$name])) {
            return redirect()->route('admin.settings.layouts')->with('error', 'Name already in use');
        }
        $layouts[$name] = $colType;
        $this->saveLayoutsConfig($layouts);
        // Create empty layout config file
        $data = '<?php if (!defined(\'gogies\')){ exit;} ?>';
        file_put_contents(storage_path('app/layouts/' . $name . '.php'), $data);
        return redirect()->route('admin.settings.layouts')->with('success', 'Layout created');
    }

    public function deleteLayout($name)
    {
        $layouts = $this->loadLayouts();
        $defaultLayout = 'Homepage layout';
        if ($name == $defaultLayout) {
            return redirect()->route('admin.settings.layouts')->with('error', 'Cannot delete default layout');
        }
        if (isset($layouts[$name])) {
            unset($layouts[$name]);
            $this->saveLayoutsConfig($layouts);
            $file = storage_path('app/layouts/' . $name . '.php');
            if (file_exists($file)) @unlink($file);
            return redirect()->route('admin.settings.layouts')->with('success', 'Layout deleted');
        }
        return redirect()->route('admin.settings.layouts')->with('error', 'Layout not found');
    }

    public function layoutBlocks(Request $request, $name)
    {
        $layouts = $this->loadLayouts();
        if (!isset($layouts[$name])) {
            return redirect()->route('admin.settings.layouts')->with('error', 'Layout not found');
        }
        $colType = $layouts[$name];
        $sliders = \DB::table('en33_slider')->get();
        
        // Load existing blocks for this layout
        $layoutFile = storage_path('app/layouts/' . $name . '.php');
        $centerTop = []; $centerBottom = []; $leftSide = []; $rightSide = [];
        $sliderId = 0;
        if (file_exists($layoutFile)) {
            // Parse the layout config
            if (!defined('gogies')) define('gogies', true);
            $GOGIES = ['slider' => 0];
            $center_top = []; $center_bottom = []; $left_side = []; $right_side = [];
            include $layoutFile;
            $centerTop = $center_top ?? [];
            $centerBottom = $center_bottom ?? [];
            $leftSide = $left_side ?? [];
            $rightSide = $right_side ?? [];
            $sliderId = $GOGIES['slider'] ?? 0;
        }

        return view('admin.settings.layout-blocks', compact(
            'name', 'colType', 'sliders', 'centerTop', 'centerBottom',
            'leftSide', 'rightSide', 'sliderId'
        ));
    }

    public function saveLayoutBlocks(Request $request, $name)
    {
        $layouts = $this->loadLayouts();
        if (!isset($layouts[$name])) {
            return response()->json(['success' => false, 'message' => 'Layout not found']);
        }

        $data = '<?php if (!defined(\'gogies\')){ exit;}' . "\n";
        $data .= '$GOGIES[\'slider\']=' . intval($request->input('slider', 0)) . ';' . "\n";

        $zones = [
            'center_top_blocks' => 'center_top',
            'center_bottom_blocks' => 'center_bottom',
            'left_blocks' => 'left_side',
            'right_blocks' => 'right_side'
        ];

        foreach ($zones as $requestKey => $phpVarName) {
            $items = $request->input($requestKey, []);
            $data .= '$' . $phpVarName . '=[';
            if (!empty($items)) {
                foreach ($items as $item) {
                    $parts = explode('$-$', $item);
                    if (count($parts) >= 3) {
                        $data .= '[\'' . addslashes($parts[0]) . '\',\'' . addslashes($parts[1]) . '\',\'' . addslashes($parts[2]) . '\',\'' . addslashes($parts[2]) . '\'],';
                    }
                }
            }
            $data .= '];' . "\n";
        }
        $data .= '?>';

        file_put_contents(storage_path('app/layouts/' . $name . '.php'), $data);
        return response()->json(['success' => true]);
    }

    public function getBlocks(Request $request)
    {
        $mod = preg_replace('/[^a-zA-Z0-9_]/', '', $request->query('mod'));
        if (!$mod) return '';

        $blocks = [];
        if ($mod == 'custom') {
            $blocksFile = storage_path('app/blocks/blocks.php');
            if (file_exists($blocksFile)) {
                // Parse: 'key'=>['name'=>'value','desc'=>'value'],
                $content = file_get_contents($blocksFile);
                if (preg_match_all("/'([^']+)'=>\['name'=>'([^']*)','desc'=>'([^']*)'\]/", $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $m) {
                        $blocks[$m[1]] = ['name' => $m[2], 'desc' => $m[3]];
                    }
                }
            }
        } else {
            // Assume module blocks are in public/gogies/blocks/blocks.php or similar
            $blocksFile = public_path('gogies/blocks/blocks.php');
            if (file_exists($blocksFile)) {
                $tours_lang = ['latest_tours'=>'Latest Tours','featured_tours'=>'Featured Tours','special_offer'=>'Special Offers','search_box'=>'Search Box','latest_tours_alt'=>'Latest Tours Alt','random_tours'=>'Random Tours'];
                if (!defined('gogies')) define('gogies', true);
                include $blocksFile;
                $varName = $mod . '_blocks';
                if (isset($$varName)) {
                    $blocks = $$varName;
                }
            }
        }

        $res = '';
        foreach ($blocks as $k => $b) {
            $res .= '<li title="' . ($b['desc'] ?? '') . '" id="' . $mod . '$-$' . $k . '$-$' . ($b['name'] ?? $k) . '" style="cursor:move; padding:8px; margin:2px 0; background:#eee;">' . ($b['name'] ?? $k) . '</li>';
        }
        return $res;
    }

    public function layoutSettings()
    {
        $layouts = $this->loadLayouts();
        // Load current settings
        $settings = [];
        $settingsFile = storage_path('app/layouts/layouts_settings.php');
        if (file_exists($settingsFile)) {
            $content = file_get_contents($settingsFile);
            if (preg_match_all("/\\\$GOGIES\['layouts_settings'\]\['([^']+)'\]='([^']*)'/", $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $settings[$m[1]] = $m[2];
                }
            }
        }
        // Module items with labels
        $items = [
            'tours' => 'Tours Booking',
            'media_galleries' => 'Media Galleries ??',
            'users' => 'Users',
            'invoice' => 'Invoice',
            'contact-us' => 'contact-us',
            'services' => 'Services',
            'home_page_layout' => 'Home page layout',
        ];
        return view('admin.settings.layout-settings', compact('layouts', 'settings', 'items'));
    }

    public function saveLayoutSettings(Request $request)
    {
        $layouts = $this->loadLayouts();
        $items = ['tours', 'media_galleries', 'users', 'invoice', 'contact-us', 'services', 'home_page_layout'];
        $data = '<?php if (!defined(\'gogies\')){ exit;} ';
        foreach ($items as $item) {
            $val = $request->input($item, '');
            if (isset($layouts[$val])) {
                $data .= '$GOGIES[\'layouts_settings\'][\'' . $item . '\']=\'' . $val . '\'; ';
            }
        }
        $data .= ' ?>';
        file_put_contents(storage_path('app/layouts/layouts_settings.php'), $data);
        return redirect()->route('admin.settings.layout-settings')->with('success', 'Settings saved');
    }

    public function destinations()
    {
        return view('admin.settings.destinations');
    }

    public function storeDestination(Request $request)
    {
        return redirect()->route('admin.settings.destinations')->with('success', 'Destination added');
    }

    public function cities()
    {
        $cities = City::where('lang', 'en')->paginate(20);
        return view('admin.settings.cities', compact('cities'));
    }

    public function storeCity(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        City::create(['name' => $request->name, 'lang' => 'en', 'country_id' => $request->country_id ?? 0]);
        return redirect()->route('admin.settings.cities')->with('success', 'City added');
    }

    public function backup()
    {
        return view('admin.settings.backup');
    }

    public function fileManager(Request $request)
    {
        $baseDir = public_path('uploads/filemanager');
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        $currentDir = $request->query('dir', '');
        // Sanitize directory path
        $currentDir = str_replace(['..', '\\'], '', $currentDir);
        $currentDir = trim($currentDir, '/');

        $fullPath = $baseDir . ($currentDir ? '/' . $currentDir : '');
        if (!is_dir($fullPath)) {
            $currentDir = '';
            $fullPath = $baseDir;
        }

        $folders = [];
        $files = [];

        foreach (new \DirectoryIterator($fullPath) as $item) {
            if ($item->isDot()) continue;
            if ($item->isDir()) {
                $folders[] = $item->getFilename();
            } else {
                $files[] = $item->getFilename();
            }
        }

        sort($folders);
        sort($files);

        return view('admin.settings.file-manager', compact('folders', 'files', 'currentDir'));
    }

    public function fileManagerUpload(Request $request)
    {
        $baseDir = public_path('uploads/filemanager');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $targetDir = $baseDir . ($currentDir ? '/' . $currentDir : '');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $targetPath = $targetDir . '/' . $name;
            if (file_exists($targetPath)) {
                $name = time() . $name;
            }
            $file->move($targetDir, $name);
            return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('success', 'Success');
        }

        return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('error', 'No file selected');
    }

    public function fileManagerNewFolder(Request $request)
    {
        $baseDir = public_path('uploads/filemanager');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $folderName = preg_replace('/[^a-zA-Z0-9_ -]/', '', $request->input('folder_name', ''));
        $targetDir = $baseDir . ($currentDir ? '/' . $currentDir : '') . '/' . $folderName;

        if ($folderName && !is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
            return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('success', 'Success');
        }

        return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('error', 'Could not create folder');
    }

    public function fileManagerDeleteFile(Request $request)
    {
        $baseDir = public_path('uploads/filemanager');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $fileName = $request->input('file');
        $filePath = $baseDir . ($currentDir ? '/' . $currentDir : '') . '/' . $fileName;

        if ($fileName && file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
            return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('success', 'Success');
        }

        return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('error', 'File not found');
    }

    public function fileManagerDeleteFolder(Request $request)
    {
        $baseDir = public_path('uploads/filemanager');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $folderName = $request->input('folder');
        $folderPath = $baseDir . ($currentDir ? '/' . $currentDir : '') . '/' . $folderName;

        if ($folderName && is_dir($folderPath)) {
            // Recursive delete
            $this->removeDir($folderPath);
            return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('success', 'Success');
        }

        return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('error', 'Folder not found');
    }

    public function fileManagerRename(Request $request)
    {
        $baseDir = public_path('uploads/filemanager');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $oldName = $request->input('old_name');
        $newName = $request->input('new_name');
        $dirPath = $baseDir . ($currentDir ? '/' . $currentDir : '');

        if ($oldName && $newName && file_exists($dirPath . '/' . $oldName)) {
            rename($dirPath . '/' . $oldName, $dirPath . '/' . $newName);
            return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('success', 'Success');
        }

        return redirect()->route('admin.settings.file-manager', ['dir' => $currentDir])->with('error', 'Could not rename');
    }

    private function removeDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir . '/' . $object)) {
                        $this->removeDir($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function modules()
    {
        $modulesPath = storage_path('app/modules.json');
        
        if (!file_exists($modulesPath)) {
            $defaultModules = [
                ['id' => 'extra', 'name' => '..', 'active' => false],
                ['id' => 'media', 'name' => 'Media Galleries ??', 'active' => true],
                ['id' => 'tours', 'name' => 'Tours Booking', 'active' => true]
            ];
            file_put_contents($modulesPath, json_encode($defaultModules, JSON_PRETTY_PRINT));
        }

        $modules = json_decode(file_get_contents($modulesPath), true) ?: [];

        return view('admin.settings.modules', compact('modules'));
    }

    public function toggleModule($id)
    {
        $modulesPath = storage_path('app/modules.json');
        
        if (file_exists($modulesPath)) {
            $modules = json_decode(file_get_contents($modulesPath), true);
            foreach ($modules as &$mod) {
                if ($mod['id'] === $id) {
                    $mod['active'] = !$mod['active'];
                    break;
                }
            }
            file_put_contents($modulesPath, json_encode($modules, JSON_PRETTY_PRINT));
        }

        return redirect()->route('admin.settings.modules')->with('success', 'Module status updated successfully!');
    }

    public function translations()
    {
        return view('admin.settings.translations');
    }
}
