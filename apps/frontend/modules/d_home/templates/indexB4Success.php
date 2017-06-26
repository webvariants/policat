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
  <?php echo UtilWidget::getInitJS();
  foreach ($styles->getRawValue() as $widget_id => $stylings) {
    echo UtilWidget::getAddStyleJS($widget_id, $stylings);
  }
  ?>
//-->
</script>

<div class="container">
    <div class="card-columns">
        <?php foreach ($actionList as $action): ?>

          <div class="card" onclick="<?php echo UtilWidget::getWidgetHereJs($action['widget_id'], true) ?>" style="cursor: pointer">
              <?php if ($action['key_visual']): ?><img style="width: 100%" class="card-img-top img-fluid" src="<?php echo image_path('keyvisual/' . $action['key_visual']) ?>" alt="" /><?php endif ?>
              <div class="card-block">
                  <p><?php echo $action['title'] ?></b> <?php echo $action['text'] ?></p>
                  <p>
                      <span title="<?php echo number_format($action['signings'], 0, '.', ',') ?>"><?php echo Util::readable_number($action['signings']) ?></span>
                  </p>
              </div>
              <div class="card-footer text-center">
                  <a class="btn btn-secondary">sign!</a>
              </div>
          </div>
        <?php endforeach ?>
    </div>
</div>
