$(document).ready(function()
{
	// Syntax highlighter
	$('pre code').each(function(){
		$(this).addClass('brush: php');
	});

	SyntaxHighlighter.config.tagName = 'code';
	SyntaxHighlighter.defaults.toolbar = false;
	SyntaxHighlighter.defaults.gutter = false;
	SyntaxHighlighter.all();

	// Show source links
	$('#kodoc-content .method-source').each(function(){
		$(this).find('h5').each(function(){ $(this).append(' <a class="toggler" href="#">[show]</a>') });
		var link = $(this).find('.toggler');
		var code = $(this).find('pre');

		var show = function()
		{
			code.slideDown();
			link.html('[hide]');
		};

		var hide = function()
		{
			code.slideUp();
			link.html('[show]');
		};

		link.toggle(show,hide);

		code.hide();
	});
});
