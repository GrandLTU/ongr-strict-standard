<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\Tests\Unit\WhiteSpace;

use ONGR\Tests\AbstractSniffUnitTest;

/**
 * OperatorSpacingSniffTest class.
 */
class OperatorSpacingSniffTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    protected function getErrorList()
    {
        return [
            7 => ['Expected 0 spaces after "!" operator; 1 found'],
            11 => ['Expected 0 spaces after "!" operator; 3 found'],
            16 => ['Expected 0 spaces after "!" operator; 1 found'],
            17 => ['Expected 0 spaces after "!" operator; 4 found'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getWarningList()
    {
        return [];
    }
}
