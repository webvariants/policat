<div class="modal hide hidden_remove" style="width: 780px; margin: -305px 0 0 -385px" id="widget_view">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Widget view</h3>
    <b>HTML-Code:</b> &lt;script type="text/javascript" src="<?php echo url_for('api_js_widget', array('id' => $id), true) ?>"&gt;&lt;/script&gt;
  </div>
    <div class="modal-body" style="max-height: 490px">
    <script type="text/javascript">var policat_target_id_<?php echo $id ?> = 'widget_target';</script>
    <script type="text/javascript" src="<?php echo url_for('api_js_widget', array('id' => $id)) ?>"></script>
    <div id="widget_target"></div>
  </div>
  <div class="modal-footer">
    <a class="btn" data-dismiss="modal">Close</a>
  </div>
</div>