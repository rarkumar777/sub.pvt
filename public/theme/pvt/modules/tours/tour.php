<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                              ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                           ///
 * @link		    www.cms.gogies.net                                    ///
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
    <div class="pad"><div class="row">
            <div class="breadcrumb full-width hide-sd ">
                <a href="<?= $GOGIES['seo_url'] ?>" class="grey"><i class="fa-home"></i> <?= $lang['home'] ?></a>
                <a href="<?= $GOGIES['seo_url'] ?>tours/" class="grey"><i class="fa-play"></i> <?= $tours_lang['tours'] ?></a>
                <a href="<?= $GOGIES['seo_url'] ?>tours/" onClick="return false;" class="active"><i class="fa-play"></i> <?= $t['title'] ?></a>

            </div>
            <div class="sd-12"><h1 class="section-title"><?= $t['title'] ?> <?= $admin_btn ?></h1></div>
            <!--start tour images-->
            <div class="md-8">
                <div class="slider tour" data-autoplay="true" data-delay="4000" data-indicators="true" data-arrows="true" >
                    <?php
                    if (count($pricing_bases) >= 1 && !empty($pricing_bases))
                    {
                        ?>
                        <div class="tour_price round-corners"><small><?= $lang['start_from'] ?></small> <?= $price_from . $price_to ?></div>
                    <?php } ?>
                    <ul><?= $slides ?>

                    </ul></div>
            </div>
            <!--end tour images-->
            <div class="md-4 h-pad-l">
                <h2 class="align-center h-pad"><?= $rating ?></h2>

                <?php
                if (count($pricing_bases) >= 1 && !empty($pricing_bases))
                {
                    ?>
                    <form  action="<?= $GOGIES['seo_url'] ?>tours/book_tour/<?= $t['id'] ?>/" method="post">

                        <div class="full-width h-gap-t">

                            <?= $tours_lang['select_accommodate'] ?>
                            <select id="price_base" name="price_base" class="btn white">
                                <?php
                                foreach ($pricing_bases as $k => $v)
                                {
                                    ?>
                                    <option value="<?= $k ?>"><?= $pricing_bases_vals[$k] ?></option>
                                <?php } ?>

                            </select>
                            <input name="date" placeholder="<?= $lang['date'] ?>: DD-MM-YYYY" type="text" class="btn datepicker h-gap-t">
                        </div>
                        <!--adult-->
                        <div class="row cell">
                            <div class="sd-3 "><?= $tours_lang['adult'] ?></div>
                            <div class="sd-3 ">
                                <div id="adult_price">0</div>
                            </div>
                            <div class="sd-3">
                                <input type="number" id="adult" class="btn white full-width h-pad" accept="numbers" max="300" min="1" value="1" name="adult">

                            </div>

                            <div class="sd-3 align-right">
                                <div id="adult_total">0</div>
                            </div>
                        </div>
                        <!--child-->
                        <div class="row cell">
                            <div class="sd-3 "><?= $tours_lang['child'] ?></div>
                            <div class="sd-3 ">
                                <div id="child_price">0</div>
                            </div>
                            <div class="sd-3">
                                <input type="number" id="child" class="btn white full-width h-pad" accept="numbers" max="300" min="0" value="0" name="child">

                            </div>

                            <div class="sd-3 align-right">
                                <div id="child_total">0</div>
                            </div>
                        </div>
                        <!--infant-->
                        <div class="row cell">
                            <div class="sd-3 "><?= $tours_lang['infant'] ?></div>
                            <div class="sd-3 ">
                                <div id="infant_price">0</div>
                            </div>
                            <div class="sd-3">
                                <input type="number" id="infant" class="btn white full-width h-pad" accept="numbers" max="300" min="0" value="0" name="infant">

                            </div>

                            <div class="sd-3 align-right">
                                <div id="infant_total">0</div>
                            </div>
                        </div>
                        <!--hotel rooms-->
                        <div class="row" id="hotel_rooms">
                            <div class="row cell">
                                <strong><?= $tours_lang['hotel_rooms'] ?>:</strong>
                                <span id="rooms_alert" class="hide"><span class="h-pad error"><?= $tours_lang['rooms_capacity_alert'] ?></span></span>
                                <br>
                                <div class="row">
                                	<div class="sd-3"><?=$tours_lang['single']?></div>
<div class="sd-3"><?=$tours_lang['double']?></div>
<div class="sd-2"><?=$tours_lang['twin']?></div>
<div class="sd-2"><?=$tours_lang['triple']?></div>
<div class="sd-2"><?=$tours_lang['quad']?></div>
                                </div>
                                <div class="row">
                                	<div class="sd-3">
<input type="number" id="single" class="btn white full-width h-pad" accept="numbers" max="150" min="0" value="0" name="single">
</div>
<div class="sd-3">
<input type="number" id="double" class="btn white full-width h-pad" accept="numbers" max="300" min="0" value="0" name="double">
</div>
<div class="sd-2">
<input type="number" id="twin" class="btn white full-width h-pad" accept="numbers" max="300" min="0" value="0" name="twin">
</div>
<div class="sd-2">
<input type="number" id="triple" class="btn white full-width h-pad" accept="numbers" max="300" min="0" value="0" name="triple">
</div>
<div class="sd-2">
<input type="number" id="quad" class="btn white full-width" accept="numbers" max="300" min="0" value="0" name="quad">
</div>
                                </div>

                            </div>
                            <div class="row cell">
                                <div class="sd-9 "><?= $tours_lang['single_supplement_fee'] ?></div>
                                <div class="sd-3 align-right" id="single_supplement">0</div>
                            </div>
                        </div>
                        <!--end rooms-->
                        <div class="row cell">
                            <div class="sd-9 "><?= $lang['total'] ?> <a class="tooltip tooltip-top pull-right" data-tooltip="<?= $tours_lang['see_full_pricing'] ?>" href="<?= $_SERVER['REQUEST_URI'] ?>#full-pricing"><i class="fa-info-circle h-pad round-corners bordered "></i></a></div>
                            <div class="sd-3 align-right" id="total">0</div>
                        </div>
                        <div class="row"><button class="btn orange full-width"><i class="fa-calendar"></i> <?= $tours_lang['book_it_now'] ?></button></div>
                        <div class="row"><a href="<?= $GOGIES['seo_url'] ?>tours/inquery/<?= $t['id'] ?>/" class="btn green full-width h-gap-t"><i class="fa-edit"></i> <?= $tours_lang['customize_this_tour'] ?></a></div>
                    </form>
                    <?php
                }
                else
                {
                    include $GOGIES['theme_path'] . '/modules/tours/inquery_box.php';
                    ?>

                    <!-- <div class="box info">
                    <h1 class="align-center h-pad"><i class="fa-info-circle"></i></h1>
                    <?= $tours_lang['no_price_details'] ?>
                    </div>
                    <h3 class="section-title"><?= $lang['share_it'] ?></h3>

                    <div class="a2a_kit a2a_kit_size_48 a2a_default_style gap-t gap-b " style=" max-height:48px;white-space:nowrap; overflow:hidden;">
                    <a class="a2a_button_facebook h-gap-l"></a>
                    <a class="a2a_button_twitter h-gap-l"></a>
                    <a class="a2a_button_email h-gap-l"></a>
                    <a class="a2a_button_linkedin h-gap-l"></a>
                    <a class="a2a_button_google_plus h-gap-l"></a>
                    <a class="a2a_button_printfriendly h-gap-l"></a>
                    <a class="a2a_dd gap-l" href="https://www.addtoany.com/share"></a>
                    </div>
                    <script async src="https://static.addtoany.com/menu/page.js"></script>

                    -->
                <?php } ?>


            </div>
        </div>

        <?php ///////////////////////////////    ?>
        <div class="modal" id="full-pricing">
            <div class="table">
                <a href="<?= $_SERVER['REQUEST_URI'] ?>#close" title="Close" class="close">&times;</a>
                <h3 class="nogap"><i class="fa-info-circle"></i> <?= $lang['pricing'] ?></h3><?= $full_pricing ?>
                <div class="d-pad align-center"><a href="<?= $_SERVER['REQUEST_URI'] ?>#close" title="Close" class="btn red small "><i class="fa-close"></i> <?= $lang['close'] ?></a></div>
            </div>
        </div>
        <div class="row">
            <div class="md-4">
                <h3 class="section-title gap-t"><i class="fa-info-circle"></i> <?= $lang['general'] ?></h3>
                <div class="row cell"><?= '<b>' . $t['days'] . '</b> ' . $lang['days'] . ' <b>' . $t['nights'] . ' </b>' . $lang['nights'] ?></div>
                <div class="row cell"><?= '<b>' . $lang['category'] . ': </b>' . $t['tour_category'] ?></div>
                <div class="row cell"><?= '<b>' . $lang['type'] . ': </b>' . $t['tour_type'] ?></div>

                <div class="row cell"><?= '<b>' . $tours_lang['start_in'] . ': </b>' . $t['tour_start_country'] . '-' . $t['tour_start_city'] ?></div>
                <div class="row cell"><?= '<b>' . $tours_lang['finish_in'] . ': </b>' . $t['tour_finish_country'] . '-' . $t['tour_finish_city'] ?></div>
                <?php
                if (count($pricing_bases) >= 1 && !empty($pricing_bases))
                {
                    ?>
                    <div class="row cell"><strong><?= $lang['pricing'] ?></strong> <a class=""  href="<?= $_SERVER['REQUEST_URI'] ?>#full-pricing"><i class="fa-info-circle h-pad round-corners bordered "><?= $tours_lang['see_full_pricing'] ?></i></a></div>
                <?php } ?>
                <h3 class="section-title d-gap-t"><?= $tours_lang['technichal_details'] ?></h3>

                <?= $TEC ?>
                <h3 class="section-title gap-b d-gap-t"><i class="check-check "></i> <?= $tours_lang['inclusions'] ?></h3>
                <?= $inc_col ?>


                <h3 class="section-title gap-b d-gap-t"><i class="fa-close"></i> <?= $tours_lang['exclusions'] ?></h3>
                <?= $exc_col ?>

                <?php
                if (strlen($t['map']) > 20)
                {
                    ?>

                    <h3 class="section-title"><i class="fa-map-marker"></i> <?= $lang['map'] ?></h3>
                    <div class="row">
                        <div class="pad">
                            <iframe src="<?= html_entity_decode($t['map']) ?>" width="100%" height="300" frameborder="0"></iframe>
                        </div></div>
                <?php } ?>
                <h3 class="section-title"><?= $lang['share_it'] ?></h3>

                <!-- AddToAny BEGIN -->
                <div class="a2a_kit a2a_kit_size_48 a2a_default_style gap-t gap-b " style=" max-height:48px;white-space:nowrap; overflow:hidden;">
                    <a class="a2a_button_facebook h-gap-l"></a>
                    <a class="a2a_button_twitter h-gap-l"></a>
                    <a class="a2a_button_email h-gap-l"></a>
                    <a class="a2a_button_linkedin h-gap-l"></a>
                    <a class="a2a_button_google_plus h-gap-l"></a>
                    <a class="a2a_button_printfriendly h-gap-l"></a>
                    <a class="a2a_dd gap-l" href="https://www.addtoany.com/share"></a>
                </div>
                <script async src="https://static.addtoany.com/menu/page.js"></script>
                <!-- AddToAny END -->

            </div>
            <div class="md-8 pad-l">
                <div class="row">

                <h3 class="section-title gap-b gap-t"><i class="fa-th"></i> <?= $tours_lang['itinerary'] ?></h3>
                <?= $t['desc'] ?>
</div>
<div class="row">
	            	<?php if ($departure){?>
	            	<div class="box white nopad">
         	    <h3 class=" grey bordered-b align-center"><i class="fa-plane"></i> <?= $tours_lang['guaranteed_departure'] ?></h3>
         	    
         	    <div class="row table-head align-center">
         	    	<div class="md-2"><?=$lang['date']?></div>
         	    	<div class="md-2"><?=$lang['days']?></div>
         	    	<div class="md-2"><?=$lang['price']?></div>
         	    	<div class="md-2"><?=$lang['status']?></div>
         	    	<div class="md-2"><?=$tours_lang['booked']?></div>
         	    	<div class="md-2"></div>
         	    </div>
         	    <?php 
         	    while ($gres=mysqli_fetch_array($guaranteed_departue_sql))
         	          {
         	          $gdep_date = date('Y-m-d', strtotime($gres['date'] . ' + ' . $t['days'] . ' days'));
                      $gdep_date = date('Y-m-d', strtotime($gdep_date . ' - 1 days'));
         	          $gprice=$tourslib->get_guaranteed_departure_price($gres);
         	          $qspecial_offer=NULL;
         	          if ($gprice['adult']<$gres['adult_price']){
         	          	$qspecial_offer='<span class="small danger-text line-through">'.$G['invoice']->convert_currency($gres['adult_price'],TRUE).'</span><br>'.$lang['limited_time'].'<br>';
         	          }
         	          $gstatus=($gres['booked_paid']>=$gres['min_to_operate'])?
         	          '<i class="fa-check-circle success-text fa-2x"></i><br>'.$lang['confirmed']:
         	           '<i class="fa-clock-o danger-text fa-2x"></i> <br>'.$lang['pending'];
         	          ?>
         	          <div class="row cell align-center">
         	          	<div class="md-2 small"><?=$gres['date']?><br><?=$gdep_date?></div>
         	          	<div class="md-2"><?=$t['days']?></div>
         	          	<div class="md-2 small"><b><?=$qspecial_offer.$G['invoice']->convert_currency($gprice['adult'],TRUE)?></b></div>
         	          	<div class="md-2 small"><?=$gstatus?></div>
         	          	<div class="md-2 small">
         	          	<span class="label green small"><?=$gres['booked_paid'].' '.$lang['confirmed']?></span>
         	          	<span class="label orange small h-gap-t"><?=$gres['booked_pending'].' '.$lang['pending']?></span>
         	          	</div>
         	          	<div class="md-2">
         	          		<?php if ($gres['max_to_operate']>$gres['booked_paid']){?>
         	          		<a href="<?=$GOGIES['seo_url'].'tours/guaranteed-departure-booking/'.$gres['id'].'/'?>" class="btn green"><?=$tours_lang['book_it_now']?></a>
         	          		<?php } else { ?>
         	          		<b class="success-text small"><?=$tours_lang['fully_booked']?></b>
         	          		<?php }?>
         	          		</div>
         	          </div>	
         	    <?php }?>
         	    </div>
            	    <?php }?>
</div>
                <div class="row">
                    <h3 class="section-title d-gap-t"><i class="fa-heart"></i> <?= $lang['you_may_also_like'] ?></h3>
                    <?= $relative_tours ?>
                </div>
            </div>
        </div></div></div>
