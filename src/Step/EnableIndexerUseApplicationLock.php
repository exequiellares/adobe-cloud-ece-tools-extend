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
use Magento\MagentoCloud\Config\Environment;
use Psr\Log\LoggerInterface;
use Magento\MagentoCloud\Step\StepInterface;
use Magento\MagentoCloud\Step\StepException;

/**
 * Switching magento to production mode.
 * This is making for compatibility magento with cloud environment read-only file system.
 * As magento contains logic that skips checking on read-only file system only in production mode.
 */
class EnableIndexerUseApplicationLock implements StepInterface
{
    const INDEXER_USE_APPLICATION_LOCK = 'CONFIG_INDEXER_USE_APPLICATION_LOCK';
    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Environment
     */
    private $env;

    /**
     * @param LoggerInterface $logger
     * @param WriterInterface $deployConfigWriter
     * @param Environment $env
     */
    public function __construct(
        LoggerInterface $logger,
        WriterInterface $deployConfigWriter,
        Environment $env
    ) {
        $this->logger = $logger;
        $this->writer = $deployConfigWriter;
        $this->env = $env;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->logger->info('Customized step for enabling indexer application lock');
        $variables = $this->env->getVariables();
        $enable = false;
        if (isset($variables[self::INDEXER_USE_APPLICATION_LOCK])) {
            $enable = (bool)$variables[self::INDEXER_USE_APPLICATION_LOCK];
            $this->logger->info('Value read from environment variable CONFIG_INDEXER_USE_APPLICATION_LOCK: ' . $enable);
        } else {
            $this->logger->info('Variable ' . self::INDEXER_USE_APPLICATION_LOCK .' is not set. Set to FALSE. Current variables: ' . json_encode($variables));
        }
        try {
            $config = [
                'indexer' => [
                    'use_application_lock' => $enable
                ]
            ];
            $this->writer->update($config);
            $this->logger->info('Values updated in env.php ' . json_encode($config));
        } catch (FileSystemException $e) {
            throw new StepException($e->getMessage(), Error::BUILD_ENV_PHP_IS_NOT_WRITABLE, $e);
        } catch (\Exception $e) {
            throw new StepException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
