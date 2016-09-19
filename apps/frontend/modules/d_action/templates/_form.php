<?php $petition = $form->getObject() ?>
<form id="petition_edit_form" class="ajax_form form-horizontal" action="<?php echo url_for('petition_edit_', array('id' => $form->getObject()->getId())) ?>" method="post" enctype="multipart/form-data">
    <div class="row tabbable tabs-left">
        <div class="span3">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#sec1" data-toggle="tab">Basic settings</a></li>
                <li><a href="#sec2" data-toggle="tab">Customise sign-up form</a></li>
                <li><a href="#sec3" data-toggle="tab">Opt-in (email verification) &amp; thank-you emails</a></li>
                <li><a href="#sec4" data-toggle="tab">Donation module (optional)</a></li>
                <li><a href="#sec5" data-toggle="tab">Promote your e-action</a></li>
                <li><a href="#sec6" data-toggle="tab">Widget adjustability and standard design</a></li>
                <?php if ($petition->getKind() == Petition::KIND_PLEDGE): ?>
                  <li><a href="#sec7" data-toggle="tab">Pledge Settings</a></li>
                <?php endif ?>
            </ul>
        </div>
        <div class="span9">
            <div class="tab-content">
                <fieldset class="tab-pane active show-before-chosen-init" id="sec1">
                    <legend>Basic settings</legend>
                    <div class="control-group">
                        <label class="control-label">
                            E-action type
                        </label>
                        <div class="controls">
                            <span class="widget_text"><?php echo $petition->getKindName() ?></span>
                        </div>
                    </div>
                    <?php echo $form->renderRows('status', 'start_at', 'end_at', 'name', '*editable') ?>
                    <fieldset>
                        <?php if ($petition->isEmailKind() && !$petition->isGeoKind()): ?><legend>Recipient(s) of the email action (your campaign targets)</legend><?php endif ?>
                        <?php echo $form->renderRows('*email_target_name_1', '*email_target_email_1', '*email_target_name_2', '*email_target_email_2', '*email_target_name_3', '*email_target_email_3') ?>
                    </fieldset>
                    <?php echo $form->renderRows('*label_mode', 'read_more_url') ?>
                </fieldset>
                <fieldset class="tab-pane show-before-chosen-init" id="sec2">
                    <legend>Customise sign-up form</legend>
                    <p class="alert alert-danger">If you make changes here for a running action you may lose data if you remove fields.</p>
                    <div class="global_error">
                        <span id="new_petition_customise"></span>
                    </div>
                    <?php echo $form->renderRows('titletype', 'nametype', 'with_address', 'with_country', 'default_country', 'country_collection_id', 'with_comments', 'with_extra1', 'policy_checkbox', 'subscribe_default') ?>
                </fieldset>
                <fieldset  class="tab-pane show-before-chosen-init" id="sec3">
                    <legend>Opt-in (email verification) &amp; thank-you emails</legend>
                    <?php echo $form->renderRows('from_name', 'from_email') ?>
                    <?php if (sfConfig::get('app_spf_ip')): ?>
                      <div class="controls">
                          <a data-collect="<?php echo Util::enc(json_encode(array('email' => '#edit_petition_from_email'))) ?>" href="<?php echo url_for('petition_spf') ?>" class="btn ajax_link post">Make SPF check</a>
                      </div>
                      <br />
                    <?php endif ?>
                    <?php echo $form->renderRows('*validation_required', 'landing_url', 'thank_you_email', 'email_button_color') ?>
                </fieldset>
                <fieldset  class="tab-pane show-before-chosen-init" id="sec4">
                    <legend>Donation module (optional)</legend>
                    <?php echo $form->renderRows('*paypal_email', 'donate_url', 'donate_widget_edit') ?>
                </fieldset>
                <fieldset  class="tab-pane show-before-chosen-init" id="sec5">
                    <legend>Promote your e-action</legend>
                    <?php echo $form->renderRows('homepage', 'twitter_tags') ?>
                </fieldset>
                <fieldset  class="tab-pane show-before-chosen-init" id="sec6">
                    <legend>Widget adjustability and standard design</legend>
                    <?php echo $form->renderRows('widget_individualise', 'themeId', 'style_font_family') ?>
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $form->renderRows('style_bg_right_color', 'style_bg_left_color', 'style_button_primary_color', 'style_button_color') ?>
                        </div>
                        <div class="span6">
                            <?php echo $form->renderRows('style_title_color', 'style_form_title_color', 'style_body_color', 'style_label_color') ?>
                        </div>
                    </div>
                    <?php echo $form->renderRows('share', 'key_visual', 'show_keyvisual', 'last_signings') ?>
                    <div id="last-signings-options" class="show-before-chosen-init">
                        <?php echo $form->renderRows('last_signings_city', 'last_signings_country') ?>
                    </div>
                </fieldset>
                <?php if ($petition->getKind() == Petition::KIND_PLEDGE): ?>
                  <fieldset  class="tab-pane show-before-chosen-init" id="sec7">
                      <legend>Pledge Settings</legend>
                      <?php echo $form->renderRows('pledge_with_comments', 'pledge_header_visual', 'pledge_key_visual', 'pledge_background_color', 'pledge_color', 'pledge_head_color', 'pledge_font', 'pledge_info_columns_comma') ?>
                  </fieldset>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="span3">&nbsp;</div>
        <?php echo $form->renderHiddenFields() ?>
        <div class="span9">
            <div class="form-actions">
                <button accesskey="s" title="[Accesskey] + S" class="btn btn-primary" type="submit">Save</button>
                <?php if ($petition->isGeoKind()): ?>
                  <a class="btn submit" data-submit='{"go_target":1}'>Save &amp; select target list</a>
                <?php elseif ($petition->getKind() == Petition::KIND_PLEDGE): ?>
                  <a class="btn submit" data-submit='{"go_pledge":1}'>Save &amp; define pledges</a>
                <?php else: ?>
                  <a class="btn submit" data-submit='{"go_translation":1}'>Save &amp; go to actions texts and translations</a>
                <?php endif ?>
            </div>
        </div>
    </div>
</form>
