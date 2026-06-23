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
    <form name="booking_form" action="<?= $GOGIES['seo_url'] ?>tours/book_tour/<?= $t['id'] ?>/" method="post">

        <div class=" gap-t breadcrumb full-width hide-sd">
            <a class="grey" href="<?= $GOGIES['seo_url'] ?>"><i class="fa-home"></i> <?= $lang['home'] ?></a>
            <a href="<?= $GOGIES['seo_url'] ?>tours/" class="grey"><i class="fa-play"></i> <?= $tours_lang['tours'] ?></a>
            <a href="<?= $GOGIES['seo_url'] ?>tours/<?= strtolower($t['tour_start_country']) . '/' . $t['url'] . '/' ?>" onClick="" class="active"><i class="fa-play"></i> <?= $t['title'] ?></a>

        </div>
        <div class="pad full-width">
            <div id="step1" class="row <?= $step1class ?>">

                <div class="sd-12"><?= $error_msg ?></div>
                <div class="md-6 pad">

                    <div class="full-width h-pad-t gap-t">

                        <?= $tours_lang['select_accommodate'] ?>
                        <select id="price_base" name="price_base" class="btn white">
                            <?php
                            foreach ($pricing_bases as $k => $v) {
                                ?>
                                <option value="<?= $k ?>" <?php
                                                            if (@$_POST['price_base'] == $k) {
                                                                print 'selected';
                                                            }
                                                            ?>><?= $pricing_bases_vals[$k] ?></option>
                            <?php } ?>

                        </select>
                        <?= $G['form']->addInput(['type' => 'text', 'attr' => ['class' => 'btn white datepicker h-gap-t h-gap-b', 'data-disable-dates' => 'past', 'name' => 'date', 'placeholder' => $lang['date'] . '...']]); ?>

                    </div>
                    <!--adult-->
                    <div class="row cell">
                        <div class="sd-3 "><?= $tours_lang['adult'] ?></div>
                        <div class="sd-3 ">
                            <div id="adult_price">0</div>
                        </div>
                        <div class="sd-3">
                            <?=
                                $G['form']->addInput(['type' => 'number', 'attr' => [
                                    'name'  => 'adult',
                                    'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '1', 'max'   => '999', 'min'   => '1', 'style' => 'width:60px;'
                                ]]);
                            ?>
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
                            <?=
                                $G['form']->addInput(['type' => 'number', 'attr' => [
                                    'name'  => 'child',
                                    'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;'
                                ]]);
                            ?>
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
                            <?=
                                $G['form']->addInput(['type' => 'number', 'attr' => [
                                    'name'  => 'infant',
                                    'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;'
                                ]]);
                            ?></div>

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
                                <div class="sd-3"><?= $tours_lang['single'] ?></div>
                                <div class="sd-3"><?= $tours_lang['double'] ?></div>
                                <div class="sd-2"><?= $tours_lang['twin'] ?></div>
                                <div class="sd-2"><?= $tours_lang['triple'] ?></div>
                                <div class="sd-2"><?= $tours_lang['quad'] ?></div>
                            </div>
                            <div class="row">
                                <div class="sd-3">
                                    <?= $G['form']->addInput(['type' => 'number', 'attr' => [
                                        'name' => 'single',
                                        'class' => 'btn white h-pad ', 'type' => 'number', 'value' => '0', 'max' => '999', 'min' => '0', 'style' => 'width:60px;'
                                    ]]); ?>
                                </div>
                                <div class="sd-3">
                                    <?= $G['form']->addInput(['type' => 'number', 'attr' => [
                                        'name' => 'double',
                                        'class' => 'btn white h-pad nogap', 'type' => 'number', 'value' => '0', 'max' => '999', 'min' => '0', 'style' => 'width:60px;'
                                    ]]); ?>
                                </div>
                                <div class="sd-2">
                                    <?= $G['form']->addInput(['type' => 'number', 'attr' => [
                                        'name' => 'twin',
                                        'class' => 'btn white h-pad nogap', 'type' => 'number', 'value' => '0', 'max' => '999', 'min' => '0', 'style' => 'width:60px;'
                                    ]]); ?>
                                </div>
                                <div class="sd-2">
                                    <?= $G['form']->addInput(['type' => 'number', 'attr' => [
                                        'name' => 'triple',
                                        'class' => 'btn white h-pad', 'type' => 'number', 'value' => '0', 'max' => '999', 'min' => '0', 'style' => 'width:60px;'
                                    ]]); ?>
                                </div>
                                <div class="sd-2">
                                    <?= $G['form']->addInput(['type' => 'number', 'attr' => [
                                        'name' => 'quad',
                                        'class' => 'btn white h-pad', 'type' => 'number', 'value' => '0', 'max' => '999', 'min' => '0', 'style' => 'width:60px;'
                                    ]]); ?>
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
                    <div class="row cell"><?=
                                                $G['form']->addTextarea(['type' => 'textarea', 'attr' => [
                                                    'name'        => 'notes',
                                                    'class'       => 'full-width white h-pad ', 'placeholder' => $lang['notes'] . '...'
                                                ]]);
                                            ?>
                    </div>

                    <div class="row"><button name="check_out" class="btn orange full-width"><i class="fa-calendar"></i> <?= $tours_lang['book_it_now'] ?></button></div>

                </div>
                <div class="md-6 pad">

                    <div class="section-title sd-12"><i class="fa-info-circle"></i> <?= $t['title'] ?></div>
                    <div class="row cell"><?= '<b>' . $t['days'] . '</b> ' . $lang['days'] . ' <b>' . $t['nights'] . ' </b>' . $lang['nights'] ?></div>

                    <div class="row cell"><?= '<b>' . $tours_lang['start_in'] . ': </b>' . $t['tour_start_country'] . '-' . $t['tour_start_city'] ?></div>
                    <img class="full-width" src="<?= $t['image'] ?>">

                </div>

            </div>
            <div class="sd-12 <?= $step2class ?>" id="step2">
                <div class="box white nopad">
                    <h3 class="box nogap"><i class="fa-info-circle"></i> <?= $tours_lang['confirm_your_booking_details'] ?></h3>
                    <div class="row">
                        <div class="md-6">
                            <div class="box white text-capitalize"><strong><?= $t['title'] ?>:</strong>
                                <?= $t['days'] . ' ' . $lang['days'] . ' - ' . $t['nights'] . ' ' . $lang['nights'] ?></div>

                            <div class="box white"><strong><?= $tours_lang['travel_date'] ?>:</strong> <?= $travel_date ?></div>

                            <div class="box"><?= $tours_lang['adult'] ?>: <?= $adult_count ?> &times; <?= $G['invoice']->convert_currency($adult_price, true) ?> = <?= $G['invoice']->convert_currency($adult_total, true) ?></div>

                            <?php
                            if ($child_count > 0) {
                                ?><div class="box"><?= $tours_lang['child'] ?>: <?= $child_count ?> &times; <?= $G['invoice']->convert_currency($child_price, true) ?> = <?= $G['invoice']->convert_currency($child_total, true) ?></div> <?php } ?>

                            <?php
                            if ($infant_count > 0) {
                                ?><div class="box"><?= $tours_lang['infant'] ?>: <?= $infant_count ?> &times; <?= $G['invoice']->convert_currency($infant_price, true) ?> = <?= $G['invoice']->convert_currency($infant_total, true) ?></div> <?php } ?>

                            <?php
                            if ($single_supplement_fee_total > 0) {
                                ?><div class="box"><?= $tours_lang['single_supplement_fee'] ?>: <?= $rooms_single ?> &times; <?= $G['invoice']->convert_currency($single_supplement_fee, true) ?> = <?= $G['invoice']->convert_currency($single_supplement_fee_total, true) ?></div> <?php } ?>

                            <?php
                            if ($tax > 0) {
                                ?><div class="box"><?= $lang['total'] ?>: <?= $G['invoice']->convert_currency($total, true) ?> </div>
                                <div class="box"><?= $lang['tax'] ?>: <?= $G['invoice']->convert_currency($tax, true) ?> </div>
                            <?php } ?>

                            <div class="box"><?= $lang['grand_total'] ?>: <?= $G['invoice']->convert_currency($grand_total, true) ?> </div>

                            <div class="box white"><strong><?= $tours_lang['hotel_rooms'] ?></strong> <br>
                                <?php
                                if ($hotel_category > 0) {
                                    ?>
                                    <div class="pad-t"><?= $tours_lang['hotel_grade'] ?>: <?= $hotel_rate ?></div>
                                    <div class="pad-t">
                                        <?= $tours_lang['single'] ?> &times; <?= $rooms_single ?> <br>
                                        <?= $tours_lang['double'] ?> &times; <?= $rooms_double ?> <br>
                                        <?= $tours_lang['twin'] ?> &times; <?= $rooms_twin ?> <br>
                                        <?= $tours_lang['triple'] ?> &times; <?= $rooms_triple ?><br>
                                        <?= $tours_lang['quad'] ?> &times; <?= $rooms_quad ?>
                                    </div>
                                <?php
                            } else {
                                ?>
                                    <div class="pad-t"><?= $tours_lang['no_hotel_accommodations'] ?></div>

                                <?php } ?>
                            </div>
                            <div class="box alert">
                                <strong><?= $lang['notes'] ?>:</strong><br>
                                <?= $booking_note ?>
                            </div>
                        </div>
                        <div class="md-6">
                            <div class="row align-center">

                                <?php
                                if ($GOGIES['is_user']) {
                                    ?>

                                    <div class="sd-12 pad-t"><a href="#" onClick="edit_booking(); return false;" class="btn orange"><i class="fa-edit"></i> <?= $tours_lang['edit_booking_details'] ?></a></div>
                                    <div class="sd-12 d-pad-t"><button name="check_out_confirm" class="btn blue"><i class="fa-check"></i> <?= $tours_lang['place_booking'] ?></button></div>
                                <?php
                            } else {
                                ?>
        </form>
        <h3 class=""><?= $tours_lang['please_login_to_complete_your_booking'] ?></h3>
        <?php include_once $GOGIES['path'] . '/theme/' . $GOGIES['theme'] . '/login.php'; ?>

    <?php } ?>
</div>


</div>
</div>
</div>
</div>
</div>

<?php 
?>
<div class="modal" id="full-pricing">
    <div class="table">
        <a href="<?= $_SERVER['REQUEST_URI'] ?>#close" title="Close" class="close">&times;</a>
        <h3 class="nogap"><i class="fa-info-circle"></i> <?= $lang['pricing'] ?></h3><?= $full_pricing ?>
        <div class="d-pad align-center"><a href="<?= $_SERVER['REQUEST_URI'] ?>#close" title="Close" class="btn red small "><i class="fa-close"></i> <?= $lang['close'] ?></a></div>
    </div>

</div>
</form>
</div>
