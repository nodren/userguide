<h1>
	<?php echo $doc->modifiers, $doc->class->name ?>
	<?php $parent = $doc->class; ?><br/>
	<?php while ($parent = $parent->getParentClass()): ?>
	<small>extends <?php echo html::anchor('userguide/api/'.$parent->name, $parent->name) ?></small>
	<?php endwhile ?>
</h1>
<?php echo $doc->description ?>

<?php if ($doc->tags) echo View::factory('userguide/api/tags')->set('tags', $doc->tags) ?>

<?php if ($doc->constants): ?>
	<div class="constants">
		<h2 id="constants">Constants</h2>
		<dt>
			<?php foreach ($doc->constants as $name => $value): ?>
				<dt id="constant:<?php echo $name ?>"><?php echo $name ?></dt>
				<dd><?php echo $value ?></dd>
			<?php endforeach ?>
		</dt>
	</div>
<?php endif ?>

<?php if ($properties = $doc->properties()): ?>
<h2 id="properties">Properties</h2>
<div class="properties">
<dt>
<?php foreach ($properties as $prop): ?>
<dt id="property:<?php echo $prop->property->name ?>"><?php echo $prop->modifiers ?> <code><?php echo $prop->type ?></code> <?php echo $prop->property->name ?></dt>
<dd><?php echo $prop->description ?></dd>
<dd><?php echo $prop->value ?></dd>
<?php endforeach ?>
</dt>
</div>
<?php endif ?>

<?php if ($methods = $doc->methods()): ?>
	<h2 id="methods">Methods</h2>
	<div class="methods">
		<?php foreach ($methods as $method): ?>
			<?php echo View::factory('userguide/api/method', array('doc' => $method)) ?>
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
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript=kohana2xdocumentation">comments powered by Disqus.</a></noscript>

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