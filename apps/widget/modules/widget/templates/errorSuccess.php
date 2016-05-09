<?php if ($json):
  echo json_encode(array('error' => $message));
  else: ?>
<h1>Error</h1>
  <?php if (isset($message)) echo $message; else echo 'sorry'; ?>
<?php endif ?>
