/////////////////////////////////////
function save_blocks(layoutname){
	var slider=$('#slider').val();
	var list = document.getElementById("left_blocks").getElementsByTagName("li");	left_blocks='';
	for (var i = 0; i < list.length; i++) {	left_blocks=left_blocks+list[i].id+"$-$"+list[i].title+'$:$'; }
	/////////////////////////////
		var list = document.getElementById("right_blocks").getElementsByTagName("li");	right_blocks='';
	for (var i = 0; i < list.length; i++) {	right_blocks=right_blocks+list[i].id+"$-$"+list[i].title+'$:$'; }
	//////////////////////////////
		var list = document.getElementById("center_top_blocks").getElementsByTagName("li");	center_top_blocks='';
	for (var i = 0; i < list.length; i++) {	center_top_blocks=center_top_blocks+list[i].id+"$-$"+list[i].title+'$:$'; }
	///////////////////////////////
		var list = document.getElementById("center_bottom_blocks").getElementsByTagName("li");	center_bottom_blocks='';
	for (var i = 0; i < list.length; i++) {	center_bottom_blocks=center_bottom_blocks+list[i].id+"$-$"+list[i].title+'$:$'; }
    /////////////////////////////////

	var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  document.getElementById('loading').innerHTML ='<div class="loader"><span><i class="fa-life-ring spin-it fa-3x"></i></span></div>';
xmlhttp.onreadystatechange=function()
 {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    //el.style.display='none';
	//el.className='';
    document.getElementById('loading').innerHTML=xmlhttp.responseText;

	//ShowInline(div);
    }
  }
  
xmlhttp.open("POST","ajax/save_blocks.php");
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send('slider='+slider+'&left='+encodeURIComponent(left_blocks)+'&right='+encodeURIComponent(right_blocks)+'&center_top='+encodeURIComponent(center_top_blocks)+'&center_bottom='+encodeURIComponent(center_bottom_blocks)+'&layoutname='+encodeURIComponent(layoutname));
	}
$('.set_icon').each(function(){
	$(this).on('click',function(){
	$(this).parents('.dropdown').children('#toggler').html('<i class="'+$(this).attr('value')+'"></i>');})
	});
function save_mail_template(data)
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
msg=document.getElementById('mail_msg').value;
el.innerHTML='<div class="loader"><span><i class="fa-life-ring spin-it fa-3x"></i></span></div>';
xmlhttp.open('POST',data,true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send('data='+encodeURIComponent(msg));
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 )
    {
	if (xmlhttp.status==200){
    el.innerHTML=xmlhttp.responseText;
window.location='#edit_mail'; 
	        }
	else{
	el.innerHTML='';
	
	}
    }
  
  }
}
