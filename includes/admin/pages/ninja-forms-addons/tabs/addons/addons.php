<?php
add_action('init', 'ninja_forms_register_tab_addons');

function ninja_forms_register_tab_addons(){
    $args = array(
        'name' => 'Extend Ninja Forms',
        'page' => 'ninja-forms-extend',
        'display_function' => 'ninja_forms_tab_addons',
        'save_function' => '',
        'show_save' => false,
    );
    ninja_forms_register_tab('extend', $args);

}

function ninja_forms_tab_addons(){
    $uri = 'http://wpninjas.com/downloads/category/ninja-forms/feed/';
    //include_once(ABSPATH . WPINC . '/feed.php');
    $feed = fetch_feed( $uri );

    if (!is_wp_error( $feed ) ) :
        $items = $feed->get_items(0, 0);
    endif;

    $items = array(
        array (
            'title' => 'Layout and Styles',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2013/01/layout-styles-300x121.png',
            'content' => 'This extension gives you power over the look and feel of your Ninja Forms from within your WordPress admin. It gives you the ability to style almost every part of your form, down to the the smallest detail, with little to no …',
            'link' => 'http://wpninjas.com/downloads/layout-styles/',
            'plugin' => 'ninja-forms-style/ninja-forms-style.php',
            'docs' => 'http://wpninjas.com/ninja-forms/docs/section/layout-styles/',
        ),
        array (
            'title' => 'Save User Progress',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2012/10/save-user-progress-300x121.png',
            'content' => 'Sometimes forms can grow quite large, and it would be very helpful for users to be able to save their progress and come back at a later time. This extension does just that for you. Using the built-in WordPress user …',
            'link' => 'http://wpninjas.com/downloads/save-user-progress/',
            'plugin' => 'ninja-forms-save-progress/save-progress.php',
            'docs' => '',
        ),
        array (
            'title' => 'File Uploads',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2012/10/file-uploads1-300x121.png',
            'content' => 'File Uploads for Ninjas Forms gives you the ability to insert file upload fields to your forms. This will allow users the ability to upload images, docs, audio or video files, or anything else you may need. You can easily …',
            'link' => 'http://wpninjas.com/downloads/file-uploads/',
            'plugin' => 'ninja-forms-uploads/file-uploads.php',
            'docs' => 'http://wpninjas.com/ninja-forms/docs/section/file-uploads-extensions/',
        ),
        array (
            'title' => 'Front-End Posting',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2012/10/front-end-posting-300x121.png',
            'content' => 'The Ninja Forms Front-end Posting extension gives you the power of the WordPress post editor on any publicly viewable page you choose. You can allow users the ability to create content and have it assigned to any publicly available built-in or custom …',
            'link' => 'http://wpninjas.com/downloads/front-end-posting/',
            'plugin' => 'ninja-forms-post-creation/post-creation.php',
            'docs' => '',
        ),        
        array (
            'title' => 'Front-End Editor',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2013/03/front-end-editor.png',
            'content' => 'The Front-End Editor Extension brings the power of your WordPress admin to your front-facing site. It is a one-stop solution for almost all your front-end editing needs. Users can now be allowed to create, edit, or delete posts, pages, or any custom post type without the need to see the WordPress admin. ',
            'link' => 'http://wpninjas.com/downloads/front-end-editor/',
            'plugin' => 'ninja-forms-front-end-editor/front-end-editor.php',
            'docs' => '',
        ),
        array (
            'title' => 'Multi-Part Forms',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2012/10/multi-part-forms-300x121.png',
            'content' => 'The Multi-Part Forms extension allows you to break long forms into sections, creating a natural flow for your visitors. You can add a breadcrumb trail through the various sections of the form and a progress bar so that your users …',
            'link' => 'http://wpninjas.com/downloads/multi-part-forms/',
            'plugin' => 'ninja-forms-multi-part/mutli-part.php',
            'docs' => '',
        ),
        array (
            'title' => 'Conditional Logic',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2012/10/conditional-logic-300x121.png',
            'content' => 'This extension for Ninja Forms allows you to create “smart” forms. Fields within these forms can dynamically change based upon user input; show or hide fields based on a selected item, set field values based upon a list selection, or …',
            'link' => 'http://wpninjas.com/downloads/conditional-logic/',
            'plugin' => 'ninja-forms-conditionals/conditionals.php',
            'docs' => '',
        ),        
        array (
            'title' => 'MailChimp',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2013/04/mailchimp-for-ninja-forms-300x121.png',
            'content' => 'The MailChimp extension allows you to quickly create newsletter signup forms for your MailChimp account using the power and flexibility that Ninja Forms provides. …',
            'link' => 'http://wpninjas.com/downloads/mail-chimp/',
            'plugin' => 'ninja-forms-mailchimp/ninja-forms-mailchimp.php',
            'docs' => '',
        ),        
        array (
            'title' => 'Campaign Monitor',
            'image' => 'http://wpninjas.com/wp-content/uploads/edd/2013/05/campaign-monitor-header-300x121.png',
            'content' => 'The Campaign Monitor extension allows you to quickly create newsletter signup forms for your Campaign Monitor account using the power and flexibility that Ninja Forms provides. …',
            'link' => 'http://wpninjas.com/downloads/mail-chimp/',
            'plugin' => 'ninja-forms-campaign-monitor/ninja-forms-campaign-monitor.php',
            'docs' => '',
        ),
    );

    foreach ($items as $item) {
        echo '<div class="nf-extend">';
            echo '<img src="' . $item['image'] . '" />';
            echo '<h2>' . $item['title'] . '</h2>';
            echo '<div>';
                echo '<p>' . $item['content'] . '</p>';
                if( !empty( $item['docs'] ) ) {
                    echo '<p><a href="' . $item['docs'] . '">' . $item['title'] . ' Extension Documentation</a></p>';
                } else {
                    echo '<p>Documentation coming soon.</a>.</p>';
                }
            echo '</div>';
            if( file_exists( WP_PLUGIN_DIR.'/'.$item['plugin'] ) ){
              if( is_plugin_active( $item['plugin'] ) ) {
                    echo '<span class="button-secondary nf-button">Active</span>';
                } elseif( is_plugin_inactive( $item['plugin'] ) ) {
                    echo '<span class="button-secondary nf-button">Installed</span>';
                } else {
                    echo '<a href="' . $item['link'] . '" title="' . $item['title'] . '" class="button-primary nf-button">Learn More</a>';
                }
            }else{
                echo '<a href="' . $item['link'] . '" title="' . $item['title'] . '" class="button-primary nf-button">Learn More</a>';
            }
  
        echo '</div>';
    }
}

function ninja_forms_save_addons($data){
    global $wpdb;

}