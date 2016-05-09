<?php if ($admin): ?>
  <h2>policat.org admin settings:</h2>
  <?php if ($widgets): ?>
  <h3>Widgets shown on this page:</h3>
  <table>
    <tr><th>Language</th><th>Widget ID</th><th></th></tr>
    <?php foreach ($widgets as $lang => $id): ?>
    <tr>
      <td><?php echo $lang ?></td>
      <td><?php echo $id ?></td>
      <td>
        <form action="" method="POST">
          <input type="hidden" name="remove" value="1"/>
          <input type="hidden" name="secret_hash" value="<?php echo $secret_hash ?>"/>
          <input type="hidden" name="page_id" value="<?php echo $page_id ?>"/>
          <input type="hidden" name="widget_id" value="<?php echo $id ?>"/>
          <input type="submit" value="remove" />
      </form>
      </td>
    </tr>
    <?php endforeach ?>
  </table>
  <?php endif ?>
  <h3>Add or change widget for a language:</h3>
  <form action="" method="POST">
    <input type="hidden" name="secret_hash" value="<?php echo $secret_hash ?>"/>
    <input type="hidden" name="page_id" value="<?php echo $page_id ?>"/>
    <label for="widget_id">Widget ID</label>
    <input id="widget_id" type="text" name="widget_id" />
    <input type="submit" value="submit" />
  </form>
  <br />
<?php endif ?>

<?php if (isset($widget_id)): ?>
  <script type="text/javascript">var policat_ref = <?php echo json_encode('facebook_page_id_' . $page_id) ?>;</script>
  <script type="text/javascript" src="<?php echo str_replace('http://', 'https://', url_for('api_js_widget', array('id' => $widget_id), true)) ?>"></script>
  <?php

 endif ?>
