//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                              ///
 * @package		    Gogies CMS                                    ///
 * @author		    Gogies Dev Team                               ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                   ///
 * @link		    www.cms.gogies.net                            ///
 * @Version         1.0                                                  ///
 * @Created by      Ahmad Helalat                                        ///
 */ ///
//--------From the end of this line you can edit what ever you want ------///
 $('#search_services').keyup(function(){
        
        var searchText = $(this).val().toLowerCase();
      if (searchText==='')
         {
         	 $('#close-all').trigger('click');
         }
         else
         {
         	$('#open-all').trigger('click');
         }
        $('#tree li').each(function(){
            
            var currentLiText = $(this).text().toLowerCase(),
                showCurrentLi = currentLiText.indexOf(searchText);
            
            if (showCurrentLi!=-1){
                $(this).removeClass('hide');
            }
            else
            {
                $(this).addClass('hide');
            }
            
            
            
        }); 
 });
function send_vender_mail()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  var el=document.getElementById('ajax');
msg=$('#mail_msg').html();
subject=$('#subject').val();
tomail=$('#tomail').val();
from_mail=$('#from_mail').val();
invoice_id=$('#invoice_id').val();
vender_id=$('#vender_id').val();
el.innerHTML='<div class="loader"><span><i class="fa-life-ring spin-it fa-3x"></i></span></div>';
xmlhttp.open('POST','./ajax/send_notification.php?i='+invoice_id+'&v='+vender_id,true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send('msg='+encodeURIComponent(msg)+'&subject='+encodeURIComponent(subject)+'&to='+encodeURIComponent(tomail)+'&from_mail='+encodeURIComponent(from_mail));
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 )
    {
	if (xmlhttp.status==200){
    el.innerHTML=xmlhttp.responseText;
window.location='#send_notification'; 
$('#close_reload').on('click', function(){show_loader(); location.reload();});
	        }
	else{
	el.innerHTML='';
	
	}
    }
  
  }
}

 