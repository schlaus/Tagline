<?php

namespace Schlaus\Tagline;

use InvalidArgumentException;
use LogicException;

class Tagline
{

    /**
     * @var string The last tag that was searched for
     */
    private static $currentTag;

    /**
     * @var array Cached file contents
     */
    private static $currentData;

    /**
     * @var int Total lines in the current file
     */
    private static $currentLines = 0;

    /**
     * @var int The current index, stored for Tagline::findNext()
     */
    private static $currentIndex = 0;

    /**
     * The main function this class provides. Takes in a tag, a target file, and
     * optionally an offset, and looks for the first occurrence of that tag in
     * the file. Returns false is the tag was not found, and throws an
     * \InvalidArgumentException if the file could not be read. Otherwise returns
     * the line number the tag was first encountered on, as an int.
     *
     * The second argument is only required on the first search to a file. The
     * contents are cached for subsequent runs, however only one file will be
     * cached at a time. In other words, always provide a file argument when the
     * target changes, otherwise it's not needed.
     *
     * The word tag is used here only because finding tags was the intended use
     * for the class. A 'tag' can however be any string, and the function of the
     * class could be summarized as a vertical strpos().
     *
     * @param string $tag      Required. A string to look for
     *
     * @param mixed|null $file Optional. A file to look for the tag in. Can be
     *                         skipped with null. Can be one of the following:
     *
     *                         * filename as string
     *                         * file contents as string
     *                         * file contents as array
     *                         * stream resource
     *                         * \SplFileObject
     *
     *                         Any string with no line breaks is considered a
     *                         filename. If the file doesn't exist, an exception
     *                         is thrown.
     *
     * @param int $offset      Optional. Skip N lines from the top of the file.
     *
     * @param string $interpretAs Optional. Forces input to be interpreted as
     *                            a specific type, for example to force a string
     *                            to be interpreted as such regardless of wheter
     *                            it has any line breaks. Can be one of the
     *                            following:
     *
     *                            * 'string'
     *                            * 'array'
     *                            * 'filename'
     *                            * 'stream'
     *                            * 'fileobject'
     *
     * @return int|false       Line number or false if tag was not found.
     */
    public static function findTag($tag, $file = null, $offset = 0, $interpretAs = null)
    {
        if ($file === null && self::$currentData === null) {
            throw new LogicException('No file provided for Tagline before attempting to search for tags.');
        }

        self::$currentIndex = $offset;

        if (null !== $file) {
            self::$currentData = self::anyToArray($file, $interpretAs);
        }

        if (self::$currentData === false) {
            throw new InvalidArgumentException('Invalid source file provided for Tagline.');
        }

        self::$currentLines = count(self::$currentData);
        self::$currentTag = $tag;

        return self::findNext();

    }

    /**
     * Find the next occurrence of the tag last searched for.
     *
     * @return int|false Line number or false if tag was not found.
     */
    public static function findNext()
    {
        $line = false;
        for (; self::$currentIndex < self::$currentLines; self::$currentIndex++) {
            if (strpos(self::$currentData[self::$currentIndex], self::$currentTag) !== false) {
                // This is because otherwise the method will never get past the first occurrence
                self::$currentIndex++;
                $line = self::$currentIndex;
                break;
            }
        }
        return $line;
    }

    /**
     * A helper function which tries to figure out how to interpret the provided file argument,
     * and then returns the contents in a zero-based line-per-key array.
     *
     * @param mixed $source     Source to interpret. See findTag() for a list of supported types.
     *
     * @param string|null $type Force input to be interpreted as a specific type. See findTag().
     *
     * @return array|false      Zero-indexed line-per-key array or false on failure.
     */
    public static function anyToArray($source, $type = null)
    {
        if ($type === 'array' || ($type === null && is_array($source))) {
            return $source;
        }

        if ($type === 'filename' || ($type === null && is_string($source))) {
            if ($type === 'filename' || (is_readable($source) && is_file($source))) {
                return file($source);
            }

            // \R will match any linebreak sequence
            if (!preg_match('/\R/', $source)) {
                return false;
            }
        }

        if ($type === 'string' || ($type === null && is_string($source))) {
            //return preg_split('/\\\\r\\\\n?|\\\\n/', $source);
            return preg_split("/\r\n?|\n/", $source);
        }

        if ($type === 'stream' || ($type === null && is_resource($source) && get_resource_type($source) === 'stream')) {
            $readableModes = array('r', 'r+', 'w+', 'a+', 'x+', 'c+');
            $meta = stream_get_meta_data($source);
            if ($type === 'stream' || in_array($meta['mode'], $readableModes, true)) {
                rewind($source);
                $lines = array();
                while (!feof($source)) {
                    $lines[] = fgets($source);
                }
                return $lines;
            }
        }

        if ($type === 'fileobject' ||
            (
                is_object($source) &&
                is_a($source, 'SplFileObject') &&
                $source->isReadable() &&
                $source->isFile()
            )
        ) {
            $source->rewind();
            $lines = array();
            while (!$source->eof()) {
                $lines[] = $source->fgets();
            }
            return $lines;
        }

        return false;
    }
}
