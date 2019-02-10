<?php $sf_response->setTitle('policat.org - API') ?>

<h2>Get data about an action</h2>
<p>To retrieve statistical data about an action, you can use this simple, REST-like API. At the
moment, it only supports a very limited range of resources.</p>
<p>Data is available both as raw JSON and as JSONP. To use JSONP, give your callback via the
<code>callback</code> query string parameter (e.g. <code>...stuff?callback=mycallback</code>).</p>

<h3>URL</h3>
<p>To get the number of signings per action, perform <code>GET</code> request to the following
URL: <code><?php echo url_for('api_v2', array('action_id' => 42), true) ?></code> (replace the
<code>42</code> with your action ID).</p>

<p>Request the Action ID and your individual token from the action coordinator.</p>

<h3>Parameter</h3>
<p>By default, you will get a summary over the entire lifespan of the action. <!--including all
<strong>used</strong> widgets (i.e., widgets that did not lead to a signing will not show up in
the API response). You can however filter the result using the following parameters, all of which
are optional and can be combined in any fashion you like:--></p>

<dl>
  <dt>widgets (optional)</dt>
  <dd>
    A comma separated list of widget IDs that you want to get data from, e.g.
    <code>?widgets=23,420,597</code> &ndash; unknown or invalid IDs will be silently ignored.
    Set <code>?widgets=true</code> to return data of all widgets.
  </dd>

  <dt>from_via_policat / to_via_policat (optional)</dt>
  <dd>
    UNIX timestamps for restricting the date range of which data is aggregated. You can give both,
    one or none. Note that bad dates are not automatically corrected (i.e. if you ask for data of
    1832, you will not get any data back). For example <code>?from_via_policat=1308908966&to=widgets_1308909048</code>.
  </dd>

  <dt>segregate (optional)</dt>
  <dd>If you set this parameter to "countries" the number of signings will be segregated by countries where possible.
      (You will get an object with countries as keys and number of signings as values)
  </dd>
</dl>

<p>
  To add signings collected elsewhere to the counter of an action, request a token from the action admin
  (admins can generate tokens on the Action-Tab "Counter & API"). Update the action counter on PoliCAT with
  your data, using the parameters below. You must submit your counts country-by-country, using 2-digit ISO codes.
  <br />Note:
  (1) Submit your total count up to date (no increment). Previous submissions will be overwritten; this
  allows you to correct erroneous data previously submitted.<br />
  (2) The response data will include your submitted data. To add the PoliCAT counts to your own counter,
  subtract your submitted count from the "signings_total" PoliCAT response, before added it to your own counter.
</p>
<dl>
  <dt>token (optional, POST)</dt>
  <dd>
      Your authentication token given by action admin. Use only server-side!<br />
      You must do a <code>POST</code>-request instead of a GET request.
  </dd>

  <dt>signings[ISO] (optional, POST)</dt>
  <dd>
      Use POST-parameter of the kind signings[FR], signings[DE] to submit the number of signings of your
      organisation. Authentication with a token is required.
  </dd>
</dl>

<h3>Response</h3>
<dl>
  <dt>action_id</dt><dd>ID of the action that has been requested.</dd>
  <dt>signings_via_policat</dt><dd>The number of signings per country collected by policat only.</dd>
  <dt>policat_first_signing (optional)</dt><dd>UNIX timestamp of the first signing.</dd>
  <dt>policat_last_signing (optional)</dt><dd>UNIX timestamp of the last signing.</dd>
  <dt>signings_via_api</dt><dd>The number of signings for the entire action collected by other organisations (API) only. (segregateable by countries)</dd>
  <dt>signings_total (optional)</dt><dd>The number of signings for the entire action. (signings_via_policat + signings_via_api, segregateable by countries)</dd>
  <dt>widgets (optional)</dt><dd>The number of signings of each widget requested. (segregateable by countries)</dd>
  <dt>widget_first_signing (optional)</dt><dd>UNIX timestamp of the first signing of each widget requested.</dd>
  <dt>widget_last_signing (optional)</dt><dd>UNIX timestamp of the last signing of each widget requested.</dd>
  <dt>widgets_first_signing (optional)</dt><dd>UNIX timestamp of the first signing of all widget requested.</dd>
  <dt>widgets_last_signing (optional)</dt><dd>UNIX timestamp of the last signing of all widget requested.</dd>
  <dt>manual_counter_tweak</dt><dd>Gobal offset by policat, added in signings_total.</dd>
</dl>

<p>If you can, please cache the data, because aggregating can be expensive.</p>

<h3>Example</h3>

<h4>Using jQuery to fetch data</h4>

<pre><code>$.ajax({
  dataType: 'jsonp',
  url: '<?php echo url_for('api_v2', array('action_id' => 42), true) ?>',
  data: {
    widgets: 'true',
    from_via_policat: 1308908966
  },
  cache: true,
  success: function(data) { alert(data); }
});</code></pre>

<h4>Using curl to update and fetch data</h4>

<p>URL: <?php echo url_for('api_v2', array('action_id' => 42), true) ?></p>
<p>POST parameters</p>
<ul>
    <li>token=#YOUR-TOKEN#</li>
    <li>signings[FR]=100</li>
    <li>signings[DE]=200</li>
</ul>
<p>Query parameters</p>
<ul>
    <li>widgets=true</li>
    <li>segregate=countries</li>
</ul>

<pre><code>curl --data "token=#YOUR-TOKEN#&signings[FR]=100&signings[DE]=200" "<?php echo url_for('api_v2', array('action_id' => 42), true) ?>?widgets=true&amp;segregate=countries"</code></pre>

<p>Response</p>
<pre><code>
{
  "action_id": 42,
  "manual_counter_tweak": 0,
  "widgets": {
    "61": {
      "DE": 123
    },
    "62": {
      "DE": 123,
      "FR": 345
    },
    "63": {
      "FR": 432
    }
  },
  "widget_first_signing": {
    "61": 1411647136,
    "62": 1411647136,
    "63": 1411647136
  },
  "widget_last_signing": {
    "61": 1411649732,
    "62": 1412175028,
    "63": 1412175028
  },
  "signings_via_policat": {
    "DE": 246,
    "FR": 777
  },
  "policat_first_signing": 1411647136,
  "policat_last_signing": 1412175028,
  "signings_via_api": {
    "DE": 876,
    "FR": 765
  },
  "signings_total": {
    "DE": 1122,
    "FR": 1542
  }
}
</code></pre>
