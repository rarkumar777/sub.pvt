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
if (!defined('gogies'))
{
    print 'Direct script access is not allowed';
    exit;
}
?>
<div class="wrap">
    <h1><i class="fa-user"></i> <?= $lang['my_account'] ?></h1>
    <div class="row bordered-t">
        <div class="md-3 h-pad">
            <ul class="list-group grey">
                <li>
                    <div class="align-center"><img width="120px" src="<?= $GOGIES['user']['avatar'] ?>" /></div></li>
                <li><a href="<?= $GOGIES['seo_url'] ?>users/account/edit-account/"><?= $lang['edit_account'] ?></a></li>
<?= $account_links ?>
            </ul>
        </div>

        <div class="md-9 h-pad">
            <div class="d-pad-b"><?php include $user_contents_file ?></div>
        </div>

    </div>
</div>