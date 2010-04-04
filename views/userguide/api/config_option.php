<div class="method">
	<h3 id="<?php echo $option->name ?>"><?php echo $option->name ?> = <code><?php echo $option->value ?></code></h3>
	<div class="tabs">
		<ul>
			<li><a href="#<?php echo $option->name ?>-Overview">Overview</a></li>
			<li><a href="#<?php echo $option->name ?>-Comments" class="comments">Comments</a></li>
		</ul>
		<div id="<?php echo $option->name ?>-Overview">
			<div class="description">
				<?php if (empty($option->description)): ?>
					<p>This config option is currently not documented. Check the source view for more information.</p>
				<?php else: ?>
					<?php echo $option->description ?>
				<?php endif; ?>
				<?php if ($option->default): ?>
					<p><strong>Default value:</strong> <code><?php echo $option->default ?></code></p>
				<?php endif; ?>
			</div>
		</div>
		<div id="<?php echo $option->name ?>-Comments">
			<p>There are no comments for this method.</p>
		</div>
	</div>
	<?php if ($option->tags): ?>
		<div class="tags">
			<?php foreach ($option->tags as $name => $set): ?>
				<h5><?php echo ucfirst($name) ?></h5>
				<ul>
				<?php foreach ($set as $tag): ?>
					<li><?php echo text::auto_link_urls($tag); ?></li>
				<?php endforeach ?>
				</ul>
			<?php endforeach ?>
		</div>
	<?php endif; ?>
	<a class="top" href="#top">(top)</a>
</div>
