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
if (file_exists($GOGIES['path'].'/config/modules/tours_categories'.$GOGIES['lang'].'.php'))
   {
	   include_once $GOGIES['path'].'/config/modules/tours_categories'.$GOGIES['lang'].'.php';
   }
   else
   {
$GOGIES['tours_categories_sql']=$G['db']->q('SELECT * FROM '.$GOGIES['dbprf'].'tours_categories WHERE `lang`=\''.$GOGIES['lang'].'\' ORDER BY name ASC');
$GOGIES['save_date']='<?php ';
while ($GOGIES['tours_categories_res']=mysqli_fetch_array($GOGIES['tours_categories_sql']))
{
$GOGIES['tours']['categories'][$GOGIES['tours_categories_res']['lang_id']]=$GOGIES['tours_categories_res']['name'];
$GOGIES['save_date'].=
' $GOGIES[\'tours\'][\'categories\']['.$GOGIES['tours_categories_res']['lang_id'].']=\''.$GOGIES['tours_categories_res']['name'].'\'; ';
}
$GOGIES['save_date'].=' ?>';
file_put_contents($GOGIES['path'].'/config/modules/tours_categories'.$GOGIES['lang'].'.php',$GOGIES['save_date']);
   }