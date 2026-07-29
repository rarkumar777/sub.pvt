<?php 
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                    ///
 * @link		    www.cms.gogies.net                             ///
 * @Version         1.0                                                   /// 
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
if (!defined('gogies')){print 'Direct script access is not allowed'; exit;}
include_once $GOGIES['path'].'/config/modules/tours_settings.php';
include_once $GOGIES['path'].'/modules/tours/lang/'.$GOGIES['lang'].'/lang_data.php';
include_once $GOGIES['path'].'/modules/tours/gogies/init.php';
////categs///////
include_once $GOGIES['path'].'/modules/tours/gogies/tours_categories_cache.php';
$GOGIES['tours']['categories'][0]=$lang['all_categories'];
ksort($GOGIES['tours']['categories']);
////types///////
include_once $GOGIES['path'].'/modules/tours/gogies/tours_types_cache.php';
$GOGIES['tours']['types'][0]=$lang['all_types'];
ksort($GOGIES['tours']['types']);


/////// search countries//////
$country_sql=$G['db']->q('SELECT DISTINCT start_country,finish_country FROM '.$GOGIES['dbprf'].'tours');
$GOGIES['tours_countries'][0]=$lang['all_countries'];
while ($r=mysqli_fetch_array($country_sql)){
	if (isset($GOGIES['countries'][$r['start_country']]))
	   {
		   $GOGIES['tours_countries'][$r['start_country']]=$GOGIES['countries'][$r['start_country']];
		}
			if (isset($GOGIES['countries'][$r['finish_country']]))
	   {
		   $GOGIES['tours_countries'][$r['finish_country']]=$GOGIES['countries'][$r['finish_country']];
		}
	}


include $GOGIES['theme_path'].'/modules/tours/tours_search_block.php';
?>