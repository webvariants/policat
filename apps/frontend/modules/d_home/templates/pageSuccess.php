<div class="page-header">
  <h1><?php echo $page_title ?></h1>
</div>
<?php echo UtilMarkdown::transform($sf_data->getRaw('page_content'), false) ?>
