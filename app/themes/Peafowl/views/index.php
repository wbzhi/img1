<?php if (!defined('access') or !access) {
    die('This file cannot be directly accessed.');
} ?>
<?php G\Render\include_theme_header(); ?>

<?php
    if (CHV\Settings::get('homepage_style') == 'split') {
        CHV\Render\show_theme_inline_code('snippets/index.js');
        if (function_exists('get_list')) {
            $list = get_list();
            $hasPrev = $list->has_page_prev;
        }
    }
?>

<?php if ($hasPrev == false) {
    ?>
<div id="home-cover" data-content="follow-scroll-opacity">
	<div id="home-cover-slideshow">
        <div class="home-cover-img" data-src="https://tva1.sinaimg.cn/large/0080xEK2ly1gbdmj6xtnfj31z40u0ai7.jpg"></div>
        <div class="home-cover-img" data-src="https://cdn.jsdelivr.net/gh/wbzhi/img/2020/04/23/144cfe.png"></div>
        <div class="home-cover-img" data-src="https://cdn.jsdelivr.net/gh/wbzhi/img/2020/04/23/e23481.png"></div>
    </div>		
    <div id="home-cover-content" class="c20 fluid-column center-box padding-left-10 padding-right-10">
		<?php CHV\Render\show_banner('home_before_title', (function_exists('get_list') ? get_list()->sfw : true)); ?>
		<img src="https://cdn.jsdelivr.net/gh/wbzhi/img/2020/04/23/4e38fa.png" alt="img.wang" border="0"/>
		<h1><?php echo CHV\getSetting('homepage_title_html') ?: _s('Upload and share your images.'); ?></h1>
		<div class="yy">
		<h1 class="yy">高速稳定的图片储存和外链服务</h1>
		</div>
		<div class="home-buttons">
			<?php
                $homepage_cta = [
                    '<a',
                    CHV\getSetting('homepage_cta_fn') == 'cta-upload' ? (CHV\getSetting('upload_gui') == 'js' ? 'data-trigger="anywhere-upload-input"' : 'href="' . G\get_base_url('upload') . '"') : 'href="' . CHV\getSetting('homepage_cta_fn_extra') . '"',
                    (CHV\getSetting('homepage_cta_fn') == 'cta-upload' and !CHV\getSetting('guest_uploads')) ? 'data-login-needed="true"' : null,
                    'class="btn btn-big ' . CHV\getSetting('homepage_cta_color') . (CHV\getSetting('homepage_cta_outline') ? ' outline' : null) . '">' . (CHV\getSetting('homepage_cta_html') ?: _s('Start uploading')) . '</a>'
                ];
    echo join(' ', $homepage_cta)
            ?>
		</div>
		<div class="overflow-auto text-align-center margin-top-20">
            <?php $stats = CHV\Stat::getTotals(); ?>
                <span class="focker yy">
                	<span><a class="yy" href="https://img.wang/page/tos">严禁上传违规内容</a></span>
                </span>
		</div>
		<?php CHV\Render\show_banner('home_after_cta', (function_exists('get_list') ? get_list()->sfw : true)); ?>
	</div>
</div>
<?php
} ?>

<?php CHV\Render\show_banner('home_after_cover', (function_exists('get_list') ? get_list()->sfw : true)); ?>

<?php if (CHV\Settings::get('homepage_style') == 'split') {
                ?>
<div class="content-width">

	<div class="header header-tabs margin-bottom-10 follow-scroll">
		<h1><strong><?php echo $home_user ? _s("%s's Images", $home_user['name_short']) : ('<span class="margin-right-5 icon ' . get_listing()['icon'] . '"></span>' . get_listing()['label']); ?></strong></h1>
        <?php G\Render\include_theme_file("snippets/tabs"); ?>
		<?php
            if (is_content_manager()) {
                G\Render\include_theme_file("snippets/user_items_editor"); ?>
        <div class="header-content-right phone-float-none">
			<?php G\Render\include_theme_file("snippets/listing_tools_editor"); ?>
        </div>
		<?php
            } ?>
    </div>

	<div class="<?php echo count($list->output) == 0 ? 'empty' : 'filled'; ?>">
		<div id="content-listing-tabs" class="tabbed-listing">
			<div id="tabbed-content-group">
				<?php
                    G\Render\include_theme_file("snippets/listing"); ?>
			</div>
		</div>
	</div>

	<?php CHV\Render\show_banner('home_after_listing', get_list()->sfw); ?>

	<?php
        if (!get_logged_user() and CHV\getSetting('enable_signups')) {
            ?>
	<div id="home-join" class="c20 fluid-column center-box text-align-center">
		<h1><?php _se('Sign up to unlock all the features'); ?></h1>
		<p><?php _se('Manage your content, create private albums, customize your profile and more.'); ?></p>
		<div class="home-buttons"><a href="<?php echo G\get_base_url('signup'); ?>" class="btn btn-big blue"><?php _se('Create account'); ?></a></div>
	</div>
	<?php
        } ?>

</div>

<?php
            } ?>

<?php if (CHV\getSetting('enable_powered_by')) { ?>
<div class="footer">
		<div id="home-cover-footer">
		<div class="overflow-auto text-align-center margin-top-20">
            <?php $stats = CHV\Stat::getTotals(); ?>
            <p>全球CDN加速，国内线路优化</p>
<span class="focker">
                    <span class="label">本站已稳定运行了
<strong><script language="JavaScript" type="text/javascript">
var urodz= new Date("8/25/2019");
var now = new Date();
var ile = now.getTime() - urodz.getTime();
var dni = Math.floor(ile / (1000 * 60 * 60 * 24));
document.write(+dni)
</script>
</strong>天，共托管
                    <strong class="number"><?php echo $stats['Images'] > 999999 ? $stats['Images'] : number_format($stats['images']); ?></strong> 张图片</span>
                </span></div>
    </div>
</div>
<?php } ?>

<?php G\Render\include_theme_footer(); ?>
