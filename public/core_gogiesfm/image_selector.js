;(function($) {

    $.fn.imageSelector=function (){
    $(this).each(function(){
    	
    if ($(this).parent('div:first').children('#images').attr('id')!='images'){
    $(this).parent('div').append('<div id="images"></div>');
    }
    });
    
    add_image=function(target,input){
    	alert('ll');
    }
    $(this).on('click',function(){
        
        var c_id=$(this).parent('div:first').attr('id');
     
         
       $('#'+c_id).append('<div id="gogiesfm" class="modal"><div style="width:1200px; max-width:100%; "><input type="hidden" value="" id="gogies_img" name="gogies_img"><a id="closefm" class="close" href="#" onclick="$(\'#gozgiesfm\').remove(); return false;">&times;</a><h2>'+c_id+'</h2><iframe src="'+$(this).attr('data-fmurl')+'" width="100%" frameborder="0" style="height:450px; scrolling="auto"></iframe></div><script>$(\'#closefm\').click(function(){ var input_name=$(this).parent(\'div\').parent(\'div\').parent(\'div\').children(\'.image_selector\').attr(\'data-input-name\'); var ts= Math.round((new Date()).getTime() / 1000); var c_id=$(this).parent(\'div\').parent(\'div\').parent(\'div\').children(\'#images\'); var c=\"\'#\"+ts+\"\'\"; var new_image=$(\'#gogies_img\').val();  if (new_image!==\'\'){  $(\'#gogies_img\').val(\'\'); $(c_id).append(\'<div id="\'+ts+\'"  style="width:200px; max-width:100%;" class="pull-left box relative "><input type="hidden" name="\'+input_name+\'[]" value="\'+new_image+\'"><img src="\'+new_image+\'" width="100%" ><span onclick="$(\'+c+\').remove();" class="h-pad absolute top right btn red" ><i class="fa-close"></i></span></div>\'); } $(\'#gogiesfm\').remove(); });</script></div>');
      
      window.location='#gogiesfm';
    });
/*      $('#openfm').click(function () {
    $('#fm_frame').html('<iframe src="/core/gogiesfm/index.php" width="100%" frameborder="0" height="450" scrolling="auto"></iframe> ');
    

    });
var c=' . $c . ';
$('#closefm').click(function(){
c=c+1;
var input_name='images';
var new_image=$('#gogies_im').val();
if (new_image!==''){
$('#gogies_img').val('');
$('#images').append('<div id="'+c+'"  style="width:200px;" class="pull-left box relative "><input type="hidden" name="'+input_name+'[]" value="'+new_image+'"><img src="'+new_image+'" width="100%" ><span onclick="remove_parent('+c+');" class="h-pad absolute top right btn red remove-parent" ><i class="fa-close"></i></span></div>');
}
    
});

function remove_parent(c){
$('#'+c).remove();
}*/
}
}(jQuery));
$(window).ready(function(){
$('.image_selector').each(function(){
    $(this).imageSelector();
});
});