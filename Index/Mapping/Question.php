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
namespace Magebit\FaqIndexer\Index\Mapping;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Divante\VsbridgeIndexerCore\Api\MappingInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * @package Magebit\FaqIndexer\Index\Mapping
 */
class Question implements MappingInterface
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @param EventManager $eventManager
     * @return void
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function getMappingProperties()
    {
        $properties = [
            'id' => ['type' => FieldInterface::TYPE_INTEGER],
            'title' => [
                'type' => FieldInterface::TYPE_TEXT,
                'fields' => [
                    'keyword' => [
                        'type' => FieldInterface::TYPE_KEYWORD,
                        'ignore_above' => 256,
                    ]
                ],
            ],
            'short_answer' => ['type' => FieldInterface::TYPE_TEXT],
            'answer' => ['type' => FieldInterface::TYPE_TEXT],
            'visibility' => ['type' => FieldInterface::TYPE_BOOLEAN],
            'status' => ['type' => FieldInterface::TYPE_INTEGER],
            'url_key' => ['type' => FieldInterface::TYPE_KEYWORD],
            'question_category_ids' => ['type' => FieldInterface::TYPE_TEXT],
            'question_categories' => ['type' => FieldInterface::TYPE_TEXT],
        ];

        $mappingObject = new \Magento\Framework\DataObject();
        $mappingObject->setData('properties', $properties);

        $this->eventManager->dispatch(
            'elasticsearch_faq_question_mapping_properties',
            ['mapping' => $mappingObject]
        );

        return $mappingObject->getData();
    }
}
