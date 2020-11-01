<?php
class RT_RegisterTradie {
        public function __construct() 
        { 
             add_action('register_form', array($this, 'ReliableFields')); 
             add_action('reliabletradie_created_tradie', array($this, 'created_tradie'));
             add_action('reliabletradie_created_usertype', array($this, 'determine_user'));
             add_action('reliabletradie_beforecreating_tradie', array($this, 'beforeR_tradie'));
        }
        function determine_user($user_id)
        {
            global $wpdb;
            $usertype = $_POST['usertype'];
            $q = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."reliabletradie_usertype (user_id,usertype) VALUES (%d, %s)", $user_id, $usertype);
            $wpdb->query($q);
        }
        function created_tradie($user_id)
        {
            global $wpdb;
            $post = $_POST;
            if ($post['usertype'] !='tradie') return;
            $cities = isset($post['cities']) ? $post['cities'] : '';
            if (is_array($cities)) $cities = "-".implode('-', $cities)."-";
            //print_r($post);
            $post['userrq'] = (!is_array($post['userrq'])) ? (array)$post['userrq'] : $post['userrq'];
            $userrq =  "-".implode('-', $post['userrq'])."-";
            $q = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."reliabletradie_providers (user_id, filteroptions, cities) VALUES (%d, %s, %s)", $user_id, $userrq, $cities);
            $wpdb->query($q);
           $cities = $post['cities'];
           if($cities && is_array($cities)) {
           foreach ($cities as $city)
             {
                 $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."reliabletradie_provilocations (provider_id, location_id) VALUES (%d, %d)", 
                 $user_id, $city);
                  $wpdb->query( $query );
             }
           }
        }
        function beforeR_tradie()
        {
            global $reliableTradie;
           // $reliableTradie->add_error("Some fields are missing");
        }
        function tradesIncluded($providerInfo)
        {
            $filters = explode("-", $providerInfo->filteroptions);
            global $wpdb;
            $trades = array();
            foreach ($filters as $ft) { if (is_numeric($ft)) { $trades[] = $ft; } }
            $query = "SELECT * FROM {$wpdb->prefix}reliabletradie_trades WHERE id IN (".implode(',', $trades).")";
             
            $rows = $wpdb->get_results($query);
            foreach($rows as $trade)
            {  echo "<div id='trade_$trade->id' data-userid='$providerInfo->user_id' class='usertrade'>$trade->trade<div class='delete-trade'></div></div>"; }
        }
        function ReliableFields($addLocations = true)
        {
             global $reliableTradie;
             global $register_form;
             if ($register_form === true) { return; }
             else $register_form = true;
              
             if (!$reliableTradie->tradies) { $reliableTradie->createTradies(); }
              //print_r($reliableTradie->tradies);
             if ($addLocations) echo "\n<p>What type of tradie are you?</p>";
            // else echo "<p>Add a Trade:</p>";
             echo "<select id='tradieselect' name='userrq'><option></option>";
            
             foreach ($reliableTradie->tradies as $key => $value)
             { echo "<option value='$key'>".ucwords($value)."</option>\n"; }
             echo "</select>";
             if ($addLocations) $this->ajaxLocations();
        }
        function ajaxLocations($add = false)
        {
           global $reliableTradie; 
            $rows = $reliableTradie->getLocStates();
              
           //  $rows = $reliableTradie->getLocations();
              if (! $add) echo "\n<div>What's your location?</div>";
              else { echo "\n<div>Add a location?</div>"; }
              echo "<select class='selectLoc'><option></option>";
              foreach ($rows as $state)
             {
                 echo "<option value='$state->state'>".$state->state."</option>";
             }
              echo '</select>';
              //$open = false;
              echo "<br /><div id='showSuburbs'></div><br />";
              echo "<div id='subuChosen'></div>";
        }
        function userLocations()
        {
            $user_id = get_current_user_id();
            global $wpdb;
            if (!is_numeric($user_id))  return;
            $query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."reliabletradie_provilocations as a INNER JOIN ".$wpdb->prefix."reliabletradie_locations as b ON b.id = a.location_id WHERE a.provider_id = %d ORDER BY b.suburb", $user_id);
            $rows = $wpdb->get_results($query);
            if(is_array($rows)) 
            { foreach($rows as $row) {
            ?>
<div id='sbpr<?php echo $row->id; ?>' class='sbpr'><?php echo $row->suburb?> - <span style='font-size: 10px; color: #aaa;'><?php echo $row->postcode;?></span><div class='delete-loct'></div></div>
<?php
        }
        ?>
        <script>jQuery('.delete-loct').click(function() 
                         { 
                             var loc_id = jQuery(this).parent().attr('id');
                             deleteUserLocation(loc_id)
                         } );</script>
        <?php
            }
       }
}
?>