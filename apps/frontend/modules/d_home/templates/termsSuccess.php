<div class="page-header">
  <h1><?php echo $terms_title ?></h1>
</div>
<?php echo UtilMarkdown::transform($sf_data->getRaw('terms_content'), false) ?>
