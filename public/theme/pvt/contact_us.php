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
<div class="wrap">
    <div class="row">
        <h2 class="random-transition pad-t"><i class="fa-envelope-o"></i> <?= $lang['contact_us'] ?></h2>
        <?= $msg ?>
        <div class="md-6 pad"><?= $form_data ?></div>
        <div class="md-6">
            <div class="pad"></div>
            <?php if (!empty($GOGIES['company_telephone']))
            {
                ?>
                <div class="row cell"><i class="fa-phone"></i> T: <?= $GOGIES['company_telephone'] ?></div>
            <?php
            }
            if (!empty($GOGIES['company_fax']))
            {
                ?>
                <div class="row cell"><i class="fa-print"></i> F: <?= $GOGIES['company_fax'] ?></div>

            <?php
            }
            if (!empty($GOGIES['company_email']))
            {
                ?>
                <div class="row cell"><i class="fa-envelope-o"></i> M: <?= $GOGIES['company_email'] ?></div>

<?php }
?>
            <div  class="row cell"><strong><i class="fa-map-marker"></i> <?= $lang['address'] ?></strong><br>
                <span class="small"><?= html_entity_decode($GOGIES['company_address'][$GOGIES['lang']]) ?></span>
            </div>
            <div class="row cell"><strong><i class="fa-info-circle"></i> <?= $lang['opening_hours'] ?></strong><br>
                <span class="small"><?= html_entity_decode($GOGIES['company_opening_hours'][$GOGIES['lang']]) ?></span>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="sd-12 d-pad" style="height:300px;"><?= $map_data ?></div>

        <div class="d-pad"></div></div></div>