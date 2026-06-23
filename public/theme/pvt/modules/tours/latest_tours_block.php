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
<div class=" wrap white">
    <h1 class="section-title d-gap-t"> <?= $tours_lang['latest_tours'] ?></h1>
    <div class="row">
        <div class="carousel custom">
            <div class="carousel-body">
                <ul>
                    <?php
                    while ($latest_tours_res = mysqli_fetch_array($latest_tours_sql)) {
                        $tour_tec_details = $tourslib->get_tour_tec_rates($latest_tours_res['tec_details'], $GOGIES['tours']['tec']);
                        ?>
                        <li>
                            <div class="full-width">
                                <div class="pad">

                                    <div class="tours-random-box" data-transition="fadeInUp">
                                        <div class="relative overflow-hidden">
                                            <a class="nopad nogap" href="<?= $GOGIES['seo_url'] . 'tours/' . strtolower($GOGIES['countries'][$latest_tours_res['start_country']]) . '/' . $latest_tours_res['url'] . '/' ?>">

                                                <img src="<?= $latest_tours_res['image'] ?>" class="full-width block" alt="<?= $latest_tours_res['title'] ?>">
                                            </a>
                                            <div class="animated hover align-justify">
                                                <div data-truncate="4"><?= $latest_tours_res['meta_desc'] ?></div>
                                            </div>

                                            <?php
                                            if ($tourslib->date_in_range($latest_tours_res['f_start'], $latest_tours_res['f_finish'])) {
                                                ?>
                                                <div class="animated featured text-capitalize"><?= $lang['featured'] ?></div>
                                            <?php } ?>

                                            <?php
                                            if ($tourslib->date_in_range($latest_tours_res['sp_start'], $latest_tours_res['sp_finish'])) {
                                                ?>
                                                <div class="sale-label ">Sale</div>
                                            <?php } ?>

                                        </div>
                                        <div class="light-bordered">

                                            <div data-truncate="1" class="bold"><strong><?= $latest_tours_res['title'] ?></strong></div>
                                            <div class="row light-text light-bordered-b">
                                                <div class="sd-6 align-left pad"><i class="fa-calendar"></i> <?= $lang['days'] ?>: <?= $latest_tours_res['days'] ?> </div>
                                                <div class="sd-6 align-right pad"><?= $latest_tours_res['city'] . ' - ' . $GOGIES['countries'][$latest_tours_res['start_country']] ?></div>
                                            </div>
                                            <div class="tec_details align-left pad-r">
                                                <?= $tour_tec_details ?>
                                            </div>
                                            <div class="pad-t pad-b">
                                                <div class="row">
                                                    <div class="sd-4  d-pad-t">
                                                        <a class="pvt-orange round-corners h-pad pad-r d-gap-t  text-uppercase" href="<?= $GOGIES['seo_url'] . 'tours/' . strtolower($GOGIES['countries'][$latest_tours_res['start_country']]) . '/' . $latest_tours_res['url'] . '/' ?>"><i class="fa-info-circle"></i> <?= $lang['details'] ?></a></div>
                                                    <div class=" sd-8   price">
                                                        <?= $tourslib->get_tour_pricing($latest_tours_res['min_price'], $latest_tours_res['max_price'], $latest_tours_res['id']); ?></div>
                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>

                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>