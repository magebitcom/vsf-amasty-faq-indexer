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
namespace Magebit\FaqIndexer\Plugin;

use Exception;
use Magebit\FaqIndexer\Model\Indexer\CategoryProcessor;
use Magebit\FaqIndexer\Model\Indexer\QuestionProcessor;

/**
 * @package Magebit\FaqIndexer\Plugin
 */
class Save
{
    /**
     * @var CategoryProcessor
     */
    private $categoryProcessor;

    /**
     * @var QuestionProcessor
     */
    private $questionProcessor;

    /**
     * Save constructor.
     *
     * @param CategoryProcessor $categoryProcessor
     * @param QuestionProcessor $questionProcessor
     */
    public function __construct(CategoryProcessor $categoryProcessor, QuestionProcessor $questionProcessor)
    {
        $this->categoryProcessor = $categoryProcessor;
        $this->questionProcessor = $questionProcessor;
    }

    /**
     * Reindex all categories and questions
     *
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     * @throws Exception
     */
    public function afterExecute($subject, $result)
    {
        $this->categoryProcessor->reindexAll();
        $this->questionProcessor->reindexAll();

        return $result;
    }
}
