This package provides an alternative to PHP's `assert()` that allows for an simple and reliable way
to check preconditions and postconditions in PHP code.

The background of this proposal is the reoccurring discussions about whether PHP's `assert()`
can and should be used in MediaWiki code. Two relevant threads:
* [Using PHP's assert in MediaWiki code](http://www.gossamer-threads.com/lists/wiki/wikitech/275737)
* [Is assert() allowed?](http://www.gossamer-threads.com/lists/wiki/wikitech/378676)

The outcome appears to be that
* assertions are generally a good way to improve code quality
* but PHP's ''assert()'' is broken by design

Following a [suggestion by Tim Starling](http://www.gossamer-threads.com/lists/wiki/wikitech/378815#378815),
this package provides an alternative to PHP's built in `assert()`.