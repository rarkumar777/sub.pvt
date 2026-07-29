<div class="wrap">
    <div class="md-6 bd-3 relative scroll-animate">
        <div class="pad">
            <div class="tours-random-box light-bordered">
                <div class="relative">

                    <img src="<?= $sr['image'] ?>" class="full-width block">
                    <div class="animated hover">
                        <div data-truncate="4"><?= $sr['meta_desc'] ?></div>
                    </div>

                    <?php if ($tourslib->date_in_range($sr['f_start'], $sr['f_finish'])) { ?>
                        <div class="animated featured text-capitalize"><?= $lang['featured'] ?></div>
                    <?php } ?>

                    <?php if ($tourslib->date_in_range($sr['sp_start'], $sr['sp_finish'])) { ?>
                        <div class="sale-label ">Sale</div>
                    <?php } ?>

                </div>


                <div data-truncate="1" class="bold"><strong><?= $sr['title'] ?></strong></div>
                <div class="row light-text light-bordered-b">
                    <div class="sd-6 align-left pad"><i class="fa-calendar"></i> <?= $lang['days'] ?>: <?= $sr['days'] ?> </div>
                    <div class="sd-6 align-right pad"><?= $sr['tour_start_city'] . ' - ' . $GOGIES['countries'][$sr['start_country']] ?></div>
                </div>
                <div class="tec_details align-left pad-r">
                    <?= $tour_tec_details ?>
                </div>
                <div class="pad-t pad-b">
                    <div class="row">
                        <div class="sd-4  d-pad-t">
                            <a class="pvt-orange round-corners h-pad pad-r d-gap-t  text-uppercase" href="<?= $GOGIES['seo_url'] . 'tours/' . strtolower($GOGIES['countries'][$sr['start_country']]) . '/' . $sr['url'] . '/' ?>"><i class="fa-info-circle"></i> <?= $lang['details'] ?></a></div>
                        <div class="sd-8   price">
                            <?= $tourslib->get_tour_pricing($sr['min_price'], $sr['max_price'], $sr['id']); ?></div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>