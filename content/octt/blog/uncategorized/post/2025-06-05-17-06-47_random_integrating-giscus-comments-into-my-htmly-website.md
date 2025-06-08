<!--t Integrating Giscus Comments into my HTMLy Website t-->
<!--d After I&#039;ve had honestly a lot of fun yesterday with starting to try HTMLy, an half-unknown but really refreshing PHP CMS, I really think I want d-->
<!--tag Random tag-->

After I've had honestly a lot of fun yesterday with [starting to try HTMLy](https://stuff.octt.eu.org/2025/06/setting-up-an-htmly-website-to-waste-time-and-reduce-girlrotting), an half-unknown but really refreshing PHP CMS, I really think I want to keep this new website I made now, and keep it on HTMLy specifically. With my brand new dreams of fame quickly growing in my heart, then, I figured my next best move would be to add a comment section for each of my posts there, to try and be prepared to leverage readers' engagement.

Out of the box, HTMLy includes support for Disqus and Facebook for comments on posts. Given how I don't like the former, and that the latter is dead (in the sense that Facebook in 2025 is a wasteland, but I'm not even sure the crusty old code used by this CMS is still supported by Facebook after more than a decade), I decided I would try to integrate something more modern, which I've already tested and liked: [Giscus](https://github.com/giscus/giscus).

## What is Giscus?

Giscus is a really clever commenting system, ideal for static sites and small blogs: supported by the open-source community, it simply sits on top of GitHub Discussions, requiring no hosting on our part, being completely free, with a clean UX and no malware... the bare minimum, I would say. Its biggest downside — using a platform owned by big tech Microsoft behind the scenes — is also a great plus: users can just login with a GitHub account, which virtually all people in the tech sphere have nowadays, to post comments or reaction emojis.

![Comment dialog screenshot](https://stuff.octt.eu.org/content/images/20250605171302-Screenshot%202025-06-05%20at%2017-12-21%20Integrating%20Giscus%20Comments%20into%20my%20HTMLy%20Website%20-%20stuffoctt.png)

## Integrating Giscus in HTMLy

But enough yapping about why I did what I did, I don't have any obligation to explain myself. I checked out the ["Commentics Integration" page on HTMLy Docs](https://docs.htmly.com/tips-trick/commentics-integration), which showcases how to add support for a different external commenting system, but contains useful information for constructing integrations in general; namely, where to put the extra code.

Turns out that integrating something like Giscus, which requires no further software on our server, is even easier than using that Commentics things! It amounted to visiting [the Giscus website](https://giscus.app/), completing the setup and filling the form with data as instructed, and copying the embeddable `<script>` tag. Then, what was left was a matter of pasting this code into my HTMLy template code.

To keep things clean, I first went to Config > Custom in the HTMLy admin panel (`/admin/config/custom`), and created a new property, which I named `GiscusConfiguration`. I then pasted all the `data-` HTML attributes from the `<script>` code (and only those; not the entire code, and not all the attributes), and clicked Save. If, in the future, Giscus comments have to be disabled, that can be done simply by deleting the value.

The only thing left at this point was to edit the `post.html.php` file for my active theme (inside `/themes/gridzone/` in my case), to be tweaked as follows. Obviously, other themes will have slightly different sections of the code.

```php
<!-- Just below the first line,
added this to read the Giscus configuration, if present -->
<?php $giscus_config = config('GiscusConfiguration'); ?>
```

```php
<!-- Inside <header ...>, inside <div class="entry-meta">,
just before the end of the if block,
copied this to show the Comments button -->
<?php } elseif ($giscus_config) { ?>
<span class="entry-comments"><i class="far fa-comment"></i> <a href="<?php echo $p->url ?>#comments"> <?php echo i18n('Comments');?></a></span>
```

```php
<!-- Inside <div id="comments" ...>, after the other if blocks,
added this to show the actual comments box -->
<?php if ($giscus_config): ?>
<script src="https://giscus.app/client.js" <?php echo $giscus_config;?> crossorigin="anonymous" async></script>
<?php endif; ?>
```

And... it didn't work. Luckily, it was just a matter of using the Clear cache tool from the admin panel (`/admin/clear-cache`), and it did. A nice comment section, powered by the amazing Giscus, now shows up at the bottom of the page for any of my posts... including this very one. Why not try leaving a comment or a like, then, to test how this easy integration actually works in practice? 😺

<!--
In general, it amounted to the following: 
1. Creating a public GitHub repository for my website (which, unrelated, for now is empty, but I will probably set it up to keep a backup of my posts)
2. Enabling Discussions on it
3. Adding the Giscus app to the repository
3. Going to [the Giscus website](https://giscus.app/) to fill the form with the required data
-->