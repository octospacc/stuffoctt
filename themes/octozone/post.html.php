<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php $giscus_config = config('GiscusConfiguration'); ?>
<article class="post type-post status-publish format-standard has-post-thumbnail hentry">
	<div class="post-wrapper group">
		<div class="entry-media">

		<div class="post-format">

			<?php if (!empty($p->image)):?>
			<div class="image-container">
				<img src="<?php echo $p->image;?>">
			</div>
			<?php endif;?>

			<?php if (!empty($p->video)):?>
			<div class="video-container">
				 <iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/<?php echo get_video_id($p->video); ?>" frameborder="0" allowfullscreen></iframe>
			</div>
			<?php endif;?>

			<?php if (!empty($p->audio)):?>
			<div class="audio-container">
				 <iframe width="100%" height="200px" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
			</div>
			<?php endif;?>

			<?php if (!empty($p->quote)):?>
			<div class="quote-container entry">
				 <blockquote style="margin:40px 40px 0 40px;max-width:100%;"><?php echo $p->quote;?></blockquote>
			</div>
			<?php endif;?>

		</div>

		</div>
		<header class="entry-header group">
			<?php if (empty($p->link)) {?>
			<h1 class="entry-title"><?php echo $p->title;?></h1>
			<?php } else { ?>
			<h1 class="entry-title"><a href="<?php echo $p->link;?>" target="_blank" rel="bookmark"><?php echo $p->title;?> <i class="fas fa-external-link-alt"></i></a></h1>
			<?php } ?>
			<div class="entry-meta">
				<span class="entry-date"><i class="far fa-calendar"></i><?php echo format_date($p->date);?></span>
				<?php if (disqus_count()) { ?>
				<span class="entry-comments"><i class="far fa-comment"></i> <a href="<?php echo $p->url ?>#disqus_thread"> <?php echo i18n('Comments');?></a></span>
				<?php } elseif (facebook()) { ?>
				<span class="entry-comments"><i class="far fa-comment"></i> <a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> <?php echo i18n('Comments');?></span></a></span>
				<?php } elseif ($giscus_config) { ?>
				<span class="entry-comments"><i class="far fa-comment"></i> <a href="<?php echo $p->url ?>#comments"> <?php echo i18n('Comments');?></a></span>
				<?php } ?>
				<span class="entry-author"><i class="far fa-user"></i><a href="<?php echo $p->authorUrl;?>" title="<?php echo i18n('Posts_by') . ' ' . $p->authorName;?>" rel="author"><?php echo $p->authorName;?></a></span>
				<span class="entry-category"><?php echo $p->category;?></span>
				<?php if (authorized($p)) { echo '<span class="edit-post"><a href="'. $p->url .'/edit?destination=post"><i class="far fa-edit"></i>' . i18n('Edit') . '</a></span>'; } ?>
			</div>
		</header>

		<div class="entry-content">
			<div class="entry themeform">
				<?php echo $p->body;?>
				<div class="clear"></div>
			</div><!--/.entry-->
		</div>


		<div class="entry-footer group">
				<div class="post-tags"><span><?php echo i18n('Tags');?>:</span> <?php echo $p->tag;?></div>
			<div class="clear"></div>

			<div class="author-bio">
				<div class="bio-avatar"><img alt="<?php echo $author->name;?>" src="<?php echo $author->avatar;?>"></div>
				<p class="bio-name"><?php echo $author->name;?></p>
				<div class="bio-desc"><?php echo $author->about;?></div>
				<div class="clear"></div>
			</div>

			<div class="sharrre-container sharrre-header group">
				<span>Share</span>
				<div id="twitter" class="sharrre"><a class="box group" href="https://twitter.com/share?url=<?php echo $p->url ?>&text=<?php echo $p->title ?>" target="_blank" rel="nofollow"><div class="count"><i class="fas fa-plus"></i></div><div class="share"><i class="fab fa-twitter"></i></div></a></div>
				<div id="facebook" class="sharrre"><a class="box group" href="https://www.facebook.com/sharer.php?u=<?php echo $p->url ?>&t=<?php echo $p->title ?>" target="_blank" rel="nofollow"><div class="count"><i class="fas fa-plus"></i></div><div class="share"><i class="fab fa-facebook-square"></i></div></a></div>
			</div><!--/.sharrre-container-->

			<ul class="post-nav group">
			<?php if (!empty($next)): ?>
				<li class="previous"><a href="<?php echo($next['url']); ?>" rel="next"><i class="fas fa-chevron-left"></i><strong><?php echo i18n('Next');?></strong> <span><?php echo($next['title']); ?></span></a></li>
			<?php endif;?>
			<?php if (!empty($prev)): ?>
				<li class="next"><a href="<?php echo($prev['url']); ?>" rel="prev"><i class="fas fa-chevron-right"></i><strong><?php echo i18n('Prev');?></strong> <span><?php echo($prev['title']); ?></span></a></li>
			<?php endif;?>
			</ul>

			<?php if (disqus()): ?>
				<?php echo disqus($p->title, $p->url) ?>
			<?php endif; ?>
			<?php if (disqus_count()): ?>
				<?php echo disqus_count() ?>
			<?php endif; ?>

			<?php if (facebook() || disqus() || $giscus_config): ?>
			<div id="comments" class="themeform">
				<h3 class="heading"><?php echo i18n('Comments');?></h3>
				<?php if (facebook()): ?>
					<div class="fb-comments" data-href="<?php echo $p->url ?>" data-numposts="<?php echo config('fb.num') ?>" data-colorscheme="<?php echo config('fb.color') ?>"></div>
				<?php endif; ?>
				<?php if (disqus()): ?>
					<div id="disqus_thread"></div>
				<?php endif; ?>
				<?php if ($giscus_config): ?>
					<script src="https://giscus.app/client.js" <?php echo $giscus_config;?> crossorigin="anonymous" async></script>
				<?php endif; ?>
			</div><!--/#comments-->
			<?php endif; ?>
			
		</div>

	</div>

</article>

<?php $related = get_related($p->related, true, config('related.count'));?>
<?php if (!empty($related)):?>
<div class="masonry" id="masonry">
<?php foreach ($related as $r):?>
<?php $img = get_image($r->body);?>
	<article class="masonry-item post hentry">
		<div class="masonry-inner">
			<?php if (!empty($r->image) || !empty($img) || empty($vidTmb)) :?>
			<div class="entry-top">
				<a class="entry-thumbnail" href="<?php echo $r->url;?>">
					<?php if (!empty($r->image)) {?>
					<img src="<?php echo $r->image;?>" width="100%">
					<?php } elseif (!empty($r->video)) {?>
					<img src="//img.youtube.com/vi/<?php echo get_video_id($r->video);?>/sddefault.jpg" width="100%">
					<span class="thumb-icon"><i class="fas fa-play"></i></span>
					<?php } elseif (!empty($r->audio)) {?>
					<img src="<?php echo theme_path();?>img/soundcloud.jpg" width="100%">
					<span class="thumb-icon"><i class="fas fa-volume-up"></i></span>
					<?php } elseif (!empty($img)) {?>
					<img src="<?php echo $img;?>" width="100%">
					<?php } ?>
				</a>

				<div class="entry-category"><?php echo $r->category;?></div>
			</div>
			<?php endif;?>

			<?php if (!empty($r->link)) {?>
			<h2 class="entry-title">
				<a href="<?php echo $r->link;?>" target="_blank" rel="bookmark"><?php echo $r->title;?> <i class="fas fa-external-link-alt"></i></a>
			</h2><!--/.entry-title-->
			<?php } elseif (!empty($r->quote)) {?>
			<h2 class="entry-title">
				<a href="<?php echo $r->url;?>" rel="bookmark"><blockquote><i class="fas fa-quote-left"></i> <?php echo $r->quote;?> <i class="fas fa-quote-right"></i></blockquote></a>
			</h2><!--/.entry-title-->
			<?php } else {?>
			<h2 class="entry-title">
				<a href="<?php echo $r->url;?>" rel="bookmark"><?php echo $r->title;?></a>
			</h2><!--/.entry-title-->
			<?php } ?>

			<?php if (empty($r->image) && empty($img) && empty($r->video) && empty($r->audio) && empty($r->quote)) :?>
				<div class="entry-meta"><?php echo $r->description;?></div>
			<?php endif;?>
			<ul class="entry-meta group">
				<li class="entry-date"><i class="far fa-calendar"></i> <?php echo format_date($r->date);?></li>
				<?php if (disqus_count()) { ?>
				<li class="entry-comments"><i class="far fa-comment"></i> <a href="<?php echo $r->url ?>#disqus_thread"> <?php echo i18n('Comments');?></a></li>
				<?php } elseif (facebook()) { ?>
				<li class="entry-comments"><i class="far fa-comment"></i> <a href="<?php echo $r->url ?>#comments"><span><fb:comments-count href=<?php echo $r->url ?>></fb:comments-count> <?php echo i18n('Comments');?></span></a></li>
				<?php } ?>
				<?php if (authorized($r)) { echo '<li class="edit-post"><a href="'. $r->url .'/edit?destination=post"><i class="far fa-edit"></i> ' . i18n('Edit') . '</a></li>'; } ?>
			</ul>
		</div>
	</article><!--/.post-->
	<?php endforeach;?>
</div>
<?php endif;?>
