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
if (!defined('gogies'))
{
    print 'Direct script access is not allowed';
    exit;
}
include_once $GOGIES['path'] . '/config/modules/tours_settings.php';
include_once $GOGIES['path'] . '/modules/tours/lang/' . $GOGIES['lang'] . '/lang_data.php';
include_once $GOGIES['path'] . '/modules/tours/gogies/tours_tec_cache.php';
include_once $GOGIES['path'] . '/modules/tours/gogies/init.php';
$tours_today = date('Y') . '-' . date('m') . '-' . date('d');
$sp_tours_sql = $G['db']->q('SELECT t.id ,t.min_price,t.max_price,t.image,t.start_country ,t.rating,c.title ,c.desc ,c.url FROM ' . $GOGIES['dbprf'] . 'tours t LEFT JOIN ' . $GOGIES['dbprf'] . 'tours_contents c
ON t.id=c.tour_id WHERE  c.lang=\'' . $GOGIES['lang'] . '\' AND  t.status=\'1\' and
date(\'' . $tours_today . '\') between date(`sp_start`) and date(sp_finish)
   ORDER BY t.id DESC  LIMIT ' . $GOGIES['tours_settings']['latest_tours_number']);
//$tours_slides_limit=mysqli_num_rows($sp_tours_sql);

include $GOGIES['theme_path'] . '/modules/tours/special_offer_tours.php';
?>