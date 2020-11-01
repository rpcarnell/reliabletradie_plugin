<?php
class RT_Shortcode_Find_Tradie
{
    public function output()
    {
        global $reliableTradie;
        wp_enqueue_style( 'reliabletradie_search_css', plugins_url(  '/assets/css/reliabletradie.css', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'woocommerce_admin', $reliableTradie->plugin_url() . '/assets/js/rt.js');
        include_once($reliableTradie->getTemplateDir().'/findtradie.php');
     }
     public function tradiesfound()
     {
         global $reliableTradie;
         wp_enqueue_style( 'reliabletradie_search_css', plugins_url(  '/assets/css/reliabletradie.css', dirname( __FILE__ ) ) );
         wp_enqueue_script( 'woocommerce_admin', $reliableTradie->plugin_url() . '/assets/js/rt.js');
         do_action('reliabletradie_getTradies', 'word');
     }
     public function tradieshow()
     {
         global $wpdb, $reliableTradie;
         $id = (int)$_GET['id'];
         $query = "SELECT * FROM ".$wpdb->prefix."reliabletradie_providers WHERE id= $id LIMIT 1";
         $row = $wpdb->get_row($query);
         if (!$row) return;
         if ($row->filteroptions)
         {
             $provider_trades = array();
             $tradies = explode('-', $row->filteroptions);
             foreach ($tradies as $trd)
             {
                 if (!is_numeric($trd)) continue;
                 $query = "SELECT trade FROM ".$wpdb->prefix."reliabletradie_trades WHERE id=$trd LIMIT 1";
                 $trade = $wpdb->get_var($query);
                 $provider_trades[] = ucwords(strtolower($trade));
             }
         }
          
         $query = "SELECT * FROM ".$wpdb->prefix."reliabletradie_providerimages WHERE provider_id = $id LIMIT 12";
         $image_rows = $wpdb->get_results($query);
         $url = get_site_url();
         $query = "SELECT * FROM ".$wpdb->prefix."eliabletradie_providerimages WHERE provider_id = $id LIMIT 12";
         include_once($reliableTradie->getTemplateDir().'/tradieinfo.php');
     }
     public function getOccupation($trade, & $wpdb)
     {
         $q = $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."reliabletradie_trades WHERE trade = %s", urldecode($trade));
         //echo $q;
         $row = $wpdb->get_var($q);
       //  print_r($row);
         return $row;
     }
     public function findTradie()
     {
         global $wpdb, $reliableTradie;
         $offset = isset($_POST['pageNum']) ? $_POST['pageNum'] : 0;
         global $wp_query;
         $filter = '';
         $where = '';
        // print_r($_POST);
         $rt = new RT_Shortcode_Find_Tradie();
         if (!isset($_POST['rtdata']) || $_POST['rtdata'] == '') 
         {
             echo "<p><b>One of your fields is empty</b></p>";
             include_once($reliableTradie->getTemplateDir().'/tradiesfound.php');
             return;
         }
         else $trade = $rt->getOccupation($_POST['rtdata'], $wpdb);
         $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
         $location_id = $rt->getLocation($_POST['city'], $postcode, $wpdb);
         /*echo "system finds users who have just registered";
          exit;*/
         if (! $trade) {  /*echo "<p><b>Sorry, your search yielded no results</b></p>"; die();*/ }
         else {
           $filter .= $wpdb->prepare("a.filteroptions LIKE '%%-%d-%%'", $trade);
           $filter_2 =  $wpdb->prepare(" AND b.location_id = %d", $location_id);
           if ($filter != '') $where = "WHERE a.company_name != '' AND $filter $filter_2";
           $per_page = PER_RESULT;
           $offset = $offset.", ";
           $q = "SELECT * FROM ".$wpdb->prefix."reliabletradie_providers as a INNER JOIN ".$wpdb->prefix."reliabletradie_provilocations as b ON a.user_id = b.provider_id $where LIMIT $offset ".($per_page + 1);
           
           $rows = $wpdb->get_results($q);
          //  echo $q;
            $total_pages = ceil( count($rows) / $per_page );
             
           $wp_query->max_num_pages = $total_pages;
           if ((count($rows) <= $per_page) && isset($_POST['ajaxed']))//it should be more than per_page, so if it isn't more, get rid of the more button.
           { echo "<script> getRidMore()</script>"; } 
           elseif ((count($rows) <= $per_page) && ! isset($_POST['ajaxed']))//it should be more than per_page, so if it isn't more, get rid of the more button.
           { $wp_query->max_num_pages = 0; }
           elseif (count($rows) > $per_page) { array_pop($rows); }//get rid of the last one. We are doing an ajax pagination here similar to that of LinkedIn
           
         }
         if (!$rows && isset($_POST['ajaxed'])) { echo 0; die(); }
         elseif (!$rows && !isset($_POST['ajaxed'])) echo "<p><b>Sorry, your search yielded no results for ".$_POST['rtdata']."</b></p>";
         include_once($reliableTradie->getTemplateDir().'/tradiesfound.php');
     }
     public function getLocation($suburb, $postcode, & $wpdb)
     {
         if ($postcode != '') $postcode_qr = "AND postcode = %s";
         else $postcode_qr = '';
         $q = $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."reliabletradie_locations WHERE suburb = %s $postcode_qr LIMIT 1", $suburb, $postcode);
        // echo $q;
         $row = $wpdb->get_var($q);
         return $row;
     }
}
?>
