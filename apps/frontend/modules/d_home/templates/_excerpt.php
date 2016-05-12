<?php
use_helper('Text');

$title = Util::enc($widget['title'] ? $widget['title'] : $text['title']);
if (in_array($petition['kind'], Petition::$EMAIL_KINDS, false)) {
  $body = Util::enc($widget['email_subject'] ? $widget['email_subject'] : $text['email_subject']) . ', ';
  $body .= Util::enc($widget['email_body'] ? $widget['email_body'] : $text['email_body']);
}
else
  $body =
    UtilMarkdown::transform(($widget['intro'] ? $widget['intro'] . " \n\n" : '') . $text['body']);

$shorten = truncate_text(preg_replace('/#[A-Z-]+#/', '', strip_tags($body)), 200);
?>
<div class="row" onclick="<?php echo UtilWidget::getWidgetHereJs($widget['id'], true) ?>" style="cursor: pointer">
  <div class="span1"><?php if ($petition['key_visual']): ?><img class="home_teaser" src="<?php echo image_path('keyvisual/' . $petition['key_visual']) ?>" alt="" /><?php else: ?><div>&nbsp;</div><?php endif ?></div>
  <div class="span3">
    <p><b><?php echo $title ?></b> <?php echo $shorten ?></p>
  </div>
  <div class="span1 span1_10px">
    <dl class="well well-small petition_stats">
      <dt>Total</dt>
      <dd title="<?php echo number_format($petition['signings'], 0, '.', ',') ?>"><?php echo Util::readable_number($petition['signings']) ?></dd>
      <dt>Today</dt>
      <dd title="<?php echo number_format($petition['signings24'], 0, '.', ',') ?>"><?php echo Util::readable_number($petition['signings24']) ?></dd>
    </dl>
    <a class="btn btn-mini show">sign!</a>
  </div>
  <div class="span5 bottom_line"></div>
</div>