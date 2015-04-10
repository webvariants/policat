<div class="page-header">
  <h1><?php echo $contact_title ?></h1>
</div>
<?php echo UtilMarkdown::transform($sf_data->getRaw('contact_content'), true, true) ?>