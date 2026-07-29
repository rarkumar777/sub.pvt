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
?>
<div class="center-block pad" style="max-width:600px">
<div class="box trans-back noborder random-transition  ">
<h3><i class="fa-lock"></i> <?=$lang['insert_code_from_you_authenticator_app']?></h3>
  
<?=$error_msg?>


<form method="post" >
 <div class="row">
  <div class="md-10">

  <input name="auth_code" class="btn nogap " style="padding-left:35px;"  type="text"  maxlength="50"  />
  <span class=" pad-l h-pad-t medium absolute top left"><i class="fa-key"></i></span>
  </div>
  <div class="md-2"><button class="btn blue full-width" ><b><i class="fa fa-power-off"></i> <?=$lang['login']?></b></button></div>
</div>  		
	</form>
</div>
</div>