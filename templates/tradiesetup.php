<?php  
$page_id = $reliableTradie->get_page_id('tradie_setup');
$gpart = (isset($_GET['part'])) ? $_GET['part'] : 1;
$permalink = get_option('permalink_structure');
$quotation = $permalink  ? "?" : "&";
if ($gpart == 1) { include_once('tradiesetup2.php'); }
elseif ($gpart == 3) { include_once('tradiesetup3.php'); }
else
{


?>
<p><a href='<?php echo get_permalink($page_id); echo $quotation; ?>part=1'>User Data</a> | Edit Images | <a href='<?php echo get_permalink($page_id); echo $quotation; ?>part=3'>Add Location</a></p>
 <form method="post" enctype="multipart/form-data" action="<?php echo get_permalink($page_id); echo $quotation; ?>part=2" >
    <table cellpadding='5px'><!--<tr><td valign='top' colspan="2">Your Provider Description:<br />
                <textarea name='description' style='width: 500px; height: 200px;'><?php // echo wp_unslash($providerInfo->description); ?></textarea></td></tr>-->
        <tr><td valign='top' colspan="2">Upload Image: <input type='file' name='providerimage' /></td></tr>
        
    </table>
        <input type="hidden" name="providerid" value="<?php echo $user_id; ?>" />
        <br /><input type="submit" name="submit" value="Submit" />
        <input type="hidden" name="action" value="rt_tradie_upload" />
        <?php $reliableTradie->nonce_field( 'update_tradie_info'); ?>
    </form><br />
    <?php
    if ($rows)
    {
        $url = get_site_url();
        $a = 1;
        echo "<div id='deleteLayer'>\n<div id='deleteImageWarning'>Delete ?</div>"
        . "\n<div>\n<input type='button' value='YES' onClick='yesDeleteImg(".$user_id.")' class='deleteImg' id='deleteImg' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='NO' onClick='hideDeleteLayer()' />"
                . "\n</div></div>\n<br />";
        foreach ($rows as $image)
        { 
?>
    <div style="float: left; width: 200px; margin-right: 10px; position: relative;" id='tradimageLayer_<?php echo $image->id; ?>'><div class='deleteImage' style="position: absolute; top: -20px; right: -5px; color: #a00; border: 1px solid #000; background: #fff; border-radius: 30px; padding: 4px;">X</div><img id='tradimage_<?php echo $image->id; ?>' title='<?php echo $image->provider_image; ?>' src="<?php echo $url."/".$image->image_url;?>" style='width: 200px;' alt="" /></div>
    
<?php
    if ($a % 4 == 0 && $a > 0) { echo "<div style='clear: both;'></div><br />"; }
    $a++; }
}
}
?>
 