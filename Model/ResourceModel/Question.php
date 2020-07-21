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
namespace Magebit\FaqIndexer\Model\ResourceModel;

use Amasty\Faq\Api\Data\QuestionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class Question
 */
class Question
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Rates constructor.
     *
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param QuestionInterface $questionInterface
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        QuestionInterface $questionInterface
    ) {
        $this->resource = $resourceConnection;
        $this->metaDataPool = $metadataPool;
        $this->questionInterface = $questionInterface;
    }

    /**
     * @param int $storeId
     * @param array $questionIds
     * @param int $fromId
     * @param int $limit
     *
     * @return array
     * @throws \Exception
     */
    public function loadQuestions($storeId = 1, array $questionIds = [], $fromId = 0, $limit = 1000)
    {
        $select = $this->getConnection()->select()->from(['cms_faq' => 'amasty_faq_question']);

        if (!empty($questionIds)) {
            $select->where('cms_faq.question_id IN (?)', $questionIds);
        }

        $select->where("question_id IN (SELECT question_id FROM amasty_faq_question_store WHERE store_id IN (?))", [0, intval($storeId)]);
        $select->where('cms_faq.question_id > ?', $fromId)
            ->limit($limit)
            ->order('cms_faq.question_id');

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }
}
