<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>" lang="<?php echo $lang ?>">
    <?php
    use_helper('Text', 'I18N', 'Date');
    /* @var $petition Petition */
    /* @var $widget Widget */
    /* @var $form_embed WidgetPublicForm */
    ?>
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="language" content="<?php echo $lang ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <?php include_title() ?>
        <script type="text/javascript">
      var widget_id = <?php echo $widget['id'] ?>;
<?php
/* @var $petition Petition */
$target_selectors = $petition->getTargetSelectors();
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
      var target_selectors = <?php echo json_encode($target_selectors) ?>;
      var t_sel = <?php echo json_encode(__('select')) ?>;
      var t_sel_all = <?php echo json_encode($petition->getKind() == Petition::KIND_PLEDGE ? '--' . __('select') . '--' : __('select all')) ?>;
<?php if ($target_selectors && $petition->isGeoKind() && !$petition->getWithCountry()): /* @var $petition_text PetitionText */ ?>
        var CT_extra = <?php echo json_encode($petition_text->utilCultureInfo()->getCountries($petition_text->utilCountries())) ?>;
<?php else: ?>
        var CT_extra = null;
<?php endif ?>
        </script>
        <?php echo '<style type="text/css">' . file_get_contents(sfConfig::get('sf_web_dir') . '/css/dist/policat_widget.css') . "\n</style>"; ?>
        <script type="text/javascript" src="/js/static/jquery-1.10.2.min.js"></script>
        <?php printf("<script type=\"text/javascript\">/* <![CDATA[ */\n%s\n/* ]]> */</script>\n", file_get_contents(sfConfig::get('sf_web_dir') . '/js/dist/policat_widget.js')); ?>
        <?php if ($font_css_file): ?><link href="<?php echo $font_css_file ?>" rel="stylesheet" type="text/css" /><?php endif ?>
        <style type="text/css">
            #html,body,div,span,h1,h2,h3,h4,h5,h6,p,a,em,img,q,dl,dt,dd,ol,ul,li,form,label,input,textarea, select { font-family: <?php echo $font_family ?>;}
            #body_policat_widget, a { color: <?php echo $body_color ?>; }
            label, #count_target, .pet_subtitle, .label_color, .no_tabs .right_tab, .no_tabs .read_more { color: <?php echo $label_color ?>; }
            h1, h2 { color: <?php echo $title_color ?>; }
            #policat_widget_right .submit, .button_color { background-color: <?php echo $button_color; ?> ; }
            #policat_widget_right .submit_sign { background-color: <?php echo $button_primary_color; ?> ; }
            #policat_widget { background: <?php echo $bg_right_color ?>; }
            div#count { background: <?php echo $bg_left_color ?>; }
            div#count div { background: <?php echo $button_color ?>; }
            #petition_tabs .tab-mid {
              border: 13px solid <?php echo $bg_left_color ?>;
            }
            #petition_tabs.left .left span,
            #petition_tabs.right .right span,
            #petition_tabs .tab_body, #petition_tabs .left_tab
            {
              background: <?php echo $bg_left_color ?>;
            }
            <?php if ($form_title_color): ?>
            h2.form_title,
            #petition_tabs.left .right,
            #petition_tabs.right .left
            { color: <?php echo $form_title_color ?>; }
            <?php endif ?>
        </style>
    </head>
    <body id="body_policat_widget">
        <div id="policat_widget">
            <div id="policat_widget_left">
                <div id="content_left">
                    <div id="petition">
                        <div id="petition_head">
                            <h1 id="pet_title"><?php echo htmlentities($title, ENT_COMPAT, 'utf-8') ?></h1>
                            <div class="pet_subtitle" class="title"><?php echo UtilMarkdown::transform($target) ?></div>
                        </div>
                        <a id="down_button" class="button_color button_btn"><?php echo __($petition->isEmailKind() ? 'Send an Email' : ($petition->getLabelMode() == PetitionTable::LABEL_MODE_PETITION ? 'Sign the Petition' : 'Support the initiative')) ?></a>
                        <div id="petition_tabs" class="left">
                            <div class="tab_head">
                                <div class="left tab"><span><?php
                                        switch ($petition->getKind()):
                                          case Petition::KIND_EMAIL_TO_LIST:
                                          case Petition::KIND_EMAIL_ACTION: echo __('Email action');
                                            break;
                                          case Petition::KIND_PLEDGE: echo __('Recipients');
                                            break;
                                          default: echo $petition->getLabelMode() == PetitionTable::LABEL_MODE_PETITION ? __('Petition') : __('Initiative');
                                        endswitch
                                        ?>
                                    </span></div>
                                <div class="tab-mid"></div>
                                <div class="right tab"><span><?php echo __('Background') ?></span></div>
                            </div>
                            <div class="tab_body">
                                <div class="left_tab">
                                    <?php include_partial('petition', array('petition_text' => $petition_text, 'widget' => $widget, 'petition' => $petition)) ?>
                                </div>
                                <div class="right_tab scroll">
                                    <div id="background_text" class="right_tab_content" style="width: 100%;">
                                        <?php echo UtilMarkdown::transform($background); ?>
                                        <?php if (is_string($read_more_url) && strlen($read_more_url) > 6): ?>
                                          <br /><a id="readmore" href="<?php echo Util::enc($read_more_url) ?>" class="newwin read_more"><?php echo __('Read more') ?></a>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="privacy_policy">
                        <div class="scroll">
                            <div id="privacy_policy_text">
                                <h1 id="priv_title"><?php echo __('PP Heading') ?></h1>
                                <?php
                                $privacy_policy = strtr($petition_text['privacy_policy_body'], $widget->getDataOwnerSubst('<br />', $petition));
                                echo UtilMarkdown::transform($privacy_policy);
                                ?>
                            </div>
                        </div>
                        <a class="back button_color button_btn"><?php echo __('Back') ?></a>
                    </div>
                    <?php if (!$form_embed->isOneSide()): ?>
                      <div id="embed_this_left" class="embed_this">
                          <h1 id="embed_h1"><?php echo __('Customise your widget') ?> (?)</h1>
                          <div id ="embed_this_help_head"><?php echo __('Customise contents: use your own words to convince your target group. To change the title, introduction and background texts, copy the texts from the edit fields into a text editor, make your changes and paste them back into the edit fields. Thereafter, adapt the width and the colours to seamlessly integrate the widget into your website design.') ?></div>
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

            <div id="policat_widget_right" class="show_sign show_share">
                <div id="content_right">
                    <div class="stage_right">
                        <div class="sign">
                            <h2 class="form_title"><?php echo __($petition->isEmailKind() ? 'Send an Email' : ($petition->getLabelMode() == PetitionTable::LABEL_MODE_PETITION ? 'Sign the Petition' : 'Support the initiative')) ?></h2>
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
                            if ($petition->isBefore()|| $require_billing_before): $disabled = true
                              ?>
                              <?php if ($petition->getKeyVisual()): ?><div id="kv_ot"><img src="<?php echo image_path('keyvisual/' . $petition->getKeyVisual()) ?>" alt="" /></div><?php endif ?>
                              <p><?php echo __('The action starts on #DATE#. Stay tuned and spread the word!', array('#DATE#' => $petition->getStartAt() ? format_date($petition->getStartAt(), 'D') : 'XX.XX.XXXX')) ?></p>
                            <?php elseif ($petition->isAfter() || $require_billing_after): $disabled = true ?>
                              <?php if ($petition->getKeyVisual()): ?><div id="kv_ot"><img src="<?php echo image_path('keyvisual/' . $petition->getKeyVisual()) ?>" alt="" /></div><?php endif ?>
                              <p>
                                  <?php echo __('This action is over. Thanks to the #COUNTER# people who signed-up!', array('#COUNTER#' => '<b>' . $petition->countSigningsPlus() . '</b>')) ?>
                                  <a target="_blank" href="<?php echo url_for('homepage') ?>"><?php echo __('More actions') ?></a>
                              </p>
                            <?php endif ?>
                            <?php if (!$disabled): ?><div id="count"><div></div><span></span></div><?php endif ?>
                            <?php echo $form->renderGlobalErrors() ?>
                            <form <?php if ($disabled): ?>style="display:none"<?php endif ?> id="sign" action="" method="post" autocomplete="off">
                                <?php echo $form->renderHiddenFields() ?>
                                <?php
                                foreach ($form as $fieldname => $fieldwidget) {
                                  $group = $form->isGroupedField($fieldname);
                                  if (!$fieldwidget->isHidden()) {
                                    printf('<div class="%s%s%s">%s</div>', $fieldname, $group ? ' group' : '', $group === 2 ? ' first' : '', $fieldwidget->renderRow());
                                  }
                                }
                                if (!isset($form[Petition::FIELD_PRIVACY])): ?>
                                <div class="privacy"><label style="text-decoration:none"><?php echo UtilBold::format(__('By signing, I agree with the _privacy policy_.')) ?></label></div>
                                <?php endif; ?>
                                <button type="button" class="submit submit_sign"><span id="btn_sign"><?php echo strtr(__($petition->isEmailKind() ? 'Send' : 'Sign'), array(' ' => '&nbsp;')) ?></span></button>
                            </form>
                            <?php if ($disabled): ?>
                              <div id="footer_ot"></div>
                            <?php else: ?>
                              <?php if ($petition->getShowKeyvisual() && $petition->getKeyVisual()): ?><div id="kv_ot" class="kv_bottom"><img src="<?php echo image_path('keyvisual/' . $petition->getKeyVisual()) ?>" alt="" /></div><?php endif ?>
                            <?php endif ?>
                        </div>
                        <div class="embed_this">
                            <h2 class="form_title"><?php echo __('Embed this') ?></h2>
                            <?php echo $form_embed->renderGlobalErrors(); ?>
                            <form id="embed" action="" method="post">
                                <?php echo $form_embed->renderHiddenFields(); ?>
                                <?php
                                foreach (array('styling_type', 'styling_width', 'styling_title_color', 'styling_body_color', 'styling_bg_left_color', 'styling_bg_right_color', 'styling_form_title_color', 'styling_button_color', 'styling_button_primary_color', 'styling_label_color', 'styling_font_family') as $fieldname):
                                  if (isset($form_embed[$fieldname])):
                                    $group = $form_embed->isGroupedField($fieldname);
                                    printf('<div class="%s%s%s">%s</div>', $fieldname, $group ? ' group' : '', $group === 2 ? ' first' : '', $form_embed[$fieldname]->renderRow());
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
                                <?php if (isset($form_embed['landing_url']) && $form_embed->isOneSide()) {
                                  echo $form_embed['landing_url']->renderRow();
                                }
                                if (isset($form_embed['paypal_email'])): ?>
                                  <div class="checkbox">
                                      <?php echo $form_embed['paypal_email'], $form_embed['paypal_email']->renderLabel() ?>
                                  </div>
                                <?php endif ?>
                                <h2 id="h_widget_reg"><?php echo __('Register your widget') ?>:</h2>
                                <?php
                                foreach (array('email', 'organisation') as $fieldname) {
                                  printf('<div class="%s">%s</div>', $fieldname, $form_embed[$fieldname]->renderRow());
                                }
                                ?>
                                <button type="button" class="submit button_small">
                                    <span id="p_widget_reg"><?php echo __('Generate widget') ?></span>
                                    <span id="p_widget_reg_alt" style="display:none"><?php echo __('Change widget') ?></span>
                                </button>
                            </form>
                            <div>
                                <label><?php echo __('Embed this code') ?>:</label>
                                <input type="text" id="embed_markup" readonly="readonly"/>
                            </div>
                            <div id="embed_this_help_type"><span><?php echo __("Choose 'Embed' to have this box (\"widget\") embedded into your webpage, including texts and action-form. Visitors can instantly read all and take action. However, you need at least 440px width to embed the widget. Choose 'Popup' if you lack sufficient space on your webpage. You will get a small box (\"teaser\") with flexible width (at least 150px). If visitors click on the teaser, the big action-widget pops up.") ?></span></div>
                            <div id="embed_this_help_width"><span><?php echo __("You may define a precise widget width. Select \"auto\" and the widget will adapt to the space available (max: 1000px). Should there be less than 440px width available, contents will display in one column (instead of two) with the sign-on-form below the petition text. On mobile devices with less than 768px device-width, the widget-width is set to 360px for smooth reading on smartphones.") ?></span></div>
                            <a class="back button_color button_btn"><?php echo __('Back') ?></a>
                        </div>
                        <div class="thankyou">
                            <h2 class="form_title"><?php echo __('Thank you') ?></h2>
                            <p class="form_message label_color"><?php echo __('You verified your email address. Your action is confirmed. Use this moment to tell friends and family.') ?></p>
                            <h2 class="label_color"><?php echo __('Tell your friends') ?></h2>
                        </div>
                        <div class="share">
                            <a href="https://www.facebook.com/sharer/sharer.php?t=<?php echo urlencode($title) ?>&amp;u=" class="newwin sicon facebook" title="Facebook"><img class="no_load" alt="Facebook" src="<?php echo image_path('facebook-32.png') ?>" /></a>
                            <a href="https://twitter.com/share?text=<?php echo urlencode($title) ?>&amp;url=" class="newwin sicon twitter" title="Twitter"><img class="no_load" alt="Twitter" src="<?php echo image_path('twitter-32.png') ?>" /></a>
                            <?php
                            list($mail_subject, $mail_body) = UtilMail::tellyourmail($widget, $petition, $petition_text, 'UURRLLRREEFF', 'UURRLLMMOORREE');
                            ?>
                            <a href="mailto:?subject=<?php echo $mail_subject ?>&amp;body=<?php echo $mail_body ?>" class="sicon mailto" title="Email" target="_top"><img  class="no_load" alt="Email" src="<?php echo image_path('email-32.png') ?>" /></a>
                            <a id="a_embed_this" class="sicon" title="<?php echo __('Embed this') ?>"><img class="no_load" alt="<?php echo __('Embed this') ?>" src="<?php echo image_path('code-32.png') ?>" /></a>
                            <?php if ($paypal_email || $donate_url): ?>
                                <?php if ($donate_direct): ?>
                                    <a class="sicon donate_btn" target="_blank" href="<?php echo $donate_url ?>" title="<?php echo __('Donate') ?>"><img class="no_load" alt="<?php echo __('Donate') ?>" src="<?php echo image_path('charity-32.png') ?>" /></a>
                                <?php else: ?>
                                    <a id="a_donate" class="sicon donate_btn" title="<?php echo __('Donate') ?>"><img class="no_load" alt="<?php echo __('Donate') ?>" src="<?php echo image_path('charity-32.png') ?>" /></a>
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
                                  <input type="hidden" name="business" value="<?php echo $paypal_email; ?>" />
                                  <input type="hidden" name="item_name" value="<?php echo $title; ?>" />
                                  <input type="hidden" name="item_number" value="<?php echo $paypal_ref; ?>" />
                                  <input type="hidden" name="lc" value="<?php echo strtoupper($lang) ?>" />
                                  <input type="hidden" name="no_shipping" value="2" />
                                  <input type="hidden" name="no_note" value="1" />
                                  <input type="hidden" name="tax" value="0" />
                                  <input type="hidden" name="bn" value="IC_Beispiel" />
                                  <div class="amount group first">
                                      <label><?php echo __('Amount') ?></label>
                                      <input id="paypal_amount" type="text" name="amount" value="" />
                                  </div>
                                  <div class="currency_code group">
                                      <label>&nbsp;</label>
                                      <select name="currency_code">
                                          <option value="EUR">Euro</option>
                                          <option value="USD">Dollar</option>
                                          <option value="GBP">Pound</option>
                                      </select>
                                  </div>
                                  <button type="button" class="submit button_small"><?php echo __('Donate') ?></button>
                              </form>
                            <?php endif ?>
                            <?php if ($donate_url): ?>
                              <?php if ($donate_text): ?>
                                <div class="label_color external_links"><?php echo UtilMarkdown::transform($sf_data->getRaw('donate_text')) ?></div>
                              <?php endif ?>
                              <form>
                                <a class="submit button_small" target="_blank" href="<?php echo $donate_url ?>"><?php echo __('Donate') ?></a>
                              </form>
                            <?php endif ?>
                            <?php if ($paypal_email || $donate_url): ?>
                              <a class="back button_color button_btn"><?php echo __('Back') ?></a>
                            <?php endif ?>
                        </div>
                        <div class="reload">
                          <a class="reload-iframe button_color button_btn"><?php echo __('Back') ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>
        <div id="policat_widget_loading"></div>
    </body>
</html>
<!-- <?php echo $petition->getId() ?> / <?php echo $widget->getPetitionTextId() ?> / <?php echo $widget->getId() ?> -->