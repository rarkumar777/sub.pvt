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
$latest_tours_sql = $G['db']->q('SELECT t.id ,t.start_country,t.image,t.days,t.rating,t.f_start,t.f_finish,t.sp_start,t.sp_finish,t.start_city,c.title,t.min_price,t.max_price ,t.tec_details,c.desc,c.meta_desc,c.url,city.name as city FROM ' . $GOGIES['dbprf'] . 'tours t LEFT JOIN ' . $GOGIES['dbprf'] . 'tours_contents c
ON t.id=c.tour_id LEFT JOIN ' . $GOGIES['dbprf'] . 'cities city ON city.lang_id=t.start_city WHERE  c.lang=\'' . $GOGIES['lang'] . '\' AND  t.status=\'1\' AND city.lang=\'' . $GOGIES['lang'] . '\'  ORDER BY t.id DESC  LIMIT ' . $GOGIES['tours_settings']['latest_tours_number']);
include $GOGIES['theme_path'] . '/modules/tours/latest_tours_block.php';
?>