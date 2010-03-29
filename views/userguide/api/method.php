<div class="method">
	<h3 id="<?php echo $doc->method->name ?>">
		<?php echo $doc->modifiers, html::anchor('#'.$doc->method->name, $doc->method->name),	' ( ',	( $doc->params ) ? $doc->params_short() : '' ,	' ) ' ?>
	</h3>
	<div class="description">
		<?php echo $doc->description ?>
	</div>
	<?php if ($doc->params): ?>
		<h5>Parameters:</h5>
		<ul class="parameters">
			<?php foreach ($doc->params as $param): ?>
				<li><code><?php echo '$'.$param->name ?></code> - <?php echo $param->description ?>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	<?php if ($doc->return): ?>
		<h5>Returns:</h5>
		<ul class="returns">
			<?php foreach ($doc->return as $set): list($type, $text) = $set; ?>
				<li><code><?php echo html::chars($type) ?></code> <?php echo HTML::chars($text) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	<?php if ($doc->tags) echo View::factory('userguide/api/tags')->set('tags', $doc->tags) ?>
	<?php if ($doc->source): ?>
		<div class="method-source">
			<h5>Source Code:</h5>
			<pre><code><?php echo html::chars($doc->source) ?></code></pre>
		</div>
	<?php endif ?>
</div>
