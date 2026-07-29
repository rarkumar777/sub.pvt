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
include_once $GOGIES['path'].'/core/users/facebook_login.php';?>
<div class="center-block pad" style="max-width:600px;">
<div class="box round-corners noborder	"><h2 class="h-pad gap-b d-pad-l"><i class="fa-user"></i> <?=$lang['login']?></h2>
<?=@$GOGIES['errors']?>
<form  method="post"  action="<?=$GOGIES['seo_url']?>users/login/?ret=<?=@urlencode($GOGIES['login_return'])?>" >
 
  

  
  <div class="sd-12">

  <input name="user_email" class="btn nogap " style="padding-left:35px;" placeholder="<?=$lang['email']?>" type="text"  maxlength="50"  />
  <span class="pad-l h-pad-t medium absolute top left"><i class="fa-envelope-o"></i></span>
  </div>
  
  <div class="sd-12 gap-t">
 <input name="user_pass" type="password" class="btn nogap" style="padding-left:35px;" placeholder="<?=$lang['password']?>" maxlength="50" />
  <span class=" d-pad-l h-pad-t medium absolute top left"><i class="fa-lock"></i></span>
  </div>
  <div class="sd-12 gap-t">
<?= $G['form']->addCaptcha(); ?>
</div>

			
			
			<div class="row">
	     <div class="md-6 gap-t">
         <div class=""><a class=" small" href="<?=$GOGIES['seo_url']?>users/restpassword/"><i class="fa-warning"></i> <?=$lang['forgot_password']?></a></div>
<div class="gap-t"><a class=" small"  href="<?=$GOGIES['seo_url']?>users/register/"><i class="fa-edit"></i> <?=$lang['create_new_account']?></a></div>
</div>
<div class="md-6 gap-t"><button class="btn orange full-width" ><b><i class="fa fa-power-off"></i> <?=$lang['login']?></b></button></div></div>
<span class="<?=$GOGIES['facebook_login_btn_class']?>"><a class="btn blue " href="<?=$GOGIES['facebook_login_url']?>"> <?=$lang['login_with_facebook']?></a></span>
</form>
</div></div>