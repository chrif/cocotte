<?php declare(strict_types=1);

namespace Chrif\Cocotte\Finder;

use Symfony\Component\Finder\SplFileInfo;

interface Finder extends \IteratorAggregate, \Countable
{
    /**
     * Adds VCS patterns.
     *
     * @see ignoreVCS()
     *
     * @param string|string[] $pattern VCS patterns to ignore
     */
    public static function addVCSPattern($pattern);

    /**
     * Restricts the matching to directories only.
     *
     * @return $this
     */
    public function directories();

    /**
     * Restricts the matching to files only.
     *
     * @return $this
     */
    public function files();

    /**
     * Adds tests for the directory depth.
     *
     * Usage:
     *
     *   $finder->depth('> 1') // the Finder will start matching at level 1.
     *   $finder->depth('< 3') // the Finder will descend at most 3 levels of directories below the starting point.
     *
     * @param string|int $level The depth level expression
     *
     * @return $this
     *
     * @see DepthRangeFilterIterator
     * @see NumberComparator
     */
    public function depth($level);

    /**
     * Adds tests for file dates (last modified).
     *
     * The date must be something that strtotime() is able to parse:
     *
     *   $finder->date('since yesterday');
     *   $finder->date('until 2 days ago');
     *   $finder->date('> now - 2 hours');
     *   $finder->date('>= 2005-10-15');
     *
     * @param string $date A date range string
     *
     * @return $this
     *
     * @see strtotime
     * @see DateRangeFilterIterator
     * @see DateComparator
     */
    public function date($date);

    /**
     * Adds rules that files must match.
     *
     * You can use patterns (delimited with / sign), globs or simple strings.
     *
     * $finder->name('*.php')
     * $finder->name('/\.php$/') // same as above
     * $finder->name('test.php')
     *
     * @param string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function name($pattern);

    /**
     * Adds rules that files must not match.
     *
     * @param string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function notName($pattern);

    /**
     * Adds tests that file contents must match.
     *
     * Strings or PCRE patterns can be used:
     *
     * $finder->contains('Lorem ipsum')
     * $finder->contains('/Lorem ipsum/i')
     *
     * @param string $pattern A pattern (string or regexp)
     *
     * @return $this
     *
     * @see FilecontentFilterIterator
     */
    public function contains($pattern);

    /**
     * Adds tests that file contents must not match.
     *
     * Strings or PCRE patterns can be used:
     *
     * $finder->notContains('Lorem ipsum')
     * $finder->notContains('/Lorem ipsum/i')
     *
     * @param string $pattern A pattern (string or regexp)
     *
     * @return $this
     *
     * @see FilecontentFilterIterator
     */
    public function notContains($pattern);

    /**
     * Adds rules that filenames must match.
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     * $finder->path('some/special/dir')
     * $finder->path('/some\/special\/dir/') // same as above
     *
     * Use only / as dirname separator.
     *
     * @param string $pattern A pattern (a regexp or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function path($pattern);

    /**
     * Adds rules that filenames must not match.
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     * $finder->notPath('some/special/dir')
     * $finder->notPath('/some\/special\/dir/') // same as above
     *
     * Use only / as dirname separator.
     *
     * @param string $pattern A pattern (a regexp or a string)
     *
     * @return $this
     *
     * @see FilenameFilterIterator
     */
    public function notPath($pattern);

    /**
     * Adds tests for file sizes.
     *
     * $finder->size('> 10K');
     * $finder->size('<= 1Ki');
     * $finder->size(4);
     *
     * @param string|int $size A size range string or an integer
     *
     * @return $this
     *
     * @see SizeRangeFilterIterator
     * @see NumberComparator
     */
    public function size($size);

    /**
     * Excludes directories.
     *
     * Directories passed as argument must be relative to the ones defined with the `in()` method. For example:
     *
     *     $finder->in(__DIR__)->exclude('ruby');
     *
     * @param string|array $dirs A directory path or an array of directories
     *
     * @return $this
     *
     * @see ExcludeDirectoryFilterIterator
     */
    public function exclude($dirs);

    /**
     * Excludes "hidden" directories and files (starting with a dot).
     *
     * This option is enabled by default.
     *
     * @param bool $ignoreDotFiles Whether to exclude "hidden" files or not
     *
     * @return $this
     *
     * @see ExcludeDirectoryFilterIterator
     */
    public function ignoreDotFiles($ignoreDotFiles);

    /**
     * Forces the finder to ignore version control directories.
     *
     * This option is enabled by default.
     *
     * @param bool $ignoreVCS Whether to exclude VCS files or not
     *
     * @return $this
     *
     * @see ExcludeDirectoryFilterIterator
     */
    public function ignoreVCS($ignoreVCS);

    /**
     * Sorts files and directories by an anonymous function.
     *
     * The anonymous function receives two \SplFileInfo instances to compare.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @param \Closure $closure
     * @return $this
     *
     * @see SortableIterator
     */
    public function sort(\Closure $closure);

    /**
     * Sorts files and directories by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByName();

    /**
     * Sorts files and directories by type (directories before files), then by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByType();

    /**
     * Sorts files and directories by the last accessed time.
     *
     * This is the time that the file was last accessed, read or written to.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByAccessedTime();

    /**
     * Sorts files and directories by the last inode changed time.
     *
     * This is the time that the inode information was last modified (permissions, owner, group or other metadata).
     *
     * On Windows, since inode is not available, changed time is actually the file creation time.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByChangedTime();

    /**
     * Sorts files and directories by the last modified time.
     *
     * This is the last time the actual contents of the file were last modified.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return $this
     *
     * @see SortableIterator
     */
    public function sortByModifiedTime();

    /**
     * Filters the iterator with an anonymous function.
     *
     * The anonymous function receives a \SplFileInfo and must return false
     * to remove files.
     *
     * @param \Closure $closure
     * @return $this
     *
     * @see CustomFilterIterator
     */
    public function filter(\Closure $closure);

    /**
     * Forces the following of symlinks.
     *
     * @return $this
     */
    public function followLinks();

    /**
     * Tells finder to ignore unreadable directories.
     *
     * By default, scanning unreadable directories content throws an AccessDeniedException.
     *
     * @param bool $ignore
     *
     * @return $this
     */
    public function ignoreUnreadableDirs($ignore = true);

    /**
     * Searches files and directories which match defined rules.
     *
     * @param string|array $dirs A directory path or an array of directories
     *
     * @return $this
     *
     * @throws \InvalidArgumentException if one of the directories does not exist
     */
    public function in($dirs);

    /**
     * Returns an Iterator for the current Finder configuration.
     *
     * This method implements the IteratorAggregate interface.
     *
     * @return \Iterator|SplFileInfo[] An iterator
     *
     * @throws \LogicException if the in() method has not been called
     */
    public function getIterator();

    /**
     * Appends an existing set of files/directories to the finder.
     *
     * The set can be another Finder, an Iterator, an IteratorAggregate, or even a plain array.
     *
     * @param mixed $iterator
     *
     * @return $this
     *
     * @throws \InvalidArgumentException when the given argument is not iterable
     */
    public function append($iterator);

    /**
     * Check if the any results were found.
     *
     * @return bool
     */
    public function hasResults();

    /**
     * Counts all the results collected by the iterators.
     *
     * @return int
     */
    public function count();

    /**
     * Find one file by its real path
     *
     * @param string $realPath
     * @return CocotteFinder
     */
    public function exactFile(string $realPath);
}