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
<div class="body-wrap">
<div class="row">
<div class="md-3 hide-sd">
 <?php
 
 if (isset($left_side)&&is_array($left_side))
 {
   foreach($left_side as $k=>$v)
          {
          print '<div class="left_side">';
		  
		  if ($v[0]=='custom'){$inc=$GOGIES['path'].'/config/blocks/'.$GOGIES['lang'].$v[1].'.php';}
		  elseif (in_array($v[0],$GOGIES['active_mods'])){$inc= $GOGIES['path'].'/modules/'.$v[0].'/gogies/blocks/'.$v[1].'.php';}
		  include $inc;
		  print '</div>';
		  }
   }
 
 ?>

</div>


<div class="md-9  ">
<?php 
 if (isset($center_top)&&is_array($center_top))
 {
   foreach($center_top as $k=>$v)
          {
          print '<div class="center_top">';
		  
		  if ($v[0]=='custom'){$inc=$GOGIES['path'].'/config/blocks/'.$GOGIES['lang'].$v[1].'.php';}
		  elseif (in_array($v[0],$GOGIES['active_mods'])){$inc= $GOGIES['path'].'/modules/'.$v[0].'/gogies/blocks/'.$v[1].'.php';}
		  include $inc;
		  print '</div>';
		  }
   }?>
  <div class="row"><?php
include $contents_file;  ?></div><?php
 if (isset($center_bottom)&&is_array($center_bottom))
 {
   foreach($center_bottom as $k=>$v)
          {
          print '<div class="center_bottom">';
		  
		  if ($v[0]=='custom'){$inc=$GOGIES['path'].'/config/blocks/'.$GOGIES['lang'].$v[1].'.php';}
		  elseif (in_array($v[0],$GOGIES['active_mods'])){$inc= $GOGIES['path'].'/modules/'.$v[0].'/gogies/blocks/'.$v[1].'.php';}
		  include $inc;
		  print '</div>';
		  }
   }
?>
</div>


</div></div>