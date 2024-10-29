<?php
require_once "functions.php";
$chosen_langs = get_option('bentr_preferred_languages');
$link = WP_PLUGIN_URL . '/bens-translator/images/cache/';
?>

<script type="text/javascript" src="<?php echo WP_PLUGIN_URL . '/bens-translator/js/jquery.min.js'; ?>"></script> 
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL . '/bens-translator/js/jquery.tablesorter.min.js'; ?>"></script> 
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL . '/bens-translator/js/jquery.tablesorter.pager.js'; ?>"></script> 
<script type="text/javascript">
$(function() {
  $("#post-table")
    .tablesorter({widthFixed: true, widgets: ['zebra']})
    .tablesorterPager({container: $("#pager"), positionFixed: false})
    .show();
  $("#please-wait").hide();
});
</script> 
  
<div>
  <h2>Bens Translator - <?php _e('Translated Pages', 'bens-translator') ?></h2>
</div>

<div id="pager" class="pager"> 
  <form action="<?php $location ?>" method="post">
    <img src="<?php echo $link . 'first.png'; ?>" alt="first" class="first"/> 
    <img src="<?php echo $link . 'prev.png'; ?>" alt="prev" class="prev"/> 
    <label>Page: </label><input type="text" class="pagedisplay" readonly="readonly" size="10" /> 
    <img src="<?php echo $link . 'next.png'; ?>" alt="next" class="next"/> 
    <img src="<?php echo $link . 'last.png'; ?>" alt="last" class="last"/> 
    <label style="margin-left:10px;"><?php _e('Posts per Page', 'bens-translator') ?>
      <select class="pagesize"> 
        <option selected="selected"  value="10">10</option> 
        <option value="20">20</option> 
        <option value="30">30</option> 
        <option  value="40">40</option> 
        <option  value="50">50</option> 
        <option  value="50">100</option> 
        <option  value="50">200</option>
        <option  value="50">300</option> 
        <option  value="50">400</option> 
        <option  value="50">500</option> 
        <option  value="50">1000</option>  
      </select> 
    </label>
  <label>Type: </label>
  <select name="type">
    <option <?php if ($_POST['type'] == 'posts') { echo "SELECTED"; } ?> value="posts"><?php _e('Posts/pages', 'bens-translator') ?></option>
    <option <?php if ($_POST['type'] == 'categories') { echo "SELECTED"; } ?> value="categories"><?php _e('Categories', 'bens-translator') ?></option>
    <option <?php if ($_POST['type'] == 'tags') { echo "SELECTED"; } ?> value="tags"><?php _e('Tags', 'bens-translator') ?></option>
  </select>
  <?php  if ($_POST['type'] == 'posts' || !isset($_POST['type']) ) {  ?>
    <label>Sort by: </label>
    <select name="sort">
      <option value="name" <?php if( $sortby == 'post_title') echo "selected"; ?>><?php _e('Name', 'bens-translator') ?></option>
      <option value="date" <?php if( $sortby == 'post_date') echo "selected"; ?>><?php _e('Date', 'bens-translator') ?></option>
    </select>  
  <?php } ?>
  <input type="submit" value="Submit" />
  </form> 
</div> 
<br />
<div>
  <div id="please-wait"> <img src="<?php echo WP_PLUGIN_URL . '/bens-translator/images/throbber.gif'; ?>" alt="Loading..." style="vertical-align: -2px;" /> <?php _e('Please wait. Data is loading...', 'bens-translator') ?></div>

  <table id="post-table" style="border:1px solid grey; display: none;width:95%;">
  <thead style="border:1px solid grey;">
    <tr style="border:1px solid grey;">
      <td style="width: 360px; font-weight: bold;"><?php _e('Page', 'bens-translator') ?></td>
    <?php
      foreach($chosen_langs as $lang){
        $flg_image_url = bentr_get_flag_image($lang);
        echo "<td style='width: 22px;'><img src=\"$flg_image_url\" alt=\"$lang\" title=\"$lang\"  border=\"0\" /></td>";
      }
    ?>
    </tr>
  </thead>
<?php    
if ($_POST['sort'] == 'name') {
  $sortby = 'post_title';
} else if ($_POST['sort'] == 'date') {
  $sortby = 'post_date';
} else {
  $sortby = 'post_title';
}

if (!isset($_POST['type'])){
  $no_post = "true";
}
  
if ($_POST['type'] == 'posts' || $no_post == 'true' ) {
  $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' ORDER BY $wpdb->posts.$sortby ASC";
  $pageposts = $wpdb->get_results($querystr, OBJECT);
  $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' ORDER BY $wpdb->posts.$sortby ASC";
  $pageposts_count = $wpdb->get_results($querystr, OBJECT);
if ($start == '0' || !isset($start)){
  array_unshift($pageposts, BLOG_HOME);
  }

    if ($pageposts):
    foreach ($pageposts as $post):
    setup_postdata($post);
    $home = BLOG_HOME;
    ?>
      <tr>
        <td>        
          <?php 
          if ($post == BLOG_HOME){
            echo "<a href=\"$home\" title=\"Link to Homepage\">Home</a>";
          }
          else {
          $title = get_the_title();
          $short_name = substr($title, 0 , 50);
          ?>
            <a href="<?php the_permalink(); ?>" title="Link to <?php the_title(); ?>"><?php echo $short_name; ?></a>
          <?php
          }
          
          ?>
        </td>
      <?php
      foreach($chosen_langs as $lang){
      if ($post == BLOG_HOME){
        $permalink = BLOG_HOME;
      }
      else{
        $permalink = get_permalink();
      }

      $hash = preg_replace("/".BLOG_HOME_ESCAPED."/", "/" . $lang, $permalink );
      $hash = bentr_hashReqUri($hash);
      $cachedir = $bentr_cache_dir;
      $staledir = $bentr_stale_dir;
      $filename = $cachedir . '/' . $lang . '/' . $hash; 
      $filename_stale = $staledir . '/' . $lang . '/' . $hash; 
      $permalink_lang = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME . "/$lang" , $permalink );
      $permalink_lang = $permalink_lang . "/";
      $picture = WP_PLUGIN_URL . '/bens-translator/images/lights';
      
        if (file_exists($filename) && filesize($filename) > 0) {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Cached\" src=\"$picture/green-ball.gif\" alt=\"cached\" /></a></td>";
        }
        elseif (file_exists($filename_stale) && filesize($filename_stale) > 0) {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Stale\" src=\"$picture/yellow-ball.gif\" alt=\"stale\" /></a></td>";
        }
        else {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Not Cached\" src=\"$picture/red-ball.gif\" alt=\"not cached\" /></a></td>";
        }
      }
    echo "</tr>";
    
    endforeach;
    else :
    endif;
}
if ($_POST['type'] == 'categories') {
  $categories = get_categories();
  foreach($categories as $category) { 
    $category_name = $category->name;
    $category_link = get_category_link( $category->term_id );
    echo "<td>";
    echo "<a href=\"$category_link\">$category_name</a>";
    echo "</td>";

    foreach($chosen_langs as $lang){
      $permalink = $category_link;
      $hash = preg_replace("/".BLOG_HOME_ESCAPED."/", "/" . $lang, $permalink );
      $hash = bentr_hashReqUri($hash);
      $cachedir = $bentr_cache_dir;
      $staledir = $bentr_stale_dir;
      $filename = $cachedir . '/' . $lang . '/' . $hash; 
      $filename_stale = $staledir . '/' . $lang . '/' . $hash; 
      $permalink_lang = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME . "/$lang" , $permalink );
      $permalink_lang = $permalink_lang . "/";
      $picture = WP_PLUGIN_URL . '/bens-translator/images/lights';
      
        if (file_exists($filename) && filesize($filename) > 0) {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Cached\" src=\"$picture/green-ball.gif\" alt=\"cached\" /></a></td>";
        }
        elseif (file_exists($filename_stale) && filesize($filename_stale) > 0) {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Stale\" src=\"$picture/yellow-ball.gif\" alt=\"stale\" /></a></td>";
        }
        else {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Not Cached\" src=\"$picture/red-ball.gif\" alt=\"not cached\" /></a></td>";
        }
      }
      echo "</tr>";
    }
    
}
if ($_POST['type'] == 'tags') {
  $posttags = get_tags();
  if (!$posttags){
    Echo "There are no Tags!";
    exit;
  }
  if ($posttags) {
    foreach($posttags as $tag) {
      $tag_name = $tag->name;
      $tag_link = get_tag_link( $tag->term_id );
      echo "<td>";
      echo "<a href=\"$tag_link\">$tag_name</a>";
      echo "</td>";
      
    foreach($chosen_langs as $lang){
      $permalink = $tag_link;
      $hash = preg_replace("/".BLOG_HOME_ESCAPED."/", "/" . $lang, $permalink );
      $hash = bentr_hashReqUri($hash);
      $cachedir = $bentr_cache_dir;
      $staledir = $bentr_stale_dir;
      $filename = $cachedir . '/' . $lang . '/' . $hash; 
      $filename_stale = $staledir . '/' . $lang . '/' . $hash; 
      $permalink_lang = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME . "/$lang" , $permalink );
      $permalink_lang = $permalink_lang . "/";
      $picture = WP_PLUGIN_URL . '/bens-translator/images/lights';
      
        if (file_exists($filename) && filesize($filename) > 0) {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Cached\" src=\"$picture/green-ball.gif\" alt=\"cached\" /></a></td>";
        }
        elseif (file_exists($filename_stale) && filesize($filename_stale) > 0) {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Stale\" src=\"$picture/yellow-ball.gif\" alt=\"stale\" /></a></td>";
        }
        else {
          echo "<td><a href=\"$permalink_lang\"><img title=\"Not Cached\" src=\"$picture/red-ball.gif\" alt=\"not cached\" /></a></td>";
        }
      }
      echo "</tr>";
    }
  }
}
Else {

}

// Finish off Page and Display the Key
?> 
</table>
<br />
<table>
  <tr>
    <td>
      <?php echo "<img title=\"Cached\" src=\"$picture/green-ball.gif\" alt=\"cached\" />"; ?> <?php _e('Sucessfully Cached ', 'bens-translator') ?>
    </td>
  </tr>
  <tr>
    <td>
       <?php echo "<img title=\"Cached\" src=\"$picture/yellow-ball.gif\" alt=\"stale\" />"; ?> <?php _e('In Stale Cache', 'bens-translator') ?>
    </td>
  </tr>
  <tr>
    <td>
      <?php echo "<img title=\"Cached\" src=\"$picture/red-ball.gif\" alt=\"not cached\" />"; ?> <?php _e('Not Cached ', 'bens-translator') ?>
    </td>
  </tr>
</table>
</div>