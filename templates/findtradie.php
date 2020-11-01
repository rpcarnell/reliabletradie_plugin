<?php
$pageid = $reliableTradie->get_page_id( 'tradies_found' );
  
?>
<form method='post' class='form2' id='form2' action="<?php echo get_permalink($pageid); ?>">
    <?php //echo $reliableTradie->get_page_id( 'tradies_found' );?>
    <table class="trfound" style="width:100%;" cellspacing='0'>
        <thead></thead><tbody>
            
            <tr>
    <td class="legevalue" width='20%'>What type of tradie do you need:</td>
    <td>
      
                <?php

echo '<input type="text" name="rtdata" maxlength="60" autocomplete="off" onBlur="jQuery(\'.form2 ul.resultsList\').fadeOut();" placeholder="What type of tradie do you need?" class="defaultText RTsearchInput" />';
 echo "<div style='position: relative; z-index:99;'><ul class=\"resultsList\">&nbsp;</ul></div>";
?>
 </td> 
                <td class="legevalue">Where do you live?</td>
                
                <td>
                   <?php if (isset($_POST['rqstep']) && $_POST['rqstep'] == 2)
             { if (!isset($_POST['city']) || trim($_POST['city']== '')) { echo "<span class='warning'>".JText::_('CHOOSEACITY')."</span><br /><br />"; } }
               $location = (isset($_POST['city'])) ? $_POST['city'] : '';
?>
                    <input type="text" name="city" autocomplete="off" class='city' onBlur="jQuery('.form2 ul.resultsList_2').fadeOut();" value="" /><div class="postcosho"></div>
                    <div style='display: none'><input type="text" name="postcode" class='postcode' value="" /></div>
                    <div style='position: relative; z-index:99;'><ul class="resultsList_2">&nbsp;</ul></div>
		 </td></tr>
 
 
</tbody></table>
    <br />
    
  <?php  if (!isset($_POST['searching']))
  {
       
  }
?>
  <input type="hidden" name="searching" value="1" />
</form>
 <script type='text/javascript'>
		// Region list javascript
		jQuery(function() { 
			jQuery('ul.listOfRegions > li > input').change(function() {  
				if (jQuery(this).attr('checked')) {
					jQuery(this).siblings('ul').find('li').each(function(){
						jQuery(this).children('input').attr('checked', true);
					});
				} else {
					jQuery(this).siblings('ul').find('li').each(function(){
						jQuery(this).children('input').attr('checked', false);
					});
				};
			});
			jQuery('ul.listOfRegions > li > ul > li > input').each(function() {
				if (jQuery(this).attr('checked') && !jQuery(this).closest('li.region').children('input').attr('checked'))
					jQuery(this).closest('ul').show();
			});
			jQuery('ul.listOfRegions > li > ul > li > input').change(function() {
				if (!jQuery(this).attr('checked') &&jQuery(this).closest('li.region').children('input').attr('checked'))
					jQuery(this).closest('li.region').children('input').attr('checked', false);
			});
		});
		</script>
                <script type='text/javascript'>
    function SlideCitiesUp($i)
    {  
        jQuery('.citiesList').slideUp('fast');
        jQuery($i).next().slideDown('fast');
    }
</script>