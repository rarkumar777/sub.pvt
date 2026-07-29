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
?>
<div class="center-block pad" style="max-width:600px">
    <div class="box trans-back noborder random-transition  ">
        <h2><i class="fa-lock"></i> <?= $lang['password_reset'] ?></h2>

        <?= $error_msg ?>


        <form method="post" >
            <div class="row">
                <div class="md-10">

                    <input name="email" class="btn nogap " style="padding-left:35px;" placeholder="<?= $lang['email'] ?>" type="text"  maxlength="50"  />
                    <span class=" h-pad medium absolute top left"><i class="fa-envelope "></i></span>
                </div>
                <div class="md-2"><button class="btn blue full-width" ><b><i class="fa fa-power-off"></i> <?= $lang['send'] ?></b></button></div>
            </div>
        </form>
    </div>
</div>