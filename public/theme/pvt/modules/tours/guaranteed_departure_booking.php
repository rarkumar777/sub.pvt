<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                          ///
 * @link		    www.cms.gogies.net                                    ///
 * @Version         1.0                                                   ///
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
if (!defined('gogies')) {
	print 'Direct script access is not allowed';
	exit;
}
?>
<div class="wrap" style="max-width:1000px;">

	<div class=" gap-t breadcrumb full-width hide-sd">
		<a class="grey" href="<?= $GOGIES['seo_url'] ?>"><i class="fa-home"></i> <?= $lang['home'] ?></a>
		<a href="<?= $GOGIES['seo_url'] ?>tours/" class="grey"><i class="fa-play"></i> <?= $tours_lang['tours'] ?></a>
		<a href="<?= $GOGIES['seo_url'] ?>tours/<?= strtolower($tour_start_country) . '/' . $t['url'] . '/' ?>" onClick="" class="active"><i class="fa-play"></i> <?= $t['title'] ?></a>

	</div>
	<!--/////////////////////////////// !-->
	<div class="row">
		<h3 class="section-title"><?= $t['title'] ?></h3>
		<div class="md-7 pad">
			<?php if ($booking_completed) { ?>
				<div class="row">
					<div class="box success align-center">
						<i class="fa-check-circle fa-3x"></i>
						<h4>
							<?= $tours_lang['thank_you_for_your_booking'] ?>
						</h4>
					</div>
					<div class="box white align-center nopad">
						<div class="alert box nogap">
							<b><?= $tours_lang['make_payment_to_confirm_your_booking'] ?></b>
						</div>
						<div class="pad">
							<?= $lang['full_payment'] ?> : <?= $G['invoice']->convert_currency($total, TRUE) ?>
							<?= $partly_payment ?>
						</div>
						<div class="pad">
							<a href="<?= $GOGIES['seo_url'] ?>/invoice/<?= $new_invoice ?>/" class="btn blue"><i class="fa-info"></i> <?= $lang['make_payment'] ?></a>
						</div>
					</div>
				</div>
			<?php } else { ?>

				<?= $error_msg; ?>

				<form name="booking_form" action="<?= $GOGIES['seo_url'] ?>tours/guaranteed-departure-booking/<?= $t['id'] ?>/" method="post">
					<div class="<?= $first_step_class ?>">
						<div class="row">
							<div class="row cell">
								<div class="md-3"><b><?= $tours_lang['hotel_grade'] ?></b></div>
								<div class="md-9">
									<?= $G['form']->addSelect(['type' => 'select', 'attr' => ['name' => 'hotel_grade', 'style' => 'width:250px;'], 'options' => $hotel_bases]) ?>
								</div>
							</div>

							<div class="row cell">
								<div class="md-3"><b><?= $tours_lang['travelers'] ?></b></div>
								<div class="md-9">
									<div class="md-6">
										<?= $tours_lang['adult'] ?> :<?= $G['form']->addSelect(['type' => 'select', 'attr' => ['name' => 'adult', 'style' => 'width:100px;'], 'options' => $adult_options]) ?>
									</div>
									<div class="md-6">
										<?= $tours_lang['child'] ?> :<?= $G['form']->addSelect(['type' => 'select', 'attr' => ['name' => 'child', 'style' => 'width:100px;'], 'options' => $child_options]) ?>
									</div>
								</div>
							</div>


						</div>
						<!--hotel rooms-->
						<div class="<?= $hotel_rooms_class ?>" id="hotel_rooms">
							<div class="row cell">
								<strong><?= $tours_lang['hotel_rooms'] ?>:</strong>
								<span id="rooms_alert" class="hide"><span class="box error nogap h-pad"><b><?= $tours_lang['rooms_capacity_alert'] ?></b></span></span>
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
										<?= $token ?>
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
						</div>
						<!--end rooms-->
					</div>
					<div class="<?= $second_step_class ?>">
						<div class="bordered">
							<div class="grey h-pad">
								<h4><i class="fa-info-circle"></i> <?= $tours_lang['confirm_your_booking_details'] ?></h4>
							</div>
							<div class="row cell"><b><?= $t['title'] ?></b></div>
							<div class="row cell"><?= $lang['days'] ?>: <b><?= $t['days'] ?></b></div>
							<div class="row cell"><?= $lang['nights'] ?>: <b><?= $t['nights'] ?></b></div>
							<div class="align-center <?= $hotel_rooms_class ?>">
								<div class="row cell grey">
									<strong><?= $tours_lang['hotel_rooms'] ?>:</strong>
								</div>
								<div class="row cell">
									<div class="sd-3"><?= $tours_lang['single'] ?></div>
									<div class="sd-3"><?= $tours_lang['double'] ?></div>
									<div class="sd-2"><?= $tours_lang['twin'] ?></div>
									<div class="sd-2"><?= $tours_lang['triple'] ?></div>
									<div class="sd-2"><?= $tours_lang['quad'] ?></div>
								</div>

								<div class="row cell">
									<div class="sd-3"><?= intval($_POST['single']) ?></div>
									<div class="sd-3"><?= intval($_POST['double']) ?></div>
									<div class="sd-2"><?= intval($_POST['twin']) ?></div>
									<div class="sd-2"><?= intval($_POST['triple']) ?></div>
									<div class="sd-2"><?= intval($_POST['quad']) ?></div>
								</div>


								<div class="row cell"><?= $tours_lang['hotel_grade'] . ' ' . $G['common']->get_item_rating_alt($hotel_grade, 'fa-star'); ?></div>
							</div>
						</div>
					</div>
					<?php
					///////////////////////////////////////////////////////////////////////////////
					if (isset($_POST['first_step'])) {
						if (!$GOGIES['is_user'] && empty($GOGIES['errors'])) {
							print '</form>';
							require $GOGIES['theme_path'] . '/login.php';
						} elseif (empty($GOGIES['errors'])) { ?>
							<div class="sd-12 align-center pad">
								<?= $G['form']->addSubmit(['type' => 'submit', 'attr' => [
									'name' => 'first_step',
									'class' => 'btn blue', 'type' => 'number', 'value' => '<i class="fa-calendar-o"></i> ' . $tours_lang['place_booking']
								]]); ?>
								<input type="hidden" name="place_booking" value="1">
								<button class="btn green" name="reset"><i class="fa-edit"></i> <?= $tours_lang['edit_booking_details'] ?></button>

							</div>
						</form>
					<?php } else { ?>
						<div class="sd-12 align-center pad">
							<?= $G['form']->addSubmit(['type' => 'submit', 'attr' => [
								'name' => 'first_step',
								'class' => 'btn blue', 'type' => 'number', 'value' => '<i class="fa-calendar-o"></i> ' . $tours_lang['book_tour']
							]]); ?>
						</div>
						</form>
					<?php  }
			} else { ?>
					<div class="sd-12 align-center pad">
						<?= $G['form']->addSubmit(['type' => 'submit', 'attr' => [
							'name' => 'first_step',
							'class' => 'btn blue', 'type' => 'number', 'value' => '<i class="fa-calendar-o"></i> ' . $tours_lang['book_tour']
						]]); ?>
					</div>
					</form>

				<?php   }
		}
		?>
		</div>
		<div class="md-5 grey">
			<div class="row">
				<h3 class="section-title"><i class="fa-calendar"></i> <?= $tours_lang['travel_date'] ?></h3>
			</div>
			<div class="row cell"><?= $lang['from'] ?>: <?= $t['date'] ?></div>
			<div class="row cell"><?= $lang['to'] ?>: <?= $dep_date ?></div>
			<div class="row cell"><?= $tours_lang['adult'] ?>: <span id="adult_span"><?= $adult_count ?> &times <?= $G['invoice']->convert_currency($adult_price, TRUE); ?> = <?= $G['invoice']->convert_currency($adult_total, TRUE); ?></span></div>
			<div class="row cell"><?= $tours_lang['child'] ?>: <span id="child_span"><?= $child_count ?> &times <?= $G['invoice']->convert_currency($child_price, TRUE); ?> = <?= $G['invoice']->convert_currency($child_total, TRUE); ?></span></div>
			<div class="row cell" id="single_supplement_fee"><?= $tours_lang['single_supplement_fee'] ?>: <span id="single_supplement_span"><?= $single_rooms ?> &times; <?= $G['invoice']->convert_currency($single_supplement_fee, TRUE); ?> = <?= $G['invoice']->convert_currency($single_supplement_total, TRUE) ?> </span></div>
			<div class="row cell"><?= $lang['total'] ?>: <span id="total_span"><?= $G['invoice']->convert_currency($total, TRUE); ?></span></div>
			<div class="pad">
				<img src="<?= $t['image'] ?>" class="full-width">
			</div>
		</div>
	</div>
	<!--/////////////////////////////// !-->

</div>
