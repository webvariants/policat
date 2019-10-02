<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>" lang="<?php echo $lang ?>">
    <?php
    use_helper('Text', 'I18N', 'Date');
    /* @var $petition Petition */
    /* @var $widget Widget */
    /* @var $form_embed WidgetPublicForm */

    $culture_info = $petition_text->utilCultureInfo();
    ?>
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <?php if (!UtilTheme::removeClassicCss($widget, $petition)): ?><link rel="preload" as="style" href="/css/dist/policat_widget.css?<?php echo filemtime(sfConfig::get('sf_web_dir') . '/css/dist/policat_widget.css') ?>" /><?php endif ?>
        <?php if ($font_css_file): ?><link href="<?php echo $font_css_file ?>" rel="reload" as="style" /><?php endif ?>
        <link rel="preload" href="/js/dist/jquery-1.10.2.min.js" as="script">
        <link rel="preload" href="/js/dist/policat_widget.js?<?php echo filemtime(sfConfig::get('sf_web_dir') . '/js/dist/policat_widget.js') ?>" as="script">
        <?php if ($openECI): ?><link rel="preload" href="/js/lib/iframeResizer/iframeResizer.min.js" as="script"><?php endif ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="language" content="<?php echo $lang ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <?php include_title() ?>
        <script type="text/javascript">
      var widget_id = <?php echo $widget['id'] ?>;
<?php
/* @var $petition Petition */
$form_title = trim(Util::enc($petition_text->getFormTitle(), array('\n' => '<br />')));
$form_title_plain = trim(strtr($petition_text->getFormTitle(), array('\n' => ' ')));
$share_title = $title;
if ($form_title_plain) {
  $share_title .= ($share_title ? '. ' : '') . $form_title_plain;
} else {
  $share_title .= ($share_title ? '. ' : '') . __($petition->getLabel(PetitionTable::LABEL_TITLE));
}
$share_title .= ':';
if ($social_share_text) {
  $share_title = strtr($social_share_text, array('#TITLE#' => $form_title_plain, '#WIDGET-HEADING#' => $title));
}
$target_selectors = UtilTargetSelectorPreselect::staticTargetSelectors($widget); // = $petition->getTargetSelectors();
ContactTable::getInstance()->mergeKeywordSubst($target_selectors, $petition, $lang);
if (is_array($target_selectors) && count($target_selectors) == 1 && $target_selectors[0]['id'] !== 'contact') {
  if ($petition->getKind() != Petition::KIND_PLEDGE) {
    $target_selectors[] = array('id' => 'contact', 'name' => 'Recipient(s)');
  }
}
if (is_array($target_selectors)) {
  foreach ($target_selectors as &$ts) {
    $ts['name'] = __($ts['name']);
  }
}
?>
      var numberSeparator = "<?php echo $culture_info->getNumberFormat()->getGroupSeparator() ?>";
      var petition_id = <?php echo $petition->getId() ?>;
      var target_selectors = <?php echo json_encode($target_selectors) ?>;
      var t_sel = <?php echo json_encode(__('select')) ?>;
      var t_sel_all = <?php echo json_encode($petition->getKind() == Petition::KIND_PLEDGE ? '--' . __('select') . '--' : __('select all')) ?>;
<?php if ($target_selectors && $petition->isGeoKind() && !$petition->getWithCountry()): /* @var $petition_text PetitionText */ ?>
        var CT_extra = <?php echo json_encode($culture_info->getCountries($petition_text->utilCountries())) ?>;
<?php else: ?>
        var CT_extra = null;
<?php endif ?>
      var isOpenECI  = <?php echo json_encode($openECI) ?>;
      var srcOpenECI = '<?php echo Util::enc($petition->getOpeneciURL()) ?>?channel=<?php echo Util::enc($petition->getOpeneciChannel()) ?>&lang=<?php echo $lang ?>';
        </script>
        <?php if (!UtilTheme::removeClassicCss($widget, $petition)): ?><link rel="stylesheet" type="text/css" href="/css/dist/policat_widget.css?<?php echo filemtime(sfConfig::get('sf_web_dir') . '/css/dist/policat_widget.css') ?>" /><?php endif ?>
        <?php if ($font_css_file): ?><link href="<?php echo $font_css_file ?>" rel="stylesheet" type="text/css" /><?php endif ?>
        <?php UtilTheme::printCss($widget, $petition); ?><!-- <?php echo $petition['themeId'] ?> -->
    </head>
    <body>
        <div id="widget" class="widget <?php echo $openECI ? 'eci' : '' ?>">
            <?php if ($title || $target): ?>
              <div class="header">
                  <?php if ($title): ?><h1 class="action-title"><?php echo Util::enc($title) ?></h1><?php endif ?>
                  <?php if ($target): ?><div class="subtitle"><?php echo UtilMarkdown::transformMedia($target, $petition) ?></div><?php endif ?>
              </div>
            <?php endif ?>
            <div class="widget-body">
                <div id="widget-left" class="widget-left">
                    <div class="content-left">
                        <div id="action" class="action">
                            <div id="head" class="head">
                                <h1 class="form-title title-color <?php echo $form_title ? '' : 'form-title-label' ?>"><?php echo $form_title ? : __($petition->getLabel(PetitionTable::LABEL_TITLE)) ?></h1>
                                <?php if ($title): ?><h1 class="action-title font-size-auto"><?php echo Util::enc($title) ?></h1><?php endif ?>
                                <?php if ($target): ?><div class="subtitle"><?php echo UtilMarkdown::transformMedia($target, $petition) ?></div><?php endif ?>
                            </div>
                            <a id="down-button" class="button-color down-button button-btn"><?php echo __($petition->getLabel(PetitionTable::LABEL_TITLE)) ?></a>
                            <?php
                            if ($background):
                              switch ($petition->getKind()):
                                case Petition::KIND_EMAIL_TO_LIST:
                                case Petition::KIND_EMAIL_ACTION: $left_tab_text = __('Email action');
                                  break;
                                case Petition::KIND_PLEDGE: $left_tab_text = __('Recipients');
                                  break;
                                default: $left_tab_text = __($petition->getLabel(PetitionTable::LABEL_TAB));
                              endswitch
                              ?>
                              <div id="tabs" class="tabs left">
                                  <div class="tab-head">
                                      <div class="left tab to-left-tab"><span><?php echo $left_tab_text ?></span></div>
                                      <div class="tab-mid"></div>
                                      <div class="right tab to-right-tab"><span><?php echo __('Background') ?></span></div>
                                  </div>
                                  <div class="tab-body">
                                      <div class="left-tab">
                                          <?php include_partial('petition', array('petition_text' => $petition_text, 'widget' => $widget, 'petition' => $petition)) ?>
                                          <div class="tab-alternate"><a class="readmore to-right-tab"><?php echo __('Read more') ?></a></div>
                                      </div>
                                      <div class="right-tab">
                                          <?php echo UtilMarkdown::transformMedia($background, $petition); ?>
                                          <br />
                                          <div class="tab-alternate"><a class="to-left-tab tab-back"><?php echo __('Back') ?></a></div>
                                          <?php if (is_string($read_more_url) && strlen($read_more_url) > 6): ?>
                                              <a id="readmore" href="<?php echo Util::enc($read_more_url) ?>" class="newwin readmore readmore-background"><?php echo __('Read more') ?></a>
                                          <?php endif ?>
                                      </div>
                                      <div class="tab-pad"></div>
                                  </div>
                              </div>
                            <?php else: ?>
                              <?php include_partial('petition', array('petition_text' => $petition_text, 'widget' => $widget, 'petition' => $petition)) ?>
                              <?php if (is_string($read_more_url) && strlen($read_more_url) > 6): ?>
                                <a id="readmore" href="<?php echo Util::enc($read_more_url) ?>" class="newwin readmore"><?php echo __('Read more') ?></a>
                              <?php endif ?>
                            <?php endif ?>
                            <div style="clear: both"></div>
                        </div>
                        <div id="privacy-policy" class="privacy-policy">
                            <h1><?php echo __('PP Heading') ?></h1>
                            <a class="back back-priv-1 button-color button-btn"><?php echo __('Back') ?></a>
                            <?php
                            $privacy_policy = strtr($petition_text['privacy_policy_body'], $widget->getDataOwnerSubst('<br />', $petition));
                            echo UtilMarkdown::transform($privacy_policy);
                            ?>
                            <a class="back back-priv-2 button-color button-btn"><?php echo __('Back') ?></a>
                        </div>
                        <?php if (!$form_embed->isOneSide() && $petition->getShowEmbed()): ?>
                          <div id="embed-this-left" class="embed-this embed-this-left">
                              <h2><?php echo __('Customise your widget') ?> (?)</h2>
                              <div class ="embed-this-help-head"><?php echo __('Customise contents: use your own words to convince your target group. To change the title, introduction and background texts, copy the texts from the edit fields into a text editor, make your changes and paste them back into the edit fields. Thereafter, adapt the width and the colours to seamlessly integrate the widget into your website design.') ?></div>
                              <form id="embed2" action="" method="post">
                                  <?php
                                  if (isset($form_embed['petition_text_id']) && !$form_embed->isOneSide()) {
                                    echo $form_embed['petition_text_id']->renderRow(array('id' => 'widget_petition_text_id_copy'));
                                  }
                                  ?>
                                  <?php if (isset($form_embed['title'])): ?>
                                    <div>
                                        <label><?php echo __('Title') ?></label>
                                        <input type="text" id="widget_title_copy" />
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['target'])): ?>
                                    <div>
                                        <label><?php echo __('Target, Subheading') ?></label>
                                        <textarea id="widget_target_copy" rows="5" cols="10"></textarea>
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['background'])): ?>
                                    <div>
                                        <label><?php echo __('Background') ?></label>
                                        <textarea id="widget_background_copy" rows="5" cols="10"></textarea>
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['intro'])): ?>
                                    <div>
                                        <label><?php echo __('Intro') ?></label>
                                        <textarea id="widget_intro_copy" rows="5" cols="10"></textarea>
                                    </div>
                                    <div>
                                        <label><?php echo __('Body') ?></label>
                                        <textarea id="widget_body" cols="1" rows="1" disabled="disabled"><?php echo $petition_text->getBody() ?></textarea>
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['footer'])): ?>
                                    <div>
                                        <label><?php echo __('Footer') ?></label>
                                        <textarea id="widget_footer_copy" rows="5" cols="10"></textarea>
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['email_subject'])): ?>
                                    <div>
                                        <label><?php echo __('Subject') ?></label>
                                        <textarea id="widget_email_subject_copy" rows="5" cols="10"></textarea>
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['email_body'])): ?>
                                    <div>
                                        <label><?php echo __('Email body') ?></label>
                                        <textarea id="widget_email_body_copy" rows="5" cols="10"></textarea>
                                    </div>
                                  <?php endif ?>
                                  <?php if (isset($form_embed['landing_url']) && !$form_embed->isOneSide()): ?>
                                    <div>
                                        <label><?php echo __('Email Validation Landingpage - auto forwarding to external page') ?></label>
                                        <input type="text" id="widget_landing_url_copy" class="url not_required" placeholder="http://example.com/" value="<?php echo Util::enc($widget->getInheritLandingUrl()) ?>" />
                                    </div>
                                  <?php endif ?>
                              </form>
                          </div>
                        <?php endif ?>
                    </div>
                </div>

                <div id="widget-right" class="widget-right show-sign">
                    <div id="content-right" class="content-right">
                        <?php if ($openECI): ?>
                        <div class="openECI-thankyou-with-sign"><?php /* Shown if ECI is signed before policat  */ ?>
                            <h2 class="title-color"><?php echo __('Thank you') ?></h2>
                            <div class="openECI-message"><span class="eci-duplicate"><?php echo __("Attention: You've already taken part in this action (maybe on another website).") ?></span><span class="eci-success"><?php echo __('Your statement of support has been submitted successfully.') ?></span> <?php echo __('Signature identifier') ?>: <span class="eci-number"></span>. <span class="eci-tell"><?php echo __('Use this moment to tell friends and family.') ?></span><span class="eci-please-sign-policat"><?php echo __('Sign up and become part of the movement.') ?></span></div>
                        </div>
                        <?php endif ?>
                        <div class="sign">
                            <h2 class="form-title title-color"><?php echo trim(Util::enc($petition_text->getFormTitle(), array('\n' => '<br />'))) ? : __($petition->getLabel(PetitionTable::LABEL_TITLE)) ?></h2>
                            <?php
                            $disabled = false;
                            $require_billing_before = $require_billing_after = false;
                            if ($require_billing) {
                              if ($petition->countSignings() < 10) { // show begin message when action has some signings
                                $require_billing_before = true;
                              } else {
                                $require_billing_after = true;
                              }
                            }
                            if ($petition->isBefore() || $require_billing_before): $disabled = true
                              ?>
                              <?php if ($petition->getKeyVisual()): ?><div class="keyvisual"><img src="<?php echo image_path('keyvisual/' . $petition->getKeyVisual()) ?>" alt="" /></div><?php endif ?>
                              <p><?php echo __('The action starts on #DATE#. Stay tuned and spread the word!', array('#DATE#' => $petition->getStartAt() ? format_date($petition->getStartAt(), 'D') : 'XX.XX.XXXX')) ?></p>
                            <?php elseif ($petition->isAfter() || $require_billing_after): $disabled = true ?>
                              <?php if ($petition->getKeyVisual()): ?><div class="keyvisual"><img src="<?php echo image_path('keyvisual/' . $petition->getKeyVisual()) ?>" alt="" /></div><?php endif ?>
                              <p>
                                  <?php echo __('This action is over. Thanks to the #COUNTER# people who signed-up!', array('#COUNTER#' => '<b>' . $petition->countSigningsPlus() . '</b>')) ?>
                                  <a target="_blank" href="<?php echo url_for('homepage') ?>"><?php echo __('More actions') ?></a>
                              </p>
                            <?php endif ?>
                            <?php if (!$disabled):
                              $count_translation = ($petition->getKind() == Petition::KIND_EMAIL_TO_LIST && $petition->getShowEmailCounter() == Petition::SHOW_EMAIL_COUNTER_YES) ? '# emails sent' : '# Participants';
                              ?>
                              <div id="count" class="count<?php echo $petition->getShowTarget() ? '' : ' count-hide-target' ?>">
                                  <div class="count-text count-text-top"><span class="count-count"><?php echo __($count_translation) ?></span><span class="count-target"><?php echo __('Target #') ?></span></div>
                                  <div class="count-text count-target-top count-target-number"></div>
                                  <div class="count-bar"><div></div><span></span></div>
                                  <div class="count-text count-text-bottom"><span class="count-count"><?php echo __($count_translation) ?></span><span class="count-target"><?php echo __('Target #') ?></span></div>
                                  <div class="count-text count-text-alt"><span class="count-count"><?php echo __($count_translation) ?></span><span>.</span> <span class="count-target"><?php echo __('Target #') ?></span></div>
                              </div>
                            <?php endif ?>
                            <?php echo $form->renderGlobalErrors() ?>
                            <form <?php if ($disabled): ?>style="display:none"<?php endif ?> id="sign" class="sign-form" action="" method="post" autocomplete="off">
                                <?php echo $form->renderHiddenFields() ?>
                                <fieldset>
                                    <?php
                                    foreach ($form as $fieldname => $fieldwidget) {
                                      $group = $form->isGroupedField($fieldname);
                                      if (!$fieldwidget->isHidden()) {
                                        printf('<div class="form-row %s%s%s">%s</div>', $fieldname, $group ? ' group' : '', $group === 2 ? ' first' : '', $fieldwidget->renderRow());
                                      }
                                    }
                                    if (!isset($form[Petition::FIELD_PRIVACY])):
                                      ?>
                                      <div class="privacy privacy-no-check">
                                          <label class="long-text"><?php echo UtilBold::format(__('By signing, I agree with the _privacy policy_.')) ?></label>
                                          <label class="short-text">&raquo;&nbsp;<?php echo __('PP Heading') ?></label>
                                      </div>
                                    <?php endif; ?>
                                    <div id="subscribe-checkbox" style="display:none"><!--
                                        <label for="petition_signing_subscribe"><?php echo __('Keep me posted on this and similar campaigns.') ?></label>
                                        <span class="checkbox"><input class="required" type="checkbox" name="petition_signing[subscribe]" value="1" id="petition_signing_subscribe"></span>-->
                                    </div>
                                </fieldset>
                                <div class="submit-sign-container">
                                    <button type="button" class="submit submit-sign submit-no-subscribe"><span class="font-size-auto"><?php echo strtr(__($petition->getLabel(PetitionTable::LABEL_BUTTON)), array(' ' => '&nbsp;')) ?></span></button>
                                    <?php if ($openECI): ?>
                                        <button type="button" class="submit submit-sign submit-subscribe"><span class="font-size-auto-subscribe"><?php echo strtr(__($petition->getLabel(PetitionTable::LABEL_BUTTON_SUBSCRIBE)), array(' ' => '&nbsp;')) ?></span></button>
                                    <?php endif ?>
                                </div>
                                <?php if ($openECI): ?>
                                <div class="go-to-eci-form">
                                    <a class="go-to-eci-form" href=""><?php echo __('Skip and directly support the citizen initiative') ?></a>
                                </div>
                                <?php endif ?>
                            </form>
                            <?php if ($disabled): ?>
                              <div id="footer_ot"></div>
                            <?php else: ?>
                              <?php if ($petition->getShowKeyvisual() && $petition->getKeyVisual()): ?><div class="keyvisual keyvisual-bottom"><img src="<?php echo image_path('keyvisual/' . $petition->getKeyVisual()) ?>" alt="" /></div><?php endif ?>
                            <?php endif ?>
                            <?php if (is_string($read_more_url) && strlen($read_more_url) > 6 && !$background): ?>
                            <a href="<?php echo Util::enc($read_more_url) ?>" class="newwin readmore-btn"><?php echo __('Read more') ?></a>
                            <?php endif ?>
                        </div>
                        <?php if ($openECI): ?>
                        <div class="openECI" id="openECIParent">
                        </div>
                        <?php endif ?>
                        <?php if ($petition->getShowEmbed()): ?>
                        <div class="embed-this">
                            <h2 class="title-color"><?php echo __('Embed this') ?></h2>
                            <?php echo $form_embed->renderGlobalErrors(); ?>
                            <form id="embed" class="embed" action="" method="post">
                                <?php echo $form_embed->renderHiddenFields(); ?>
                                <fieldset>
                                    <?php
                                    foreach (array('styling_title_color', 'styling_body_color', 'styling_bg_left_color', 'styling_bg_right_color', 'styling_form_title_color', 'styling_button_color', 'styling_button_primary_color', 'styling_label_color', 'styling_font_family') as $fieldname):
                                      if (isset($form_embed[$fieldname])):
                                        $group = $form_embed->isGroupedField($fieldname);
                                        printf('<div class="form-row %s%s%s">%s</div>', $fieldname, $group ? ' group' : '', $group === 2 ? ' first' : '', $form_embed[$fieldname]->renderRow());
                                      endif;
                                    endforeach;
                                    ?>
                                    <?php
                                    if (isset($form_embed['petition_text_id'])):
                                      if ($form_embed->isOneSide()):
                                        echo $form_embed['petition_text_id']->renderRow();
                                      else:
                                        $form_embed_name = $form_embed->getName();
                                        $petition_text_id_name = $form_embed['petition_text_id']->getName();
                                        ?>
                                        <input name="<?php echo "{$form_embed_name}[{$petition_text_id_name}]" ?>" id="<?php echo "{$form_embed_name}_{$petition_text_id_name}" ?>" type="hidden" />
                                      <?php
                                      endif;
                                    endif
                                    ?>
                                    <?php
                                    if (isset($form_embed['landing_url']) && $form_embed->isOneSide()) {
                                      echo $form_embed['landing_url']->renderRow();
                                    }
                                    if (isset($form_embed['paypal_email'])):
                                      ?>
                                      <div class="form-row checkbox">
                                          <?php echo $form_embed['paypal_email'], $form_embed['paypal_email']->renderLabel() ?>
                                      </div>
                                    <?php endif ?>
                                    <h2 id="embed-this-register" class="form-row embed-this-register"><?php echo __('Register your widget') ?>:</h2>
                                    <?php
                                    foreach (array('email', 'organisation') as $fieldname) {
                                      printf('<div class="form-row %s">%s</div>', $fieldname, $form_embed[$fieldname]->renderRow());
                                    }
                                    ?>
                                </fieldset>
                                <button type="button" class="submit button-small">
                                    <span id="embed-this-generate"><?php echo __('Generate widget') ?></span>
                                    <span id="embed-this-change" style="display:none"><?php echo __('Change widget') ?></span>
                                </button>
                            </form>
                            <div class="embed-code">
                                <label><?php echo __('Embed this code') ?>:</label>
                                <input type="text" id="embed_markup" readonly="readonly"/>
                                <a id="embed-copy" class="embed-copy button-color" title="<?php echo __('Copy to clipboard') ?>"><img class="no_load" src="/images_static/clipboard-64.png" /></a>
                            </div>
                            <a class="back button-color button-btn"><?php echo __('Back') ?></a>
                        </div>
                        <?php endif ?>
                        <div class="thankyou"><?php /* Shown after policat (and maybe ECI) signed OR after email-validation */ ?>
                            <h2 class="title-color"><?php echo __('Thank you') ?></h2>
                            <?php if ($openECI): ?>
                            <div class="openECI-message"><span class="eci-duplicate"><?php echo __("Attention: You've already taken part in this action (maybe on another website).") ?></span><span class="eci-success"><?php echo __('Your statement of support has been submitted successfully.') ?></span> <?php echo __('Signature identifier') ?>: <span class="eci-number"></span>. <span class="eci-tell"><?php echo __('Use this moment to tell friends and family.') ?></span></div>
                            <?php endif ?>
                            <p class="form_message label_color">
                                <?php /* This can be overwritten by js through, see policat_widget.js and _json_form.php */ ?>
                                <?php echo __('You verified your email address.') . ' ' . __('Use this moment to tell friends and family.') ?>
                            </p>
                        </div>
                        <?php if ($petition->getLastSignings() != PetitionTable::LAST_SIGNINGS_NO): ?>
                          <div class="last-signings">
                              <div id="last-signers-exists" class="last-signers-exists">
                                  <h2 class="label_color"><?php echo __('Last signers') ?></h2>
                                  <div id="last-signers" data-update="<?php echo ($petition->getLastSignings() == PetitionTable::LAST_SIGNINGS_SIGN_CONFIRM) ? 1 : 0 ?>" class="last-signers">
                                      <?php if ($last_signings): ?>
                                        <?php foreach ($last_signings as $signer): /* @var $signer PetitionSigning */ ?><span><?php echo Util::enc($signer->getSignersListEntry($petition, $petition_text->getLanguageId())) ?></span><?php endforeach ?>
                                      <?php endif ?>
                                  </div>
                                  <div>
                                      <?php if ($petition_text->getSignersUrl()): ?>
                                      <a class="submit" target="_parent" href="<?php echo Util::enc($petition_text->getSignersUrl()) ?>"><?php echo __('All signers') ?></a>
                                      <?php else: ?>
                                      <a class="submit newwin" href="<?php echo url_for('signers', array('id' => $petition->getId(), 'text_id' => $widget->getPetitionTextId())) ?>"><?php echo __('All signers') ?></a>
                                      <?php endif ?>
                                  </div>
                              </div>
                          </div>
                        <?php endif ?>
                        <div class="share <?php echo $widget['share'] ? 'share-on-sign' : '' ?>">
                            <h2 class="label_color"><?php echo __('Tell your friends') ?></h2>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=" class="newwin sicon facebook" title="Facebook"><img class="no_load" alt="Facebook" src="/images_static/facebook-64.png" /></a>
                            <a href="whatsapp://send?text=<?php echo rawurlencode($share_title . ' ') ?>" class="hideDesktop sicon whatsapp" title="WhatsApp"><img class="no_load" alt="WhatsApp" src="/images_static/whatsapp-64.png" /></a>
                            <a href="https://twitter.com/share?text=<?php echo urlencode($share_title) ?>&amp;url=" class="newwin sicon twitter" title="Twitter"><img class="no_load" alt="Twitter" src="/images_static/twitter-64.png" /></a>
                            <?php
                            list($mail_subject, $mail_body) = UtilMail::tellyourmail($widget, $petition_text, 'UURRLLRREEFF', 'UURRLLMMOORREE');
                            ?>
                            <a href="mailto:?subject=<?php echo $mail_subject ?>&amp;body=<?php echo $mail_body ?>" class="sicon mailto" title="Email" target="_top"><img  class="no_load" alt="Email" src="/images_static/email-64.png" /></a>
                            <?php if ($petition->getShowEmbed()): ?><a id="a-embed-this" class="sicon a-embed-this" title="<?php echo __('Embed this') ?>"><img class="no_load" alt="<?php echo __('Embed this') ?>" src="/images_static/code-64.png" /></a><?php endif ?>
                            <?php if ($paypal_email || $donate_url): ?>
                              <?php if ($donate_direct): ?>
                                <a class="sicon donate-btn" target="_blank" href="<?php echo $donate_url ?>" title="<?php echo __('Donate') ?>"><img class="no_load" alt="<?php echo __('Donate') ?>" src="/images_static/charity-64.png" /></a>
                              <?php else: ?>
                                <a id="a-donate" class="sicon donate-btn" title="<?php echo __('Donate') ?>"><img class="no_load" alt="<?php echo __('Donate') ?>" src="/images_static/charity-64.png" /></a>
                              <?php endif ?>
                            <?php endif ?>
                        </div>
                        <div class="donate">
                            <?php if ($paypal_email): ?>
                              <?php if ($donate_text): ?>
                                <div class="label_color external_links"><?php echo UtilMarkdown::transform($sf_data->getRaw('donate_text')) ?></div>
                              <?php else: ?>
                                <h2 class="label_color"><?php echo __('Donate') ?></h2>
                                <p class="label_color"><?php echo __('Help us fund this campaign. Give whatever you can now using the safe and secure paypal form below.') ?></p>
                              <?php endif ?>
                              <form id="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                  <input type="hidden" name="cmd" value="_xclick" />
                                  <input type="hidden" name="business" value="<?php echo Util::enc($paypal_email); ?>" />
                                  <input type="hidden" name="item_name" value="<?php echo Util::enc($petition_title) ?>" />
                                  <input type="hidden" name="item_number" value="<?php echo $paypal_ref; ?>" />
                                  <input type="hidden" name="lc" value="<?php echo strtoupper($lang) ?>" />
                                  <input type="hidden" name="no_shipping" value="2" />
                                  <input type="hidden" name="no_note" value="1" />
                                  <input type="hidden" name="tax" value="0" />
                                  <input type="hidden" name="bn" value="IC_Beispiel" />
                                  <fieldset>
                                      <div class="form-row amount group first">
                                          <label><?php echo __('Amount') ?></label>
                                          <input id="paypal_amount" type="text" name="amount" value="" />
                                      </div>
                                      <div class="form-row currency_code group">
                                          <label>&nbsp;</label>
                                          <select name="currency_code">
                                              <option value="EUR">Euro</option>
                                              <option value="USD">Dollar</option>
                                              <option value="GBP">Pound</option>
                                          </select>
                                      </div>
                                  </fieldset>
                                  <div>
                                      <button type="button" class="submit button-small"><?php echo __('Donate') ?></button>
                                  </div>
                              </form>
                            <?php endif ?>
                            <?php if ($donate_url && $donate_text): ?>
                              <h2 class="label_color"><?php echo __('Donate') ?></h2>
                              <div class="label_color external_links"><?php echo UtilMarkdown::transform($sf_data->getRaw('donate_text')) ?></div>
                              <a class="submit button-small" target="_blank" href="<?php echo $donate_url ?>"><?php echo __('Donate') ?></a>
                            <?php endif ?>
                            <?php if ($paypal_email || $donate_url): ?>
                              <a class="back button-color button-btn"><?php echo __('Back') ?></a>
                            <?php endif ?>
                        </div>
                        <div class="reload">
                            <a class="reload-iframe button-color button-btn"><?php echo __('Back') ?></a>
                        </div>
                    </div>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <div id="policat-widget-loading" class="policat-widget-loading"></div>
        <div id="labels-inside"></div>
        <script type="text/javascript" src="/js/dist/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="/js/dist/policat_widget.js?<?php echo filemtime(sfConfig::get('sf_web_dir') . '/js/dist/policat_widget.js') ?>"></script>
        <?php if ($openECI): ?><script type="text/javascript" src="/js/lib/iframeResizer/iframeResizer.min.js"></script><?php endif ?>
    </body>
</html>
<!-- <?php echo $petition->getId() ?> / <?php echo $widget->getPetitionTextId() ?> / <?php echo $widget->getId() ?> / <?php echo gmdate('Y-m-d H:i:s') ?> GMT -->
