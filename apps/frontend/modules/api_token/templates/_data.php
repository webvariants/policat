<tr id="token_data_<?php echo $token->getId() ?>">
    <td colspan="4">
        <table class="table table-responsive-md table-bordered table-striped table-condensed">
            <?php foreach ($offsets as $offset): /* @var $offset ApiTokenOffset */ ?>
              <tr>
                  <td><?php echo $offset->getCountry() ?></td>
                  <td><?php echo number_format($offset->getAddnum(), 0, '.', ',') ?></td>
              </tr>
            <?php endforeach ?>
        </table>
    </td>
    <td><a class="btn btn-secondary" href="javascript:(function(){$('#token_data_<?php echo $token->getId() ?>').remove();})();">close</a></td>
</tr>
