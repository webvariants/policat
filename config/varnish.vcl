# integrate this in your varnish (2) setup

sub vcl_recv {
	if (req.url ~ "^((/favicon.ico)|(/images/)|(/images_static/)|(/js/)|(/sfDoctrinePlugin)|(/sfFormExtraPlugin)|(/css/)|(/widget_page)|(/api/js/widget)|(/sign/)|(/sign_hp/))") {
		unset req.http.Cookie;
	}
}
