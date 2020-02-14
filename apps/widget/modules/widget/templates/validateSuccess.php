<style>
body {
  background-color: <?php echo $backgroundColor ?>;
}
</style>
<script type="text/javascript">
  var policat_verified = <?php echo $petition_id ?>;
  var policat_ref = <?php echo json_encode($ref) ?>;
  var policat_width = 'auto';
  var policat_name = <?php echo json_encode($name) ?>;
  var policat_sign_id = <?php echo json_encode($id) ?>;
  var policat_ref_code = <?php echo json_encode($ref_code) ?>;
</script>
<p class="iframe-center">
  <br />
  <script type="text/javascript" src="<?php echo url_for('api_js_widget_no_redirect', array('id' => $wid)) ?>"></script>
</p>
<br /><br />
<?php UtilOpenActions::render([UtilOpenActions::HOTTEST], [(int) $petition_id]) ?>
