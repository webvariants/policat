<html>
    <head>
        <title>Invoice #<?php echo $bill->getId() ?></title>
        <style type="text/css">
          td, th {padding: 5px; border: 0.3pt solid black; }
          table { border-collapse: collapse; }
        </style>
    </head>
    <body style="font-family: serif">
      <?php echo $bill->getRaw('markup') ?>
    </body>
</html>