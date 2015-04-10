<div class="page-header"><h1><?php echo $title ?></h1></div>
<?php
if (isset($markup)):
  $markup = $sf_data->getRaw('markup');

  echo $markup;
endif;

if (isset($widget_id)):
  use_helper('I18N');

  $stylings = $stylings->getRawValue();
  $stylings['count'] = number_format($count, 0, '.', ',') . ' ' . __('people so far');
  $stylings['target'] = $target;
  ?>
  <script type="text/javascript">
    <?php echo UtilWidget::getInitJS() ?>
    <?php echo UtilWidget::getAddStyleJS($widget_id, $stylings) ?>
    <?php echo UtilWidget::getWidgetHereJs($widget_id, false) ?>
  </script>
<?php endif ?>