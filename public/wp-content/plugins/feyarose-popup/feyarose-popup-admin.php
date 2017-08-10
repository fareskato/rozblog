<?php

//******************************************
// kick out if access directly:
if (!defined("ABSPATH")) {
    exit;
}
if ( get_magic_quotes_gpc() ) {
    $_POST      = array_map( 'stripslashes_deep', $_POST );
    $_GET       = array_map( 'stripslashes_deep', $_GET );
    $_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
    $_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
}
/*
 * The main function which will get the pages from the database
 */

    if(isset($_POST['reset_cookie'])) {

        echo '<div class="updated"><p><strong>'. __('Cookie reset' ).'</strong></p></div>';
    }
    if ($_POST['save-roses'] == "1") {
        $elements = $_POST['option'];
        $option = $elements;
        update_option('feyarose_settings', serialize($option));
        echo '<div class="updated"><p><strong>'. __('Settings saved' ).'</strong></p></div>';
    }
    $option = unserialize(get_option('feyarose_settings'));
    echo "<div class='wrap'>
        <h2>Rozblog Popup Window Setting Page</h2>
        <hr/>";
    echo "<h3>PLease select which page you want to display in the popup window ! </h3>";
    echo "<form method='POST' action=''><tr><td>";
    echo " <b>Page name : </b> " ?>
<?php
//    Database query object
    global $wpdb;
    $results = $wpdb->get_results(" SELECT * FROM wp_posts  WHERE
                                        (post_type IN ('page' , 'post'))  AND post_status = 'publish'
                                         AND post_content NOT LIKE '%woocommerce%'
                                         ORDER BY post_modified DESC ");
    $list_posts = array("_none" => '- No post or page selected -');
    foreach ($results as $result) :
        $page_id = $result->ID;
        $page_title = $result->ID. ' - ' .$result->post_title;
        $list_posts[$page_id] = $page_title;
    endforeach;

    echo "<select name='option[popup_id]'>";
    // Display the fetched data
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['popup_id'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach;
    echo "</select></td></tr>";
    // Roses start here!
    // Rose 01
    echo "</tr><br>";
    echo "<h3>PLease select which post or page you want to display in the each rose on the home page then click save ! </h3>";
    echo "<tr><td><b>Rose 01</b>"; ?>
    <select name='option[rose01]'>
    <?php
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose01'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<?php
// Rose 02
    echo "</tr><br>";
    echo "<tr><td><b>Rose 02</b>
    <select name='option[rose02]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose02'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<?php
// Rose 03
    echo "</tr><br>";
    echo "<tr><td><b>Rose 03</b>
    <select name='option[rose03]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose03'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<?php
// Rose 04
    echo "</tr><br>";
    echo "<tr><td><b>Rose 04</b>
    <select name='option[rose04]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose04'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<?php
// Rose 05
    echo "</tr><br>";
    echo "<tr><td><b>Rose 05</b>
    <select name='option[rose05]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose05'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<?php
// Rose 06
    echo "</tr><br>";
    echo "<tr><td><b>Rose 06</b>
    <select name='option[rose06]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose06'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<?php
// Rose 07
    echo "</tr><br>";
    echo "<tr><td><b>Rose 07</b>
    <select name='option[rose07]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option['rose07'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr>
<input type="hidden" name="save-roses" value="1" />
 <br><tr><td><input type='submit'  class='button button-primary' name='save' value='Save' /></td></tr>
</form>
<form action="" method="POST">
    <p>Reset Cookie : <input type="submit" name="reset_cookie" value="Reset"/></p>
</form>

<?php

if(isset($_POST['footer-popups'])) {
    $option_footer_popup = $_POST['option'];
    update_option('feyarose_footer_popups', serialize($option_footer_popup));
}
$option_footer_popup = unserialize(get_option('feyarose_footer_popups'));
?>


<form action="" method="POST">
    <input type="hidden" name="footer-popups" value="1" />
    <h2>Footer popups links</h2>
    <?php
    // Footer link Excellence
    echo "<tr><td><b>Excellence popup page</b>
    <select name='option[Premium]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option_footer_popup['Premium'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr><br />

    <?php
    // Footer link Excellence
    echo "<tr><td><b>Delivery popup page</b>
    <select name='option[Delivery]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option_footer_popup['Delivery'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr><br />

    <?php
    // Footer link How we work
    echo "<tr><td><b>How we work popup page</b>
            <select name='option[HowWeWork]'>";
                foreach ($list_posts as $post_id => $post_title) :
                $selected = ($option_footer_popup['HowWeWork'] == $post_id) ? 'selected="selected"' : '';
                echo "<option value='$post_id' $selected>$post_title</option>";
                endforeach; ?>
            </select></td></tr><br />
<?php
    // Footer link How we work
    echo "<tr><td><b>Secure Payment popup page </b>
            <select name='option[SecurePayment]'>";
                foreach ($list_posts as $post_id => $post_title) :
                $selected = ($option_footer_popup['SecurePayment'] == $post_id) ? 'selected="selected"' : '';
                echo "<option value='$post_id' $selected>$post_title</option>";
                endforeach; ?>
            </select></td></tr><br />


    <?php
    // Quality logo popup
    echo "<tr><td><b>Quality popup page </b>
            <select name='option[Quality]'>";
    foreach ($list_posts as $post_id => $post_title) :
        $selected = ($option_footer_popup['Quality'] == $post_id) ? 'selected="selected"' : '';
        echo "<option value='$post_id' $selected>$post_title</option>";
    endforeach; ?>
    </select></td></tr><br />


    <input type='submit'  class='button button-primary' name='save' value='Save' />
</form>

<?php

if(isset($_POST['footer-instagram-shortcode'])) {
    $option_instagram_shortcode = $_POST['option'];
    update_option('feyarose_instagram_shortcode', $option_instagram_shortcode);
}
$option_instagram_shortcode = stripslashes(get_option('feyarose_instagram_shortcode', ''));
?>
<form action="" method="POST">
    <input type="hidden" name="footer-instagram-shortcode" value="1" />
    <h2>Instagram shortcode</h2>
    <tr><td>
            <b>Shortcode</b>
        <input type="text" name="option" value='<?php echo $option_instagram_shortcode; ?>' />
    </td></tr><br />
    <input type='submit'  class='button button-primary' name='save' value='Save' />
</form>
</div>



