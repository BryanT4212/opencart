<div id="banner<?php echo $module; ?>" class="owl-carousel">
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <?php } ?>
  </div>
  <?php } ?>
</div>
<script type="text/javascript"><!--
$('#banner<?php echo $module; ?>').owlCarousel({
	singleItem: <?php echo $singleItem; ?>,
	autoPlay: <?php echo $autoPlay; ?>,
	navigation: <?php echo $navigation; ?>,
	pagination: <?php echo $pagination; ?>,
	transitionStyle: '<?php echo $transitionStyle; ?>'
});
--></script>
