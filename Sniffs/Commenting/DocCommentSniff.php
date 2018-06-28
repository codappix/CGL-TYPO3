<?php
namespace Codappix\CglTypo3\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DocCommentSniff implements Sniff
{
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    public function register()
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens       = $phpcsFile->getTokens();
        $commentStart = $stackPtr;
        $commentEnd   = $tokens[$stackPtr]['comment_closer'];

        $empty = [
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR,
        ];

        $short = $phpcsFile->findNext($empty, ($stackPtr + 1), $commentEnd, true);
        if ($short === false) {
            // No content at all.
            $error = 'Doc comment is empty';
            $phpcsFile->addError($error, $stackPtr, 'Empty');
            return;
        }

        // The first line of the comment should just be the /** code.
        if ($tokens[$short]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'The open comment tag must be the only content on the line';
            $fix   = $phpcsFile->addFixableError(
                $error,
                $stackPtr,
                'ContentAfterOpen'
            );
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($stackPtr);
                $phpcsFile->fixer->addContentBefore($short, '* ');
                $phpcsFile->fixer->endChangeset();
            }
        }

        // The last line of the comment should just be the */ code.
        $prev = $phpcsFile->findPrevious($empty, ($commentEnd - 1), $stackPtr, true);
        if ($tokens[$prev]['line'] === $tokens[$commentEnd]['line']) {
            $error = 'The close comment tag must be the only content on the line';
            $fix   = $phpcsFile->addFixableError(
                $error,
                $commentEnd,
                'ContentBeforeClose'
            );
            if ($fix === true) {
                $phpcsFile->fixer->addNewlineBefore($commentEnd);
            }
        }

        // Check for additional blank lines at the end of the comment.
        if ($tokens[$prev]['line'] < ($tokens[$commentEnd]['line'] - 1)) {
            $error = 'Additional blank lines found at end of doc comment';
            $fix   = $phpcsFile->addFixableError(
                $error,
                $commentEnd,
                'SpacingAfter'
            );
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($prev + 1); $i < $commentEnd; $i++) {
                    if ($tokens[($i + 1)]['line'] === $tokens[$commentEnd]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        // No extra newline before short description.
        if ($tokens[$short]['line'] !== ($tokens[$stackPtr]['line'] + 1)) {
            $error = 'Doc comment short description must be on the first line';
            $fix   = $phpcsFile->addFixableError(
                $error,
                $short,
                'SpacingBeforeShort'
            );
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $stackPtr; $i < $short; $i++) {
                    if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                        continue;
                    } elseif ($tokens[$i]['line'] === $tokens[$short]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        // Account for the fact that a short description might cover
        // multiple lines.
        $shortContent = $tokens[$short]['content'];
        $shortEnd     = $short;
        for ($i = ($short + 1); $i < $commentEnd; $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING) {
                if ($tokens[$i]['line'] === ($tokens[$shortEnd]['line'] + 1)) {
                    $shortContent .= $tokens[$i]['content'];
                    $shortEnd      = $i;
                } else {
                    break;
                }
            }
        }

        if (preg_match('/\p{Lu}|\P{L}/u', $shortContent[0]) === 0) {
            $error = 'Doc comment short description must start with a capital letter';
            $phpcsFile->addError($error, $short, 'ShortNotCapital');
        }

        $long = $phpcsFile->findNext(
            $empty,
            ($shortEnd + 1),
            ($commentEnd - 1),
            true
        );
        if ($long === false) {
            return;
        }

        if ($tokens[$long]['code'] === T_DOC_COMMENT_STRING) {
            if ($tokens[$long]['line'] !== ($tokens[$shortEnd]['line'] + 2)) {
                $error = 'There must be exactly one blank line between descriptions in a doc comment';
                $fix   = $phpcsFile->addFixableError(
                    $error,
                    $long,
                    'SpacingBetween'
                );
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($shortEnd + 1); $i < $long; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$shortEnd]['line']) {
                            continue;
                        } elseif ($tokens[$i]['line'] === ($tokens[$long]['line'] - 1)) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }

            if (preg_match('/\p{Lu}|\P{L}/u', $tokens[$long]['content'][0]) === 0) {
                $error = 'Doc comment long description must start with a capital letter';
                $phpcsFile->addError($error, $long, 'LongNotCapital');
            }
        }

        if (empty($tokens[$commentStart]['comment_tags']) === true) {
            // No tags in the comment.
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        $prev     = $phpcsFile->findPrevious(
            $empty,
            ($firstTag - 1),
            $stackPtr,
            true
        );
        if ($tokens[$firstTag]['line'] !== ($tokens[$prev]['line'] + 2)) {
            $error = 'There must be exactly one blank line before the tags in a doc comment';
            $fix   = $phpcsFile->addFixableError(
                $error,
                $firstTag,
                'SpacingBeforeTags'
            );
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($prev + 1); $i < $firstTag; $i++) {
                    if ($tokens[$i]['line'] === $tokens[$firstTag]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $indent = str_repeat(' ', $tokens[$stackPtr]['column']);
                $phpcsFile->fixer->addContent(
                    $prev,
                    $phpcsFile->eolChar . $indent . '*' . $phpcsFile->eolChar
                );
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Break out the tags into groups and check alignment within each.
        // A tag group is one where there are no blank lines between tags.
        // The param tag group is special as it requires all @param tags to
        // be inside.
        $tagGroups    = array();
        $groupid      = 0;
        $paramGroupid = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $pos => $tag) {
            if ($pos > 0) {
                $prev = $phpcsFile->findPrevious(
                    T_DOC_COMMENT_STRING,
                    ($tag - 1),
                    $tokens[$commentStart]['comment_tags'][($pos - 1)]
                );

                if ($prev === false) {
                    $prev = $tokens[$commentStart]['comment_tags'][($pos - 1)];
                }

                if ($tokens[$prev]['line'] !== ($tokens[$tag]['line'] - 1)) {
                    $groupid++;
                }
            }

            if ($tokens[$tag]['content'] === '@param') {
                if (($paramGroupid === null
                    && empty($tagGroups[$groupid]) === false)
                    || ($paramGroupid !== null
                    && $paramGroupid !== $groupid)
                ) {
                    $error = 'Parameter tags should be grouped together in a doc comment';
                    $phpcsFile->addWarning($error, $tag, 'ParamGroup');
                }

                if ($paramGroupid === null) {
                    $paramGroupid = $groupid;
                }
            } elseif ($groupid === $paramGroupid) {
                $error = 'Tag cannot be grouped with parameter tags in a doc comment';
                $phpcsFile->addError($error, $tag, 'NonParamGroup');
            }

            $tagGroups[$groupid][] = $tag;
        }

        foreach ($tagGroups as $group) {
            $maxLength = 0;
            $paddings  = array();
            foreach ($group as $pos => $tag) {
                $tagLength = strlen($tokens[$tag]['content']);
                if ($tagLength > $maxLength) {
                    $maxLength = $tagLength;
                }

                // Check for a value. No value means no padding needed.
                $string = $phpcsFile->findNext(
                    T_DOC_COMMENT_STRING,
                    $tag,
                    $commentEnd
                );
                if ($string !== false && $tokens[$string]['line'] === $tokens[$tag]['line']) {
                    $paddings[$tag] = strlen($tokens[($tag + 1)]['content']);
                }
            }

            // Check that there was single blank line after the tag block
            // but account for a multi-line tag comments.
            $lastTag = $group[$pos];
            $next    = $phpcsFile->findNext(
                T_DOC_COMMENT_TAG,
                ($lastTag + 3),
                $commentEnd
            );
            if ($next !== false) {
                $prev = $phpcsFile->findPrevious(
                    array(
                     T_DOC_COMMENT_TAG,
                     T_DOC_COMMENT_STRING,
                    ),
                    ($next - 1),
                    $commentStart
                );
                if ($tokens[$next]['line'] !== ($tokens[$prev]['line'] + 2)) {
                    $error = 'There must be a single blank line after a tag group';
                    $fix   = $phpcsFile->addFixableError(
                        $error,
                        $lastTag,
                        'SpacingAfterTagGroup'
                    );
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $next; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $indent = str_repeat(' ', $tokens[$stackPtr]['column']);
                        $phpcsFile->fixer->addContent(
                            $prev,
                            $phpcsFile->eolChar . $indent . '*' . $phpcsFile->eolChar
                        );
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }
        }

        $foundTags = array();
        foreach ($tokens[$stackPtr]['comment_tags'] as $pos => $tag) {
            $tagName = $tokens[$tag]['content'];
            if (isset($foundTags[$tagName]) === true) {
                $lastTag = $tokens[$stackPtr]['comment_tags'][($pos - 1)];
                if ($tokens[$lastTag]['content'] !== $tagName) {
                    $error = 'Tags should be grouped together in a doc comment';
                    $phpcsFile->addWarning($error, $tag, 'TagsNotGrouped');
                }

                continue;
            }

            $foundTags[$tagName] = true;
        }

        // The data type has to be declared in short form
        // integer -> int; boolean -> bool.
        $longTypes  = array(
                       'boolean',
                       'integer',
                      );
        $shortTypes = array(
                       'bool',
                       'int',
                      );
        $fix        = false;
        foreach ($tokens[$stackPtr]['comment_tags'] as $pos => $tag) {
            $dataType = $phpcsFile->findNext(T_DOC_COMMENT_STRING, ($tag + 1));
            for ($i = 0; $i < count($longTypes); $i++) {
                if (strpos($tokens[$dataType]['content'], $longTypes[$i]) !== false) {
                    $error = 'Use short form of data types; expected "%s" but found "%s".';
                    $data  = array(
                              $shortTypes[$i],
                              $longTypes[$i],
                             );
                    $fix   = $phpcsFile->addFixableError($error, $tag, 'UseShortDataType', $data);
                }

                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(
                        $dataType,
                        substr_replace($tokens[$dataType]['content'], $shortTypes[$i], 0, strlen($longTypes[$i]))
                    );
                    $fix = false;
                }
            }
        }
    }
}
