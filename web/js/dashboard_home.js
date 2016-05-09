String.prototype.parseURL = function() { return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&\?\/.=]+/, function(url) { return url.link(url); });};
String.prototype.parseUsername = function() { return this.replace(/[@]+[A-Za-z0-9-_]+/, function(u) { var username = u.replace("@",""); return u.link("https://twitter.com/"+username); });};
String.prototype.parseHashtag = function() { return this.replace(/[#]+[A-Za-z0-9-_]+/, function(t) { var tag = t.replace("#","%23"); return t.link("https://search.twitter.com/search?q="+tag);});};

$(function() {
	if ($('#twitter').length) {
		return false;
		var target = $('#twitter');
		var twitter_cache = new Array();
		var twitter = function(name) {
			var display = function(data) {
				target.html('');
				var num = 4;
				$.each(data.results, function(i, tw){
					if (--num < 0) return;

					var div = $('<div class="span3"></div>');
					var quote = $('<blockquote></blockquote');
					target.append(div);
					div.append(quote)
					var p = $('<div></div>').html(tw['text'].parseURL().parseUsername().parseHashtag());
					quote.append(p);
					p.prepend($('<img class="pull-left" alt="" />').attr('src', tw['profile_image_url'].replace(/http:\/\/a\d.twimg.com\//, 'https://s3.amazonaws.com/twitter_production/')));
					quote.append('<br/ >');
					var author = $('<small></small>');
					quote.append(author);
					author.append($('<a class="user"></a>').text(tw['from_user']).attr('href', 'https://twitter.com/' + tw['from_user']));
					author.append('<br/ >');
					quote.append($('<span></span>').text(tw['created_at']));
				});
			}

			if (twitter_cache[name] == undefined)
				$.ajax({
					'dataType': 'jsonp',
					'url': 'https://search.twitter.com/search.json',
					'data': {
						'q': policat.tags[name]
					},
					'success': function(data) {
						twitter_cache[name] = data;
						display(data);
					}
				})
			else
				display(twitter_cache[name]);
		}

		twitter('hottest');
	}
});