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
if (!defined('gogies')){print 'Direct script access is not allowed'; exit;}?>
<div class="pvt-orange d-pad-t d-pad-b pad-l pad-r table full-width" style="z-index:999">

 
 <span class="pull-right">
     <?php if (!$GOGIES['is_user']){?>
<a class="small" href="<?=$GOGIES['seo_url']?>users/login/"><i class="fa-power-off"></i><?=$lang['login']?></a> <span class="pad-l pad-r">|</span> 
<a class="small" href="<?=$GOGIES['seo_url']?>users/register/"><i class="fa-edit"></i> <?=$lang['create_new_account']?></a>
<?php }
else{?>
<a class="small" href="<?=$GOGIES['seo_url']?>users/account/edit-account/"><i class="fa-edit"></i> <?=$lang['my_account']?></a> <span class="pad-l pad-r">|</span>
<a class="small" href="<?=$GOGIES['seo_url']?>?LogOut"><i class="fa-power-off"></i> <?=$lang['logout']?></a>
<?php
}?>
</span>
<?php if (count($GOGIES['active_langs'])>1){?>
<span class="pad-l pad-r pull-right">|</span>
<span class="dropdown pull-right">
  <a href="#" class="text-uppercase small h-pad" id="toggler"><img src="<?=$GOGIES['url'].'/lang/'.$GOGIES['lang'].'/'.$GOGIES['lang_flags'][$GOGIES['lang']]?>" width="18" height="12" class="h-gap-t" title="<?=$GOGIES['lang']?>"/></a>
				<ul class="right">

<?php foreach ($GOGIES['active_langs'] as $k)
{if ($k!=$GOGIES['lang']){

?><li><a class="small text-uppercase "  href="#" 
  onclick="window.location='<?= $_SERVER['REQUEST_URI'] . $GOGIES['url_swtich_char'] . 'set_lang=' . $k ?>'; return false;">
<img src="<?=$GOGIES['url'].'/lang/'.$k.'/'.$GOGIES['lang_flags'][$k]?>" width="18" height="12" title="<?=$k?>"/> 
</a></li>
<?php }}?>

         </ul>
				</span>

<?php }?>


 <?php if (count($GOGIES['currencies'])>1){?>
<span class="pad-l pad-r pull-right">|</span>
<span class="dropdown pull-right">
 <a class="text-uppercase  small h-pad" href="#" id="toggler"><?=$GOGIES['currencies'][$GOGIES['currency']]['name']?></a>

 <ul class="right" >

<?php

 foreach ($GOGIES['currencies'] as $k=>$v)
         {
		 if ($k!=$GOGIES['currency'])
		    {
              ?><li ><a class="small text-uppercase" href="#" onclick="window.location='<?=$_SERVER['REQUEST_URI'].$GOGIES['url_swtich_char'].'set-currency='.$k;?>'; return false;"><?=$v['name']?></a></li><?php
			  }
		}?>

         </ul>
         
 </span>

 <?php }?>

</div>


<nav class="pvt navbar absolute"  id="fixed-nav" >
<div class="nav-toggle"></div>
<span class="brand-small"><strong><img src="<?=$GOGIES['url']?>/<?=$GOGIES['company_logo']?>" height="28" alt="<?=$GOGIES['company_name']['en']?>"></a></strong></span>
<ul class="nav-menu">
<li class="brand nopad"><a href="<?=$GOGIES['url']?>"><img src="<?=$GOGIES['url']?>/<?=$GOGIES['company_logo']?>" height="39" alt="<?=$GOGIES['company_name']['en']?>"></a></li>
<?=$GOGIES['topnav']?>


</ul></nav> 

<?=@$GOGIES['bread_crumb']?>
<?php if (!isset($GOGIES['slider'])or  $GOGIES['slider']==0)
   {
	print '<div class="pvt-orange" style="
	height:52px;">&nbsp;</div>';   
   }?>
   
   
