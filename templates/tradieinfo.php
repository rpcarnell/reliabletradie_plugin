<h1 style='margin: auto; text-align: center;'><?php echo $row->company_name;?></h1>
<br />
<p>Address: <?php echo $row->address;?>
<br />Phone: <?php echo $row->phone;?>
<br />E-mail: <a href='mailto:<?php echo $row->provider_email;?>'><?php echo $row->provider_email;?></a>

</p>
<div style='background: #fff; border: 1px solid #777; border-radius: 5px; width: 40%; padding: 15px; margin: 15px 5px;'>
    <h2>Skills</h2><ul>
    <?php foreach ($provider_trades as $pr_tr)
        echo "<li>$pr_tr</li>";
     ?>
    </ul>
</div>
    <?php
//print_r($image_rows);
if ($image_rows)
{
    foreach($image_rows as $irg)
    {
         echo "<div style=\"float: left; margin-right: 10px; \"><img title='".$irg->provider_image."' src=\"".$url."/".$irg->image_url."\" style='width: 200px;' alt=\"\" /></div>";
    }
}
if ($row->description) {    
?>
<div style='background: #ddd; border: 1px solid #ddd; border-radius: 5px; width: 90%; padding: 10px; margin: 10px;'><?php echo $row->description;?></div><?php } ?>
