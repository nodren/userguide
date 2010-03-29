<h1>Userguides</h1>
<p>Please select a module:</p>
<?php if( ! empty($modules)): ?>
	<?php foreach($modules as $url => $name): ?>
		<p>
			<strong><?php echo html::anchor('userguide/guide/'.$url, $name) ?></strong>
		</p>
	<?php endforeach; ?>
<?php else: ?>
	<p class="error">New userguide modules are enabled. Please enable a module in the userguide.php config file and try again.</p>
<?php endif; ?>
