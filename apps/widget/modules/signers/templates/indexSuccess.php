<?php use_helper('I18N');?>
<?php if (!$disabled): ?>
<h1><?php echo $title ?></h1>
<h2><?php echo __('All signers') ?></h2>
<div id="signers" class="signers-list" data-signers="<?php echo Util::enc(json_encode($data)) ?>"></div>
<div class="pagination"><ul id="pager"></ul></div>
<?php else: ?>
<h2><?php echo __('All signers') ?></h2>
<p>Disabled for this action.</p>
<?php endif ?>
