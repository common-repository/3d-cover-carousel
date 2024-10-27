<?php
defined('ABSPATH') OR exit;
/*
  Plugin Name: 3D Cover Carousel
  Plugin URI:http://kuldipmakdiya.wordpress.com
  Version: 1.0
  Author:kuldip_raghu
  Author URI:http://kuldipmakdiya.wordpress.com
  Description: 3D Cover Carousel is a cool plugin & Html5 based carousel plugin that has the ability to continuously rotate/slide a set of images with the familiar 'cover flow' effect. You can easily add this plugin in and displayed at your front side with 3D animation slider images.
  License: GNU General Public License v2.0 or later
  Copyright 2016 kuldip_raghu
 */
add_action('admin_menu', 'covercarousel_add_admin_menu');
register_activation_hook(__FILE__, 'install_covercarousel');
add_action('wp_enqueue_scripts', 'covercarousel_load_styles_and_js');
add_shortcode('cover_carousel_slider', 'print_covercarousel_func');

function covercarousel_load_styles_and_js() {
    if (!is_admin()) {
        $url = plugin_dir_url(__FILE__);
        wp_enqueue_script('jquery');
        wp_enqueue_script('covercarousel', $url . 'js/jquery.covercarousel.js');
        wp_enqueue_style('cover-carousel', $url . 'css/main.css');
        
    }
}

function install_covercarousel() {
    set_time_limit(500);
    global $wpdb;
    $table_name = $wpdb->prefix . "covercarousel_slider";
    $sql = "CREATE TABLE " . $table_name . " (
        id int(10) unsigned NOT NULL auto_increment,
        title varchar(1000) NOT NULL,
        image_name varchar(500) NOT NULL,
        createdon datetime NOT NULL,
        custom_link varchar(1000) default NULL,
        post_id int(10) unsigned default NULL,
        PRIMARY KEY  (id)
        );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $covercarousel_slider_settings = array('linkimage' => '1', 'sliderheight' => '400', 'auto' => '', 'fadein' => '1500', 'imageheight' => '120', 'imagewidth' => '120', 'visible' => '5', 'resizeImages' => '0', 'scollerBackground' => '#FFFFFF', 'scollergradient' => '#cccccc');

    if (!get_option('covercarousel_slider_settings')) {

        update_option('covercarousel_slider_settings', $covercarousel_slider_settings);
    }
}

function covercarousel_add_admin_menu() {
    $suffix_cover_carousel = add_menu_page(__('3D Cover Carousel Slider'), __('3D Cover Carousel Slider'), 'administrator', 'covercarousel_slider', 'covercarousel_slider_admin_data');
    $suffix_cover_carousel = add_submenu_page('covercarousel_slider', __('3D Carousel Setting'), __('3D Carousel Setting'), 'administrator', 'covercarousel_slider', 'covercarousel_slider_admin_data');
    $suffix_cover_carousel_1 = add_submenu_page('covercarousel_slider', __('Manage 3D Carousel'), __('Manage 3D Carousel'), 'administrator', 'covercarousel_slider_image_management', 'covercarousel_thumbnail_image_management');
    $suffix_cover_carousel_2 = add_submenu_page('covercarousel_slider', __('3D Carousel Preview'), __('3D Carousel Preview'), 'administrator', 'covercarousel_slider_preview', 'covercarousel_previewSliderAdmin');

    add_action('load-' . $suffix_cover_carousel, 'covercarousel_my_plugin_admin_init');
    add_action('load-' . $suffix_cover_carousel_1, 'covercarousel_my_plugin_admin_init');
    add_action('load-' . $suffix_cover_carousel_2, 'covercarousel_my_plugin_admin_init');
}

function covercarousel_my_plugin_admin_init() {
    $url = plugin_dir_url(__FILE__);
    if (is_admin()) {
        wp_enqueue_script('jquery-validate', $url . 'js/jquery.validate.js');
        wp_enqueue_style('cover-carousel', $url . 'css/main.css');
        wp_enqueue_script('reflection', $url . 'js/jquery.reflection.js');
        wp_enqueue_script('customjs', $url . 'js/custom.js');
        wp_enqueue_script('covercarousel', $url . 'js/jquery.covercarousel.js');
    }
}

function covercarousel_slider_admin_data() {
    
    if (isset($_POST['covercarouselnonce'])|| wp_verify_nonce($_POST['covercarouselnonce'], 'covercarouselaction' ) ) {
        if (isset($_POST['btnsave'])) {
        
        $auto = trim($_POST['isauto']);
        if ( ! $auto ) {
           $auto = '';
        }
        $auto = $auto == 'auto' ? true : false;
        
        $visible = intval(trim($_POST['visible']));
        if ( ! $visible ) {
           $visible = '';
        }
        
        $linkimage = isset($_POST['linkimage']) ? true : false;
        if ( ! $linkimage ) {
           $linkimage = '';
        }
        $scroll = esc_html(trim($_POST['scroll']));
        if ( ! $scroll ) {
           $scroll = '';
        }
        $scroll = isset($scroll) == "" ? 1 : '';
        $sliderheight = intval(trim($_POST['sliderheight']));
        if ( ! $sliderheight ) {
           $sliderheight = '';
        }
        $fadein = intval($_POST['fadein']);
        if ( ! $fadein ) {
           $fadein = '';
        }
        $imageheight = intval(trim($_POST['imageheight']));
        if ( ! $imageheight ) {
           $imageheight = '';
        }
        $imagewidth = intval(trim($_POST['imagewidth']));
        if ( ! $imagewidth ) {
           $imagewidth = '';
        }
        $resizeImages = intval(trim($_POST['resizeImages']));
        if ( ! $resizeImages ) {
           $resizeImages = '';
        }
        $scollerBackground = esc_html(trim($_POST['scollerBackground']));
        if ( ! $scollerBackground ) {
           $scollerBackground = '';
        }
        $scollergradient = esc_html(trim($_POST['scollergradient']));
        if ( ! $scollergradient ) {
           $scollergradient = '';
        }

        $data = array();
        $data['linkimage'] = $linkimage;
        $data['sliderheight'] = $sliderheight;
        $data['auto'] = $auto;
        $data['fadein'] = $fadein;
        $data['imageheight'] = $imageheight;
        $data['imagewidth'] = $imagewidth;
        $data['visible'] = $visible;
        $data['scroll'] = $scroll;
        $data['resizeImages'] = $resizeImages;
        $data['scollerBackground'] = $scollerBackground;
        $data['scollergradient'] = $scollergradient;

        $settings = update_option('covercarousel_slider_settings', $data);
        $covercarousel_message = array();
        $covercarousel_message['type'] = 'success';
        $covercarousel_message['message'] = 'Settings saved successessfully.';
        update_option('covercarousel_message', $covercarousel_message);
    }
   
} 

    $settings = get_option('covercarousel_slider_settings');
    ?>
    <div id="poststuff">
        <div id="post-body"  class="metabox-holder columns-2" >
            <div id="post-body-content">
                <div class="wrap">
                    <table>
                        <tr>
                            <td><a href="https://twitter.com/kuldipraghu" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @kuldipraghu</a>
                                <script>!function(d, s, id){var js, fjs = d.getElementsByTagName(s)[0]; if (!d.getElementById(id)){js = d.createElement(s); js.id = id; js.src = "//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); }}(document, "script", "twitter-wjs");</script></td>
                        </tr>
                    </table>
                    <?php
                    $messages = get_option('covercarousel_message');
                    $type = '';
                    $message = '';
                    if (isset($messages['type']) and $messages['type'] != "") {
                        $type = $messages['type'];
                        $message = $messages['message'];
                    }
                    if ($type == 'error') {
                        echo "<div class='errormsg'>";
                        echo $message;
                        echo "</div>";
                    } else if ($type == 'success') {
                        echo "<div class='successmsg'>";
                        echo $message;
                        echo "</div>";
                    }
                    update_option('covercarousel_message', array());
                    ?>
                    <h2>3D Cover Carousel Settings</h2>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <form method="post" action="" id="scrollersettiings" name="scrollersettiings" >
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Scroll</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input style="width:20px;" type='radio' <?php if ($settings['auto'] == true) {
                        echo "checked='checked'";
                    } ?>  name='isauto' value='auto' >
                                                        Auto &nbsp;
                                                        <input style="width:20px;" type='radio' name='isauto' <?php if ($settings['auto'] == false) {
                        echo "checked='checked'";
                    } ?> value='manuall' >
                                                        Scroll By Left & Right Arrow
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Allow Image Link ?</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="checkbox" id="linkimage" size="30" name="linkimage" value="" <?php if ($settings['linkimage'] == true) {
                        echo "checked='checked'";
                    } ?> style="width:20px;">
                                                        &nbsp;Add link to image ?
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Slider Height</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="textbox" id="sliderheight" required="" name="sliderheight" value="<?php echo $settings['sliderheight'] ?>" style="width:100px;">
                                                        &nbsp;Set slider height ex. 400
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Fade In Time</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="text" id="fadein" size="30" required="" name="fadein" value="<?php echo $settings['fadein']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Slider Background color</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="text" id="scollerBackground" size="30" name="scollerBackground" value="<?php echo $settings['scollerBackground']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Slider Gradient color</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="text" id="scollergradient" size="30" name="scollergradient" value="<?php echo $settings['scollergradient']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Visible Image</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="text" id="visible" size="30" name="visible" value="<?php echo $settings['visible']; ?>" maxlength="1" min="1" max="9" required=""  style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            specifies the number of items visible at all times within the slider minimum 1 and maximum 9.
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Resized Image Height</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="text" id="imageheight" size="30" name="imageheight" value="<?php echo $settings['imageheight']; ?>" maxlength="1" min="1" max="9" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            Define slider height no more than 296px becuase image streched and not displayed at proper.
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Resized Image Width</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input type="text" id="imagewidth" size="30" name="imagewidth" value="<?php echo $settings['imagewidth']; ?>" maxlength="1" min="1" max="9" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            Define slider width no more than 297px becuase image streched and not displayed at proper.
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3>
                                            <label>Physically resize images ?</label>
                                        </h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td><input style="width:20px;" type='radio' <?php if ($settings['resizeImages'] == 1) {
                        echo "checked='checked'";
                    } ?>  name='resizeImages' value='1' >
                                                        Yes &nbsp;
                                                        <input style="width:20px;" type='radio' name='resizeImages' <?php if ($settings['resizeImages'] == 0) {
                        echo "checked='checked'";
                    } ?> value='0' >
                                                        Resize using css
                                                        <div style="clear:both;padding-top:5px">If you choose "<b>Resize using css</b>" the quality will be good but some times large images takes time to load </div>
                                                        <div></div></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <input type="submit" name="btnsave" id="btnsave" value="Save Changes" class="button-primary">
                                    <?php wp_nonce_field( 'covercarouselaction', 'covercarouselnonce' ); ?>
                                    &nbsp;&nbsp;
                                    <input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href = 'admin.php?page=covercarousel_slider_image_management'">
                                </form>
                                <p style="color:#000; font-weight: bold;">Note :- If slider not working then please check have you include jquery files in your template or theme.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h3 class="hndle"><span></span>Website Templates & Themes</h3>
                    <div class="inside">
                        <center>
                            <a href="https://themeforest.net/user/kuldip1/portfolio?ref=kuldip1" target="_blank"><img border="0" src="/images/theme_forest_250x250.jpg" width="250" height="250"></a>
                        </center>
                        <div style="margin:10px 5px"></div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle"><span></span>Recommended WordPress Themes</h3>
                    <div class="inside">
                        <center>
                            <a target="_blank" href="https://codecanyon.net/?ref=kuldip1"><img src="images/code_canyon_250x250.jpg" alt="WP Engine" border="0"></a>
                        </center>
                        <div style="margin:10px 5px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <?php
}

function covercarousel_thumbnail_image_management() {
    $url = plugin_dir_url(__FILE__);
    $action = 'gridview';
    global $wpdb;
    if (isset($_GET['action']) and $_GET['action'] != '') {
        $action = esc_html(trim($_GET['action']));
        if(!$action){
            $action = '';
        }
    }

    if (strtolower($action) == strtolower('gridview')) {
        $wpcurrentdir = dirname(__FILE__);
        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
        require_once "$wpcurrentdir/Pager/Pager.php";
        ?>
        <div class="wrap">
            <!--[if !IE]><!-->
            <!--<![endif]-->
            <table>
                <tr>
                    <td>
                        <a href="https://twitter.com/kuldipraghu" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @kuldipraghu</a>
                        <script>!function(d, s, id){var js, fjs = d.getElementsByTagName(s)[0]; if (!d.getElementById(id)){js = d.createElement(s); js.id = id; js.src = "//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); }}(document, "script", "twitter-wjs");</script>
                    </td>
                </tr>
            </table>
                        <?php
                        $messages = get_option('covercarousel_message');
                        $type = '';
                        $message = '';
                        if (isset($messages['type']) and $messages['type'] != "") {
                            $type = $messages['type'];
                            $message = $messages['message'];
                        }
                        if ($type == 'error') {
                            echo "<div class='errormsg'>";
                            echo $message;
                            echo "</div>";
                        } else if ($type == 'success') {
                            echo "<div class='successmsg'>";
                            echo $message;
                            echo "</div>";
                        }
                        update_option('covercarousel_message', array());
                        ?>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="icon32 icon32-posts-post" id="icon-edit"><br>
                        </div>
                        <h1>3D Cover Carousel Images</h1>
        <?php
        $settings = get_option('covercarousel_slider_settings');
        $visibleImages = $settings['visible'];
        $query = "SELECT * FROM " . $wpdb->prefix . "covercarousel_slider order by createdon desc";
        $rows = $wpdb->get_results($query, 'ARRAY_A');
        $rowCount = sizeof($rows);
        if ($rowCount < $visibleImages) {
            ?>
                            <a class="button add-new-h2" href="admin.php?page=covercarousel_slider_image_management&action=addcarousel">Add New 3D Cover Carousel</a>
                                        <?php } ?>
                        <form method="POST" action="admin.php?page=covercarousel_slider_image_management&action=deleteselected"  id="posts-filter">
                            <br class="clear">
                                        <?php if ($rowCount < $visibleImages) { ?>
                                <h4 style="color: green">You are maximum added <?php echo $visibleImages; ?> images</h4>
                                        <?php } else { ?>
                                <h4 style="color: red">Please increase your visible image setting if you want to add more image.</h4>
                                        <?php } ?>
                            <div id="no-more-tables">
                                <table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf" >
                                    <thead>
                                        <tr>
                                            <th><span>Title</span></th>
                                            <th><span>Image</span></th>
                                            <th><span>Published On</span></th>
                                            <th><span>Edit</span></th>
                                            <th><span>Delete</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="the-list">
                                        <?php
                                        if (count($rows) > 0) {
                                            $params = array(
                                                'mode' => 'Sliding',
                                                'perPage' => 10,
                                                'delta' => 10,
                                                'itemData' => $rows,
                                                'fixFileName' => false,
                                            );
                                            $pager = & Pager::factory($params);
                                            $pageset = $pager->getPageData();
                                            $rows = $pageset;
                                            foreach ($rows as $row) {
                                                $id = $row['id'];
                                                $editlink = "admin.php?page=covercarousel_slider_image_management&action=addcarousel&id=$id";
                                                $deletelink = "admin.php?page=covercarousel_slider_image_management&action=delete&id=$id";
                                                $date = date("F j, Y", strtotime($row['createdon']));
                                                ?>
                                                <tr valign="top" >
                                                    <td data-title="Title" class="alignCenter"><strong><?php echo stripslashes($row['title']) ?></strong></td>
                                                    <td data-title="image" class="alignCenter" ><img height="100" width="100" src="<?php echo $url . 'covercarouselimages/' . $row['image_name']; ?>" /></td>
                                                    <td class="alignCenter"   data-title="Published On" ><?php echo $date; ?></td>
                                                    <td class="alignCenter"   data-title="Edit Record" ><strong><a href='<?php echo $editlink; ?>' title="edit">Edit</a></strong></td>
                                                    <td class="alignCenter"   data-title="Delete Record" ><strong><a href='<?php echo $deletelink; ?>' onclick="return confirmDelete();"  title="delete">Delete</a> </strong></td>
                                                </tr>
                <?php
            }
        } else {
            ?>
                                            <tr valign="top" class="" id="">
                                                <td colspan="5" data-title="No Record" align="center"><strong>No Cover Carousel Images Found</strong></td>
                                            </tr>
        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
        <?php
        if (sizeof($rows) > 0) {
            $links = $pager->getLinks();
            echo "<div class='paggingDiv' style='padding-top:10px'>";
            echo $links['all'];
            echo "</div>";
        }
        ?>
                            <br/>
                        </form>
                        <div class="clear"></div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div class="postbox">
                            <h3 class="hndle"><span></span>Website Templates & Themes</h3>
                            <div class="inside">
                                <center>
                                    <a href="https://themeforest.net/user/kuldip1/portfolio?ref=kuldip1" target="_blank"><img border="0" src="/images/theme_forest_250x250.jpg" width="250" height="250"></a>
                                </center>
                                <div style="margin:10px 5px"></div>
                            </div>
                        </div>
                        <div class="postbox">
                            <h3 class="hndle"><span></span>Recommended WordPress Themes</h3>
                            <div class="inside">
                                <center>
                                    <a target="_blank" href="https://codecanyon.net/?ref=kuldip1"><img src="images/code_canyon_250x250.jpg" alt="WP Engine" border="0"></a>
                                </center>
                                <div style="margin:10px 5px"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <?php
    } else if (strtolower($action) == strtolower('addcarousel')) {
        if (isset($_POST['btnsave'])) {
            if (isset($_POST['imageid'])) {
                $location = 'admin.php?page=covercarousel_slider_image_management';
                $complete_url = wp_nonce_url( $location, 'addpost_'.$post->ID );
                $title = esc_html(trim(addslashes($_POST['imagetitle'])));
                if(!$title){
                    $title = '';
                }
                $imageurl = esc_html(trim($_POST['imageurl']));
                if(!$imageurl){
                    $imageurl = '';
                }
                $imageid = esc_html(trim($_POST['imageid']));
                if(!$imageid){
                    $imageid = '';
                }
                $imagename = "";
                if ($_FILES["image_name"]['name'] != "" and $_FILES["image_name"]['name'] != null) {
                    if ($_FILES["image_name"]["erroror"] > 0) {
                        $covercarousel_message = array();
                        $covercarousel_message['type'] = 'error';
                        $covercarousel_message['message'] = 'Error while file uploading.';
                        update_option('covercarousel_message', $covercarousel_message);
                        echo "<script type='text/javascript'> location.href='$complete_url';</script>";
                        exit;
                    } else {
                        $wpcurrentdir = dirname(__FILE__);
                        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                        $imagename = $_FILES["image_name"]["name"];
                        $imageUploadTo = $wpcurrentdir . '/covercarouselimages/' . $_FILES["image_name"]["name"];
                        move_uploaded_file($_FILES["image_name"]["tmp_name"], $imageUploadTo);
                    }
                }
                try {
                    if ($imagename != "") {
                        $query = "update " . $wpdb->prefix . "covercarousel_slider set title='$title',image_name='$imagename',
                            custom_link='$imageurl' where id=$imageid";
                    } else {
                        $query = "update " . $wpdb->prefix . "covercarousel_slider set title='$title',
                            custom_link='$imageurl' where id=$imageid";
                    }
                    $wpdb->query($query);

                    $covercarousel_message = array();
                    $covercarousel_message['type'] = 'success';
                    $covercarousel_message['message'] = 'image updated successessfully.';
                    update_option('covercarousel_message', $covercarousel_message);
                } catch (Exception $e) {

                    $covercarousel_message = array();
                    $covercarousel_message['type'] = 'error';
                    $covercarousel_message['message'] = 'Error while updating image.';
                    update_option('covercarousel_message', $covercarousel_message);
                }
                echo "<script type='text/javascript'> location.href='$complete_url';</script>";
                exit;
            } else {
                $location = 'admin.php?page=covercarousel_slider_image_management';
                $complete_url = wp_nonce_url( $location, 'addpost_'.$post->ID );
                $title = esc_html(trim(addslashes($_POST['imagetitle'])));
                if(!$title){
                    $title = '';
                }
                $imageurl = esc_html(trim($_POST['imageurl']));
                if(!$imageurl){
                    $imageurl = '';
                }
                $createdOn = date('Y-m-d h:i:s');
                if (function_exists('date_i18n')) {
                    $createdOn = date_i18n('Y-m-d' . ' ' . get_option('time_format'), false, false);
                    if (get_option('time_format') == 'H:i')
                        $createdOn = date('Y-m-d H:i:s', strtotime($createdOn));
                    else
                        $createdOn = date('Y-m-d h:i:s', strtotime($createdOn));
                }
                if ($_FILES["image_name"]["erroror"] > 0) {
                    $covercarousel_message = array();
                    $covercarousel_message['type'] = 'error';
                    $covercarousel_message['message'] = 'Error while file uploading.';
                    update_option('covercarousel_message', $covercarousel_message);
                    echo "<script type='text/javascript'> location.href='$complete_url';</script>";
                    exit;
                } else {
                    $location = 'admin.php?page=covercarousel_slider_image_management';
                    try {
                        $wpcurrentdir = dirname(__FILE__);
                        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                        $imagename = $_FILES["image_name"]["name"];
                        $imageUploadTo = $wpcurrentdir . '/covercarouselimages/' . $_FILES["image_name"]["name"];
                        move_uploaded_file($_FILES["image_name"]["tmp_name"], $imageUploadTo);

                        $query = "INSERT INTO " . $wpdb->prefix . "covercarousel_slider (title, image_name,createdon,custom_link) 
                            VALUES ('$title','$imagename','$createdOn','$imageurl')";

                        $wpdb->query($query);

                        $covercarousel_message = array();
                        $covercarousel_message['type'] = 'success';
                        $covercarousel_message['message'] = 'New image added successessfully.';
                        update_option('covercarousel_message', $covercarousel_message);
                    } catch (Exception $e) {
                        $covercarousel_message = array();
                        $covercarousel_message['type'] = 'error';
                        $covercarousel_message['message'] = 'Error while adding image.';
                        update_option('covercarousel_message', $covercarousel_message);
                    }
                }
                echo "<script type='text/javascript'> location.href='$complete_url';</script>";
                exit;
            }
        } else {
            ?>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="wrap">
                            <?php
                            if (isset($_GET['id']) and $_GET['id'] > 0) {
                                $id = intval($_GET['id']);
                                if(!$id){
                                    $id = '';
                                }
                                $query = "SELECT * FROM " . $wpdb->prefix . "covercarousel_slider WHERE id=$id";
                                $myrow = $wpdb->get_row($query);
                                if (is_object($myrow)) {
                                    $title = stripslashes($myrow->title);
                                    $image_link = $myrow->custom_link;
                                    $image_name = stripslashes($myrow->image_name);
                                }
                                ?>
                                <h2>Update Cover Carousel Image </h2>
            <?php
            } else {
                $title = '';
                $image_link = '';
                $image_name = '';
                ?>
                                <h2>Add Cover Carousel Images</h2>
                                                        <?php } ?>
                            <br/>
                            <div id="poststuff">
                                <div id="post-body" class="metabox-holder columns-2">
                                    <div id="post-body-content">
                                        <form method="post" action="" id="addimage" name="addimage" enctype="multipart/form-data" >
                                            <div class="stuffbox" id="namediv" style="width:100%;">
                                                <h3>
                                                    <label for="link_name">Image Title</label>
                                                </h3>
                                                <div class="inside">
                                                    <input type="text" id="imagetitle"   size="30" name="imagetitle" value="<?php echo $title; ?>">
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                    <div style="clear:both"></div>
                                                </div>
                                            </div>
                                            <div class="stuffbox" id="namediv" style="width:100%;">
                                                <h3>
                                                    <label for="link_name">Image Url(
                                                    <?php _e('On click redirect to this url.'); ?>
                                                        )</label>
                                                </h3>
                                                <div class="inside">
                                                    <input type="text" id="imageurl" class="url"   size="30" name="imageurl" value="<?php echo $image_link; ?>">
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                    <div style="clear:both"></div>
                                                    <p>
                                            <?php _e('On image click users will redirect to this url.'); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="stuffbox" id="namediv" style="width:100%;">
                                                <h3>
                                                    <label for="link_name">Upload Image</label>
                                                </h3>
                                                <div class="inside" id="fileuploaddiv">
            <?php if ($image_name != "") { ?>
                                                        <div><b>Current Image : </b><a id="currImg" href="<?php echo $url; ?>covercarouselimages/<?php echo $image_name; ?>" target="_new"><img src="<?php echo $url . 'covercarouselimages/' . $image_name; ?>" height="50" width="50" /></a></div>
            <?php } ?>
                                                    <input type="file" name="image_name" onchange="reloadfileupload();"  id="image_name" size="30" />
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </div>
                                            </div>
            <?php if (isset($_GET['id']) and $_GET['id'] > 0) { ?>
                                                <input type="hidden" name="imageid" id="imageid" value="<?php echo $_GET['id']; ?>">
            <?php } ?>
                                            <input type="submit" onclick="return validateFile();" name="btnsave" id="btnsave" value="Save Changes" class="button-primary">
                                            &nbsp;&nbsp;
                                            <input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href = 'admin.php?page=covercarousel_slider_image_management'">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- advertisement here -->
                </div>
            </div>
        <?php
        }
    } else if (strtolower($action) == strtolower('delete')) {
        $location = 'admin.php?page=covercarousel_slider_image_management';
        $deleteId = intval($_GET['id']);
        if(!$deleteId){
            $deleteId = '';
        }
        try {
            $query = "SELECT * FROM " . $wpdb->prefix . "covercarousel_slider WHERE id=$deleteId";
            $myrow = $wpdb->get_row($query);

            if (is_object($myrow)) {
                $image_name = stripslashes($myrow->image_name);
                $wpcurrentdir = dirname(__FILE__);
                $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                $imagename = $_FILES["image_name"]["name"];
                $imagetoDel = $wpcurrentdir . '/covercarouselimages/' . $image_name;
                @unlink($imagetoDel);

                $query = "delete from  " . $wpdb->prefix . "covercarousel_slider where id=$deleteId";
                $wpdb->query($query);

                $covercarousel_message = array();
                $covercarousel_message['type'] = 'success';
                $covercarousel_message['message'] = 'Image deleted successessfully.';
                update_option('covercarousel_message', $covercarousel_message);
            }
        } catch (Exception $e) {
            $covercarousel_message = array();
            $covercarousel_message['type'] = 'error';
            $covercarousel_message['message'] = 'Error while deleting image.';
            update_option('covercarousel_message', $covercarousel_message);
        }
        echo "<script type='text/javascript'> location.href='$location';</script>";
        exit;
    }
}

function covercarousel_previewSliderAdmin() {
    $settings = get_option('covercarousel_slider_settings');
    ?>
    <div style="width: 100%;">
        <div style="float:left;width:69%;">
            <div class="wrap">
                <h2>3D Carousel Preview</h2>
                <br>
                <div id="poststuff">
                    <div id="post-body" class="">
                        <div id="post-body-content">
                            <div style="clear: both;"></div>
                                <?php $url = plugin_dir_url(__FILE__); ?>
                            <style>
                                #showcase {
                                    width: 100%;
                                    height: <?php echo isset($settings['sliderheight']) ? $settings['sliderheight'] : '400'; ?>px;
                                    background: <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?>; /* Old browsers */
                                    background: -moz-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* FF3.6+ */
                                    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?>), color-stop(100%,<?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?>)); /* Chrome, Safari4+ */
                                    background: -webkit-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* Chrome10+, Safari5.1+ */
                                    background: -o-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* Opera 11.10+ */
                                    background: -ms-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* IE10+ */
                                    background: linear-gradient(to bottom, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* W3C */
                                    border-radius: 8px;
                                    visibility: hidden;
                                }
                            </style>
                            <div id="showcase" class="noselect">
                                <?php
                                global $wpdb;
                                $imageheight = $settings['imageheight'];
                                $imagewidth = $settings['imagewidth'];
                                $query = "SELECT * FROM " . $wpdb->prefix . "covercarousel_slider order by createdon desc";
                                $rows = $wpdb->get_results($query);
                                if (count($rows) > 0) {
                                    foreach ($rows as $row) {
                                        $wpcurrentdir = dirname(__FILE__);
                                        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                                        $imagename = $row->image_name;
                                        $imageUploadTo = $wpcurrentdir . '/covercarouselimages/' . $imagename;
                                        $imageUploadTo = str_replace("\\", "/", $imageUploadTo);
                                        $pathinfo = pathinfo($imageUploadTo);
                                        $filenamewithoutextension = $pathinfo['filename'];
                                        $outputimg = "";
                                        if ($settings['resizeImages'] == 0) {
                                            $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                                        } else {
                                            $imagetoCheck = $wpcurrentdir . '/covercarouselimages/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                                            if (file_exists($imagetoCheck)) {
                                                $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                                            } else {
                                                if (function_exists('wp_get_image_editor')) {
                                                    $image = wp_get_image_editor($wpcurrentdir . "/covercarouselimages/" . $row->image_name);
                                                    if (!is_wp_erroror($image)) {
                                                        $image->resize($imagewidth, $imageheight, true);
                                                        $image->save($imagetoCheck);
                                                        $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                                                    } else {
                                                        $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                                                    }
                                                } else if (function_exists('image_resize')) {
                                                    $return = image_resize($wpcurrentdir . "/covercarouselimages/" . $row->image_name, $imagewidth, $imageheight);
                                                    if (!is_wp_erroror($return)) {
                                                        $isrenamed = rename($return, $imagetoCheck);
                                                        if ($isrenamed) {
                                                            $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                                                        } else {
                                                            $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                                                        }
                                                    } else {
                                                        $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                                                    }
                                                } else {
                                                    $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                                                }
                                            }
                                        }
                                        ?>
            <?php if ($settings['linkimage'] == true) { ?>
                                            <a target="_blank" href="<?php if ($row->custom_link == "") {
                    echo '#';
                } else {
                    echo $row->custom_link;
                } ?>"><img class="cloud9-item" title="<?php echo $row->title; ?>" style="width:<?php echo $settings['imagewidth']; ?>px;height:<?php echo $settings['imageheight']; ?>px" src="<?php echo $outputimg; ?>" alt="<?php echo $row->title; ?>" /></a>
            <?php } else { ?>
                                            <img class="cloud9-item" title="<?php echo $row->title; ?>" style="width:<?php echo $settings['imagewidth']; ?>px;height:<?php echo $settings['imageheight']; ?>px" src="<?php echo $outputimg; ?>" alt="<?php echo $row->title; ?>" />
            <?php } ?>
        <?php }
    }
    ?>
                            </div>
    <?php if ($settings['auto'] == false) { ?>
                                <div class="nav" class="noselect">
                                    <button class="left"> ← </button>
                                    <button class="right"> → </button>
                                </div>
    <?php } ?>
                        </div>
                        <div class="clear"></div>
                        <script>
                            jQuery(function () {
                            var showcase = jQuery("#showcase")
                                    showcase.CoverCarousel({
                                    yPos: 42,
                                            yRadius: 48,
                                            mirrorOptions: {
                                            gap: 12,
                                                    height: 0.2
                                            },
                                            buttonLeft: jQuery(".nav > .left"),
                                            buttonRight: jQuery(".nav > .right"),
    <?php if ($settings['auto'] == true) { ?>
                                        autoPlay: true,
    <?php } ?>
                                    bringToFront: true,
                                            onRendered: showcaseUpdated,
                                            onLoaded: function () {
                                            showcase.css('visibility', 'visible')
                                                    showcase.css('display', 'none')
                                                    showcase.fadeIn(<?php echo isset($settings['fadein']) ? $settings['fadein'] : 150 ?>)
                                            }
                                    })

                                    function showcaseUpdated(showcase) {
                                    var title = jQuery('#item-title').html(
                                            jQuery(showcase.nearestItem()).attr('alt')
                                            )

                                            var c = Math.cos((showcase.floatIndex() % 1) * 2 * Math.PI)
                                            title.css('opacity', 0.5 + (0.5 * c))
                                    }
                            jQuery('.nav > button').click(function (e) {
                            var b = jQuery(e.target).addClass('down')
                                    setTimeout(function () {
                                    b.removeClass('down')
                                    }, 80)
                            })

                                    jQuery(document).keydown(function (e) {
                            switch (e.keyCode) {
                            case 37:
                                    jQuery('.nav > .left').click()
                                    break
                                    case 39:
                                    jQuery('.nav > .right').click()
                            }
                            })
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <h3>WordPress Post/Page use below Short code</h3>
    <input type="text" value="[cover_carousel_slider]" style="width: 400px;height: 30px" onclick="this.focus(); this.select()" />
    <div class="clear"></div>
    <h3>WordPress theme/template PHP files use below php code</h3>
    <input type="text" value="echo do_shortcode('[cover_carousel_slider]');" style="width: 400px;height: 30px" onclick="this.focus(); this.select()" />
    <div class="clear"></div>
        <?php
    }

    function print_covercarousel_func() {
        $settings = get_option('covercarousel_slider_settings');
        ob_start();
        ?>
    <div style="clear: both;"></div>
        <?php $url = plugin_dir_url(__FILE__); ?>
    <style>
        #showcase {
            width: 100%;
            height: <?php echo isset($settings['sliderheight']) ? $settings['sliderheight'] : '400'; ?>px;
            background: <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?>; /* Old browsers */
            background: -moz-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?>), color-stop(100%,<?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?>)); /* Chrome, Safari4+ */
            background: -webkit-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* Chrome10+, Safari5.1+ */
            background: -o-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* IE10+ */
            background: linear-gradient(to bottom, <?php echo isset($settings['scollerBackground']) ? $settings['scollerBackground'] : '#16235e'; ?> 0%, <?php echo isset($settings['scollergradient']) ? $settings['scollergradient'] : '#ffffff'; ?> 100%); /* W3C */
            border-radius: 8px;
            visibility: hidden;
        }
    </style>
    <div id="showcase" class="noselect">
        <?php
        global $wpdb;
        $imageheight = $settings['imageheight'];
        $imagewidth = $settings['imagewidth'];
        $query = "SELECT * FROM " . $wpdb->prefix . "covercarousel_slider order by createdon desc";
        $rows = $wpdb->get_results($query);
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $wpcurrentdir = dirname(__FILE__);
                $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                $imagename = $row->image_name;
                $imageUploadTo = $wpcurrentdir . '/covercarouselimages/' . $imagename;
                $imageUploadTo = str_replace("\\", "/", $imageUploadTo);
                $pathinfo = pathinfo($imageUploadTo);
                $filenamewithoutextension = $pathinfo['filename'];
                $outputimg = "";
                if ($settings['resizeImages'] == 0) {
                    $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                } else {
                    $imagetoCheck = $wpcurrentdir . '/covercarouselimages/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                    if (file_exists($imagetoCheck)) {
                        $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                    } else {
                        if (function_exists('wp_get_image_editor')) {
                            $image = wp_get_image_editor($wpcurrentdir . "covercarouselimages/" . $row->image_name);
                            if (!is_wp_erroror($image)) {
                                $image->resize($imagewidth, $imageheight, true);
                                $image->save($imagetoCheck);
                                $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                            } else {
                                $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                            }
                        } else if (function_exists('image_resize')) {

                            $return = image_resize($wpcurrentdir . "/covercarouselimages/" . $row->image_name, $imagewidth, $imageheight);
                            if (!is_wp_erroror($return)) {

                                $isrenamed = rename($return, $imagetoCheck);
                                if ($isrenamed) {
                                    $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
                                } else {
                                    $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                                }
                            } else {
                                $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                            }
                        } else {

                            $outputimg = plugin_dir_url(__FILE__) . "covercarouselimages/" . $row->image_name;
                        }
                    }
                }
                if ($settings['linkimage'] == true) {
                    ?>
                    <a target="_blank" href="<?php if ($row->custom_link == "") {
                        echo '#';
                    } else {
                        echo $row->custom_link;
                    } ?>"><img class="cloud9-item" title="<?php echo $row->title; ?>" style="width:<?php echo $settings['imagewidth']; ?>px;height:<?php echo $settings['imageheight']; ?>px" src="<?php echo $outputimg; ?>" alt="<?php echo $row->title; ?>" /></a>
            <?php } else { ?>
                    <img class="cloud9-item" title="<?php echo $row->title; ?>" style="width:<?php echo $settings['imagewidth']; ?>px;height:<?php echo $settings['imageheight']; ?>px" src="<?php echo $outputimg; ?>" alt="<?php echo $row->title; ?>" />
            <?php
            }
        }
    }
    ?>
    </div>
    <?php if ($settings['auto'] == false) { ?>
        <div class="nav" class="noselect">
            <button class="left"> ← </button>
            <button class="right"> → </button>
        </div>
    <?php } ?>
    <script>
        jQuery(function () {
        var showcase = jQuery("#showcase")
                showcase.CoverCarousel({
                yPos: 42,
                        yRadius: 48,
                        mirrorOptions: {
                        gap: 12,
                                height: 0.2
                        },
                        buttonLeft: jQuery(".nav > .left"),
                        buttonRight: jQuery(".nav > .right"),
    <?php if ($settings['auto'] == true) { ?>
                    autoPlay: true,
    <?php } ?>
                bringToFront: true,
                        onRendered: showcaseUpdated,
                        onLoaded: function () {
                        showcase.css('visibility', 'visible')
                                showcase.css('display', 'none')
                                showcase.fadeIn(<?php echo isset($settings['fadein']) ? $settings['fadein'] : 150 ?>)
                        }
                })

                function showcaseUpdated(showcase) {
                var title = jQuery('#item-title').html(
                        jQuery(showcase.nearestItem()).attr('alt')
                        )

                        var c = Math.cos((showcase.floatIndex() % 1) * 2 * Math.PI)
                        title.css('opacity', 0.5 + (0.5 * c))
                }
        jQuery('.nav > button').click(function (e) {
        var b = jQuery(e.target).addClass('down')
                setTimeout(function () {
                b.removeClass('down')
                }, 80)
        })

                jQuery(document).keydown(function (e) {
        switch (e.keyCode) {
        case 37:
                jQuery('.nav > .left').click()
                break
                case 39:
                jQuery('.nav > .right').click()
        }
        })
        });
    </script>
    <?php
    $output = ob_get_clean();
    return $output;
}
?>