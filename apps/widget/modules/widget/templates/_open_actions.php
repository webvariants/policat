<?php if (array_key_exists(UtilOpenActions::LARGEST, $data['open'])): ?>
  <div class="row">
      <?php
      foreach ($data['open'][UtilOpenActions::LARGEST]['excerpts'] as $excerpt) {
        include_partial('excerpt', array('excerpt' => $excerpt));
      }
      ?></div><?php
      ?>

  <script type="text/javascript">/*<!--*/
  <?php
  echo UtilWidget::getInitJS();
  foreach ($data['styles'] as $widget_id => $stylings) {
    echo UtilWidget::getAddStyleJS($widget_id, $stylings);
  }
  ?>

  if (policat_validate_action_id) {
    document.getElementById('widget-action-id-' + policat_validate_action_id).style.display = 'none';
  }

  (function() {
  if (document.querySelectorAll) {
    var max = 4;
    var list = document.querySelectorAll('.excerpt');
    
    for (var i = 0; i < list.length; i++) {
      var element = list.item(i);
      if (element.style.display === 'none') {
        continue;
      }

      if (max <= 0) {
        element.style.display = 'none';
        
      }

      if (max % 2 === 0) {
        element.style.clear = 'left';
      }

      max--;
    }
  }
  })();

  //-->
  </script>
  <?php

 endif;