<?php
$providerCities = explode('-', $providerInfo->cities);
if (!is_array($providerCities)) $providerCities = array();
?>
<p><a href='<?php echo get_permalink($page_id); echo $quotation; ?>part=1'>User Data</a> | <a href='<?php echo get_permalink($page_id); echo $quotation; ?>part=2'>Edit Images</a> | Add Location</p>
 <form method="post" enctype="multipart/form-data" action="<?php echo get_permalink($page_id); echo $quotation; ?>part=3" >
<input type="hidden" name="action" value="rt_tradie_locations" />
        <?php $reliableTradie->nonce_field( 'update_tradie_info'); ?>
  <input type="hidden" name="providerid" value="<?php echo $user_id; ?>" />
     <?php
      include_once(WP_CONTENT_DIR.'/plugins/reliatabletradie/classes/class-rt-registertradie.php');
      $regrt = new RT_RegisterTradie();
     $regrt->ajaxLocations(true);
     
//$rows = $reliableTradie->getLocations();
             /* echo "\n<p>What's your location?</p>";
              $state = '';
              $open = false;
              $opentable = false;
              $ra = 0;
             foreach ($rows as $row)
             {
                 if ($row->state != $state)
                 {
                     $state = $row->state;
                     if ($state == '') echo "<div style='border: 1px solid #000; padding: 10px;'>";
                     else {
                         if ($opentable === true) 
                         {
                             $tableClose = '';
                             $closerow = false;
                             if ($ra > 0 || $ra % 4 != 0 ) 
                             {
                                 $closerow = true;
                                 while ($ra < 4) {$tableClose .= "<td></td>"; $ra++; }
                             }
                             if ($closerow === true) $tableClose .= "</tr>";
                             $tableClose .= "</table>\n";
                             $ra = 0;
                             
                         }
                         echo "$tableClose";
                         if ($ra > 0) echo "</div>";
                         echo "\n<div class='state'><p href='javascript:void(0)' onclick='slideDownStates(this)'>$state</p>\n"
                     . "\n<table class='listOfRegions' style='padding: 5px; display: none; font-size: 10px;'><tr><td colspan='4'><b>Select All</b>&nbsp;&nbsp;<input type='checkbox' class='allcities' /></td></tr>";}
                     $open = true;
                     $opentable = true;
                 }
                 if ($ra == 0 || $ra % 4 == 0 ) { echo "<tr>"; $ra = 0; }
                     if (in_array($row->id, $providerCities)) {     $checked = "checked=checked";}
                     else $checked = '';
                     echo "<td>\n<input type='checkbox' $checked name='cities[]' value='$row->id'  />&nbsp;&nbsp;$row->suburb</td>";
                 if ($ra == 3) {echo "</tr>";}
                     $ra++; 
             }
             if ($open === true) echo "</table></div>";*/
             ?>
 
        <input type="submit" name="submit" value="Submit" />
       
 </form><br /><div id='previewSubrb'>PREVIEW</div>
<div id='SubrbPrw'></div>
<?php
$regrt->userLocations();
?>