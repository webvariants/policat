<div class="container">
  <h1><?php echo $page_title ?></h1>
  <?php echo UtilMarkdown::transform($sf_data->getRaw('page_content'), false) ?>
</div>
