<div id="quotation_contents">

	<div style=" background:url(https://pvt.jo/uploads/filemanager/quotation-header.jpg) center top repeat;">
		<div class="align-center pad"><img src="https://pvt.jo/uploads/filemanager/logo-round-small.png"></div>
		<div class="wrap" style="max-width:1000px;">
			<div id="detail_row" class=" gap-l gap-r shadow-box  nopad  white-text" style="background-color:rgba(0,0,0,0.3)">
				<div class="row">

					<div class="md-8">
						<div class="sd-12 section-title-center">
							<h3 class="align-center"><?= $data['description']; ?> </h3>
						</div>
						<div class="md-6 pad">

							<b><?= $lang['name'] ?>:</b> <?= $data['customer_name'] ?>

						</div>
						<div class="md-6 d-pad">

							<b><?= $tours_lang['travel_date'] ?>:</b> <?= $data['travel_date'] ?>

						</div>
						<div class="md-6 d-pad">

							<b><?= $tours_lang['travelers_number'] ?>:</b> <?= $data['travelers_number'] ?>
						</div>

						<div class="md-6 d-pad">

							<b><?= $lang['reference'] ?>:</b> <?= $data['travelers_number'] ?>

						</div>

						<div class="md-6 d-pad">

							<b><?= $lang['days'] ?>:</b> <?= $data['days'] ?>
						</div>
						<div class="md-6 d-pad">

							<b><?= $lang['nights'] ?>:</b> <?= $data['nights'] ?>

						</div>
						<div class="md-6 d-pad">

							<b><?= $lang['price_per_person'] ?>:</b> <?= $G['invoice']->convert_currency($data['price_per_person'], TRUE) ?>

						</div>
						<div class="md-6 d-pad">

							<b><?= $lang['total'] ?>:</b> <?= $G['invoice']->convert_currency($data['total'], TRUE) ?>

						</div>
					</div>
					<div class="md-4 pad pvt-orange" id="agent_row">
						<div class="sd-12 align-center ">
							<h3 class="section-title-center"><?= $lang['You_have_questions'] ?> ?</h3>
						</div>
						<div class="sd-5 d-pad-t align-center">
							<img class="circle" width="90" src="<?= $data['agent_avatar'] ?>">
						</div>
						<div class="sd-7">
							<div class="pad"><b class="small"><?= $data['agent_first_name'] . ' ' . $data['agent_last_name'] ?></b></div>
							<div class="pad"><b class="small"><a href="mailto:<?= $data['agent_mail'] ?>"><i class="fa-envelope-o"></i> <?= $data['agent_mail'] ?></a></b></div>
							<?php if (!empty($data['agent_mobile'])) { ?>
								<div class="pad"><b class="small"><a href="tel:<?= $data['agent_mobile'] ?>"><i class="fa-whatsapp"></i> <?= $data['agent_mobile'] ?></b></a></div>
							<?php } ?>
							<?php if (!empty($data['agent_phone'])) { ?>
								<div class="pad"><b class="small"><a href="tel:<?= $data['agent_phone'] ?>"><i class="fa-phone"></i> <?= $data['agent_phone'] ?></a></b></div>
							<?php } ?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
<div class="wrap" style="max-width:1000px;">
	<?php

	while ($days_res = mysqli_fetch_array($days_sql)) {
		$days_fix = $days_res['day_number'] - 1;
		$current_date = date('Y-m-d', strtotime($data['travel_date'] . ' + ' . $days_fix . ' days'));
		$included = unserialize($days_res['included'], ['allowed_classes' => FALSE]);
		$include_data = NULL;
		if (!empty($included)) {
			foreach ($included as $inc_id => $inc_name) {
				$include_data .= '<div class="row cell"><i class="fa-check success-text"></i> ' . $inc_name . '</div>';
			}
		}
		$excluded = unserialize($days_res['excluded'], ['allowed_classes' => FALSE]);
		$exclude_data = NULL;
		if (!empty($excluded)) {
			foreach ($excluded as $exc_id => $exc_name) {
				$exclude_data .= '<div class="row cell"><i class="fa-close danger-text"></i> ' . $exc_name . '</div>';
			}
		}

		$images = unserialize($days_res['images'], ['allowed_classes' => FALSE]);
		$images_data = NULL;
		if (!empty($images)) {
				foreach ($images as $img) {
						$images_data .= '<div class="pull-left box h-pad">
	  	       	       <a href="javascript:void(0);" >	<img class="img" src="' . $img . '" width="150px;"></a>
	  	       	       	</div>';

						$all_images .= '<li>
<img class="slider-img animated fadeIn" src="' . $img . '">
<div class="caption animated fadeIn">
<h3><span>' . $lang['day'] . ' ' . $days_res['day_number'] . '( ' . $current_date . ' )</span></h3>
 </div>

</li>';
					}
			}
		?>
		<div class="pad gap shadow-box white nopad day-data">
			<div class="row">
				<div class="md-2 align-center d-pad-t day-left-col">
					<div class="center-block inline-block">
						<span class="pull-left"><i class="fa-calendar orange-text  fa-2x"></i></span>
						<span class="h-pad-l pull-left text-uppercase blue-text" style="font-weight:700; line-height:0.8; font-size:25px; font-family:roman;"><?= $lang['day'] ?><br>
							<span class="small pull-left full-width"><b><?= $current_date ?></b></span>
						</span>
						<span class="pull-left orange-text h-pad-l" style="font-weight:900; line-height:0.8; font-size:38px; font-family:roman;"><?= $days_res['day_number'] ?></span>

					</div>
				</div>
				<div class="md-10 bordered-l pad-t d-pad-b"><?= html_entity_decode($days_res['contents']) ?>
					<div class="row">
						<?php if (!empty($include_data)) { ?>
							<div class="md-6 pad">
								<div class="row cell align-center"><b><?= $tours_lang['included'] ?></b></div>
								<?= $include_data ?>

							</div>

						<?php } ?>
						<?php if (!empty($exclude_data)) { ?>
							<div class="md-6 pad">
								<div class="row cell align-center"><b><?= $tours_lang['excluded'] ?></b></div>
								<?= $exclude_data ?>

							</div>

						<?php } ?>

					</div>
					<div class="row">
						<?= $images_data ?>

					</div>

				</div>

			</div>
		</div>
	<?php } ?>
</div>
</div>
</div>
<div class="modal" id="show_img">
	<div><a style="z-index:99999;" href="<?= $_SERVER['REQUEST_URI'] ?>#close" class="absolute top right red h-pad-l h-pad-r fa-2x"><b>&times;</b></a>
		<div class="slider" data-autoplay="false" data-delay="4000" data-indicators="true" data-arrows="true">

			<ul>
				<?= $all_images ?>


			</ul>
		</div>
	</div>
	<script>
		window.addEventListener('DOMContentLoaded', function() {
			//		var contents=$('#quotation_contents').html();
			$('#agent_row').height($('#detail_row').height());
			$('.day-left-col').each(function() {
				var parent_height = $(this).parent().closest('.day-data').height() / 3;

				$(this).css('padding-top', parent_height);
			});
			$('#fixed-nav').addClass('hide_on_print');
			$('#navbar-placeholder').addClass('hide_on_print');
			$('#main-sidebar').addClass('hide_on_print');
			$('#footer').addClass('hide_on_print');
			$('.img').on('click', function() {
				var src_image = $(this).attr('src');
				$('#selected_img').attr('src', src_image);
				window.location = '<?= $_SERVER['REQUEST_URI'] ?>#show_img';
			})
		});
	</script>