$("input[name=date]").attr('data-disable-dates','past');

//////////////////////////////////////////////////

function calc_total(){
var current_pricing_groups=pricing_groups;
var current_pricing_bases=pricing_bases;
//// date & season/////
var current_date=$("input[name=date]").val();
if (current_date===''){return;}
selected_date=new Date(current_date+' 00:00');
selected_date=Date.parse(selected_date);
season_type=null;
$.each(global_seasons , function( index,val) {
date_from=new Date(val['from']+' 00:00');
date_from=Date.parse(date_from);
date_to=new Date(val['to']+' 00:00');
date_to=Date.parse(date_to);
if (selected_date>=date_from && selected_date<=date_to){season_type=val['type'];}
});
if (season_type=='H'){
var current_pricing_groups=pricing_groups_high;
var current_pricing_bases=pricing_bases_high;    
}
if (season_type=='L'){
var current_pricing_groups=pricing_groups_low;
var current_pricing_bases=pricing_bases_low;    
}

///// get travelers number ////////////
var adult_count=$('#adult').val()*1;
var child_count=$('#child').val()*1;
var infant_count=$('#infant').val()*1;
var travelers_count=adult_count+child_count;
var price_base=$('#price_base').val();


////////////get adult group pricing///////////////
var adult_price=current_pricing_bases[price_base]['price'];
$.each(current_pricing_groups[price_base] , function( index,val) {
	
if (travelers_count>=index){adult_price=val['adult'];}
});
var adult_price=adult_price*currency_rate;
var adult_total=adult_price*adult_count;
$('#adult_price').html(adult_price.toFixed(2)+' '+currency_symbol);
$('#adult_total').html(adult_total.toFixed(2)+' '+currency_symbol); 

////////////get child group pricing///////////////
var child_price=current_pricing_bases[price_base]['price'];
$.each(current_pricing_groups[price_base] , function( index,val) {
	
if (travelers_count>=index){child_price=val['child'];}
});
var child_price=child_price*currency_rate;
var child_total=child_price*child_count;
if (child_count>0){
$('#child_price').html(child_price.toFixed(2)+' '+currency_symbol);
$('#child_total').html(child_total.toFixed(2)+' '+currency_symbol);
}
else
{
    $('#child_price').html('0');
$('#child_total').html('0');
}

////////////get infant group pricing///////////////
var infant_price=current_pricing_bases[price_base]['price'];
$.each(current_pricing_groups[price_base] , function( index,val) {
	
if (travelers_count>=index){infant_price=val['infant'];}
});
var infant_price=infant_price*currency_rate;
var infant_total=infant_price*infant_count;
if (infant_count>0){
$('#infant_price').html(infant_price.toFixed(2)+' '+currency_symbol);
$('#infant_total').html(infant_total.toFixed(2)+' '+currency_symbol);
}
else
{
    $('#infant_price').html('0');
$('#infant_total').html('0');
}
if (price_base>0){
	$('#hotel_rooms').css('opacity',1);
/////////////////////room validate///////////////////
 var double_count=$('#double').val()*2;
 var twin_count=$('#twin').val()*2;
 var triple_count=$('#triple').val()*3;
 var single_count=$('#single').val()*1;
 var quad_count=$('#quad').val()*4;
 var rooms_capacity=single_count+double_count+triple_count+twin_count+quad_count;
 var persons_count=adult_count + child_count ;
 if (persons_count!==rooms_capacity){ $('#rooms_alert').attr('class','animated fadeIn'); }
 else{$('#rooms_alert').attr('class','hide');}
///////////////single supplement//////////
if (persons_count!=1 && single_count >0)
   {
   var single_supplement=current_pricing_bases[price_base]['single_supplement']*currency_rate;
    single_supplement=single_supplement*single_count;
	
   
	   }
	   else
	   {
		   single_supplement=0
	   }

}
 //////////////
 if (price_base==0){$('#hotel_rooms').css('opacity',0.3); single_supplement=0;} 
$('#single_supplement').html(single_supplement.toFixed(2)+' '+currency_symbol);
var total=adult_total+infant_total+child_total+single_supplement;
$('#total').html(total.toFixed(2)+' '+currency_symbol);


}
//calc_total(); 
 $('#adult').change(function(){calc_total();});
 $('#child').change(function(){calc_total();});
 $('#infant').change(function(){calc_total(); });
 $('#single').change(function(){calc_total(); });
 $('#double').change(function(){calc_total(); });
 $('#triple').change(function(){calc_total(); });
 $('#twin').change(function(){calc_total(); });
  $('#quad').change(function(){calc_total(); });
  $("input[name=date]").change(function(){calc_total();});
$('#price_base').change(function(){calc_total(); });
