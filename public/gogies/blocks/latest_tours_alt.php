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
$latest_tours_sql = $G['db']->q('SELECT t.id ,t.start_country,t.image ,t.rating,t.days,t.nights,c.title,t.min_price,t.max_price ,c.desc,c.meta_desc,c.url FROM ' . $GOGIES['dbprf'] . 'tours t LEFT JOIN ' . $GOGIES['dbprf'] . 'tours_contents c
ON t.id=c.tour_id WHERE  c.lang=\'' . $GOGIES['lang'] . '\' AND  t.status=\'1\'  ORDER BY rand() DESC  LIMIT ' . $GOGIES['tours_settings']['latest_tours_number']);
include $GOGIES['theme_path'] . '/modules/tours/latest_tours_block_alt.php';
?>