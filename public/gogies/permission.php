<?php

//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                              ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                           ///
 * @link		    www.cms.gogies.net                                    ///
 * @Version         1.0                                                   /// 
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
if (!defined('gogies'))
{
    print 'Direct script access is not allowed';
    exit;
}
////////////// install module ////////////
include_once $GOGIES['path'] . '/modules/tours/lang/' . $GOGIES['admin_lang'] . '/lang_data.php';
$mod_name = (!isset($GOGIES['mod_names']['tours'])) ? 'tours' : $GOGIES['mod_names']['tours'];
$GOGIES['permission']['tours'] = ['name' => $mod_name, 'module' => 'tours', 'sections' => [
        'index' => ['name' => $tours_lang['booking'], 'module' => 'tours', 'sections' => [
                'add_booking' => ['name' => $lang['add_new'], 'module' => 'tours'],
                'edit_booking' => ['name' => $lang['edit'], 'module' => 'tours'],
            ]],
        'quotations' => ['name' => $lang['quotation'], 'module' => 'tours', 'sections' => [
                'quotation' => ['name' => $lang['manage_quotations'], 'module' => 'tours', 'sections' => [
                        'add_quotation' => ['name' => $lang['add_new'], 'module' => 'tours'],
                        'edit_quotation' => ['name' => $lang['edit'], 'module' => 'tours'],
                        'send_quotation' => ['name' => $lang['send'], 'module' => 'tours'],
                        'delete_quotation' => ['name' => $lang['delete'], 'module' => 'tours'],
                        'copy_quotation' => ['name' => $lang['copy'], 'module' => 'tours'],
                        'quotation_fast_access' => ['name' => $lang['expenses_fast_access'], 'module' => 'tours'],
                    //  'quotation_expenses_cost' => ['name' => $lang['view'] . '->' . $lang['expenses'] . '->' . $lang['cost'], 'module' => 'tours'],
                    //  'quotation_expenses_venders' => ['name' => $lang['view'] . '->' . $lang['expenses'] . '->' . $lang['venders'], 'module' => 'tours']
                    ]],
                'quotation_pricing' => ['name' => $lang['pricing'], 'module' => 'tours'],
                'canned_days' => ['name' => $tours_lang['canned_days'], 'module' => 'tours', 'sections' => [
                        'add_canned_day' => ['name' => $lang['add_new'], 'module' => 'tours'],
                        'edit_canned_day' => ['name' => $lang['edit'], 'module' => 'tours'],
                        'delete_canned_day' => ['name' => $lang['delete'], 'module' => 'tours']
                    ]],
                'quotation_email_template' => ['name' => $lang['emails_templates'], 'module' => 'tours']
            ]],
        'manage_tours' => ['name' => $tours_lang['tours'], 'module' => 'tours', 'sections' => [
                'add_tour' => ['name' => $lang['add_new'], 'module' => 'tours'],
                'edit_tour' => ['name' => $lang['edit'], 'module' => 'tours'],
                'delete_tour' => ['name' => $lang['delete'], 'module' => 'tours'],
                 'tour_guaranteed_departure' => ['name' => $tours_lang['guaranteed_departure'], 'module' => 'tours']
            ]],
        'tour_types' => ['name' => $lang['types'], 'module' => 'tours'],
        'tour_categories' => ['name' => $lang['categories'], 'module' => 'tours'],
        'tour_inclusions' => ['name' => $tours_lang['inclusions'], 'module' => 'tours'],
        'tec_details' => ['name' => $tours_lang['technichal_details'], 'module' => 'tours'],
        'seasons' => ['name' => $lang['seasons'], 'module' => 'tours'],
        'settings' => ['name' => $lang['settings'], 'module' => 'tours']
        ]];




