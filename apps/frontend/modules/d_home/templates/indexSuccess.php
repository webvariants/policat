<script type="text/javascript" src="/js/dist/policat_widget_outer.js"></script>
<div>
    <?php
    if (isset($markup)):
      $markup = $sf_data->getRaw('markup');

      echo $markup;
    endif;
    ?>
</div>

<?php UtilOpenActions::render() ?>