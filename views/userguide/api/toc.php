<h1><?php echo __('Available Classes'); $package = 'kohana'; ?></h1>

<div id="toc">
	<?php foreach ($toc['kohana'] as $group => $list): $total = count($list); $per_row = ceil($total / 3) ?>
		<?php if (count($toc) > 0): $i = $c = 0; ?>
			<div class="toc" style="overflow: auto">
				<h5><?php echo ucfirst($group) ?></h5>
				<?php foreach (array_keys($list) as $name): $c++; $i++; ?>
					<?php if ($c === 1): ?>
						<ul>
					<?php endif; ?>
						<li><?php echo html::anchor('userguide/api/'.$package.'/'.$name, $name) ?></li>
					<?php if ($c == $per_row OR $i == $total): $c = 0; ?>
						</ul>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<p>No Files Found</p>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
