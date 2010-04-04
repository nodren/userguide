<h1><?php echo ucfirst($config->name) ?> Config</h1>
<?php echo $config->description ?>

<?php if ($options = $config->options): ?>
<h2 id="config_options">Config Options</h2>
<div class="methods">
<?php foreach ($options as $option): ?>
<?php echo View::factory('userguide/api/config_option')->set('option', $option) ?>
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