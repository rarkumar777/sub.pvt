@php
    $footerPath = public_path('config/footer/' . $lang . '.php');
    $footerColumns = [];
    if (file_exists($footerPath)) {
        $html = file_get_contents($footerPath);
        if ($html) {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            // Load HTML with UTF-8 encoding prefix to preserve Arabic/localized text
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);
            
            // Find all divs representing columns
            $divs = $xpath->query("//div[contains(@class, 'md-6')]");
            foreach ($divs as $div) {
                // Check if this is the Secure Payments column (contains h3)
                $h3 = $xpath->query(".//h3", $div);
                if ($h3->length > 0) {
                    continue;
                }
                
                // Get Title
                $title = '';
                $pre = $xpath->query(".//pre", $div);
                if ($pre->length > 0) {
                    $title = trim(strip_tags($pre->item(0)->textContent));
                } else {
                    $strong = $xpath->query(".//strong", $div);
                    if ($strong->length > 0) {
                        $title = trim(strip_tags($strong->item(0)->textContent));
                    }
                }
                
                // Clean title whitespace
                $title = preg_replace('/\s+/', ' ', $title);
                $title = trim($title);
                
                // Get Links
                $links = [];
                $aElements = $xpath->query(".//ul/li/a | .//ul/li/span/a | .//ul/li/em/a | .//ul/li/span/em/a", $div);
                foreach ($aElements as $a) {
                    $href = $a->getAttribute('href');
                    $text = trim(strip_tags($a->textContent));
                    $titleAttr = $a->getAttribute('title');
                    
                    // Normalize href
                    if ($href && strpos($href, 'http') !== 0 && strpos($href, '/') !== 0) {
                        $href = '/' . $href;
                    }
                    
                    if ($text) {
                        $links[] = [
                            'href' => $href,
                            'text' => $text,
                            'title' => $titleAttr
                        ];
                    }
                }
                
                if ($title && count($links) > 0) {
                    $footerColumns[] = [
                        'title' => $title,
                        'links' => $links
                    ];
                }
            }
        }
    }

    // Fallback columns if file doesn't exist, is empty, or parsing failed
    if (empty($footerColumns)) {
        $footerColumns = [
            [
                'title' => 'About Us',
                'links' => [
                    ['href' => '/' . $lang . '/about-us/', 'text' => 'About Us', 'title' => 'About Us'],
                    ['href' => '/' . $lang . '/company-profile/', 'text' => 'Company Profile', 'title' => 'Company Profile'],
                    ['href' => '/' . $lang . '/our-team/', 'text' => 'Our Team', 'title' => 'Our Team'],
                    ['href' => '/' . $lang . '/privacy-policy-for-pv-travels/', 'text' => 'Privacy Policy', 'title' => 'Privacy Policy'],
                    ['href' => '/' . $lang . '/terms-and-conditions/', 'text' => 'Terms & Conditions', 'title' => 'Terms & Conditions'],
                    ['href' => '/' . $lang . '/jobs-in-jordan/', 'text' => 'Jobs in Jordan', 'title' => 'Jobs in Jordan'],
                    ['href' => '/' . $lang . '/volunteer-abroad-in-jordan/', 'text' => 'Volunteer Abroad', 'title' => 'Volunteer Abroad'],
                    ['href' => '/' . $lang . '/contact-us/', 'text' => 'Contact Us', 'title' => 'Contact Us']
                ]
            ],
            [
                'title' => 'Tours and Activities',
                'links' => [
                    ['href' => '/' . $lang . '/where-to-go-in-jordan/', 'text' => 'Where to Go', 'title' => 'Where to Go'],
                    ['href' => '/' . $lang . '/team-building-activities/', 'text' => 'Team Building', 'title' => 'Team Building'],
                    ['href' => '/' . $lang . '/business-trip/', 'text' => 'Business Trip', 'title' => 'Business Trip'],
                    ['href' => '/' . $lang . '/tours-in-jordan/', 'text' => 'Tours in Jordan', 'title' => 'Tours in Jordan'],
                    ['href' => '/' . $lang . '/tours-in-oman/', 'text' => 'Tours in Oman', 'title' => 'Tours in Oman'],
                    ['href' => '/' . $lang . '/combined-tours/', 'text' => 'Combined Tours', 'title' => 'Combined Tours'],
                    ['href' => '/' . $lang . '/tips-for-travellers/', 'text' => 'Tips for Travellers', 'title' => 'Tips for Travellers'],
                    ['href' => '/' . $lang . '/jordan-pass/', 'text' => 'Jordan Pass', 'title' => 'Jordan Pass']
                ]
            ],
            [
                'title' => 'Information',
                'links' => [
                    ['href' => '/' . $lang . '/about-jordan/', 'text' => 'Jordan', 'title' => 'Jordan'],
                    ['href' => '/' . $lang . '/visas-to-jordan/', 'text' => 'Visas to Jordan', 'title' => 'Visas to Jordan'],
                    ['href' => '/' . $lang . '/best-time-to-visit-jordan/', 'text' => 'Visit Jordan', 'title' => 'Visit Jordan'],
                    ['href' => '/' . $lang . '/transportation-in-jordan/', 'text' => 'Transportation', 'title' => 'Transportation'],
                    ['href' => '/' . $lang . '/hotels-in-jordan/', 'text' => 'Hotels in Jordan', 'title' => 'Hotels in Jordan'],
                    ['href' => '/' . $lang . '/museums-in-jordan/', 'text' => 'Museums in Jordan', 'title' => 'Museums in Jordan'],
                    ['href' => '/' . $lang . '/airports-in-jordan/', 'text' => 'Airports', 'title' => 'Airports'],
                    ['href' => '/' . $lang . '/airlines-to-jordan/', 'text' => 'Airlines', 'title' => 'Airlines'],
                    ['href' => '/' . $lang . '/natural-reserves-in-jordan/', 'text' => 'Natural Reserves', 'title' => 'Natural Reserves'],
                    ['href' => '/' . $lang . '/currency-converter/', 'text' => 'Currency Converter', 'title' => 'Currency Converter']
                ]
            ],
            [
                'title' => 'My Account',
                'links' => [
                    ['href' => '/' . $lang . '/users/account/edit-account/', 'text' => 'My Account', 'title' => 'My Account'],
                    ['href' => '/' . $lang . '/users/account/my-bookings/', 'text' => 'Booking History', 'title' => 'Booking History']
                ]
            ]
        ];
    }
@endphp

<!-- Frontend Footer (Dark Navy Design) -->
<footer class="mt-20 bg-[#0f1729] text-slate-300 font-sans">
    <div class="max-w-7xl mx-auto px-8 py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-12">

            <!-- Column 1: Brand -->
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset((isset($profile['logo']) && $profile['logo']) ? 'uploads/' . $profile['logo'] : 'Pvtnew1.png') }}" alt="Pv Travels" class="h-10 object-contain" onerror="this.style.display='none'">
                </div>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Your trusted partner for unforgettable Jordan travel experiences since 2009.
                </p>
                <div class="flex items-center gap-2 pt-1">
                    <a href="https://www.facebook.com/pvtgo" target="_blank" class="w-9 h-9 rounded-lg bg-[#1a2744] flex items-center justify-center text-slate-400 hover:text-[#1877f2] hover:bg-[#1877f2]/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://twitter.com/pvtgo" target="_blank" class="w-9 h-9 rounded-lg bg-[#1a2744] flex items-center justify-center text-slate-400 hover:text-[#1da1f2] hover:bg-[#1da1f2]/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/pvtgo2/" target="_blank" class="w-9 h-9 rounded-lg bg-[#1a2744] flex items-center justify-center text-slate-400 hover:text-[#e4405f] hover:bg-[#e4405f]/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="https://www.youtube.com/user/PetraVoyageTours" target="_blank" class="w-9 h-9 rounded-lg bg-[#1a2744] flex items-center justify-center text-slate-400 hover:text-[#ff0000] hover:bg-[#ff0000]/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/in/pvtjor/" target="_blank" class="w-9 h-9 rounded-lg bg-[#1a2744] flex items-center justify-center text-slate-400 hover:text-[#0077b5] hover:bg-[#0077b5]/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>

                <!-- Secure Payments -->
                <div class="pt-5 border-t border-slate-800">
                    <h5 class="text-white text-xs font-bold uppercase tracking-wider mb-3">Secure Payments</h5>
                    <div class="flex items-center gap-2">
                        <!-- Visa Logo -->
                        <div class="bg-white rounded px-2 py-0.5 h-8 flex items-center justify-center">
                            <svg class="h-6 w-auto" viewBox="0 0 780 500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Visa">
                                <path d="M489.823 143.111C442.988 143.111 401.134 167.393 401.134 212.256C401.134 263.706 475.364 267.259 475.364 293.106C475.364 303.989 462.895 313.731 441.6 313.731C411.377 313.731 388.789 300.119 388.789 300.119L379.123 345.391C379.123 345.391 405.145 356.889 439.692 356.889C490.898 356.889 531.19 331.415 531.19 285.784C531.19 231.419 456.652 227.971 456.652 203.981C456.652 195.455 466.887 186.114 488.122 186.114C512.081 186.114 531.628 196.014 531.628 196.014L541.087 152.289C541.087 152.289 519.818 143.111 489.823 143.111ZM61.3294 146.411L60.1953 153.011C60.1953 153.011 79.8988 156.618 97.645 163.814C120.495 172.064 122.122 176.868 125.971 191.786L167.905 353.486H224.118L310.719 146.411H254.635L198.989 287.202L176.282 167.861C174.199 154.203 163.651 146.411 150.74 146.411H61.3294ZM333.271 146.411L289.275 353.486H342.756L386.598 146.411H333.271ZM631.554 146.411C618.658 146.411 611.825 153.318 606.811 165.386L528.458 353.486H584.542L595.393 322.136H663.72L670.318 353.486H719.805L676.633 146.411H631.554ZM638.848 202.356L655.473 280.061H610.935L638.848 202.356Z" fill="#1434CB"/>
                            </svg>
                        </div>
                        <!-- Mastercard Logo -->
                        <div class="bg-white rounded px-2 py-0.5 h-8 flex items-center justify-center">
                            <svg class="h-6 w-auto" viewBox="0 0 780 500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Mastercard">
                                <path d="M465.738 113.525H313.812V386.475H465.738V113.525Z" fill="#FF5A00"/>
                                <path d="M323.926 250C323.926 194.545 349.996 145.326 390 113.525C360.559 90.3769 323.42 76.3867 282.91 76.3867C186.945 76.3867 109.297 154.035 109.297 250C109.297 345.965 186.945 423.614 282.91 423.614C323.42 423.614 360.559 409.623 390 386.475C349.94 355.123 323.926 305.455 323.926 250Z" fill="#EB001B"/>
                                <path d="M670.711 250C670.711 345.965 593.062 423.614 497.098 423.614C456.588 423.614 419.449 409.623 390.008 386.475C430.518 354.618 456.082 305.455 456.082 250C456.082 194.545 430.012 145.326 390.008 113.525C419.393 90.3769 456.532 76.3867 497.041 76.3867C593.062 76.3867 670.711 154.541 670.711 250Z" fill="#F79E1B"/>
                            </svg>
                        </div>
                        <!-- American Express Logo -->
                        <div class="bg-white rounded px-2 py-0.5 h-8 flex items-center justify-center">
                            <svg class="h-6 w-auto" viewBox="0 0 780 500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="American Express">
                                <path d="m575.61 145.11l-15.092 35.039h30.266l-15.174-35.039zm-174.15 21.713c2.845-1.422 4.52-4.515 4.52-8.356 0-3.764-1.76-6.49-4.604-7.771-2.591-1.42-6.577-1.584-10.399-1.584h-27v19.523h26.638c4.266 1e-3 7.831-0.059 10.845-1.812zm-345.97-21.713l-14.921 35.039h29.932l-15.011-35.039zm694.7 224.47h-42.344v-18.852h42.173c4.181 0 7.109-0.525 8.872-2.178 1.667-1.473 2.609-3.555 2.592-5.732 0-2.562-1.062-4.596-2.68-5.813-1.588-1.342-3.907-1.953-7.726-1.953-20.588-0.67-46.273 0.609-46.273-27.211 0-12.75 8.451-26.172 31.461-26.172h43.677v-17.492h-40.58c-12.246 0-21.144 2.81-27.443 7.181v-7.181h-60.022c-9.597 0-20.863 2.279-26.191 7.181v-7.181h-107.19v7.181c-8.529-5.897-22.925-7.181-29.565-7.181h-70.702v7.181c-6.747-6.262-21.758-7.181-30.902-7.181h-79.127l-18.104 18.775-16.959-18.775h-118.2v122.68h115.97l18.655-19.076 17.575 19.076 71.484 0.06v-28.859h7.03c9.484 0.146 20.67-0.223 30.542-4.311v33.106h58.962v-31.976h2.844c3.628 0 3.988 0.146 3.988 3.621v28.348h179.12c11.372 0 23.26-2.786 29.841-7.853v7.853h56.817c11.822 0 23.369-1.588 32.154-5.653v-22.853c-5.324 7.462-15.707 11.245-29.751 11.245zm-363.58-28.967h-27.36v29.488h-42.618l-27-29.102-28.058 29.102h-86.854v-87.914h88.19l26.976 28.818 27.89-28.818h70.064c17.401 0 36.952 4.617 36.952 28.963 0 24.422-19.016 29.463-38.182 29.463zm131.56-3.986c3.097 4.291 3.544 8.297 3.634 16.047v17.428h-22.016v-10.998c0-5.289 0.533-13.121-3.544-17.209-3.2-3.148-8.086-3.9-16.088-3.9h-23.432v32.107h-22.031v-87.914h50.62c11.105 0 19.188 0.473 26.384 4.148 6.92 4.006 11.275 9.494 11.275 19.523-2e-3 14.031-9.769 21.189-15.541 23.389 4.878 1.725 8.866 4.818 10.739 7.379zm90.575-36.258h-51.346v15.982h50.091v17.938h-50.091v17.492l51.346 0.078v18.242h-73.182v-87.914h73.182v18.182zm56.344 69.731h-42.705v-18.852h42.535c4.16 0 7.109-0.527 8.957-2.178 1.507-1.359 2.591-3.336 2.591-5.73 0-2.564-1.174-4.598-2.676-5.818-1.678-1.34-3.993-1.947-7.809-1.947-20.506-0.674-46.186 0.605-46.186-27.213 0-12.752 8.363-26.174 31.35-26.174h43.96v18.709h-40.225c-3.987 0-6.579 0.146-8.783 1.592-2.405 1.424-3.295 3.535-3.295 6.322 0 3.316 2.04 5.574 4.797 6.549 2.314 0.771 4.797 0.996 8.533 0.996l11.805 0.309c11.899 0.273 20.073 2.25 25.04 7.068 4.266 4.232 6.559 9.578 6.559 18.625-2e-3 18.913-12.335 27.742-34.448 27.742zm-170.06-68.313c-2.649-1.508-6.559-1.588-10.461-1.588h-27.001v19.744h26.64c4.265 0 7.892-0.145 10.822-1.812 2.842-1.646 4.543-4.678 4.543-8.438s-1.701-6.482-4.543-7.906zm244.99-1.59c-3.988 0-6.641 0.145-8.873 1.588-2.314 1.426-3.202 3.537-3.202 6.326 0 3.314 1.953 5.572 4.794 6.549 2.315 0.771 4.796 0.996 8.448 0.996l11.887 0.303c11.99 0.285 19.998 2.262 24.879 7.08 0.889 0.668 1.423 1.42 2.034 2.174v-25.014h-39.965l-2e-3 -2e-3zm-352.65 0h-28.59v22.391h28.336c8.424 0 13.663-4.006 13.667-11.611-4e-3 -7.688-5.497-10.78-13.413-10.78zm-190.81 0v15.984h48.136v17.938h-48.136v17.49h53.909l25.047-25.791-23.983-25.621h-54.973zm140.77 61.479v-70.482l-33.664 34.674 33.664 35.808zm-138.93-141.15v15.148h183.19l-0.085-32.046h3.545c2.483 0.083 3.205 0.302 3.205 4.229v27.818h94.748v-7.461c7.642 3.924 19.527 7.461 35.168 7.461h39.86l8.531-19.522h18.913l8.342 19.522h76.811v-18.544l11.629 18.543h61.555v-122.58h-60.915v14.477l-8.53-14.477h-62.507v14.477l-7.833-14.477h-84.434c-14.135 0-26.555 1.89-36.591 7.158v-7.158h-58.268v7.158c-6.387-5.43-15.089-7.158-24.762-7.158h-212.87l-14.282 31.662-14.668-31.662h-67.047v14.477l-7.367-14.477h-57.18l-26.553 58.284v46.621l39.264-87.894h32.579l37.29 83.217v-83.217h35.789l28.695 59.625 26.362-59.625h36.507v87.894h-22.475l-0.082-68.837-31.796 68.837h-19.252l-31.877-68.898v68.898h-44.6l-8.425-19.605h-45.654l-8.512 19.605h-23.814v17.682h37.466l8.447-19.523h18.914l8.425 19.523h73.713v-14.927l6.579 14.989h38.266l6.58-15.214zm288.67-80.176c7.085-7.015 18.188-10.25 33.298-10.25h21.227v18.833h-20.782c-7.998 0-12.521 1.14-16.871 5.208-3.74 3.7-6.304 10.696-6.304 19.908 0 9.417 1.955 16.206 6.028 20.641 3.376 3.478 9.513 4.533 15.283 4.533h9.851l30.902-69.12h32.853l37.124 83.134v-83.133h33.386l38.543 61.213v-61.213h22.46v87.891h-31.072l-41.562-65.968v65.968h-44.656l-8.532-19.605h-45.55l-8.278 19.605h-25.66c-10.657 0-24.151-2.258-31.793-9.722-7.707-7.462-11.713-17.571-11.713-33.553-4e-3 -13.037 2.389-24.953 11.818-34.37zm-45.101-10.249h22.372v87.894h-22.372v-87.894zm-100.87 0h50.432c11.203 0 19.464 0.285 26.553 4.21 6.936 3.926 11.095 9.658 11.095 19.46 0 14.015-9.763 21.254-15.448 23.429 4.796 1.75 8.896 4.841 10.849 7.401 3.096 4.372 3.629 8.277 3.629 16.126v17.267h-22.115l-0.083-11.084c0-5.29 0.528-12.896-3.461-17.122-3.2-3.09-8.088-3.763-15.983-3.763h-23.538v31.97h-21.927l-3e-3 -87.894zm-88.393 0h73.249v18.303h-51.32v15.843h50.088v18.017h-50.088v17.553h51.32v18.177h-73.249v-87.893z" fill="#2557D6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columns 2-5: Dynamic link lists -->
            @foreach($footerColumns as $col)
            <div>
                <h4 class="text-white font-bold text-base mb-6">{{ $col['title'] }}</h4>
                <ul class="space-y-3">
                    @foreach($col['links'] as $link)
                    <li class="flex items-center gap-2.5">
                        <svg class="w-3 h-3 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        <a href="{{ $link['href'] }}" title="{{ $link['title'] }}" class="text-sm text-slate-400 hover:text-white transition-colors">{{ $link['text'] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach

        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-[#1a2744]">
        <div class="max-w-7xl mx-auto px-8 py-5 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} PV Travels. All rights reserved.
            </p>
            <div class="flex items-center gap-6">
                <a href="/{{ $lang }}/privacy/" class="text-slate-500 text-sm hover:text-white transition-colors">Privacy</a>
                <a href="/{{ $lang }}/terms/" class="text-slate-500 text-sm hover:text-white transition-colors">Terms</a>
            </div>
        </div>
    </div>
</footer>

<script type="application/ld+json">
{
    "@@context": "http://schema.org",
    "@@id": "{{ url('/') }}/#localbusiness",
    "@@type": "LocalBusiness",
    "url": "{{ url('/') }}/",
    "name": "Pv Travels",
    "logo": "{{ asset((isset($profile['logo']) && $profile['logo']) ? 'uploads/' . $profile['logo'] : 'Pvtnew1.png') }}",
    "description": "Looking for a family holiday? Jordan is one of the ideal destination or family vacations. Pv Travels provide best vacation packages for Jordan at affordable prices.",
    "telephone": "+96232159933",
    "email": "info@@pvt.jo",
    "address": {
        "@@type": "PostalAddress",
        "streetAddress": "P.O.Box 43",
        "addressCountry": "JO",
        "addressLocality": "Petra",
        "postalCode": "71810"
    },
    "image": "{{ asset((isset($profile['logo']) && $profile['logo']) ? 'uploads/' . $profile['logo'] : 'Pvtnew1.png') }}",
    "openingHoursSpecification": {
        "@@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
        "opens": "10:00",
        "closes": "22:00"
    },
    "priceRange": "$$$",
    "sameAs": [
        "https://www.linkedin.com/in/pvtjor/",
        "https://www.facebook.com/pvtgo",
        "https://twitter.com/pvtgo",
        "https://www.tripadvisor.com/Attraction_Review-g318895-d6027673-Reviews-Petra_Voyage_Tours_Day_Tour-Petra_Wadi_Musa_Ma_an_Governorate.html",
        "https://www.instagram.com/pvtgo2/",
        "https://www.youtube.com/user/PetraVoyageTours"
    ]
}
</script>
