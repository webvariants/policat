<?php use_helper('Number') ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('campaign_edit_', array('id' => $petition->getCampaignId())) ?>"><?php echo $petition->getCampaign()->getName() ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo url_for('petition_overview', array('id' => $petition->getId())) ?>"><?php echo $petition->getName() ?></a></li>
    <li class="breadcrumb-item active">Overview</li>
  </ol>
</nav>
<?php include_component('d_action', 'notice', array('petition' => $petition)) ?>
<?php include_partial('d_action/tabs', array('petition' => $petition, 'active' => 'tokens')) ?>

<h2>Counter</h2>
<?php include_partial('form', array('form' => $form)) ?>

<h3>Counter API</h3>
<p>
  You can update your counter with signings collected elsewhere, whether in other e-petition systems, or at offline
  events or canvassing. A simple method is to tweak the overall counter manually by adding a number into the
  "Sign-on counter start" field. Another option is to let your partners update your counter with their data
  automatically, via our <a href="<?php echo url_for('api_v2_doc') ?>">API</a>. Create a token for each organisation
  you want to allow to add their counts to your counter.
</p>
<p>Action ID: <?php echo $petition->getId() ?></p>
<?php if ($tokens->count()): ?>
  <table class="table table-responsive-md table-bordered">
      <thead>
          <tr>
              <th>Name</th>
              <th>Token</th>
              <th>Total count</th>
              <th>Status</th>
              <th></th>
          </tr>
      </thead>
      <tbody>
          <?php foreach ($tokens as $token): /* @var $token PetitionApiToken */ ?>
            <tr id="token_<?php echo $token->getId() ?>">
                <td><?php echo $token->getName() ?></td>
                <td><?php echo $token->getToken() ?></td>
                <td><?php echo number_format($token->getOffsetSum(60), 0, '.', ',') ?></td>
                <td><?php echo $token->getStatusName() ?></td>
                <td>
                    <a class="btn btn-secondary btn-sm" href="<?php echo url_for('petition_token_edit', array('id' => $token->getId())) ?>">edit</a>
                    <a class="btn btn-secondary btn-sm ajax_link" href="<?php echo url_for('petition_token_data', array('id' => $token->getId())) ?>">data</a>
                </td>
            </tr>
          <?php endforeach ?>
      </tbody>
  </table>
<?php else: ?>
  <p>No Api tokens yet</p>
<?php endif ?>
<a class="btn btn-secondary" href="<?php echo url_for('petition_token_new', array('id' => $petition->getId())) ?>">Create API token</a>

<?php include_component('mailexport', 'setting', array('petition' => $petition)) ?>