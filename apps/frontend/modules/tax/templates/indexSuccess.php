<?php use_helper('Number', 'I18N') ?>
<?php include_partial('dashboard/admin_tabs', array('active' => 'tax')) ?>
<h2>Tax countries</h2>
<table class="table table-responsive-md table-bordered table-striped">
    <thead>
        <tr><th class="span3">Country</th><th class="span3">without VAT-ID (%)</th><th class="span3">with VAT-ID (%)</th><th class="span2"></th></tr>
    </thead>
    <tbody>
        <?php foreach ($taxes as $tax): /* @var $tax CountryTax */ ?>
          <tr>
              <td><?php echo format_country($tax->getCountry()) ?> (<?php echo $tax->getCountry() ?>)</td>
              <td>
                  <?php echo format_number($tax->getTaxNoVat()) ?> <?php echo $tax->getNoVatNoteName() ?>
              </td>
              <td>
                  <?php echo format_number($tax->getTaxVat()) ?> <?php echo $tax->getVatNoteName() ?>
              </td>
              <td>
                  <a class="btn btn-primary btn-sm" href="<?php echo url_for('tax_edit', array('id' => $tax->getId())) ?>">edit</a>
                  <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('tax_delete', array('id' => $tax->getId())) ?>">delete</a>
              </td>
          </tr>
        <?php endforeach ?>
    </tbody>
</table>
<a class="btn btn-primary" href="<?php echo url_for('tax_new') ?>">Create tax country</a>
<hr />
<h2>Tax notes</h2>
<table class="table table-responsive-md table-bordered table-striped">
    <thead>
        <tr><th class="span3">Name</th><th>Note</th><th class="span2"></th></tr>
    </thead>
    <tbody>
        <?php foreach ($notes as $note): /* @var $note TaxNote */ ?>
          <tr>
              <td><?php echo $note->getName() ?></td>
              <td><?php echo $note->getNote() ?></td>
              <td>
                  <a class="btn btn-primary btn-sm" href="<?php echo url_for('tax_note_edit', array('id' => $note->getId())) ?>">edit</a>
                  <a class="btn btn-danger btn-sm ajax_link" href="<?php echo url_for('tax_note_delete', array('id' => $note->getId())) ?>">delete</a>
              </td>
          </tr>
        <?php endforeach ?>
    </tbody>
</table>
<a class="btn btn-primary" href="<?php echo url_for('tax_note_new') ?>">Create tax note</a>
