jQuery(document).ready(function($) {
     jQuery('.reliable-pagination').click(function() 
     { 
         var pageNum = jQuery(this).attr('data-pageNum');
         var perPage = jQuery(this).attr('data-perPage');
         var newPage = parseInt(pageNum) + parseInt(perPage);
         jQuery(this).attr('data-pageNum', newPage); 
            $fragment_refresh = {
                    url: rt_vars.rt_urlajax,
                    type: 'POST',
                    data: { action: 'rt_fndtrad_page', ajaxed: 1, pageNum:  pageNum, rtdata: jQuery('#src_rtdata').html(), city: jQuery('#src_rtcty').html(), postcode: jQuery('#src_postcode').html()},
                    success: function( data ) {  if (data !=0) 
                        { 
                            
                            jQuery('#pageResponse').append(data); 
                            jQuery('.fndcheckout').click(function(){ scrollTradDesc(this); });
                        } /*else { jQuery('.reliable-pagination').remove();* }*/  
                       
                    }
            };
            jQuery.ajax( $fragment_refresh );
            
     });
     jQuery('.delete-trade').click(function(){
            var trade_id = jQuery(this).parent().attr('id');
            var rawTradeID = trade_id;
            trade_id = trade_id.replace('trade_', '');
            var user_id = jQuery(this).parent().attr('data-userid');
            if (isNaN(trade_id)) return;
            if (isNaN(user_id)) return;
             $fragment_refresh = {
                    url: rt_vars.rt_urlajax,
                    type: 'POST',
                    data: { action: 'rt_delTrade', userid: user_id, trade_id: trade_id},
                    success: function( data ) { jQuery('#' + rawTradeID).remove(); }
            };
            jQuery.ajax( $fragment_refresh );
     });
     
     jQuery('.fndcheckout').click(function(){ scrollTradDesc(this); });
     jQuery('.deleteImage').click(function(){ deleteImageLayer( jQuery(this).parent().attr('id')); });
    /*  jQuery('table.listOfRegions tr td input').css({'background' : '#ff0', 'padding' : '20px'});*/
     jQuery('table.listOfRegions tr td input.allcities').change(function() {   
				if (jQuery(this).attr('checked')) {  
					jQuery(this).parent().parent().siblings('tr').find('td').each(function(){
						jQuery(this).children('input').attr('checked', true);
					});
				} else {
					jQuery(this).parent().parent().siblings('tr').find('td').each(function(){
						jQuery(this).children('input').attr('checked', false);
					});
				};
			});
                        
      jQuery('.form2 input[type="text"].RTsearchInput').keyup(function () {
        _appUrl = jQuery(this).val();
       getTradies(_appUrl);


    });
    jQuery('.form2 input[type="text"].city').keyup(function () {
        _appUrl = jQuery(this).val();
        getSuburbs(_appUrl);
    });
  // jQuery('.gradientFrame').click(function() { alert( jQuery(this).children().attr('id') ); } );
 
    jQuery('.selectLoc').change(function() 
    {    
        var state = jQuery( "select.selectLoc option:selected" ).text();
         if(! state) return;
        jQuery('#showSuburbs').html("<img style='height: 20px;' src='"+ rt_vars.rt_url + "/wp-content/plugins/reliatabletradie/assets/images/small_loading.gif' />");
        //alert ( jQuery( "select.selectLoc option:selected" ).text() );
        
         $fragment_refresh = {
		url: rt_vars.rt_urlajax,
		type: 'POST',
		data: { action: 'rt_getSubSec', state: state},
		success: function( data ) 
                {  jQuery('#showSuburbs').html('<select class="suburbchoose"><option></option></select><span class="locButton" id="addSub" autocomplete="off">Choose</span>');
                    suburbs = JSON.parse(data); for (sub in suburbs) { jQuery('.suburbchoose').append("<option value='"+ suburbs[sub].id +"'>" + suburbs[sub].suburb + "</option>"); } 
                    jQuery('#addSub').click(function()
                    {  
                         var subId = jQuery( "select.suburbchoose option:selected" ).val();
                         if(typeof jQuery('#sbpr' + subId).html() != 'undefined') { jQuery('#sbpr' + subId).css({'border-color' : '#f00'}); return false; }
                         else { jQuery('.sbpr').css({'border-color' : '#aaa'}); }
                         if (! subId) return false;
                         jQuery('#subuChosen').append("<input type='text' id='inputCity"+subId+"' name='cities[]' value='" + subId +"' />");
                         if (!jQuery('#previewSubrb').html()) {jQuery('#previewSubrb').css({'display' : 'block'}).html('PREVIEW');}
                         jQuery('#SubrbPrw').append("<div id='sbpr"+subId+"' class='sbpr'>" + jQuery( "select.suburbchoose option:selected" ).text() + "<div class='delete-loct'></div></div>");
                         jQuery('.delete-loct').click(function() 
                         { 
                             var loc_id = jQuery(this).parent().attr('id');
                             deleteUserLocation(loc_id)
                         } );
                         return false;
                    });
                    
                }
	};
        jQuery.ajax( $fragment_refresh );
    }  );
    
 
     
});
function deleteUserLocation($location)
{
    $oldloca = $location;
    $location = $location.replace('sbpr', '');
    var inputCity = jQuery('#inputCity'+$location);
    if (typeof(inputCity) != 'undefined') { inputCity.remove(); }
    $fragment_refresh = {
		url: rt_vars.rt_urlajax,
		type: 'POST',
		data: { action: 'rt_delUserLoc', location: $location},
		success: function( data ) 
                { jQuery('#' +  $oldloca).remove(); }
	};
        jQuery.ajax( $fragment_refresh );
        return false;
}
function slideDownStates($this)
{
    jQuery('.listOfRegions').slideUp();
    jQuery($this).next(".listOfRegions").slideToggle("slow");
}
function getSuburbs(sb)
{
    jQuery(".form2 ul.resultsList").html('');
    $fragment_refresh = {
		url: rt_vars.rt_urlajax,
		type: 'POST',
		data: { action: 'rt_getSuburb', suburb: sb},
		success: function( data ) { jQuery(".form2 ul.resultsList_2").html(''); trade = JSON.parse(data); 
                    for (tr in trade) 
                    { 
                        jQuery(".form2 ul.resultsList_2").css("display", "block").append(jQuery('<li data-pc="'+ trade[tr].postcode +'" data-sub="'+ trade[tr].suburb +'"></li>').append(trade[tr].suburb + " - <span class='postcode'>" + trade[tr].postcode + "</span>")); }
                        jQuery(".form2 ul.resultsList_2 li").click(function () {   
                           if (typeof jQuery(this).attr('data-pc') != 'undefined') {jQuery('.postcosho').html("Postcode: " + jQuery(this).attr('data-pc'));}
                           else { jQuery('.postcosho').html(''); }
                           jQuery('.form2 input[type="text"].city').val(jQuery(this).attr('data-sub'));//provide the input box with the name
                           jQuery('.form2 input[type="text"].postcode').val(jQuery(this).attr('data-pc')); 
                           jQuery(".form2 ul.resultsList_2").fadeOut();
                           document.getElementById("form2").submit();
                       }) 
                    }
	};
    jQuery.ajax( $fragment_refresh );
}
function getTradies(trade)
{
    jQuery(".form2 ul.resultsList").html('');
    $fragment_refresh = {
		url: rt_vars.rt_urlajax,
		type: 'POST',
		data: { action: 'rt_getTrade', trade: trade},
		success: function( data ) { jQuery(".form2 ul.resultsList").html(''); trade = JSON.parse(data); 
                    for (tr in trade) 
                    { 
                        jQuery(".form2 ul.resultsList").css("display", "block").append(jQuery('<li></li>').append(trade[tr].trade) ); }
                        jQuery(".form2 ul.resultsList li").click(function () {  
                           jQuery('.form2 input[type="text"].RTsearchInput').val(jQuery(this).html().replace(/&amp;/g, "&"));//provide the input box with the name
                           jQuery(".form2 ul.resultsList").fadeOut();
                       }) 
                    }
	};
    jQuery.ajax( $fragment_refresh );
}

function deletingImage(userid, imageid)
{
    
     $fragment_refresh = {
		url: rt_vars.rt_urlajax,
		type: 'POST',
		data: { action: 'rt_deletingiImage', userid: userid, imageid:imageid},
		success: function( data ) { }
	};
    jQuery.ajax( $fragment_refresh );
}
function deleteImageLayer(image_id)
{
     var image_id = image_id.replace('tradimageLayer_', '');
     var title = jQuery('#tradimage_' + image_id).attr('title');
     jQuery('#deleteImageWarning').html("Are you sure you want to delete image "+ title + "?");
     jQuery('#deleteLayer').fadeIn();
     jQuery('.deleteImg').attr('id', 'deleteImg_' + image_id );
}
function hideDeleteLayer() { jQuery('#deleteLayer').fadeOut(); }

function yesDeleteImg(userid)
{
    var image_id = jQuery('.deleteImg').attr('id').replace('deleteImg_', '');
    jQuery('#tradimageLayer_' + image_id).remove();
    deletingImage(userid, image_id);
    jQuery('#deleteLayer').fadeOut();
}
function getRidMore()
{  
    jQuery('.reliable-pagination').remove();
}
function scrollTradDesc($this)
{
      var rowid = jQuery($this).attr('id').replace('trfound', '');
      var clickedID = 'slideTra_'+ rowid;
      jQuery('.slideTradFnd').each(function() { 
                if (jQuery(this).css('display') != 'none' && jQuery(this).attr('id') != clickedID) { jQuery(this).slideUp() }
      });
     jQuery('#' + clickedID).slideDown();
}