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
namespace Magebit\FaqIndexer\Model\Indexer\Action;

use Amasty\Faq\Api\QuestionRepositoryInterface;
use Magebit\FaqIndexer\Model\ResourceModel\Category as CategoryResource;
use Magebit\FaqIndexer\Model\ResourceModel\Question as QuestionResource;
use Magebit\StaticContentProcessor\Helper\Resolver;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Question
 */
class Question
{
    /**
     * @var QuestionResource
     */
    protected $resourceModel;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var AreaList
     */
    protected $areaList;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var CategoryResource
     */
    protected $categoryResource;

    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param AreaList $areaList
     * @param QuestionResource $questionResource
     * @param CategoryResource $categoryResource
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     * @param Resolver $resolver
     * @param ScopeConfigInterface $scopeConfig
     * @param QuestionRepositoryInterface $questionRepository
     * @return void
     */
    public function __construct(
        AreaList $areaList,
        QuestionResource $questionResource,
        CategoryResource $categoryResource,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        Resolver $resolver,
        ScopeConfigInterface $scopeConfig,
        QuestionRepositoryInterface $questionRepository
    ) {
        $this->areaList = $areaList;
        $this->filterProvider = $filterProvider;
        $this->resourceModel = $questionResource;
        $this->storeManager = $storeManager;
        $this->resolver = $resolver;
        $this->scopeConfig = $scopeConfig;
        $this->categoryResource = $categoryResource;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @param int $storeId
     * @param array $questionIds
     * @return \Traversable
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function rebuild($storeId = 1, array $questionIds = [])
    {
        $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        $rewritesEnabled = $this->scopeConfig->getValue(
            'vsbridge_indexer_settings/url_rewrites/faq_enabled',
            ScopeInterface::SCOPE_STORE
        );

        $lastQuestionId = 0;

        do {
            $questions = $this->resourceModel->loadQuestions($storeId, $questionIds, $lastQuestionId);

            foreach ($questions as $questionData) {
                $questionData['id'] = (int) $questionData['question_id'];
                $lastQuestionId = $questionData['question_id'];

                $post = $this->questionRepository->getById($questionData["question_id"]);

                if ($rewritesEnabled) {
                    $questionData['title'] = $post->getTitle() ? $this->resolver->resolve($post->getTitle(), $storeId) : null;
                    $questionData['short_answer'] = $post->getShortAnswer() ? $this->resolver->resolve($post->getShortAnswer(), $storeId) : null;
                    $questionData['answer'] = $post->getAnswer() ? $this->resolver->resolve($post->getAnswer(), $storeId) : null;
                } else {
                    $questionData['title'] = $post->getTitle();
                    $questionData['short_answer'] = $post->getShortAnswer();
                    $questionData['answer'] = $post->getAnswer();
                }

                $questionData['question_category_ids'] = $post->getCategories() ? array_map('intval', explode(",", $post->getCategories())) : [];

                $productIds = $post->getProductIds(); // For some reason this can be a string, null and array

                if (is_string($productIds)) {
                    $productIds = explode(",", $productIds);
                }

                $questionData['product_ids'] = $productIds ? array_map('intval', $productIds) : [];

                $questionData['exclude_sitemap'] = (int) $questionData['exclude_sitemap'];
                $questionData['is_show_full_answer'] = (int) $questionData['is_show_full_answer'];
                $questionData['negative_rating'] = (int) $questionData['negative_rating'];
                $questionData['nofollow'] = (int) $questionData['nofollow'];
                $questionData['noindex'] = (int) $questionData['noindex'];
                $questionData['position'] = (int) $questionData['position'];
                $questionData['positive_rating'] = (int) $questionData['positive_rating'];
                $questionData['status'] = (int) $questionData['status'];
                $questionData['total_rating'] = (int) $questionData['total_rating'];
                $questionData['visibility'] = (int) $questionData['visibility'];
                $questionData['visit_count'] = (int) $questionData['visit_count'];

                if ($post->getCategories()) {
                    $categories = $this->categoryResource->loadPages(1, explode(",", $post->getCategories()));

                    $categoryArray = [];
                    foreach ($categories as $category) {
                        $categoryArray[$category['url_key']] = $category['title'];
                    }

                    $questionData['question_categories'] = (string) json_encode($categoryArray);
                }

                unset($questionData['question_id']);
                yield $lastQuestionId => $questionData;
            }
        } while (!empty($questions));
    }
}
