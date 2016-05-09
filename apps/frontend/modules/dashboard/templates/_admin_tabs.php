<?php
$list = array(
    'store' => array('title' => 'Global Settings', 'route' => 'store'),
    'product' => array('title' => 'Products', 'route' => 'product_index'),
    'order' => array('title' => 'Orders', 'route' => 'order_list'),
    'tax' => array('title' => 'Tax', 'route' => 'tax_list'),
    'users' => array('title' => 'User Management', 'route' => 'user_idx'),
    'languages' => array('title' => 'Languages', 'route' => 'language_index'),
    'country' => array('title' => 'Countries', 'route' => 'country_index'),
    'mappings' => array('title' => 'Mappings', 'route' => 'mapping_index'),
    'target' => array('title' => 'Global target-lists', 'route' => 'target_index_global'),
    'campaigns' => array('title' => 'Campaigns', 'route' => 'campaign_list'),
    'testmail' => array('title' => 'Testmail', 'route' => 'admin_testmail'),
    'stats' => array('title' => 'Stats', 'route' => 'admin_stats'),
);
if (!isset($active))
  $active = '';
?>
<div class="page-header">
  <h1>Admin<?php if (array_key_exists($active, $list)): ?>: <?php echo $list[$active]['title'] ?><?php endif ?><?php if (isset($extra_title)): ?>: <?php echo $extra_title ?><?php endif ?></h1>
</div>
<?php if ($list): ?>
<ul class="nav nav-tabs">
  <?php foreach ($list as $key => $entry): ?>
    <li class="<?php if ($active == $key) echo 'active' ?>">
      <a href="<?php echo url_for($entry['route']) ?>"><?php echo $entry['title'] ?></a>
    </li>
  <?php endforeach ?>
</ul>
<?php endif ?>