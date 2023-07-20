<?php

namespace Hibrido\NewHomePage\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Widget\Model\Widget\InstanceFactory;
use Magento\Widget\Model\ResourceModel\Widget\Instance as WidgetInstanceResource;
use Magento\Catalog\Model\CategoryFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var InstanceFactory
     */
    private $widgetInstanceFactory;

    /**
     * @var WidgetInstanceResource
     */
    private $widgetInstanceResource;

    /**
     * @param AppState $appState
     * @param BlockFactory $blockFactory
     * @param CategoryFactory $categoryFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param InstanceFactory $widgetInstanceFactory
     * @param WidgetInstanceResource $widgetInstanceResource
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        AppState                 $appState,
        BlockFactory             $blockFactory,
        CategoryFactory          $categoryFactory,
        ProductCollectionFactory $productCollectionFactory,
        InstanceFactory          $widgetInstanceFactory,
        WidgetInstanceResource   $widgetInstanceResource,
        ImageHelper              $imageHelper
    ) {
        $this->appState = $appState;
        $this->blockFactory = $blockFactory;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->widgetInstanceResource = $widgetInstanceResource;
        $this->widgetInstanceFactory = $widgetInstanceFactory;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->createCmsBlocks();
            $this->createCmsWidgets();
        }

        $setup->endSetup();
    }

    /**
     * Function to create CMS blocks with HTML tags
     *
     * @return void
     * @throws \Exception
     */
    private function createCmsBlocks()
    {
        $baseUrl = $this->getBaseUrl();

        $mainBannerBlock = $this->blockFactory->create();
        $mainBannerBlock->setTitle('Main Banner Slider')
            ->setIdentifier('main_banner_slider')
            ->setIsActive(true)
            ->save();

        $bannerImages = [
            'main_banner1.jpg',
            'main_banner2.jpg',
        ];

        $mainBannerContent = '<div class="main-banner">
            <div class="slick-carousel">';

        foreach ($bannerImages as $bannerImage) {
            $bannerImageUrl = $baseUrl . '/banners/' . $bannerImage;

            $mainBannerContent .= '
                <div class="carousel-item">
                    <img src="' . $bannerImageUrl . '" alt="Banner">
                </div>';
        }
        $mainBannerContent .= '</div>
        </div>';

        $mainBannerBlock->setContent($mainBannerContent)->save();

        $commercialAppealsBlock = $this->blockFactory->create();
        $commercialAppealsBlock->setTitle('Commercial Appeals Block')
            ->setIdentifier('commercial_appeals')
            ->setIsActive(true)
            ->save();

        $commercialAppealsImages = [
            'commercial1.png',
            'commercial2.png',
            'commercial3.png',
            'commercial4.png'
        ];

        $commercialAppealsContent = '<div class="commercial-appeals"><ul>';

        foreach ($commercialAppealsImages as $commercialImage) {
            $commercialImageUrl = $baseUrl . '/commercial_appeals/' . $commercialImage;

            $commercialAppealsContent .= '
                <li style="display: inline-block; margin: 10px;">
                    <img src="' . $commercialImageUrl . '" alt="Commercial Appeal" width="300" height="200">
                </li>
            ';
        }
        $commercialAppealsContent .= '</ul></div>';

        $commercialAppealsBlock->setContent($commercialAppealsContent)->save();

        $productCarouselContent = '<div class="product-carousel">
            <div class="slick-carousel">';

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')
            ->setPageSize(10)
            ->load();

        foreach ($productCollection as $product) {
            $productImage = $this->imageHelper->init($product, 'product_page_image_small')
                ->getUrl();
            $productName = $product->getName();
            $productUrl = $product->getProductUrl();

            $productCarouselContent .= '
                <div class="carousel-item">
                    <a href="' . $productUrl . '">
                        <img src="' . $productImage . '" alt="' . $productName . '">
                    </a>
                </div>
            ';
        }

        $productCarouselContent .= '</div>
        </div>';

        $productsCarouselBlock = $this->blockFactory->create();
        $productsCarouselBlock->setTitle('Products Carousel Block')
            ->setIdentifier('products_carousel')
            ->setContent($productCarouselContent)
            ->setIsActive(true)
            ->save();

        $gridOfBannersBlock = $this->blockFactory->create();
        $gridOfBannersBlock->setTitle('Grid of Banners Block')
            ->setIdentifier('grid_of_banners')
            ->setIsActive(true)
            ->save();

        $gridBannerImages = [
            'chairs.gif',
            'pc-gamer.gif',
            'graphics-card.gif',
            'monitors.gif'
        ];

        $gridContent = '<div class="grid-of-banners">';

        for ($i = 0; $i < count($gridBannerImages); $i += 2) {
            $gridContent .= '<div class="banner-row">';

            $bannerImageUrl1 = $baseUrl . '/banners_for_grid/' . $gridBannerImages[$i];
            $categoryUrl1 = $this->getCategoryUrlFromImageName($gridBannerImages[$i]);
            $gridContent .= '
        <div class="banner-item" style="display: inline-block; margin: 10px;">
            <a href="' . $categoryUrl1 . '">
                <img src="' . $bannerImageUrl1 . '" alt="Banner ' . ($i + 1) . '">
            </a>
        </div>';

            if (isset($gridBannerImages[$i + 1])) { // Fix the variable name here
                $bannerImageUrl2 = $baseUrl . '/banners_for_grid/' . $gridBannerImages[$i + 1];
                $categoryUrl2 = $this->getCategoryUrlFromImageName($gridBannerImages[$i + 1]);
                $gridContent .= '
            <div class="banner-item" style="display: inline-block; margin: 10px;">
                <a href="' . $categoryUrl2 . '">
                    <img src="' . $bannerImageUrl2 . '" alt="Banner ' . ($i + 2) . '">
                </a>
            </div>';
            }
            $gridContent .= '</div>';
        }

        $gridContent .= '</div>';
        $gridOfBannersBlock->setContent($gridContent)->save();

    }

    /**
     * Function to create each CMS Widget for blocks
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function createCmsWidgets()
    {
        $widgetInstance = $this->widgetInstanceFactory->create();
        $widgetInstance->setType('Magento\Cms\Block\Widget\Block')
            ->setTitle('Main Banner')
            ->setWidgetParameters('{"block_id":"main_banner"}')
            ->setSortOrder(10);
        $this->widgetInstanceResource->save($widgetInstance);

        $widgetInstance = $this->widgetInstanceFactory->create();
        $widgetInstance->setType('Magento\Cms\Block\Widget\Block')
            ->setTitle('Commercial Appeals')
            ->setWidgetParameters('{"block_id":"commercial_appeals"}')
            ->setSortOrder(20);
        $this->widgetInstanceResource->save($widgetInstance);

        $widgetInstance = $this->widgetInstanceFactory->create();
        $widgetInstance->setType('Magento\Cms\Block\Widget\Block')
            ->setTitle('Products Carousel')
            ->setWidgetParameters('{"block_id":"products_carousel"}')
            ->setSortOrder(30);
        $this->widgetInstanceResource->save($widgetInstance);

        $widgetInstance = $this->widgetInstanceFactory->create();
        $widgetInstance->setType('Magento\Cms\Block\Widget\Block')
            ->setTitle('Grid of Banners')
            ->setWidgetParameters('{"block_id":"grid_of_banners"}')
            ->setSortOrder(40);
        $this->widgetInstanceResource->save($widgetInstance);
    }

    /**
     * Function to retrieve category url based on the image name
     *
     * @param $image
     * @return string
     */
    private function getCategoryUrlFromImageName($image)
    {
        $categoryKey = pathinfo($image, PATHINFO_FILENAME);
        $category = $this->categoryFactory->create()->loadByAttribute('url_Key', $categoryKey);

        if ($category) {
            return $category->getUrl();
        }
        return '#';
    }

    /**
     * Function to retrieve the base url to reach the media directory
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBaseUrl()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . 'wysiwyg/custom_homepage';
    }
}
