<!DOCTYPE html>
<?php use_helper('I18N', 'Date'); $culture = $sf_user->getCulture(); ?>
<html lang="<?php echo $culture ?>">
  <?php
  /* @var $sf_content string */
  /* @var $sf_user myUser */
  /* @var $petition_contact PetitionContact */
  /* @var $petition_text PetitionText */
  /* @var $petition Petition */
  /* @var $pledges array */

  ?>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $portal_name = StoreTable::value(StoreTable::PORTAL_NAME);
    $title = $sf_response->getTitle();
    $sf_response->setTitle(($title ? $title . ' - ' : '') . $portal_name);
    $sf_response->addMeta('description', StoreTable::value(StoreTable::PORTAL_META_DESCRIPTION));
    $sf_response->addMeta('keywords', StoreTable::value(StoreTable::PORTAL_META_KEYWORDS));
    include_http_metas();
    include_metas();
    include_title()
    ?>
    <link rel="shortcut icon" href="<?php echo public_path('favicon.ico') ?>" />
    <?php
    include_stylesheets();
    include_javascripts();
    ?>
    <style type="text/css">
      body {
        background-color: <?php echo '#' . $petition->getPledgeBackgroundColor() ?>;
      }
      .page-header h1 {
        color: <?php echo '#' . $petition->getPledgeHeadColor() ?>;
      }
      body, legend, label {
        color: <?php echo '#' . $petition->getPledgeColor() ?>;
      }
      body, textarea, button, input, p, h1, h2, h3, h4, h5, h6 {
        font-family: <?php echo $petition->getPledgeFont() ?>;
      }
    </style>
  </head>
  <body class="container">
    <header class="row">
      <div id="pledge_header" class="span12">
        <?php if ($petition->getPledgeHeaderVisual()): ?>
          <img class="pledge_header_visual" src="<?php echo image_path('pledge_header_visual/' . $petition->getPledgeHeaderVisual()) ?>" alt="" />
        <?php endif ?>
        <?php if ($petition->getPledgeKeyVisual()): ?>
          <img class="img-polaroid pledge_key_visual" src="<?php echo image_path('pledge_key_visual/' . $petition->getPledgeKeyVisual()) ?>" alt="" />
        <?php endif ?>
      </div>
      <?php if ($languages->count() > 1): ?>
        <div class="span12">
          <form method="get">
            <button class="btn pull-right" type="submit">&raquo;</button>
            <select name="lang" class="pull-right" style="width: auto">
              <?php foreach ($languages as $language): /* @var $language Language */ ?>
                <option <?php if ($culture == $language->getId()): ?>selected="selected"<?php endif ?> value="<?php echo $language->getId() ?>"><?php echo Util::enc($language->getName()) ?></option>
              <?php endforeach ?>
            </select>
          </form>
        </div>
      <?php endif ?>

    </header>
    <div class="page-header border0">
      <h1><?php echo Util::enc(trim($petition_text->getPledgeTitle()) ? : $petition_text->getTitle()) ?></h1>
    </div>
    <p>
      <?php echo Util::enc($salutation) ?>
    </p>
    <?php echo UtilMarkdown::transform($petition_text->getIntro()) ?>
    <form method="post" action="<?php echo url_for('pledge_contact', array('petition_id' => $petition_contact->getPetitionId(), 'contact_id' => $petition_contact->getContactId(), 'secret' => $petition_contact->getSecret())) ?>?lang=<?php echo $culture ?>">
      <?php if ($session): ?><input type="hidden" name="session" value="<?php echo Util::enc($session) ?>" /><?php endif ?>
      <?php if ($ask_password): ?>
        <fieldset>
          <div class="control-group <?php if ($wrong_password): ?>error<?php endif ?>">
            <label class="control-label"><?php echo __('Please enter your password.') ?></label>
            <div class="controls">
              <input type="password" placeholder="<?php echo __('Password') ?>" name="password" />
            </div>
          </div>
        </fieldset>
        <button type="submit" class="btn bottom20" <?php if ($petition_contact->isNew()): ?>disabled="disabled"<?php endif ?>><?php echo __('Enter') ?></button>
      <?php else: ?>
        <?php foreach ($pledges as $pledge): /* @var $pledge Pledge */ ?>
          <div class="pledge_row">
            <fieldset class="row">
              <div class="col-md-8">
                <?php echo UtilMarkdown::transform($petition_text->getPledgeTextByPledgeItem($pledge->getPledgeItem())); ?>
              </div>
              <div class="control-group col-md-4 pledge_select">
                <?php if ($pledge->getStatus() == PledgeTable::STATUS_YES): ?>
                  <label><span class="pledge_done_space pledge_color pledge_yes pledge_color_<?php echo $pledge->getPledgeItem()->getColor() ?>"></span><span><?php echo __('Yes') ?> (<?php echo __('I pledged on #DATE#', array('#DATE#' => format_date($pledge->getStatusAt(), 'D'))) ?>)</span></label>
                <?php else: ?>
                  <label class="radio">
                    <input type="radio" name="status_<?php echo $pledge->getPledgeItemId() ?>" value="<?php echo PledgeTable::STATUS_YES ?>" <?php if ($pledge->getStatus() == PledgeTable::STATUS_YES): ?>checked="checked"<?php endif ?> />
                    <span class="pledge_color pledge_yes pledge_color_<?php echo $pledge->getPledgeItem()->getColor() ?>"></span><span><?php echo __('Yes') ?></span>
                  </label>
                  <label class="radio">
                    <input type="radio" name="status_<?php echo $pledge->getPledgeItemId() ?>" value="<?php echo PledgeTable::STATUS_NO ?>" <?php if ($pledge->getStatus() == PledgeTable::STATUS_NO): ?>checked="checked"<?php endif ?> />
                    <span class="pledge_color pledge_no pledge_color_<?php echo $pledge->getPledgeItem()->getColor() ?>"></span><span><?php echo __('No') ?></span>
                  </label>
                  <label class="radio">
                    <input type="radio" name="status_<?php echo $pledge->getPledgeItemId() ?>" value="<?php echo PledgeTable::STATUS_NO_COMMENT ?>" <?php if ($pledge->getStatus() == PledgeTable::STATUS_NO_COMMENT): ?>checked="checked"<?php endif ?> />
                    <span class="pledge_color pledge_no_comment pledge_color_<?php echo $pledge->getPledgeItem()->getColor() ?>"></span><span><?php echo __('No comment') ?></span>
                  </label>
                <?php endif ?>
              </div>
            </fieldset>
          </div>
        <?php endforeach ?>
        <?php if ($petition->getPledgeWithComments()): ?>
          <fieldset>
            <div class="control-group">
              <label><?php echo __('Comment') ?></label>
              <textarea class="span12" name="comment" rows="4"><?php echo Util::enc($petition_contact->getComment()) ?></textarea>
            </div>
          </fieldset>
        <?php endif ?>
        <fieldset>
          <?php if ($session): ?>
            <label><?php echo __('Change your password (optional).') ?></label>
          <?php else: ?>
            <label><?php echo __('Secure your pledge page with a password (optional).') ?></label>
          <?php endif ?>
          <input type="password" placeholder="<?php echo __('Password') ?>" name="new_password1" />
          <input type="password" placeholder="<?php echo __('Password again') ?>" name="new_password2" />
          <?php if ($password_no_match): ?><span class="help-inline"><?php echo __('Passwords do not match.') ?></span><?php endif ?>
          <?php if ($password_too_short): ?><span class="help-inline"><?php echo __('Password is too short. Use at least 8 characters.') ?></span><?php endif ?>
        </fieldset>
        <button type="submit" class="btn bottom20" <?php if ($petition_contact->isNew()): ?>disabled="disabled"<?php endif ?>><?php echo __('Save & submit my pledges') ?></button>
      <?php endif ?>
      <?php echo UtilMarkdown::transform($petition_text->getPledgeExplantoryAnnotation()) ?>
    </form>
    <?php if ($show_thankyou): ?>
      <div class="modal hide modal_show" id="pledge_done_modal">
        <div class="modal-header">
          <a class="close" data-dismiss="modal">&times;</a>
          <h3><?php echo __('Thank you') ?></h3>
        </div>
        <div class="modal-body">
          <?php echo UtilMarkdown::transform($petition_text->getPledgeThankYou()) ?>
        </div>
        <div class="modal-footer">
          <a class="btn btn-secondary" data-dismiss="modal">OK</a>
        </div>
      </div>
    <?php endif ?>
    <div id="waiting"><b></b><i></i><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>
  </body>
</html>