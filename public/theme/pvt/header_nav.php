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

<nav class="pvt navbar absolute"  id="fixed-nav">

    <div class="nav-toggle"></div>
    <span class="brand-small"><strong><a href="<?= $GOGIES['seo_url'] ?>"><img src="<?= $GOGIES['url'] ?>/<?= $GOGIES['company_logo'] ?>" height="28" alt="<?= $GOGIES['company_name']['en'] ?>"></a></strong></span>
    <ul class="nav-menu">
        <li class="brand nopad"><a href="<?= $GOGIES['seo_url'] ?>"><img src="<?= $GOGIES['url'] ?>/<?= $GOGIES['company_logo'] ?>" height="39" alt="<?= $GOGIES['company_name']['en'] ?>"></a></li>
                <?= $GOGIES['topnav'] ?>

  <?php if (count($GOGIES['currencies']) > 1)
	  {
		?>
    	  <li>
    		<a class="text-uppercase" href="#"><?= $GOGIES['currencies'][$GOGIES['currency']]['name'] ?></a>
    		<ul class="bordered">
			  <?php
			  foreach ($GOGIES['currencies'] as $k => $v)
			  {
				if ($k != $GOGIES['currency'])
				{
				    ?><li ><a class="text-uppercase" href="#" onclick="window.location='<?=$_SERVER['REQUEST_URI'].$GOGIES['url_swtich_char'].'set-currency='.$k;?>'; return false;"><?= $v['name'] ?></a></li><?php
				}
			  }
			  ?>

    		</ul>
    	  </li>
	  <?php } ?>
	  <?php if (count($GOGIES['active_langs']) > 1)
	  {
		?>
    	  <li>
    		<a href="#" class="text-uppercase">  <?= $GOGIES['lang'] ?></a>
    		<ul class="bordered">

			  <?php
			  foreach ($GOGIES['active_langs'] as $k)
			  {
				if ($k != $GOGIES['lang'])
				{
				    ?><li><a class="text-uppercase"  href="<?=$GOGIES['url'].'/'.$k.'/'?>" 
  onclick="window.location='<?= $_SERVER['REQUEST_URI'] . $GOGIES['url_swtich_char'] . 'set_lang=' . $k ?>'; return false;"> <?= $k ?></a></li>
	  <?php }
    }
    ?>

    		</ul>
    	  </li>

<?php } ?>

	  <li class="right">
		<a class="" href="#"> <?=$lang['my_account']?></a>
		<ul class="bordered">
		    <?php if (!$GOGIES['is_user'])
		    {
			  ?>
    		    <li><a class="" href="<?= $GOGIES['seo_url'] ?>users/login/"><i class="fa-lock"></i> <?= $lang['login'] ?></a></li>
    		    <li><a class="" href="<?= $GOGIES['seo_url'] ?>users/register/"><i class="fa-edit"></i> <?= $lang['create_new_account'] ?></a></li>
<?php
}
else
{
    ?>
    		    <li><a class="" href="<?= $GOGIES['seo_url'] ?>users/account/edit-account/"><i class="fa-edit"></i> <?= $lang['edit_account'] ?></a></li>
    		    <li><a class="" href="<?= $GOGIES['url'] ?>?LogOut"><i class="fa-power-off"></i> <?= $lang['logout'] ?></a></li>
<?php }
?>

		</ul></li>

    </ul></nav>

<?= @$GOGIES['bread_crumb'] ?>
<?php
if (!empty($Q[1])){
if (!isset($GOGIES['slider'])or $GOGIES['slider'] == 0)
{
    print '<div id="navbar-placeholder" class="pvt-orange" style="
	height:52px;">&nbsp;</div>';
}
} else {
?>
<div class="relative">

<div id="main-vid" style="height:100vh;  width:100%; top:0; left:0;">
	<div class="full-width absolute bottom align-center d-pad">
		<a href="#" class="white-text" onclick="return false;"  id="main-vid-scroll"><i class="fa-angle-double-down fa-4x animated infinite pulse"></i></a>
		</div>
</div>

</div>
<div id="main-vid-end"></div>

<?php }?>
