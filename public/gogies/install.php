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
////////////// install module ////////////
if (!file_exists($GOGIES['path'].'/config/modules/tours_settings.php'))
   {
   $data='<?php if (!defined(\'gogies\')){ exit;} 
   $GOGIES[\'tours_settings\'][\'tax\']=\'0\'; $GOGIES[\'tours_settings\'][\'latest_tours_number\']=\'5\';
   $GOGIES[\'tours_settings\'][\'deposit\']=\'10\';
   ?>'; 
   $action1=file_put_contents($GOGIES['path'].'/config/modules/tours_settings.php',$data);
   }
$GOGIES['task_success']=TRUE;