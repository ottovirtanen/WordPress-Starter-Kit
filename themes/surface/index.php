<?php get_header(); ?>

<?php $type = template_get_post_type(); ?>

<div id="content-primary">
	
	<div id="<?php echo $type; ?>" class="hatom">
		<?php rewind_posts(); ?>
		<?php get_template_part('loop', $type); ?>
	<!-- end of div #<?php echo $type; ?> -->
	</div>
	
	<?php get_template_part('paginate'); ?>
	
<!-- end of div id #content-primary -->
</div>

<?php get_sidebar($type); ?>

<?php get_footer(); ?>