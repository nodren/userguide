<div id="class-menu">
	<h3 id="toc"><?php echo $class->name ?></h3>

	<h4>Constants</h4>
	<ul>
	<?php if ($class->constants): ?>
	<?php foreach ($class->constants as $name => $value): ?>
		<li><a href="#constant:<?php echo $name ?>"><?php echo $name ?></a></li>
	<?php endforeach ?>
	<?php else: ?>
		<li><em>None</em></li>
	<?php endif ?>
	</ul>


	<h4>Properties</h4>
	<ul>
	<?php if ($class->properties): ?>
	<?php foreach ($class->properties as $prop): ?>
		<li><a href="#property:<?php echo $prop->property->name ?>">$<?php echo $prop->property->name ?></a></li>
	<?php endforeach ?>
	<?php else: ?>
		<li><em>None</em></li>
	<?php endif ?>
	</ul>

	<h4>Methods</h4>
	<ul>
	<?php if ($class->methods): ?>
	<?php foreach ($class->methods as $method): ?>
		<li><a href="#<?php echo $method->name ?>"><?php echo $method->name ?>()</a></li>
	<?php endforeach ?>
	<?php else: ?>
		<li><em>None</em></li>
	<?php endif ?>
	</ul>
</div>

