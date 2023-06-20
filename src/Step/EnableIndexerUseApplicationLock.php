<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ExequielLares\CloudCustomScenario\Step;

use Magento\MagentoCloud\App\Error;
use Magento\MagentoCloud\Filesystem\FileSystemException;
use Magento\MagentoCloud\Config\Magento\Env\WriterInterface;
use Psr\Log\LoggerInterface;
use Magento\MagentoCloud\Step\StepInterface;

/**
 * Switching magento to production mode.
 * This is making for compatibility magento with cloud environment read-only file system.
 * As magento contains logic that skips checking on read-only file system only in production mode.
 */
class EnableIndexerUseApplicationLock implements StepInterface
{
    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param WriterInterface $deployConfigWriter
     */
    public function __construct(
        LoggerInterface $logger,
        WriterInterface $deployConfigWriter
    ) {
        $this->logger = $logger;
        $this->writer = $deployConfigWriter;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $this->logger->info('Customized step for enabling indexer application lock');
            $this->writer->update(['indexer' => ['use_application_lock' => true]]);
        } catch (FileSystemException $e) {
            throw new StepException($e->getMessage(), Error::BUILD_ENV_PHP_IS_NOT_WRITABLE, $e);
        } catch (\Exception $e) {
            throw new StepException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
