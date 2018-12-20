<div class="container">
  <h1><?php echo $terms_title ?></h1>
  <?php echo UtilMarkdown::transform($sf_data->getRaw('terms_content'), false) ?>
</div>
