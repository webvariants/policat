<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
	<style type="text/css">
	  body { width: 900px; }
	  div#sidebar { width: 200px; margin-right: 10px; background-color: yellow; float: left; margin-top: 20px }
	  div#content { width: 610px; float: left; margin-top: 20px; }
	</style>
  </head>
  <body>
	<div id="sidebar">
	Sidebar
	<ul>
	  <li>Foobar 1</li>
	  <li>Foobar 2</li>
	  <li>Foobar 3</li>
	  <li>Foobar 4</li>
	</ul>
	</div>
	<div id="content">
    <?php foreach (split(',', $id) as $id): ?>
      <script type="text/javascript" src="/api_js_widget/<?php echo $id ?>"></script>
    <?php endforeach ?>
	<h1>Lorem</h1>
	<p>
	Praesent eu consequat lacus. Integer rutrum pharetra purus, et convallis purus ultrices non. Aenean lorem sapien, rhoncus a laoreet molestie, congue vel justo. Maecenas id tortor purus! Suspendisse arcu odio, vehicula nec porttitor eget, posuere eget ligula. In sit amet tincidunt nunc. Etiam dui est; molestie a auctor vel, viverra sed augue. Maecenas venenatis dignissim odio id hendrerit? Nam in elit eu mauris dapibus facilisis sit amet et elit. In hac habitasse platea dictumst. Aenean quis urna est. Curabitur nec velit mi, vitae feugiat augue? Suspendisse pretium, metus dignissim luctus imperdiet, libero nibh porta dolor, non commodo purus est vitae nisl. Praesent imperdiet ligula et tortor fringilla eget pulvinar metus hendrerit. Integer quis nunc nibh, et condimentum enim! Ut vel urna id lectus sodales suscipit!
	</p>
<!--	<object width="480" height="385"><param name="wmode" value="transparent"></param><param name="movie" value="http://www.youtube.com/v/617ANIA5Rqs&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/617ANIA5Rqs&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>-->
	<p>
	In vulputate gravida libero; ut adipiscing dolor euismod vitae. Maecenas sagittis risus sed nisl adipiscing interdum. Nullam scelerisque augue sit amet sapien iaculis bibendum. Donec posuere neque vel sapien vulputate ullamcorper dapibus diam consequat. Proin auctor semper dui ultricies blandit. Aenean velit orci, pellentesque in fringilla eget, faucibus a quam. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas bibendum pretium ultrices. Etiam non imperdiet felis. Aenean eget elit id lacus scelerisque pharetra! Mauris elementum sodales tincidunt.
	</p>
	<p>
	In blandit est id augue facilisis a fringilla nulla posuere. Phasellus libero justo, varius et bibendum quis, pellentesque eu metus. Maecenas auctor mauris at ante suscipit et luctus metus luctus. Quisque fermentum scelerisque enim, sed malesuada ante lobortis et. Nunc lobortis vehicula tempus. Integer faucibus; enim eget sodales varius, metus ante malesuada quam, nec lacinia tortor diam vel lacus. Aenean commodo lacinia dui, in pharetra lectus tristique non. Sed congue cursus lectus in ornare! Aenean porttitor commodo nibh, id consequat ipsum interdum a. Praesent nec rutrum lorem. Cras placerat iaculis neque, sed mollis nisl cursus vel? Aliquam venenatis sapien vel leo imperdiet dignissim? In nec mollis leo. Duis vitae lectus ut nisi iaculis faucibus eget nec sem. Suspendisse potenti. Suspendisse a dapibus felis.
	</p>
	</div>
	<div class="clear"></div>
 </body>
</html>
