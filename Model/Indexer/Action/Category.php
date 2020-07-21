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

use Magebit\FaqIndexer\Model\ResourceModel\Category as CategoryResource;
use Magebit\StaticContentProcessor\Helper\Resolver;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Category
 */
class Category
{
    /**
     * @var CategoryResource
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
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @param AreaList $areaList
     * @param CategoryResource $categoryResource
     * @param Magebit\FaqIndexer\Model\Indexer\Action\ScopeConfigInterface $scopeConfig
     * @param FilterProvider $filterProvider
     * @return void
     */
    public function __construct(
        AreaList $areaList,
        CategoryResource $categoryResource,
        ScopeConfigInterface $scopeConfig,
        Resolver $resolver,
        FilterProvider $filterProvider
    ) {
        $this->areaList = $areaList;
        $this->filterProvider = $filterProvider;
        $this->resolver = $resolver;
        $this->resourceModel = $categoryResource;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $storeId
     * @param array $categoryIds
     *
     * @return \Traversable
     */
    public function rebuild($storeId = 1, array $categoryIds = [])
    {
        $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        $rewritesEnabled = $this->scopeConfig->getValue(
            'vsbridge_indexer_settings/url_rewrites/faq_enabled',
            ScopeInterface::SCOPE_STORE
        );

        $lastPageId = 0;

        do {
            $categories = $this->resourceModel->loadPages($storeId, $categoryIds, $lastPageId);
            foreach ($categories as $categoryData) {
                $lastPageId = $categoryData['category_id'];

                $categoryData['id'] = (int) $categoryData['category_id'];
                $categoryData['exclude_sitemap'] = (int) $categoryData['exclude_sitemap'];
                $categoryData['nofollow'] = (int) $categoryData['nofollow'];
                $categoryData['noindex'] = (int) $categoryData['noindex'];
                $categoryData['position'] = (int) $categoryData['position'];
                $categoryData['status'] = (int) $categoryData['status'];
                $categoryData['visit_count'] = (int) $categoryData['visit_count'];

                if ($rewritesEnabled) {
                    $categoryData['description'] = $this->resolver->resolve($categoryData['description'], $storeId);
                }

                unset($categoryData['category_id']);
                yield $lastPageId => $categoryData;
            }
        } while (!empty($categories));
    }
}
