<script type="text/javascript">
  var policat_verified = <?php echo $petition_id ?>;
  var policat_ref = <?php echo json_encode($ref) ?>;
  var policat_width = 'auto';
</script>
<p class="iframe-center">
  <br />
  <script type="text/javascript" src="<?php echo url_for('api_js_widget_no_redirect', array('id' => $wid)) ?>"></script>
</p>
<br /><br />
<?php include_component('widget', 'open_actions'); ?>