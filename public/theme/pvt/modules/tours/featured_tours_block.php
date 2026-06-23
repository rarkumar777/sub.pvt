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
<div class="wrap">
  <h2 class="section-title d-gap-t"> <?= $tours_lang['featured_tours'] ?></h2>
  <div class="carousel ">
    <div class="carousel-body ">
      <ul>
        <?php while ($featured_tours_res = mysqli_fetch_array($featured_tours_sql)) { ?>
          <li>
            <div class="sd-12">
              <div class="relative">
                <a href="<?= $GOGIES['seo_url'] . 'tours/' . strtolower($GOGIES['countries'][$featured_tours_res['start_country']]) . '/' . $featured_tours_res['url'] . '/' ?>">
                  <span class="item-top"><?= $G['common']->get_item_rating($featured_tours_res['rating'], $GOGIES['tours_settings']['rate_icon']) ?><span class="pull-right"><?= $GOGIES['countries'][$featured_tours_res['start_country']] ?></span></span>


                  <img src="<?= $featured_tours_res['image'] ?>">

                </a>
                <span class="item-bottom"><strong><?= $featured_tours_res['title'] ?></strong></span>
              </div>
              <div class="h-pad align-center "><?= $tourslib->get_tour_pricing($featured_tours_res['min_price'], $featured_tours_res['max_price'], $featured_tours_res['id'], $lang['start_from']) ?></div>
            </div>

          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="row"></div>
</div>