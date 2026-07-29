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
<div class="wrap h-pad full-width">
    <div class="row">

        <!--search-->
        <div class="orange row bordered gap-t" style="display:table" >
            <div class="">
                <div class="md-2 pad-t h-pad-l">
                    <span class="red h-pad-l h-pad-r small round-corners"> <strong><?= $count ?></strong></span> <?= $tours_lang['tours_found'] ?>
                </div>
                <div class="md-10 h-pad align-right">

                    <?php
                    $countries_list = NULL;
                    foreach ($GOGIES['tours_countries'] as $k => $v)
                    {
                        if ($k == @$_GET['country'])
                        {
                            $current_country = $v;
                        }
                        else
                        {
                            $countries_list.='<li><a href="' . $GOGIES['seo_url'] . 'tours/?country=' . @$k . '&category=' . @$_GET['category'] . '&type=' . @$_GET['type'] . '&price_min=' . @$_GET['min_price'] . '&price_max=' . @$_GET['max_price'] . '&days=' . @$_GET['days'] . '&rate=' . @$_GET['rate'] . '&p=' . @$_GET['page'] . '" >' . $v . '</a></li>';
                        }
                    }
                    ?>
                    <span class="dropdown align-left right">
                        <button class="btn white small" id="toggler"><?= @$current_country ?></button>
                        <ul class="right">
                    <?= $countries_list ?>
                        </ul>
                    </span>

                            <?php
                            $types_list = NULL;
                            foreach ($GOGIES['tours']['types'] as $k => $v)
                            {
                                if ($k == @$_GET['type'])
                                {
                                    $current_type = $v;
                                }
                                else
                                {
                                    $types_list.='<li><a href="' . $GOGIES['seo_url'] . 'tours/?country=' . @$_GET['country'] . '&category=' . @$_GET['category'] . '&type=' . $k . '&price_min=' . @$_GET['min_price'] . '&price_max=' . @$_GET['max_price'] . '&days=' . @$_GET['days'] . '&rate=' . @$_GET['rate'] . '&p=' . @$_GET['page'] . '" >' . $v . '</a></li>';
                                }
                            }
                            ?>
                    <span class="dropdown align-left ">
                        <button class="btn white small" id="toggler"><?= @$current_type ?></button>
                        <ul class="right">
            <?= $types_list ?>
                        </ul>
                    </span>



                </div>
            </div>
        </div>
        <div class="row">
            <!--tours_loop-->
<?php if (mysqli_num_rows($SQ) == 0)
{ ?><h3 class="box alert"><i class="fa-warning"></i> <?= $tours_lang['no_tours_found'] ?></h3><?php }
?>
<?php
while ($sr = mysqli_fetch_array($SQ))
{
    $tour_tec_details = $tourslib->get_tour_tec_rates($sr['tec_details'], $GOGIES['tours']['tec']);
    include $GOGIES['theme_path'] . '/modules/tours/tour_search_box.php';
}
?>
            <!--tours loops end-->

            <div id="page-loader" class="center-block nopad nogap">
            </div>

<?= $scroll_postion ?>

        </div>


    </div>
</div>
