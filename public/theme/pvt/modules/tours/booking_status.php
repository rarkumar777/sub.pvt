<div class="row">
<div class="box">
<div class="row">
<div class="md-9 align-left"><h4><?=$booking_data['desc'];?> </h4></div>
<div class="md-3 align-right"><a href="<?=$GOGIES['seo_url']?>tours/booking/<?=$G['common']->get_access_code($id)?>/#itinerary" class="btn blue text-capitalize"><i class="fa-eye"></i> <?=$lang['details']?></a></div>
</div>
</div>

<div class="box white nopad">
<div class="row">
<div class="row table-head grey hide-sd">
        <div class="md-4"><?= $lang['description'] ?></div>
       <div class="md-3 small"><?= $lang['vender'] ?><br><?=$lang['status']?></div>
        <div class="md-3"><?=$lang['date']?></div>
        
        <div class="md-2 small"><?=$lang['country'] ?><br><?=$lang['category'] ?>  </div>
 
</div>

<?php
while ($r = mysqli_fetch_array($expensessql))
{
$remarks=NULL;
$vender = (!empty($r['company'])) ? $r['company'] : $r['email'];
$vtel=(!empty($r['phone'])) ? $r['phone'] : $r['mobile'];
?>
<div class="row cell">
        <div class="md-4 small"><span class="label charcoal text-capitalize"><?=$lang['qty'] ?> (<?=$r['qty']?>)</span> - <?=$r['description'] .$remarks?></div>
        
        <div class="md-3 small">
        	<div class="modal" id="ven<?=$r['id']?>">
        		<div>
        			<a href="<?=$GOGIES['seo_url'].'tours/booking/'.$Q[3]?>/#close" class="close">&times;</a>
        			<h3><?=$vender?></h3>
        			<div class="row cell">
        				<?=$lang['telephone'].': '.$r['phone'];?>
        			</div>
        			<div class="row cell">
        				<?=$lang['mobile'].': '.$r['mobile'];?>
        			</div>
        		</div>
        	</div>
<a href="<?=$GOGIES['seo_url'].'tours/booking/'.$Q[3]?>/#ven<?=$r['id']?>"><?=$vender?></a>
<br>
<a href="tel:<?=$vtel?>" class="label grey bordered"><i class="fa-phone"></i> <?=$vtel?></a>
    <div class="h-pad-t" ><?=$G['common']->status($r['status']) ?></div>
 
</div>
<div class = "md-3 small"><?=$lang['in'] ?>:&nbsp;&nbsp; <?=$r['service_date']?>
<?php 
    if ($r['service_end_date'] != '0000-00-00')
    {?>
    '<br><?=$lang['out'] ?>: <?=$r['service_end_date'];?>
    <?php } ?>
    <br><?=$r['service_time'] ?></div>

    <div class="md-2 small"><span class="label blue"><?=$GOGIES['countries'][$r['country']] ?></span>
<br>
<span class="label success h-gap-t"><?=$r['category_name']?></span>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
<div class="modal" id="itinerary">
<div style="width:1100px; max-width:100%;">
<a href="<?=$GOGIES['seo_url']?>tours/booking/<?=$G['common']->get_access_code($id)?>/#close" id="close" class="close">&times;</a>
	  <h2><i class="fa-calendar-o"></i> <?= $booking_data['desc'] ?></h2>
	  <div class="row">
				<div class="cell md-6"><?= $tours_lang['travel_date'] ?>: <?= $booking_data['travel_date'] ?></div>
		<div class="cell md-6"><?= $lang['days'] ?>: <?= $booking_data['days'] ?></div>
		<div class="cell md-6"><?= $lang['nights'] ?>: <?= $booking_data['nights'] ?></div>
		<div class="row  grey"><div class="pad grey"><strong><?= $tours_lang['travelers'] ?></strong></div></div>
		<div class="row">
		    <div class="md-4 cell"><?= $tours_lang['adult'] ?>: <?= $booking_data['adult'] ?></div>
		    <div class="md-4 cell"><?= $tours_lang['child'] ?>: <?= $booking_data['child'] ?></div>
		    <div class="md-4 cell"><?= $tours_lang['infant'] ?>: <?= $booking_data['infant'] ?></div>
		</div>

		<div class="row  grey"><div class="pad grey"><strong><?= $tours_lang['hotel_rooms'] ?></strong></div></div>
		<div class="row">
		    <div class="md-3 cell"><?= $tours_lang['single'] ?>: <?= $booking_data['room_single'] ?></div>
		    <div class="md-3 cell"><?= $tours_lang['double'] ?>: <?= $booking_data['rooms_double'] ?></div>
		    <div class="md-3 cell"><?= $tours_lang['twin'] ?>: <?= $booking_data['rooms_twin'] ?></div>
		    <div class="md-3 cell"><?= $tours_lang['triple'] ?>: <?= $booking_data['rooms_triple'] ?></div>
		</div>
		<div class="box info nogap"><strong><?= $lang['notes'] ?></strong><br>
		    <?= nl2br($booking_data['note']); ?>
		</div>
		<h3><?= $tours_lang['itinerary'] ?></h3>
		<div class="h-pad" >
		    <?= html_entity_decode($captured, ENT_COMPAT, 'UTF-8'); ?>
		</div>
	  </div>
	  <div class="align-center"><a href="<?=$GOGIES['seo_url']?>tours/booking/<?=$G['common']->get_access_code($id)?>/#close" class="btn red">&times <?= $lang['close'] ?></a></div>

</div>

</div> 