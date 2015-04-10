/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

$(document).ready(function()	{
    $('.dt_privacy_policy #default_text_body, .dt_agreement #default_text_body, .dt_agreement_email #default_text_body, .dt_agreement_reply #default_text_body, #petition_text_body, #petition_text_intro, #petition_text_footer, #petition_text_background, #widget_background, #privacy_policy_body, #petition_text_privacy_policy_body, .MU, #campaign_privacy_policy, #campaign_address').markItUp(mySettings);

	$('#petition_text_email_validation_subject').parent().before($('#petition_text_helptext'));

	$('#petition_text_language_id').change(function() {
		$.getJSON('/campaign/default_text', {'id' : $(this).val()}, function(data, textStatus) {
			$.each({
				'#petition_text_privacy_policy_body':      data['privacy_policy']['body'],
				'#petition_text_email_validation_subject': data['validation']['subject'],
				'#petition_text_email_validation_body':    data['validation']['body'],
				'#petition_text_email_tellyour_subject':   data['tellyourfriend']['subject'],
				'#petition_text_email_tellyour_body':      data['tellyourfriend']['body']
			}, function(sel, val) {
				$(sel).val(val);
			});
		});
	});
	$('#petition_text_language_id').change();
});
