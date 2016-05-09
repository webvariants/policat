<?php $sf_response->setTitle('policat.org - API') ?>
  <h2>Get status information about a widget</h2>
  <h3>URL</h3>
  <dl>
    <dt>JSON</dt><dd><code><?php echo url_for('api', array('format' => 'json'), true) ?></code></dd>
    <dt>JSONP</dt><dd><code><?php echo url_for('api', array('format' => 'jsonp'), true) ?></code></dd>
  </dl>
  <h3>Parameter</h3>
  <dl>
    <dt>widget_id</dt><dd>The ID of the widget.</dd>
    <dt>callback</dt><dd>Name of the callback function for JSONP. Default is 'callback'.</dd>
  </dl>
  <h3>Response</h3>
  <dl>
    <dt>status</dt><dd><code>ok</code> or <code>error</code></dd>
    <dt>widget_id</dt><dd>ID of the requested widget.</dd>
    <dt>petition_signings</dt><dd>Total number of signing for the entire petition.</dd>
    <dt>petition_signings24</dt><dd>Number of signings within the last 24 hours for the entire petition.</dd>
    <dt>widget_signings</dt><dd>Total number of signing for this widget.</dd>
    <dt>widget_signings24</dt><dd>Number of signings within the last 24 hours for this widget.</dd>
    <dt>timeout</dt><dd>Unix Timestamp until when the response will be cached.</dd>
  </dl>
  <p>Please cache the response at server side if you can.</p>
  <h3>Example</h3>
  <p>Using jQuery</p>
  <code>
  $.ajax({<br/>
  &nbsp;&nbsp;'dataType': 'jsonp',<br/>
  &nbsp;&nbsp;'url': '<?php echo url_for('api', array('format' => 'jsonp'), true) ?>',<br/>
  &nbsp;&nbsp;'data': { 'widget_id': 1 },<br/>
  &nbsp;&nbsp;'cache': true,<br/>
  &nbsp;&nbsp;'success': function(data) { alert(data['petition_signings']); }<br/>
  });<br/>
  </code>