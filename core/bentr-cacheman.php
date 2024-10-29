<?php
IF (isset($_POST['Backup'])){
  // create the gzipped tarfile.
  $path = dirname( __FILE__ ); 
  exec( "tar cfvz $path/benstranslatorbackup.tar.gz $path/ben-translate-cache");
  ?>
  <h2>Bens Translator - <?php _e('Cache Download', 'bens-translator') ?></h2>
  <br />
  <br />
  <a href="<?php echo $location ?>/wp-content/plugins/bens-translator/core/benstranslatorbackup.tar.gz">Download</a>
  <br />
  <p><?php _e('You may need', 'bens-translator') ?> <a href="http://www.7-zip.org"><?php _e('7ZIP', 'bens-translator') ?></a></p>
  <?php
  exit();
;

  exit();
}

IF (isset($_POST['Delete'])){
  ?>
  <h2>Bens Translator - <?php _e('Cache Delete', 'bens-translator') ?></h2>
  <br />
  <br />
  <form id="bentr_form" name="form1" method="post" action="<?php echo $location ?>">
    <br /><br /><?php _e('Are you Sure? This will', 'bens-translator') ?><b><?php _e('delete all cached pages', 'bens-translator') ?></b>!
    <input type="submit" name="Delete_confirm" value="Yes, Please Delete" />
  </form>
  <?php
  exit();
}

IF (isset($_POST['Delete_confirm'])){
  function delete_directory($dirname)  {
      if (is_dir($dirname))
         $dir_handle = opendir($dirname);
      if (!$dir_handle)
         return false;
      while($file = readdir($dir_handle)) {
         if ($file != "." && $file != "..") {
          if (!is_dir($dirname."/".$file))
             unlink($dirname."/".$file);
            else
              delete_directory($dirname.'/'.$file);    
        }
      }
     closedir($dir_handle);
     rmdir($dirname);
    return true;
  }
  
  
  $dirname = WP_CONTENT_DIR . "/ben-translate-cache";
  delete_directory($dirname);
  
  ?>
  <h2>Bens Translator - <?php _e('Cache Download', 'bens-translator') ?></h2>
  <br />
  <br />
  <p><?php _e('Cache has been deleted', 'bens-translator') ?></p>
  <?php
  exit();
}

IF (isset($_POST['Flush'])){
  ?>
  <h2>Bens Translator - <?php _e('Cache Flush', 'bens-translator') ?></h2>
  <br />
  <br />
  <form id="bentr_form" name="form1" method="post" action="<?php echo $location ?>">
    <br /><br /><?php _e('Are you Sure? This will delete all expired cached pages', 'bens-translator') ?>!
    <input type="submit" name="Flush_confirm" value="Yes, Please Flush" />
  </form>
  <?php
  exit();
}

IF (isset($_POST['Flush_confirm'])){
  bentr_erase_common_cache_files($post_ID);
  ?>
  <h2>Bens Translator - <?php _e('Cache Download', 'bens-translator') ?></h2>
  <br />
  <br />
  <p><?php _e('Cache has been Flushed', 'bens-translator') ?></p>
  <?php
  exit();
}

?>
<h2>Bens Translator - <?php _e('Cache Managenent', 'bens-translator') ?></h2>
<p><?php _e('This page provides management options for your cache', 'bens-translator') ?></p>
<div style="width:250px;">
<div style="height:35px;">
  <form id="bentr_form" name="form1" method="post" action="<?php echo $location ?>">
    <?php _e('Backup', 'bens-translator') ?>
    <input type="submit" name="Backup" value="Backup" style="float:right;" />
  </form>
</div>
<div style="height:35px;">
  <form id="bentr_form" name="form1" method="post" action="<?php echo $location ?>">
    <?php _e('Delete Cache', 'bens-translator') ?>
    <input type="submit" name="Delete" value="Delete" style="float:right;" />
  </form>
</div>
<div style="height:35px;">
  <form id="bentr_form" name="form1" method="post" action="<?php echo $location ?>">
    <?php _e('Flush Expired Pages', 'bens-translator') ?>
    <input type="submit" name="Flush" value="Flush" style="float:right;" />
  </form>
</div>
</div>