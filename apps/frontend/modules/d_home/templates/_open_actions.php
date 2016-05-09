<ul class="nav nav-tabs">
  <?php $first = true;
  foreach ($open as $key => $value):
    ?>
    <li class="pointer <?php if ($first) echo 'active' ?>"><a data-target="#<?php echo $key ?>" data-toggle="tab"><?php echo $value['title'] ?></a></li>
  <?php $first = false;
endforeach ?>
  <li class="pull-right-force"><a class="rss" href="<?php echo url_for('feed') ?>"><img class="up5" alt="RSS feed" src="<?php echo public_path('images/icon_rss.png') ?>"/></a></li>
</ul>
<div class="tab-content">
    <?php $first = true;
    foreach ($open as $key => $value):
      ?>
    <div class="tab-pane <?php if ($first) echo 'active' ?>" id="<?php echo $key ?>">
      <?php foreach ($value['data'] as $petition): $text = $petition['PetitionText'][0];
        $widget = $text['DefaultWidget']
        ?>
        <?php include_partial('excerpt', array('petition' => $petition, 'text' => $text, 'widget' => $widget)) ?>
  <?php endforeach ?>
    </div>
  <?php $first = false;
endforeach
?>
</div>
<script type="text/javascript">/*<!--*/
  <?php echo UtilWidget::getInitJS();
  foreach ($widget_styles->getRawValue() as $widget_id => $stylings)
    echo UtilWidget::getAddStyleJS($widget_id, $stylings);
  ?>
  policat['tags']=<?php echo json_encode($tags->getRawValue()) ?>;
//-->
</script>