<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Enterprise License (PEL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PEL
 */

namespace Pimcore\Bundle\DataHubBatchImportBundle\Cleanup;

use Pimcore\Model\Element\ElementInterface;

class UnpublishStrategy implements CleanupStrategyInterface
{
    public function doCleanup(ElementInterface $element = null): void
    {
        if ($element && method_exists($element, 'setPublished')) {
            $element->setPublished(false);
            $element->save();
        }
    }
}
