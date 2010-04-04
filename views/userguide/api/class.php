<h1>
	<?php echo $class->modifiers, $class->name ?>
	<?php foreach ($class->parents as $parent): ?>
		<small>extends <?php echo html::anchor('userguide/api/'.$parent->package.'/'.$parent->name, $parent->name) ?></small>
	<?php endforeach; ?>
</h1>
<?php echo $class->description ?>

<?php if ($class->tags) echo View::factory('userguide/api/tags')->set('tags', $class->tags) ?>

<?php if ($class->constants): ?>
	<h2 id="constants">Constants</h2>
	<div class="constants">
		<?php foreach ($class->constants as $name => $value): ?>
			<div class="constant">
				<h3 id="constant:<?php echo $name ?>"><?php echo $name ?></h3>
				<h5>Value:</h5>
				<?php echo $value ?>
			</div>
		<?php endforeach ?>
	</div>
<?php endif ?>

<?php if ($class->properties): ?>
	<h2 id="properties">Properties</h2>
	<div class="properties">
		<?php foreach ($class->properties as $prop): ?>
			<div class="property">
				<h3 id="property:<?php echo $prop->property->name ?>">
					<?php echo $prop->modifiers ?><?php echo $prop->property->name ?>
				</h3>
				<?php echo $prop->description ?>
				<h5>Value:</h5>
				<?php echo $prop->value ?>
			</div>
		<?php endforeach ?>
	</div>
<?php endif ?>

<?php if ($class->methods): ?>
	<h2 id="methods">Methods</h2>
	<div class="methods">
		<?php foreach ($class->methods as $method): ?>
			<?php echo View::factory('userguide/api/method', array('method' => $method)) ?>
		<?php endforeach ?>
	</div>
<?php endif ?>

<h2>User Comments</h2>
<div id="disqus_thread"></div>
<script type="text/javascript">
  (function() {
   var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
	var disqus_developer = 1;
	var disqus_url = '<?php echo url::current(); ?>';
   dsq.src = 'http://kohana2xdocumentation.disqus.com/embed.js';
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();
</script>
<noscript><p>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript=kohana2xdocumentation">comments powered by Disqus.</a></p></noscript>

<script type="text/javascript">
//<![CDATA[
(function() {
	var links = document.getElementsByTagName('a');
	var query = '?';
	for(var i = 0; i < links.length; i++) {
	if(links[i].href.indexOf('#disqus_thread') >= 0) {
		query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
	}
	}
	document.write('<script charset="utf-8" type="text/javascript" src="http://disqus.com/forums/kohana2xdocumentation/get_num_replies.js' + query + '"></' + 'script>');
})();
//]]>
</script>