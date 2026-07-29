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
include_once $GOGIES['path'].'/modules/tours/gogies/init.php';
/////////tour
$NewLocation=FALSE;
if (isset($Q[2])&&  isset($Q[3]) && !empty($Q[3]))
{
$tsql=$G['db']->q(
'SELECT a.tour_id as tour_id ,b.start_country as country , c.url as url FROM '.$GOGIES['dbprf'].'tours_contents as a LEFT JOIN '.$GOGIES['dbprf'].'tours b ON b.id=a.tour_id  LEFT JOIN '.$GOGIES['dbprf'].'tours_contents  c ON a.tour_id = c.tour_id and c.lang=\''.$G['security']->db_escape($_GET['set_lang']).'\' WHERE a.url=\''.$G['security']->db_escape($Q[3]).'\' AND a.lang=\''.$Q[0].'\' LIMIT 1'
);
$sql=$G['db']->q('SELECT name,lang_id FROM '.$GOGIES['dbprf'].'countries WHERE lang=\''.$G['security']->db_escape($_GET['set_lang']).'\' ORDER BY name ASC');

while ($R=mysqli_fetch_array($sql)){

$GOGIES['countries'][$R['lang_id']]=strtolower($R['name']);
}
$data=mysqli_fetch_array($tsql);
$NewLocation=$GOGIES['url'].'/'.@$_GET['set_lang'].'/tours/'.$GOGIES['countries'][$data['country']].'/'.$data['url'].'/';
}
elseif(isset($Q[2])&& in_array($Q[2],$GOGIES['countries']) && empty($Q[3] )){

$sql=$G['db']->q('SELECT a.lang_id ,b.name FROM '.$GOGIES['dbprf'].'countries  a LEFT JOIN '.
$GOGIES['dbprf'].'countries b ON a.lang_id=b.lang_id WHERE a.name=\''.$Q[2].'\' AND
 b.lang=\''.$G['security']->db_escape($_GET['set_lang']).'\'  LIMIT 1');

$R=mysqli_fetch_array($sql);
$NewLocation=$GOGIES['url'].'/'.$_GET['set_lang'].'/tours/'.$R['name'].'/';

}

$G['common']->switch_lang($NewLocation); exit;   
