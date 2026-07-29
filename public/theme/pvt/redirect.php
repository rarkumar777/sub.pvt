<?php 
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                              ///
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
?>
<!DOCTYPE html>
<html>
  <head>
 <meta charset="utf-8">
 
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">


<link rel="icon" href="<?=$GOGIES['url']?>/<?=$GOGIES['company_fav_icon']?>?v=1" />
<title><?=$lang['redirecting']?>...</title>
<link href="<?=$GOGIES['url']?>/gogies3d/css/gogies.css" rel="stylesheet">
<link href="<?=$GOGIES['theme_url']?>/css/front.css" rel="stylesheet">
<meta name="generator" content="Gogies-web-solutions-v-1.0" /> <!-- if you change this you will not be able to use automatic update -->

<meta http-equiv="refresh" content="<?=$rTime?>; url=<?=$rUrl?>" />
</head>
<body style="background-color:#444;">

<div class="redirect d-pad">

<div class="spinner"></div>
<div><strong><?=$lang['redirecting']?>...</strong></div>

<strong><?=$rMsg?></strong>
<?php $G['common']->get_debug();?>
</div>
<?php //$G['common']->get_debug()?>
</body></html>