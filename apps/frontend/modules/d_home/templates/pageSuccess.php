<div class="container">
  <h1><?php echo $page_title ?></h1>
  <script type="text/javascript" src="/js/dist/policat_widget_outer.js"></script>
  <?php echo UtilMarkdown::transformWithWidgets($sf_data->getRaw('page_content'), false) ?>
</div>
