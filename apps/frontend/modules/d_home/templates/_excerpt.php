<div class="row" onclick="<?php echo UtilWidget::getWidgetHereJs($excerpt['widget_id'], true) ?>" style="cursor: pointer">
  <div class="span1"><?php if ($excerpt['key_visual']): ?><img class="home_teaser" src="<?php echo image_path('keyvisual/' . $excerpt['key_visual']) ?>" alt="" /><?php else: ?><div>&nbsp;</div><?php endif ?></div>
  <div class="span3">
    <p><b><?php echo $excerpt['title'] ?></b> <?php echo $excerpt['text'] ?></p>
  </div>
  <div class="span1 span1_10px">
    <dl class="well well-small petition_stats">
      <dt>Total</dt>
      <dd title="<?php echo number_format($excerpt['signings'], 0, '.', ',') ?>"><?php echo Util::readable_number($excerpt['signings']) ?></dd>
      <dt>Today</dt>
      <dd title="<?php echo number_format($excerpt['signings24'], 0, '.', ',') ?>"><?php echo Util::readable_number($excerpt['signings24']) ?></dd>
    </dl>
    <a class="btn btn-mini show">sign!</a>
  </div>
  <div class="span5 bottom_line"></div>
</div>