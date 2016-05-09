<?php if (isset($form)): ?>
  <form id="widget_copy_form" method="post" class="ajax_form form-inline" action="<?php echo url_for('petition_widgets_copy', array('id' => $petition->getId())) ?>">
      <?php echo $form ?>
      <button class="btn btn-small add_popover" type="submit" data-content="This function allows you to quickly import the layout and ownership status of a large number of widgets from a previous action. Owners of copied widgets will also be owners of the new copy. The function creates a copy of each widget from the selected action, as long as you have already created and activated a translation in the respective language of the widget. It doesn't import content (translations), data or the counter reading. You can use this function multiple times (it ignores widgets that you imported already). To forward the widget-URLs of the original widgets to the new copies, go to the original action and activate the forwarding.">Import widgets</button>
  </form>
  <?php if ($followedBy): ?>
    <p>Status: The following actions forward widget URLs (embed-links) to this action:</p>
    <ul>
        <?php foreach ($followedBy as $followPetition): ?>
        <li><a href="<?php echo url_for('petition_edit_', array('id' => $followPetition->getId())) ?>"><?php echo $followPetition->getName() ?></a></li>
        <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>
        No other actions are forwarding to this action (yet). Note: The action(s) from which you copied
        widgets still serve their own widgets, unless you define this action as a "follow-up action" in
        the settings of this old action (tab: edit).
    </p>
  <?php endif ?>
<?php endif; ?>