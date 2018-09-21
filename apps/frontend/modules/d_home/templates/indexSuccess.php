<script type="text/javascript" src="/js/dist/policat_widget_outer.js"></script>
<div class="container">
    <?php
    if (isset($markup)):
      $markup = $sf_data->getRaw('markup');

      echo $markup;
    endif;
    ?>
</div>

<script type="text/javascript">/*<!--*/
<?php
echo UtilWidget::getInitJS();
foreach ($styles->getRawValue() as $widget_id => $stylings) {
  echo UtilWidget::getAddStyleJS($widget_id, $stylings);
}
?>
//-->
</script>

<div class="container">

    <?php foreach ($actionListChunk as $chunk): ?>
      <div class="card-deck">
          <?php foreach ($chunk as $action): ?>
            <div class="card mb-4" onclick="<?php echo UtilWidget::getWidgetHereJs($action['widget_id'], true) ?>" style="cursor: pointer">
                <?php if ($action['key_visual']): ?><img style="width: 100%" class="card-img-top img-fluid" src="<?php echo image_path('keyvisual/' . $action['key_visual']) ?>" alt="" /><?php endif ?>
                <div class="card-block">
                    <p class="mb-1 p-color-less-important"><?php echo Petition::$KIND_SHOW_FE[$action['kind']] ?></p>
                    <div class="progress mb-1">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $action['counter_percent'] ?>%;" aria-valuenow="<?php echo $action['counter_percent'] ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($action['counter_value'], 0, '.', ',') ?></div>
                    </div>
                    <dl class="p-participants text-center mb-0">
                        <dd><?php echo number_format($action['counter_value'], 0, '.', ',') ?></dd>
                        <dt><?php echo $action['counter_type'] ?></dt>
                    </dl>
                    <?php if ($action['title']): ?>
                      <h4 class="mt-1"><?php echo $action['title'] ?></h4>
                    <?php endif ?>
                    <p class="mb-0"><?php echo $action['text'] ?></p>
                </div>
                <div class="card-footer text-center">
                    <a class="btn btn-secondary d-block">Take action</a>
                </div>
            </div>
          <?php endforeach ?>
      </div>
    <?php endforeach ?>
</div>
