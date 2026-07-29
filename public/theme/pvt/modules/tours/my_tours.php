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
    <h2 class="animated fadeInDown gap-t"><i class="fa-calendar"></i> <?= $tours_lang['my_service_name'] ?></h2>




    <?php
    if (mysqli_num_rows($SQ) == 0)
    {
        print '<br /><h2 class="box alert"><i class="fa-warning"></i> ' . $tours_lang['no_tours_found'] . '</h2>';
    } 
    else
    {
        ?>
        <div class="row bordered">
            <div class="row table-head grey">
                <div class="sd-5 md-3"><?= $lang['title'] ?></div>
                <div class="sd-2 md-2"><?= $lang['status'] ?></div>
                <div class="sd-3 md-2"><?= $lang['total'] ?></div>
                <div class="sd-2 md-5"></div>
            </div>

            <?php
            while ($t = mysqli_fetch_array($SQ))
            {
            	
                $dep_date = date('Y-m-d', strtotime($t['travel_date'] . ' + ' . $t['days'] . ' days'));
                $dep_date = date('Y-m-d', strtotime($dep_date . ' - 1 days'));
                if ($t['guaranteed_departure_id']>0){?>
                 <div class="row cell">
                    <div class="md-3 sd-5"><?= $t['g_desc'] ?></div>
                    <div class="md-2 sd-2"><?= $G['invoice']->get_invoice_status($t['g_status'], 'h-pad-l h-pad-r small ') ?></div>
                    <div class="md-2 sd-3"><?= $G['invoice']->convert_currency($t['g_total'], true) ?></div>
                    <div class="md-5 sd-12">
                        <div class="row">
                            <?= $lang['from'] . ': ' . $t['travel_date'] . ' - ' . $lang['to'] . ': ' . $dep_date ?> 
                        </div>
                        <a target="_blank" class="btn orange small" href="<?= $GOGIES['url'] . '/' . $GOGIES['lang'] . '/invoice/' . $t['g_invoice_id'] . '/' ?>"><i class="fa-eye"></i> <?= $lang['invoice'] ?></a>
                        <a href="javascript:void(0);"  class="btn green small" onclick="
                                        do_ajax('#ajax', '<?= $GOGIES['url'] . '/modules/tours/ajax/view_booking_details.php?lang=' . $GOGIES['lang'] . '&b=' . $t['booking_id'] ?>', '<?= $_SERVER['REQUEST_URI'] ?>#modal');"><i class="fa-plus"></i> <?= $tours_lang['itinerary'] ?></a>
                        <a href="<?= $GOGIES['seo_url'] ?>/tours/booking/<?= $G['common']->get_access_code($t['booking_id']) ?>" class="btn blue small" target="_blank"><i class="fa-info"></i> <?= $lang['details'] ?></a>
                    </div>
                </div>	
                <?php 
                }
                else
                {
                ?>
                <div class="row cell">
                    <div class="md-3 sd-5"><?= $t['desc'] ?></div>
                    <div class="md-2 sd-2"><?= $G['invoice']->get_invoice_status($t['status'], 'h-pad-l h-pad-r small ') ?></div>
                    <div class="md-2 sd-3"><?= $G['invoice']->convert_currency($t['total'], true) ?></div>
                    <div class="md-5 sd-12">
                        <div class="row">
                            <?= $lang['from'] . ': ' . $t['travel_date'] . ' - ' . $lang['to'] . ': ' . $dep_date ?> 
                        </div>
                        <a target="_blank" class="btn orange small" href="<?= $GOGIES['url'] . '/' . $GOGIES['lang'] . '/invoice/' . $t['invoice_id'] . '/' ?>"><i class="fa-eye"></i> <?= $lang['invoice'] ?></a>
                        <a href="javascript:void(0);"  class="btn green small" onclick="
                                        do_ajax('#ajax', '<?= $GOGIES['url'] . '/modules/tours/ajax/view_booking_details.php?lang=' . $GOGIES['lang'] . '&b=' . $t['booking_id'] ?>', '<?= $_SERVER['REQUEST_URI'] ?>#modal');"><i class="fa-plus"></i> <?= $tours_lang['itinerary'] ?></a>
                        <a href="<?= $GOGIES['seo_url'] ?>/tours/booking/<?= $G['common']->get_access_code($t['booking_id']) ?>" class="btn blue small" target="_blank"><i class="fa-info"></i> <?= $lang['details'] ?></a>
                    </div>
                </div>


            <?php
            } 
           } 
           ?>
        </div>
    <?php } ?>

    <div class="text-center"><?= $pagintation ?></div>
    <div class="row" id="ajax"></div>

</div>