<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-banner"><?php echo $entry_banner; ?></label>
            <div class="col-sm-10">
              <select name="banner_id" id="input-banner" class="form-control">
                <?php foreach ($banners as $banner) { ?>
                <?php if ($banner['banner_id'] == $banner_id) { ?>
                <option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_width; ?></label>
            <div class="col-sm-10">
              <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
              <?php if ($error_width) { ?>
              <div class="text-danger"><?php echo $error_width; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-height"><?php echo $entry_height; ?></label>
            <div class="col-sm-10">
              <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
              <?php if ($error_height) { ?>
              <div class="text-danger"><?php echo $error_height; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-displayTime"><span data-toggle="tooltip" title="<?php echo $help_displayTime; ?>"><?php echo $entry_displayTime; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="displayTime" value="<?php echo $displayTime; ?>" placeholder="<?php echo $text_seconds; ?>" id="input-displayTime" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-transition"><span data-toggle="tooltip" title="<?php echo $help_transition; ?>"><?php echo $entry_transition; ?></span></label>
            <div class="col-sm-10">
              <select name="transition" id="input-transition" class="form-control">
							<?php
							  $transition_options = array_fill_keys(
																		array( 'fade', 'backSlide', 'goDown', 'fadeUp' ),
																		'');
								if (!$transition) $transition = 'fade';
								$transition_options[$transition] = 'selected="selected"';
								foreach ($transition_options as $option => $selected)
								{
									$prompt = 'text_'.$option; ?>
                <option value="<?php echo $option; ?>" <?php echo $selected ?>><?php echo ${$prompt}; ?></option>
								<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_pagination; ?>"><?php echo $entry_pagination; ?></span></label>
            <div class="col-sm-10">
						  <?php
							  $pagination_options = array( '', '' );
								$pagination_options[intval( $pagination )] = 'checked="checked"';
						  ?>
              <label class="radio-inline">
                <input type="radio" name="pagination" value="1" <?php echo $pagination_options[1]; ?> />
                <?php echo $text_yes; ?>
              </label>
              <label class="radio-inline">
                <input type="radio" name="pagination" value="0" <?php echo $pagination_options[0]; ?> />
                <?php echo $text_no; ?>
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_navigation; ?>"><?php echo $entry_navigation; ?></span></label>
            <div class="col-sm-10">
						  <?php
							  $navigation_options = array( '', '' );
								$navigation_options[intval( $navigation )] = 'checked="checked"';
						  ?>
              <label class="radio-inline">
                <input type="radio" name="navigation" value="1" <?php echo $navigation_options[1]; ?> />
                <?php echo $text_yes; ?>
              </label>
              <label class="radio-inline">
                <input type="radio" name="navigation" value="0" <?php echo $navigation_options[0]; ?> />
                <?php echo $text_no; ?>
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>