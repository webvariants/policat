<div class="modal hide hidden_remove" style="width: 780px; margin: -305px 0 0 -385px" id="widget_view">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>Widget view</h3>
        <p><b>HTML-Code:</b> &lt;script type="text/javascript" src="<?php echo url_for('api_js_widget', array('id' => $id), true) ?>"&gt;&lt;/script&gt;</p>
        <?php if ($follows): ?>
          <p><strong style="color:red">Attention: The action is following the action "<?php echo $follows ?>".</strong></p>
        <?php endif ?>
        <?php if ($petition_off): ?>
          <div class="alert">Note: Action status is not 'active'. To preview/activate widget, set action status to 'active' <a class="btn btn-mini" href="<?php echo url_for('petition_edit_', array('id' => $petition_off)) ?>">edit action</a></div>
        <?php endif ?>
        <?php if ($petition_text_off): ?>
          <div class="alert">Note: Translation status for this widget's language is not 'active'. To preview/activate widget, set translation status to 'active' <a class="btn btn-mini" href="<?php echo url_for('translation_edit', array('id' => $petition_text_off)) ?>">edit translation</a></div>
        <?php endif ?>
        <?php if ($widget_off): ?>
          <div class="alert">Note: The widget is not 'active'. To preview/activate widget, set widget status to 'active'</div>
        <?php endif ?>
    </div>
    <div class="modal-body" style="max-height: 490px">
        <?php if ($petition_off || $petition_text_off || $widget_off): ?>
        <?php else: ?>
          <script type="text/javascript">var policat_target_id_<?php echo $id ?> = 'widget_target';</script>
          <script type="text/javascript" src="<?php echo url_for('api_js_widget_no_redirect', array('id' => $id)) ?>"></script>
          <div id="widget_target"></div>
        <?php endif ?>
    </div>
    <div class="modal-footer">
        <a class="btn" href="<?php echo url_for('widget_edit', array('id' => $id)) ?>">Edit</a>
        <a class="btn" data-dismiss="modal">Close</a>
    </div>
</div>