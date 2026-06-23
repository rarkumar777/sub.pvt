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
    <form method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded" action="<?= $GOGIES['seo_url'] ?>tours/inquery/<?= $Q[3] ?>/">


        <div class="h-pad"><h2  class="section-title d-pad random-transition"><i class="fa-edit"></i>  <?= $tours_lang['customize_tour'] ?></h2>
            <?php
            if ($success)
            {
                print $msg . '</div>';
            }
            else
            {
                ?>

                <div class="table box full-width nogap">
                    <div class="sd-4  align-center warning-text"><i class="fa-edit medium h-gap-b"></i><br />
                        <strong class="small"><?= $tours_lang['customize_tour_details'] ?> </strong>
                    </div>
                    <div class="sd-4 align-center info-text"><i class="fa-paper-plane medium h-gap-b"></i><br />
                        <strong class="small"><?= $tours_lang['submit_your_tour'] ?> </strong>
                    </div>
                    <div class="sd-4 align-center success-text"><i class="fa-reply medium h-gap-b"></i><br />
                        <strong class="small"><?= $tours_lang['our_operator_will_answer_you'] ?> </strong>
                    </div>
                </div>


            </div>


            <div class="row ">
                <div class="sd-12"><?= $msg ?></div>

                <h2 ><i class="fa-th"></i> <?= $t['title'] ?></h2>
                <div class="md-8  h-pad">

                    <?= $form->addTextarea(['type' => 'textarea', 'attr' => [ 'name' => 'desc', 'value' => $t['desc'], 'class' => 'tinymce']]); ?>
                    <h3 class="section-title"><i class="fa-check success-text"></i> <?= $tours_lang['inclusions'] ?></h3>

    <?= $inc_col ?>

                </div>
                <div class="md-4  h-pad">

                    <div class="d-pad-b  required"><?= $form->addInput(['type' => 'text', 'attr' => [ 'name' => 'name', 'placeholder' => $lang['name'] . '...', 'class' => 'btn white']]); ?>
                    </div>
                    <div class="d-pad-b  required"><?= $form->addInput(['type' => 'text', 'attr' => [ 'name' => 'email', 'placeholder' => $lang['email'] . '...', 'class' => 'btn white']]); ?>
                    </div>
                    <div class="d-pad-b"><?= $form->addInput(['type' => 'text', 'attr' => [ 'name' => 'telephone', 'placeholder' => $lang['telephone'] . '...', 'class' => 'btn white']]); ?>
                    </div>
                    <div class="d-pad-b"><?= $form->addInput(['type' => 'text', 'attr' => ['class' => 'btn white datepicker', 'data-disable-dates' => 'past', 'name' => 'date', 'placeholder' => $lang['date'] . '...']]); ?>
                    </div>

                    <div class="d-pad-b">


                        <select class="selectpicker btn white" name="hotel_grade" >
                            <option value="0"><?= $tours_lang['hotel_grade'] ?> </option>
                            <option value="1" <?php
                                    if ($_GET['hotel_grade'] == 1)
                                    {
                                        print 'selected="selected"';
                                    }
                                    ?>>1 <?= $lang['star'] ?></option>
                            <option value="2" <?php
                            if ($_GET['hotel_grade'] == 2)
                            {
                                print 'selected="selected"';
                            }
                            ?>>2 <?= $lang['star'] ?></option>
                            <option value="3" <?php
                                    if ($_GET['hotel_grade'] == 3)
                                    {
                                        print 'selected="selected"';
                                    }
                                    ?>>3 <?= $lang['star'] ?></option>
                            <option value="4" <?php
                                if ($_GET['hotel_grade'] == 4)
                                {
                                    print 'selected="selected"';
                                }
                                ?>>4 <?= $lang['star'] ?></option>
                            <option value="5" <?php
                                    if ($_GET['hotel_grade'] == 5)
                                    {
                                        print 'selected="selected"';
                                    }
                                    ?>>5 <?= $lang['star'] ?></option>
                        </select>
                    </div>

                    <div class="d-gap-b gap-t">
                        <div class="row align-center small">
                            <div class="sd-4">
                                <b class="text-capitalize">
                                <?= $tours_lang['adult'] ?>:</b><br>
                                <?=
                                $form->addInput(['type' => 'number', 'attr' => ['name'  => 'adult',
                                        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '1', 'max'   => '999', 'min'   => '1', 'style' => 'width:60px;']]);
                                ?>
                            </div>
                            <div class="sd-4">
                                <b  class="text-capitalize"><?= $tours_lang['child'] ?>:</b><br> <?=
                        $form->addInput(['type' => 'number', 'attr' => ['name'  => 'child',
                                'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
                                ?>

                            </div>
                            <div class="sd-4">
                                <b  class="text-capitalize"><?= $tours_lang['infant'] ?>:</b><br> <?=
                                $form->addInput(['type' => 'number', 'attr' => ['name'  => 'infant',
                                        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
                                ?>

                            </div></div>
                        <div class="h-pad gap-t error" id="room_capacity_alert" style="opacity:0;"><i class="fa-warning"></i> <?= $tours_lang['rooms_capacity_alert'] ?></div>
                        <div class="full-width pad bordered-t ">
                                <?= $tours_lang['hotel_rooms'] ?></div>
                        <div class="row align-center small">
                            <div class="sd-4 "><strong><?= $tours_lang['single'] ?>: </strong> <br>
    <?=
    $form->addInput(['type' => 'number', 'attr' => ['name'  => 'hotel_room_single',
            'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
    ?>
                            </div>
                            <div class="sd-4"><strong><?= $tours_lang['double'] ?>: </strong><br>
                                <?=
                                $form->addInput(['type' => 'number', 'attr' => ['name'  => 'hotel_room_double',
                                        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
                                ?>
                            </div>
                            <div class="sd-4"><strong><?= $tours_lang['triple'] ?>: </strong> <br>
                                <?=
                                $form->addInput(['type' => 'number', 'attr' => ['name'  => 'hotel_room_triple',
                                        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
                                ?>
                            </div>
                        </div>
                        <div class="full-width bordered-t gap-t"></div>
                    </div>
                    <div class="d-gap-b gap-t ">
                        <div class="row align-center small">
                            <div class="sd-4">
                                <b class="text-capitalize"><?= $lang['days'] ?>: </b><br>
    <?=
    $form->addInput(['type' => 'number', 'attr' => ['name'  => 'days',
            'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => $t['days'], 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
    ?></div>
                            <div class="sd-4">
                                <b class="text-capitalize"><?= $lang['nights'] ?>:</b><br>
    <?=
    $form->addInput(['type' => 'number', 'attr' => ['name'  => 'nights',
            'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => $t['nights'], 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
    ?>
                            </div>
                            <div class="sd-4"></div>
                        </div>
                    </div>
                    <div class="full-width bordered-t pad d-gap-t">
    <?= $lang['robot_verification'] ?></div>
    <?= $form->addCaptcha(['type' => 'captcha', 'attr' => [ 'name' => 'captcha', 'placeholder' => $lang['insert_captcha'] . '...', 'class' => 'btn white full-width']]); ?>
                    <button id="set-desc" class="btn blue full-width d-gap-b gap-t"><i class="fa-reply"></i> <?= $lang['send'] ?></button>
                </div>

            </div>
    </div>
    </form>

<?php } ?>
</div>