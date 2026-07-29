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
if (!defined('gogies')) {
    print 'Direct script access is not allowed';
    exit;
}
?>
<div class="row body-bg">
    <?php while ($latest_tours_res = mysqli_fetch_array($latest_tours_sql)) { ?>
        <div class="md-4 bd-3 d-pad scroll-animate" data-transition="fadeInUp">
            <div class="row">

                <div class="latest_tours_alt_box">
                    <a class="nopad nogap" href="<?= $GOGIES['seo_url'] . 'tours/' . strtolower($GOGIES['countries'][$latest_tours_res['start_country']]) . '/' . $latest_tours_res['url'] . '/' ?>">

                        <span class="toprating"><?= $G['common']->get_item_rating($latest_tours_res['rating'], $GOGIES['tours_settings']['rate_icon']) ?><span class="pull-right"><?= $GOGIES['countries'][$latest_tours_res['start_country']] ?></span></span>
                        <div style="overflow:hidden; height:180px">
                            <div class="zoom">
                                <img src="<?= $latest_tours_res['image'] ?>" class="full-width block">
                    </a></div>
            </div>
            <div class="corner"></div>
            <div data-truncate="1" class="title"><strong><?= $latest_tours_res['title'] ?></strong></div>
            <div class="align-justify pad-l d-pad-r" data-truncate="1"><?= $latest_tours_res['meta_desc'] ?></div>
            <div class="small danger-text h-pad align-center"><b>
                    <?= $tourslib->get_tour_pricing($latest_tours_res['min_price'], $latest_tours_res['max_price'], $latest_tours_res['id']); ?></b></div>
            <div class="row">
                <div class="pad align-center">
                    <a class="btn orange h-pad-t h-pad-b  text-uppercase" href="<?= $GOGIES['seo_url'] . 'tours/' . strtolower($GOGIES['countries'][$latest_tours_res['start_country']]) . '/' . $latest_tours_res['url'] . '/' ?>"><i class="fa-info-cirlce"></i> <?= $lang['details'] ?></a></div>

            </div>

        </div>
    </div>
    </div>
<?php } ?>

</div>