<ul>
<?php foreach ($campaigns as $campaign): /* @var $campaign Campaign */ ?>
  <li><?php echo $campaign->getName() ?> <a class="ajax_link post" href="<?php echo url_for('campaign_join', array('id' => $campaign->getId())) ?>">join</a></li>
<?php endforeach; ?>
</ul>