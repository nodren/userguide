<div class="method">
	<h3 id="<?php echo $method->name ?>">
		<?php
			$params = array();

			foreach ($method->params as $param)
			{
				$param_string = '';

				if ($param->byref)
				{
					$param_string .= '<small>&</small>';
				}

				if ( ! empty($param->type))
				{
					$param_string .= ' <small>'.$param->type.'</small> ';
				}

				if ( ! empty($param->description))
				{
					$param_string .= '<span class="param" title="'.$param->description.'">$'.$param->name.'</span>';
				}
				else
				{
					$param_string .= '$'.$param->name;
				}

				if ( ! empty($param->default))
				{
					$param_string .= '<small> = '.$param->default.'</small>';
				}

				$params[] = $param_string;
			}
		?>
		<?php echo $method->modifiers, html::anchor('#'.$method->name, $method->name), '(', implode(', ', $params) , ')' ?>
	</h3>
	<div class="description">
		<?php echo $method->description ?>
	</div>
	<?php if ($method->params): ?>
		<h5>Parameters:</h5>
		<ul class="parameters">
			<?php foreach ($method->params as $param): ?>
				<li><code><?php echo '$'.$param->name ?></code> - <?php echo $param->description ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	<?php if ($method->return): ?>
		<h5>Returns:</h5>
		<ul class="returns">
			<?php foreach ($method->return as $set): list($type, $text) = $set; ?>
				<li><code><?php echo html::chars($type) ?></code> <?php echo HTML::chars($text) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	<?php if ($method->tags) echo View::factory('userguide/api/tags')->set('tags', $method->tags) ?>
	<?php if ($method->source): ?>
		<div class="method-source">
			<h5>Source Code:</h5>
			<pre><code><?php echo html::chars($method->source) ?></code></pre>
		</div>
	<?php endif ?>
</div>