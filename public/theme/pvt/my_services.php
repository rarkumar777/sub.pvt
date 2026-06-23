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
if (!defined('gogies')){print 'Direct script access is not allowed'; exit;}?>
  
    <h3><i class="fa-cubes"></i> <?=$lang['my_services']?></h3>
    <div class="row">
    <div class="row">
        <div class="pull-left"><h2 class="h-pad"><i class="fa-sliders"></i></h2></div>
        <div class="pull-left"><?=$status_filter_input?></div>
        <div class="pull-left"><?=$payment_status_filter_input?></div>
    </div>
<div class="bordered row gap-t">
    <div class="row table-head grey hide-sd">
        <div class="md-3"><?=$lang['description']?></div>
        <div class="md-1"><?=$lang['qty']?></div>
        <div class="md-2 small"><?=$lang['total']?> </div>
       <div class="md-2 small"><?=$lang['status']?></div>
        <div class="md-2"><?=$lang['date']?></div>
		 
        <div class="md-2 small"><?=$lang['country']?><br><?=$lang['category']?> </div>
 
    </div>
    <?php while ($res=mysqli_fetch_array($service_data_sql)){
    if ($res['service_end_date'] != '0000-00-00')
    {
           $out=$lang['out'].': '.$res['service_end_date'];
    }
    else
    {$out=NULL;}
    ?>

    <div class="row cell">
        <div class="md-3 small"><div class="bordered-b"><?=$res['desc']?></div><?=$res['description']?></div>
        <div class="md-1"><?=$res['qty']?></div>
        <div class="md-2 small"><?=$G['invoice']->convert_currency($res['cost'], TRUE).'<br>'.
        $G['invoice']->get_invoice_status($res['payment_status']);?>
        </div>
        <div class="md-2 small"><?=$G['common']->status($res['status'])?></div>
        <div class="md-2 small"><?='<div>'.$res['service_time'].'</div>'.$lang['in'].': '.$res['service_date']?>
        <br>
        <?=$out?>
        </div>
        <div class="md-2 small"><span class="label blue"><?=$GOGIES['countries'][$res['country']]?></span>
        <br>
       <span class="label success h-gap-t"><?=$GOGIES['services']['categories'][$res['category']]?></span>
        </div>
    </div>
    <?php }?>
    <div class="pad align-center"><?=$pagintation?></div>
    </div>
