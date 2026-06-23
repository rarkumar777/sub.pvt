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


$sql=$G['db']->q('SELECT t.status , c.lang,c.url,cc.name as start_country FROM '.$GOGIES['dbprf'].'tours_contents  c LEFT JOIN  '.$GOGIES['dbprf'].'tours t  ON c.tour_id=t.id LEFT JOIN '.$GOGIES['dbprf'].'countries cc ON t.start_country=cc.lang_id  WHERE t.status=\'1\' and cc.lang=c.lang');
$mod_data=NULL;

while ($r=mysqli_fetch_array($sql)){
	$tours_sql_data[]='(\''.$GOGIES['url'].'/'.$r['lang'].'/tours/'.strtolower($r['start_country']).'/'.$r['url'].'/\',\'tours\',\''.$timestamp.'\')';
	$mod_data.='<url>
<loc>'.$GOGIES['url'].'/'.$r['lang'].'/tours/'.strtolower($r['start_country']).'/'.$r['url'].'/</loc>
<lastmod>'.$date.'</lastmod>
<priority>0.9</priority>
</url>';
}
   $G['db']->q('INSERT INTO '.$GOGIES['dbprf'].'seo (`url`,`module`,`created`)VALUE '.implode(',',$tours_sql_data) .'ON DUPLICATE KEY UPDATE created = \''.time().'\' ');
