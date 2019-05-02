<div id="pairs_pager">
  <table class="table table-responsive-md table-bordered table-striped" id="pairs">
    <thead>
      <tr><th>A</th><th>B</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($pairs as $pair): /* @var $pair MappingPair */ ?>
        <?php
        $form = new BaseForm();
        $form->getWidgetSchema()->setNameFormat('delete_pair[%s]');
        ?>
        <tr id="pair_<?php echo $pair['id'] ?>">
          <td><?php echo $pair['a'] ?></td>
          <td><?php echo $pair['b'] ?></td>
          <td>
            <form class="ajax_form" method="post" action="<?php echo url_for('mapping_delete_pair', array('id' => $pair['id'])) ?>"><?php echo $form ?>
              <a class="btn btn-primary btn-sm ajax_link" href="<?php echo url_for('mapping_edit_pair', array('id' => $pair['id'])) ?>">edit</a>
              <button class="btn btn-sm btn-danger">delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <a class="btn btn-primary ajax_link" href="<?php echo url_for('mapping_new_pair', array('id' => $mapping->getId())) ?>">Create Mapping</a>
  <?php include_partial('dashboard/pager', array('pager' => $pairs)) ?>
</div>