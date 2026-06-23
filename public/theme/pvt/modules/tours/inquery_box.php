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
<form method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded" action="<?= $GOGIES['seo_url'] ?>tours/inquery/<?= $t['id'] ?>/">
    <div  class="section-title pad random-transition"><i class="fa-edit"></i>  <?= $tours_lang['quotation_form'] ?></div>





    <div class="row ">



        <div class="h-pad-b  required"><?= $G['form']->addInput(['type' => 'text', 'attr' => [ 'name' => 'name', 'placeholder' => $lang['name'] . '...', 'class' => 'btn white']]); ?>
        </div>
        <div class="row">
            <div class="sd-6 h-pad-b  required"><?= $G['form']->addInput(['type' => 'text', 'attr' => [ 'name' => 'email', 'placeholder' => $lang['email'] . '...', 'class' => 'btn white']]); ?>
            </div>
            <div class="sd-6 h-pad-b"><?= $G['form']->addInput(['type' => 'text', 'attr' => [ 'name' => 'telephone', 'placeholder' => $lang['telephone'] . '...', 'class' => 'btn white']]); ?>
            </div>
        </div>
        <div class="row">
            <div class=" sd-6 h-pad-b"><?= $G['form']->addInput(['type' => 'text', 'attr' => ['class' => 'btn white datepicker', 'data-disable-dates' => 'past', 'name' => 'date', 'placeholder' => $lang['date'] . '...']]); ?>
            </div>

            <div class="sd-6 h-pad-b">


                <select class="selectpicker btn white" name="hotel_grade" >
                    <option value="0"><?= $tours_lang['hotel_grade'] ?> </option>
                    <option value="1" <?php if ($_GET['hotel_grade'] == 1)
{
    print 'selected="selected"';
} ?>>1 <?= $lang['star'] ?></option>
                    <option value="2" <?php if ($_GET['hotel_grade'] == 2)
{
    print 'selected="selected"';
} ?>>2 <?= $lang['star'] ?></option>
                    <option value="3" <?php if ($_GET['hotel_grade'] == 3)
{
    print 'selected="selected"';
} ?>>3 <?= $lang['star'] ?></option>
                    <option value="4" <?php if ($_GET['hotel_grade'] == 4)
                        {
                            print 'selected="selected"';
                        } ?>>4 <?= $lang['star'] ?></option>
                    <option value="5" <?php if ($_GET['hotel_grade'] == 5)
                        {
                            print 'selected="selected"';
                        } ?>>5 <?= $lang['star'] ?></option>
                </select>
            </div>
        </div>
        <div class=" h-gap-t">
            <div class="full-width h-pad bordered-t ">
                <?= $tours_lang['participant'] ?></div>
            <div class="row align-center small">
                <div class="sd-4">

                    <b class="text-capitalize">
                    <?= $tours_lang['adult'] ?>:</b><br>
<?= $G['form']->addInput(['type' => 'number', 'attr' => ['name'  => 'adult',
        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '1', 'max'   => '999', 'min'   => '1', 'style' => 'width:60px;']]);
?>
                </div>
                <div class="sd-4">
                    <b  class="text-capitalize"><?= $tours_lang['child'] ?>:</b><br> <?= $G['form']->addInput(['type' => 'number', 'attr' => ['name'  => 'child',
        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
?>

                </div>
                <div class="sd-4">
                    <b  class="text-capitalize"><?= $tours_lang['infant'] ?>:</b><br> <?= $G['form']->addInput(['type' => 'number', 'attr' => ['name'  => 'infant',
                            'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
?>

                </div></div>
            <div class="full-width h-pad bordered-t h-gap-t ">
                <?= $tours_lang['hotel_rooms'] ?></div>
            <div class="row align-center small">
                <div class="sd-4 "><strong><?= $tours_lang['single'] ?>: </strong> <br>
                <?= $G['form']->addInput(['type' => 'number', 'attr' => ['name'  => 'hotel_room_single',
                        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
                ?>
                </div>
                <div class="sd-4"><strong><?= $tours_lang['double'] ?>: </strong><br>
                <?= $G['form']->addInput(['type' => 'number', 'attr' => ['name'  => 'hotel_room_double',
                        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
                ?>
                </div>
                <div class="sd-4"><strong><?= $tours_lang['triple'] ?>: </strong> <br>
<?= $G['form']->addInput(['type' => 'number', 'attr' => ['name'  => 'hotel_room_triple',
        'class' => 'btn white h-pad h-gap-t', 'type'  => 'number', 'value' => '0', 'max'   => '999', 'min'   => '0', 'style' => 'width:60px;']]);
?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="h-pad">
<?= $G['form']->addTextarea(['type' => 'textarea', 'attr' => ['name' => 'desc', 'class' => 'full-width', 'value' => NULL, 'placeholder' => $lang['notes'] . '...']]); ?>
            </div>
            <div class="full-width bordered-t h-pad h-gap-t">
<?= $lang['robot_verification'] ?></div>
            <div class="sd-8">

<?= $G['form']->addCaptcha(['type' => 'captcha', 'attr' => [ 'name' => 'captcha', 'placeholder' => $lang['insert_captcha'] . '...', 'class' => 'btn white full-width']]); ?>
            </div>
            <div class="sd-4">
                <button id="set-desc" class="btn blue full-width "><i class="fa-reply"></i> <?= $lang['send'] ?></button>

<?= $G['form']->addInput(['type' => 'hidden', 'attr' => ['name' => 'days', 'type' => 'number', 'value' => $t['days']]]); ?>
<?= $G['form']->addInput(['type' => 'hidden', 'attr' => ['name' => 'nights', 'type' => 'number', 'value' => $t['nights']]]); ?>

            </div>
        </div>
    </div>
</div>
</form>

