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
<div id="main-contents">
    <div class="full-wdith grey">
        <div class="wrap" style="max-width: 900px;" >
            <div class="row nogap">

                <div class="white d-gap-t shadow-box ">

                    <?php
                    if (isset($GOGIES['payment_msg']))
                    {
                        print $GOGIES['payment_msg'];
                    } else
                    {
                        ?>
                        <div class="sd-12 h-pad pvt-orange"> <h2 class="section-title-center "><i class="fa-bar-chart"></i> <?= $lang['invoice'] ?> </h2>
                            <div class="absolute top right"><?= $inv_status ?></div></div>

                        <div class="row">
                            <?php
                            if (isset($GOGIES['msg']))
                            {
                                print $GOGIES['msg'];
                            }
                            ?>

                            <div class="row white">

                                <div class="sd-4 ">
                                   <div class="shadow-box pad" id="from-box">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align:middle;"><img style="display: inline;" src="https://pvt.jo/uploads/filemanager/logo-round-small.png" width="110" border="0" hspace="10" vspace="10" /></td>
                                                <td style="vertical-align:middle;"> <b><?= $lang['from']; ?></b>                                               <br>      <?= $GOGIES['company_name'][$GOGIES['lang']]; ?>
                                                    <?= html_entity_decode($GOGIES['company_address'][$GOGIES['lang']]); ?>

                                                    <?= $GOGIES['company_email'] ?><br>


                                                        T: <?= $GOGIES['company_telephone'] ?> <br> F: <?= $GOGIES['company_fax'] ?>
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
</div>


                                </div>
                                <div class="sd-4">

                                    <div id="to-box" class="shadow-box pad"><b class="text-capitalize"><?= $lang['to']; ?></b>
                                        <br>
                                        <?= $b['last_name'] . ' ' . $b['first_name']; ?>

                                        <div> <?= $b['address'] ?></div>

                                        <?= $b['email'] ?>
                                        <br />
                                        T: <?= $b['phone'] ?> - F: <?= $b['fax'] ?>
                                    </div>
                                </div>
                                <div class="sd-4">

                                    <div class="cell"><b><?= $lang['invoice'] ?> #:</b> <?= $b['id'] ?></div>
                                    <div class="cell"><b><?= $lang['date'] ?>:</b> <?= $b['date'] ?></div>
                                    <div class="cell"><b><?= $lang['due_to_date'] ?></b>: <?= $b['due_to_date'] ?></div>
                                </div>
                            </div>


                        </div>
                        <div >
                            <h3 class="pvt-orange"><i class="fa-th"></i> <?= $b['desc'] ?></h3>




                            <?= $items_table ?>
                            <div class="row  d-pad-t">
                            	   <?php
                                    if (0 < $discount)
                                    {
                                        ?>
                            	<div class="sd-12 grey"><div class="md-8 pad"><?=$b['discount_description']?></div><div class="md-4"><div  class="row cell capitalize"><?= $lang['discount'] ?>: <b class="success-text"><?= $discount ?></b>
                                        </div>
</div></div> 
                            	                                    <?php } ?>
                                <div class="md-4 pull-right">
                                 

                                        
                                    <div  class="row cell capitalize"><?= $lang['total'] ?>: <?= $total ?></div>
                                    <?php
                                    if (0 < $tax)
                                    {
                                        ?>
                                        <div  class="row cell capitalize"><?= $lang['tax'] ?>: <?= $tax ?></div>
                                    <?php } ?>
                                    <div  class="row cell capitalize"><?= $lang['grand_total'] ?>: <b><?= $grand_total ?></b></div>
                                  <?php if ($b['total_paid']>0){?>
                                  <div  class="row cell success capitalize"><?= $lang['paid'] ?>: <b><?= $G['invoice']->convert_currency($b['total_paid'],TRUE) ?></b></div>
                                  
                                                                    <?php if ($b['total_paid']<$b['total']){?>
                                  <div  class="row cell alert capitalize"><?= $lang['payable'] ?>: <b><?= $G['invoice']->convert_currency(($b['total']-$b['total_paid']),TRUE) ?></b></div>
                                  <?php
                                                                    }
                                  }?>
                                  
                                </div>
                            </div>

                            <?php
                            if (isset($GOGIES['offline_payments']['details']))
                            {
                                ?>
                                <div class="d-pad-t d-pad-b">
                                    <h3 class="pvt-orange"><i class="fa-bank"></i> <?= $lang['offline_payments'] ?></h3>
                                    <div class="pad">
                                        <pre><?= $GOGIES['offline_payments']['details'] ?></pre>
                                    </div>
                                </div>
                            <?php } ?>



                        <?php } ?></div>
                    <div class="white bordered d-gap-t hide_on_print">
                        <div class="d-pad  grey"><b><i class="fa-bar-chart"></i> <?= $lang['select_payment_method'] ?></b></div>
                        <div class="row">
                            <div class="sd-12 pad"><?= $pay_methods ?></div>
                        </div></div>
                </div></div></div>
        <br>
        <br>
    </div>

</div>
<script>
    window.addEventListener('DOMContentLoaded', function () {
    	$('#to-box').height($('#from-box').height());
        var contents = $('#quotation_contents').html();

        $('#fixed-nav').attr('class', 'hide');
        $('#navbar-placeholder').attr('class', 'hide');
       // $('#main-sidebar').attr('class', 'hide');
        $('#footer').attr('class', 'hide');
        $('.img').on('click', function () {
            var src_image = $(this).attr('src');
            $('#selected_img').attr('src', src_image);
            window.location = '<?= $_SERVER['REQUEST_URI'] ?>#show_img';
        })
    });
</script>