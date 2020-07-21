<?php declare(strict_types = 1);
/**
 * This file is part of the Magebit_FaqIndexer package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magebit_FaqIndexer
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2020 Magebit, Ltd. (https://magebit.com/)
 * @author    Magebit <info@magebit.com>
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Magebit\FaqIndexer\Model\Indexer;

use Divante\VsbridgeIndexerCore\Indexer\GenericIndexerHandler;
use Divante\VsbridgeIndexerCore\Indexer\StoreManager;
use Magebit\FaqIndexer\Model\Indexer\Action\Category as Action;

/**
 * @package Magebit\FaqIndexer\Model\Indexer
 */
class Category implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var Action
     */
    protected $action;

    /**
     * @var GenericIndexerHandler
     */
    protected $indexHandler;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param GenericIndexerHandler $indexerHandler
     * @param StoreManager $storeManager
     * @param Action $action
     * @return void
     */
    public function __construct(
        GenericIndexerHandler $indexerHandler,
        StoreManager $storeManager,
        Action $action
    ) {
        $this->action = $action;
        $this->storeManager = $storeManager;
        $this->indexHandler = $indexerHandler;
    }

    /**
     * @inheritdoc
     */
    public function execute($ids)
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->indexHandler->saveIndex($this->action->rebuild((int)$store->getId(), $ids), $store);
            $this->indexHandler->cleanUpByTransactionKey($store, $ids);
        }
    }

    /**
     * @inheritdoc
     */
    public function executeFull()
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->indexHandler->saveIndex($this->action->rebuild((int)$store->getId()), $store);
            $this->indexHandler->cleanUpByTransactionKey($store);
        }
    }

    /**
     * @inheritdoc
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @inheritdoc
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }
}
