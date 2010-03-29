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

	// Striped tables
	$('#kodoc-content tbody tr:odd').addClass('alt');

	// Collapsable categories
	$('#kodoc-menu li:has(ul), #kodoc-menu li:has(ol)').each(function()
	{
		var link = $(this).find(':first');
		var menu = $(this).find('ul:first,ol:first');

		link.prepend('<div class="toggler"></div>');
		link.addClass('section');

		var open  = function()
		{
			menu.slideDown();
			link.addClass('section-active');
		};

		var close = function()
		{
			menu.slideUp();
			link.removeClass('section-active');
		};

		if (menu.find('a[href="'+ window.location.pathname +'"]').length || $(this).find('a[href="'+ window.location.pathname +'"]').length)
		{
			// Currently active menu
			link.find('.toggler:first').toggle(close, open);
			link.addClass('section-active');
		}
		else
		{
			menu.hide();
			link.find('.toggler:first').toggle(open, close);
			link.removeClass('section-active');
		}
	});

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
