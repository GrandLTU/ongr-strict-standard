<?php
/**
 * ONGR_Sniffs_ControlStructures_ForLoopDeclarationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

namespace ONGR\Sniffs\ControlStructures;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * ONGR_Sniffs_ControlStructures_ForLoopDeclarationSniff.
 *
 * Verifies that there is a space between each condition of for loops.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class ForLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var int How many spaces should follow the opening bracket.
     */
    public $requiredSpacesAfterOpen = 0;

    /**
     * @var int How many spaces should precede the closing bracket.
     */
    public $requiredSpacesBeforeClose = 0;

    /**
     * @var array A list of tokenizers this sniff supports.
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FOR];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->requiredSpacesAfterOpen = (int)$this->requiredSpacesAfterOpen;
        $this->requiredSpacesBeforeClose = (int)$this->requiredSpacesBeforeClose;
        $tokens = $phpcsFile->getTokens();

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openingBracket === false) {
            $error = 'Possible parse error: no opening parenthesis for FOR keyword';
            $phpcsFile->addWarning($error, $stackPtr, 'NoOpenBracket');

            return;
        }

        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($this->requiredSpacesAfterOpen === 0 && $tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found after opening bracket of FOR loop';
            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterOpen');
        } elseif ($this->requiredSpacesAfterOpen > 0) {
            $spaceAfterOpen = 0;
            if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
                $spaceAfterOpen = strlen($tokens[($openingBracket + 1)]['content']);
            }

            if ($this->requiredSpacesAfterOpen !== $spaceAfterOpen) {
                $error = 'Expected %s spaces after opening bracket; %s found';
                $data = [
                    $this->requiredSpacesAfterOpen,
                    $spaceAfterOpen,
                ];
                $phpcsFile->addError($error, $stackPtr, 'SpacingAfterOpen', $data);
            }
        }

        if ($this->requiredSpacesBeforeClose === 0 && $tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found before closing bracket of FOR loop';
            $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeClose');
        } elseif ($this->requiredSpacesBeforeClose > 0) {
            $spaceBeforeClose = 0;
            if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
                $spaceBeforeClose = strlen($tokens[($closingBracket - 1)]['content']);
            }

            if ($this->requiredSpacesBeforeClose !== $spaceBeforeClose) {
                $error = 'Expected %s spaces before closing bracket; %s found';
                $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeClose');
            }
        }

        $firstSemicolon = $phpcsFile->findNext(T_SEMICOLON, $openingBracket, $closingBracket);

        // Check whitespace around each of the tokens.
        if ($firstSemicolon !== false) {
            if ($tokens[($firstSemicolon - 1)]['code'] === T_WHITESPACE) {
                $error = 'Space found before first semicolon of FOR loop';
                $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeFirst');
            }

            if ($tokens[($firstSemicolon + 1)]['code'] !== T_WHITESPACE
                && $tokens[($firstSemicolon + 1)]['code'] !== T_SEMICOLON
            ) {
                $error = 'Expected 1 space after first semicolon of FOR loop; 0 found';
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterFirst');
            } else {
                if (strlen($tokens[($firstSemicolon + 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($firstSemicolon + 1)]['content']);
                    $error = 'Expected 1 space after first semicolon of FOR loop; %s found';
                    $data = [$spaces];
                    $phpcsFile->addError($error, $stackPtr, 'SpacingAfterFirst', $data);
                }
            }

            $secondSemicolon = $phpcsFile->findNext(T_SEMICOLON, ($firstSemicolon + 1));

            if ($secondSemicolon !== false) {
                if ($tokens[($secondSemicolon - 1)]['code'] === T_WHITESPACE
                    && $tokens[($firstSemicolon + 1)]['code'] !== T_SEMICOLON
                ) {
                    $error = 'Space found before second semicolon of FOR loop';
                    $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeSecond');
                }

                if (($secondSemicolon + 1) !== $closingBracket
                    && $tokens[($secondSemicolon + 1)]['code'] !== T_WHITESPACE
                ) {
                    $error = 'Expected 1 space after second semicolon of FOR loop; 0 found';
                    $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterSecond');
                } else {
                    if (strlen($tokens[($secondSemicolon + 1)]['content']) !== 1) {
                        $spaces = strlen($tokens[($secondSemicolon + 1)]['content']);
                        $data = [$spaces];
                        if (($secondSemicolon + 2) === $closingBracket) {
                            $error = 'Expected no space after second semicolon of FOR loop; %s found';
                            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterSecondNoThird', $data);
                        } else {
                            $error = 'Expected 1 space after second semicolon of FOR loop; %s found';
                            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterSecond', $data);
                        }
                    }
                }
            }
        }
    }
}
