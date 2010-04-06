<div id="class-menu">
	<h3 id="toc"><?php echo ucfirst($config->name).' Config' ?></h3>
	<h4>Options</h4>
	<ul>
		<?php if ($config->options): ?>
			<?php foreach ($config->options as $option): ?>
				<li><a href="#<?php echo $option->name ?>"><?php echo $option->name ?></a></li>
			<?php endforeach ?>
		<?php else: ?>
			<li><em>None</em></li>
		<?php endif ?>
	</ul>
</div>
