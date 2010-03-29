<h3>API Reference</h3>
<ul>
	<li><?php echo html::anchor('userguide/api', 'API Reference') ?></li>
</ul>
<h3>Modules</h3>
<?php if( ! empty($modules)): ?>

	<ul>
	<?php foreach($modules as $url => $name): ?>

		<li><?php echo html::anchor('userguide/guide/'.$url, $name) ?></li>

	<?php endforeach; ?>
	</ul>

<?php else: ?>

	<p class="error">I couldn't find any modules with userguide pages.</p>

<?php endif; ?>