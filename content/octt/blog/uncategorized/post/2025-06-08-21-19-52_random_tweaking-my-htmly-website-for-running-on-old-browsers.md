<!--t Tweaking my HTMLy Website for Running on Old Browsers t-->
<!--d While the field has its challenges, the web is probably, among all the others that are common today, the software platform where d-->
<!--tag Random tag-->

While the field has its challenges, the web is probably, among all the others that are common today, the software platform where backwards-compatibility is the easiest, if one cares about it. Easy, however, doesn't imply simple, and that's why ensuring compatibility with older browsers is a different problem to tackle for each different website.

My challenge this time, then, was to ensure that the very blog I'm writing this on, which is powered by HTMLy as things currently stand, is not only accessibile on older web browsers, but also properly navigable. In general this is not really a problem with pure websites (meaning: not webapps), because the data and communication models at play are essentially the same they were 30 years ago, but some special care is still needed between server configuration and frontend code.

## Allowing Plain HTTP

To ensure the site is at least reachable on any web browser, both the ones known to humankind and those coming from outer space, there's only one requirement, but it's non-negotiable: allow plain, unencrypted HTTP on the server.
Given that SSL/TLS hasn't been common for a long time on the web, that different versions of the protocol exist, and that root certificates expire throwing security errors if not kept up to date (which they aren't, in case of old browsers on old systems), there's only one sure way to make a connection always work, and that's to make both HTTP and HTTPS available for clients to connect to.

Luckily this is super-easy: the specifics may vary depending on your web server, but the essence is that you listen on both the HTTP (80) and HTTPS (443) port. All modern browsers will usually always attempt to connect with `https://`, while for older systems you can just specify the `http://` and have no trouble connecting. For example, below is how to do it with nginx:

```
server {
	listen 80;      # HTTP
	listen 443 ssl; # HTTPS

	# ... rest of config ...
}
```

## Choosing a Good Theme

Choosing a good theme — in this case meaning a theme that offers good compatibility and is responsive among many browsers, including very old ones — is important in itself. Maybe not as important as the other points, since pages in HTMLy are still always rendered to HTML on the server side, and so at least the page elements themselves should always be rendered even if the layout ends up being broken; but the final step is somewhat dependent on this, so this should go first.

Check out [the themes HTMLy has to offer](https://www.htmly.com/theme), apply some you like and test it on various devices with different browsers, to get an idea of how well each one will or won't work. I already chose the Gridzone theme for my site and like it quite a lot, so I have no interest to change it, but from my limited testing it seems to display like a charm even on browsers from a decade ago... which is not perfect, but it's enough for me; I really doubt someone is going read my blog using Internet Explorer 6 or something like that.

## Rewriting Absolute URLs

Given that HTTPS is the standard nowadays — search engines want it, users expect it, and [the European Union indirectly demands it](https://www.certauri.com/ensuring-gdpr-compliance-with-ssl-certificate-a-guide/#Understanding_GDPR_Key_Requirements_and_Implications) (if you're a business) — your site's base URL (as specified in Config > General of HTMLy's admin panel, `/admin/config`) should always be the HTTPS one, even if you allow HTTP. This will ensure that key metadata fields in the webpages, like canonical URLs for posts and social media preview images, stay what they should.

However, HTMLy doesn't seem to include any clever code for generating path-relative or domain-relative URLs when appropriate and/or convenient, like for internal links to other pages or for any embedded resources. This means that, to ensure that images, stylesheets and scripts actually load even when HTTPS is not accessible, as well as that clicking on hyperlinks brings you to a working page and not an error one, you need to configure your server to rewrite the HTML code as appropriate.

The following is what I personally did, again with nginx, to do exactly that. Apart from generally replacing all absolute URLs of my site from `src` attributes (which is used by images, scripts, etc.), and those referring to the `/themes/` and `/system/` HTMLy folders from `href` attributes (practically used just by stylesheets), I had to use 4 different formats for replacing anchor links, because my Gridzone theme puts different classes on different links. I couldn't outright rewrite all `href` attributes in general, otherwise I would have also modified all `<link>` elements, which includes many metadata tags.

```nginx
server {
	# ... rest of config ...

	location ~ .php$ {
		# ... rest of config ...
		sub_filter ' src="SITE_URL/' ' src="/';
		sub_filter ' href="SITE_URL/themes/' ' href="/themes/';
		sub_filter ' href="SITE_URL/system/' ' href="/system/';
		sub_filter '<a href="SITE_URL/' '<a href="/';
		sub_filter '<a class="nav-link" href="SITE_URL/' '<a class="nav-link" href="/';
		sub_filter '<a class="entry-thumbnail" href="SITE_URL/' '<a class="entry-thumbnail" href="/';
		sub_filter '<a class="tag-cloud-link" href="SITE_URL/' '<a class="tag-cloud-link" href="/';
		sub_filter_once off;
		# Note: replace SITE_URL here with your actual `site.url` value from your HTMLy configuration.
	}
}
```

## The Final Results

To finish, then, here's the stuffoctt website, running on HTMLy with the tweaks described above, shown working on the amazing and wonderful, even if terrible and crummy in every way imaginable, TIM Easy 4G smartphone (default Android 4.4 KitKat browser) just for an example! (As well as IE6, just to prove that it can actually be somewhat used to read this blog even if no one will ever do it, lol.) And, please, do let me know how my website or even your own one run on some bespoke piece of hardware you happen to have laying around...

![TIMfonino photo](https://stuff.octt.eu.org/content/images/20250608105615-IMG_20250608_010816.jpg)

![TIMfonino screenshot](https://stuff.octt.eu.org/content/images/20250608154445-screenshot-1749337550160.png)

![IE6 screenshot](https://stuff.octt.eu.org/content/images/20250609013715-download34567895345.png)