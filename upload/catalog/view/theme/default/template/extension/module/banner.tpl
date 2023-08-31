<div id="banner<?php echo $module; ?>" class="owl-carousel">
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" style="float: left; margin-right: 3em;" />
		<h1>Celebrating our 30th year!</h1>
		<h2>Full service lettering, embroidery, chenille, and community involvement</h2>
		<h3>Ask us about fundraising opportunities for your school, team, organization, cause, or event!</h3>
    <?php } ?>
  </div>
  <?php } ?>
</div>
<script type="text/javascript"><!--
$('#banner<?php echo $module; ?>').owlCarousel({
	singleItem: <?php echo $singleItem; ?>,
	autoPlay: <?php echo $autoPlay; ?>,
	navigation: <?php echo $navigation; ?>,
	navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
	pagination: <?php echo $pagination; ?>,
	transitionStyle: '<?php echo $transitionStyle; ?>'
});
--></script>
