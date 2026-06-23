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


<div class="footer-sep"></div>


<div id="footer" class="footer">
<?=$GOGIES['footer_contents']?>
<div class="bordered-b full-width" ></div>
<div class="d-pad hide">
<div  class="md-6 small pad align-center">
&copy; <?=$lang['copy_right']?>&nbsp;<?=@$GOGIES['company_name'][$GOGIES['lang']]?>
</div>
<div  class="md-6 align-center pad" >
	<a class="small" href="http://gogies.net" target="_blank" >Powerd by Gogies CMS</a></div>
</div></div>
<?php 
if (empty($Q[1])){
  $GOGIES['footer_js'].= '
<script src="'.$GOGIES['theme_url'].'/video/jquery.vide.min.js"></script>
<script>
 $(document).ready(function () {
    $(\'#main-vid\').vide(\''.$GOGIES['theme_url'].'/video/Marvelous-Jordan-5.mp4\'); 
 
  });
</script>';
}
$GOGIES['footer_js'].='<script>
$(\'#main-vid-scroll\').on(\'click\',function(){
	    $("body,html").animate(
      {
        scrollTop: $("#main-vid-end").offset().top-50
      },
      800 //speed
    );
});
</script>';