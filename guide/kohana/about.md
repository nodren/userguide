# About Kohana 2.x

Kohana is a PHP5 framework that uses the Model View Controller architectural pattern. It aims to be secure, lightweight, and easy to use.

#### Features

  - **Strict PHP5 OOP.** Offers many benefits: visibility protection, automatic class loading, overloading, interfaces, abstracts, singletons, etc.
  - **Community, not company, driven.** Kohana is driven by community discussion, ideas, and code. Kohana developers are from all around the world, each with their own talents. This allows a rapid and flexible development cycle that can respond to new bugs and requests within hours.
  - **GET, POST, COOKIE, and SESSION arrays all work as expected.** Kohana does not limit your access to global data, but provides XSS filtering and sanity checking of all global data.
  - **Cascading resources, modules, and inheritance.** Controllers, models, libraries, helpers, and views can be loaded from any location within your system, application, or module paths. Configuration options are inherited and can by dynamically overwritten by each application.
  - **No namespace conflicts.** Class suffixes and prefixes are used to prevent namespace conflicts.
  - **Auto-loading of classes.** All classes in Kohana are automatically loaded by the framework, and never have to be manually included.
  - **API consistency.** Classes that require access to different protocols use "drivers" to keep the the visible API completely consistent, even when the back-end changes.
  - **Powerful event handler.** Kohana events can transparently be: added, replaced, or even removed completely.

#### Goals

**To be secure** means to use best practices regarding security, at all times:

  * Kohana comes with built-in XSS protection, and can also use [HTMLPurifier](http://htmlpurifier.org) as an XSS filter.
  * All data inserted into the database is escaped using database-specific functions, like [mysql_real_escape_string](http://php.net/mysql_real_escape_string), to protect against [SQL injection](http://en.wikipedia.org/wiki/SQL_injection) attacks.
  [Magic quotes](http://php.net/magic_quotes) are disabled by Kohana.
  * All POST, GET, and COOKIE data is sanitized to prevent malicious behavior.

**To be lightweight** means to provide the highest amount of flexibility in the most efficient manner:

  * Kohana uses [convention over configuration](http://en.wikipedia.org/wiki/Convention_over_Configuration) as much as possible.
  * Sane defaults and highly optimized environment detection routines allow Kohana to run in almost any PHP5 environment.
  * [Loose coupling](http://en.wikipedia.org/wiki/Loose_coupling) is used to always load the minimum number of files, reducing resource usage.
  * A clean API and using native functions whenever possible makes Kohana one of the fastest PHP5 frameworks available.

**To be easy to use** means to provide understandable API and usage documentation, based on community feedback.

#### MVC

Kohana uses the [Model View Controller](http://en.wikipedia.org/wiki/Model-View-Controller) architectural pattern. This keeps application logic separate from the presentation and allows for cleaner and easier to work with code.

In Kohana this means:

  - A **Model** represents a data structure, usually this is a table in a database.
  - A **View** contains presentation code such as HTML, CSS and JavaScript.
  - A **Controller** contains the page logic to tie everything together and generate the page the user sees.

Credit: [[http://teknoid.wordpress.com/2009/01/06/another-way-to-think-about-mvc/|External Link]]

##### Quick Tips

   1. Fat models, skinny controllers!
   2. Keep as much business logic in the model as possible.
   3. If you see your controller getting “fat”, consider offloading some of the logic to the relevant model (or else bad things will start happening!).
   4. Models should not talk to the views directly (and vice versa).
   5. Related models provide information to the controller via their association (relation).
   6. It’s quite alright for the views to contain some logic, which deals with the view or presentation.
   7. Following strict [coding standards](coding_standards) to make your code easier to read.