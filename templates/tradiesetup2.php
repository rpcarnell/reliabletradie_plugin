<?php
if ($submitted1 == 1)
{
    $proviname_warn = (!isset($_POST['provider_name']) || empty($_POST['provider_name'])) ? "<div class='warn'> * </div>" : '';
    $compname_warn = (!isset($_POST['company_name']) || empty($_POST['company_name'])) ? "<div class='warn'> * </div>" : '';
    $providmail_warn = (!isset($_POST['provider_email']) || empty($_POST['provider_email']))? "<div class='warn'> * </div>" : '';
    $proviphone_warn = (!isset($_POST['phone']) || empty($_POST['phone'])) ? "<div class='warn'> * </div>" : '';
}
else
{
    $proviname_warn = '';
    $compname_warn = '';
    $providmail_warn = '';
    $proviphone_warn = '';
}
$rows = $rt->getImages(get_current_user_id()); 
$provider_name = $providerInfo->provider_name;
$company_name = $providerInfo->company_name;
$provider_email = $providerInfo->provider_email;
$phone = $providerInfo->phone;
$url = $providerInfo->url;
//print_r($providerInfo);
$chosenFilters =  explode('-', $providerInfo->filteroptions) ;
?>
<p>User Data | <a href='<?php echo get_permalink($page_id); echo $quotation; ?>part=2'>Edit Images</a> | <a href='<?php echo get_permalink($page_id); echo $quotation; ?>part=3'>Add Location</a></p>

 <form method="post" action="<?php echo get_permalink($page_id); echo $quotation;?>part=1" >
     <table cellpadding='5px'><tr><td valign='top' width='25%'>Your Provider Name:</td><td>
                <?php echo $proviname_warn;?><input type='text' name='provider_name' value='<?php echo $provider_name;?>' /></td></tr>
         
         <tr><td valign='top' width='25%'>Your Company Name:</td><td>
                <?php echo $compname_warn;?><input type='text' name='company_name' value='<?php echo $company_name;?>' /></td></tr>
         
          <tr><td valign='top' width='25%'>E-Mail:</td><td>
                <?php echo $providmail_warn;?><input type='text' name='provider_email' value='<?php echo $provider_email;?>' /></td></tr>
          
           <tr><td valign='top' width='25%'>Phone:</td><td>
                <?php echo $proviphone_warn;?><input type='text' name='phone' value='<?php echo $phone;?>' /></td></tr>
           
           <tr><td valign='top' width='25%'>URL:</td><td>
                <?php echo $proviphone_warn;?><input type='text' name='url' value='<?php echo $url;?>' style='width: 500px' /></td></tr>
           <tr><td valign='top' colspan="2">
               <?php
           if ($rows)
    {   echo "<p><a href='".get_permalink($page_id).$quotation."part=2' target='self'>Edit Images</a></p>";
        $url = get_site_url();
        $a = 1;
         
        foreach ($rows as $image)
        { 
?>
    <div style="float: left; width: 200px; margin-right: 10px; position: relative;" id='tradimageLayer_<?php echo $image->id; ?>'><img id='tradimage_<?php echo $image->id; ?>' title='<?php echo $image->provider_image; ?>' src="<?php echo $url."/".$image->image_url;?>" style='width: 200px;' alt="" /></div>
    
<?php
   if ($a % 4 == 0 && $a > 0) { echo "<div style='clear: both;'></div><br />"; }
    $a++; }
   
} else echo "&nbsp;";//"<a href='".get_permalink($page_id).$quotation."part=2'>Add Images</a>";
?></td></tr>
           <tr><td valign='top' colspan="2">Your Provider Description:<br />
                <textarea name='description' style='width: 500px; height: 200px;'><?php echo wp_unslash($providerInfo->description); ?></textarea></td></tr>
          
       
       <tr>
    <td class="legevalue">Add a Trade:</td>
    <td>
         
                <?php
                include_once(WP_CONTENT_DIR.'/plugins/reliatabletradie/classes/class-rt-registertradie.php');
      $regrt = new RT_RegisterTradie();
     $regrt->ReliableFields(false);
?>
</td></tr>
       <tr><td colspan="2"><?php $regrt->tradesIncluded($providerInfo); ?></td></tr>
    </table>
        <input type="hidden" name="providerid" value="<?php echo $user_id; ?>" />
        <input type="submit" name="submit" value="Submit" />
        <input type="hidden" name="action" value="rt_tradie_basics" />
        <?php $reliableTradie->nonce_field( 'update_tradie_info'); ?>
    </form><br />