<?php
/*
Plugin Name: Bookmarks exclude & add extra params
Plugin URI: http://www.WebsiteFreelancers.nl
Description: Widget to show bookmarks with exclude option, extra params are possible
Author: WebsiteFreelancers.nl
Version: Beta 1.1 (Works with WP2.7.1)
Author URI: http://www.websitefreelancers.nl
Extra info: This is my first WP plugin. Coding style adopted from "Add Link 0.3" by enej [ http://blogs.ubc.ca/support/plugins/add-links-widget/ ]
*/

function vn_plugin_bookmarks_exclude_init() {
   // check for the required API functions
   if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control'))
      return;

   // this prints the widget
   function vn_plugin_bookmarks_exclude($args) {
      // include WordPress "core" for "wp_insert_link" to work
      $root = preg_replace('/wp-content.*/', '', __FILE__);
      require_once ($root . 'wp-config.php');
      require_once ($root . 'wp-admin/includes/admin.php');

      extract($args);

      $options = get_option('vn_plugin__bookmarks_exclude_widget');
      $calloptions = 'exclude_category=' . $options['exclude'] . $options['extra'];
      wp_list_bookmarks($calloptions);

   }

   // widget options
   function vn_plugin_bookmarks_exclude_control() {
      // get our options and see if we're handling a form submission.
      $options = get_option('vn_plugin__bookmarks_exclude_widget');

      if (!is_array($options)) // default values
         $options = array (
            'exclude' => '',
            'extra' => ''
         );

      if ($_POST['vn_plugin_bookmarks_exclude_submit']) {
         $nonce = $_REQUEST['bookmarks_exclude-nonce'];
         if (!wp_verify_nonce($nonce, 'bookmarks_exclude-nonce'))
            die('Security check failed. Please use the back button and try resubmitting the information.');

         // remember to sanitize and format user input appropriately.
         $options['exclude'] = strip_tags(stripslashes($_POST['exclude']));
         $options['extra'] = strip_tags(stripslashes($_POST['extra']));

         update_option('vn_plugin__bookmarks_exclude_widget', $options);
      }
?>
      <p>
         <label for="exclude">Exclude:</label><br /><input style="width: 255px;" id="exclude" name="exclude" type="text" value="<?php echo $options['exclude']; ?>" />
      </p>
      <p>
         <label for="exclude">Extra: (<small>Add & before each option!)</small></label><br /><input style="width: 255px;" id="extra" name="extra" type="text" value="<?php echo $options['extra']; ?>" />
      </p>

      <input type="hidden" name="bookmarks_exclude-nonce" value="<?php echo wp_create_nonce('bookmarks_exclude-nonce'); ?>" />
      <input type="hidden" id="vn_plugin_add_link_submit" name="vn_plugin_bookmarks_exclude_submit" value="yes" />
      <?php

   }

   // Tell Dynamic Sidebar about our new widget and its control
   register_sidebar_widget(array (
      'Bookmarks exclude',
      'widgets'
   ), 'vn_plugin_bookmarks_exclude');

   // this registers the widget control form
   register_widget_control(array (
      'Bookmarks exclude',
      'widgets'
   ), 'vn_plugin_bookmarks_exclude_control', 335, 700);
}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('widgets_init', 'vn_plugin_bookmarks_exclude_init');


/**
 * Additional links on the plugin page
 */
function vn_plugin_bookmarks_exclude_RegisterPluginLinks($links, $file) {
   if ($file == 'bookmarks_exclude.php') {
      $links[] = '<a href="widgets.php">' . __('Settings') . '</a>';
      $links[] = '<a href="http://donate.ramonfincken.com">' . __('Donate') . '</a>';
   }
   return $links;
}

add_filter('plugin_row_meta','vn_plugin_bookmarks_exclude_RegisterPluginLinks', 10, 2);
?>
