<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<?php G\Render\include_theme_header(); ?>

<div class="content-width">
  <div class="page-not-found">
  <h1>网站地图</h1>
  <h4><a href="<?php echo G\get_base_url()."/sitemap-index.xml";?>">Index</a></h4>
  <br />

  <h5>Child Sitemaps</h5>
  <br />

  <div>
  <ul>
  <?php
  $cat = get_lists();
  foreach($cat as $key){
  echo "<li><a href=\"".G\get_base_url()."/".get_sitemapsFolder().$key["list_slug"].".xml"."\">".$key["list_name"]."</a></li>";
  }
  ?>
  </ul>
  </div>
  </div>
</div>

<?php G\Render\include_theme_footer(); ?>

<?php if(isset($_REQUEST["deleted"])) { ?>
<script>PF.fn.growl.call("<?php echo (G\get_route_name() == 'user' ? _s('The user has been deleted') : _s('The content has been deleted.')); ?>"); </script>
<?php } ?>