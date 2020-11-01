<?php
if (!isset($_POST['ajaxed'])) {include_once('findtradie.php');
echo "<br />";
echo "<br />";}

?>

<div id="tradiesfound">
<?php
/*
 * stdClass Object ( [id] => 1 [company_name] => aaaa [provider_name] => aaaa [provider_email] => aaaa [phone] => [user_id] => 9 
 * [filteroptions] => -1-2- [regions] => [cities] => [description] => [avcheck] => 0 [couponcode] => [howdidyoufindus] =>
 *  [date_added] => 2014-05-05 04:35:20 [published] => 0 [catering_persons_min] => [catering_persons_max] => ) 
 */

$ri = 1;
if (is_array($rows)) { 
foreach($rows as $row)
{
    echo "<div class='tradifnd'>";
     if (!is_numeric($row->main_image) || $row->main_image < 1)
     { //echo $row->main_image;
         $row->main_image = createMainImage($row);
     }
       
    // print_r($row);
     //get_permalink( reliabletradie_get_page_id( 'myaccount' ) )
    ?>
    <table class='trfound'><tr>
            <tr><td width='10%' rowspan='2'><?php  { 
           apply_filters( 'post_found_actions', $row); 
          // apply_filters( 'post_fnd_extimages', $row); 
           } ?></td>
            <td colspan='2'><h1 class='tradiefound'><?php echo $row->company_name; ?></h1>
             <b>E-mail:</b> <a href="mailto:<?php echo $row->provider_email; ?>"><?php echo $row->provider_email; ?></a><br />
     <br /><br />
            </td></tr><td width='15%'><?php apply_filters( 'post_fnd_getTrades', $row);  ?></td>
            <td><?php if ($row->description) { ?><h2 id='trfound<?php echo $row->id;?>' class='fndcheckout'>Check out examples of our work</h2><?php }  
    if (is_numeric($row->main_image) && $row->main_image > 0) { 
           //apply_filters( 'post_found_actions', $row); 
           apply_filters( 'post_fnd_extimages', $row); 
       }
   ?></td></tr>
    
    </table>
    <?php
    echo "<div style='clear: both;'></div>";
    if ($row->description) { ?><div class='slideTradFnd' id='slideTra_<?php echo $row->id; ?>'>
        <?php 
        echo $row->description; 
        $permalink = get_option('permalink_structure');
        $quotation = $permalink  ? "?" : "&";
       // echo "<br /><a href='".get_permalink( reliabletradie_get_page_id( 'tradie_show' ) )."{$quotation}id=".$row->id."'>Read More</a>"; ?><br /></div><?php } 
    $ri++;
    echo "</div>";
} }
if (isset($_POST['ajaxed'])) { die(); }
include_once($reliableTradie->getTemplateDir()."/loop/pagination.php");
?>
</div>
