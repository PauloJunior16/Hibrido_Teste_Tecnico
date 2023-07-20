<?php

namespace Hibrido\ChangeButtonColor\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ChangeButtonColor Class
 */
class ChangeButtonColor extends Command
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @var ReadInterface
     */
    private $read;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ChangeButtonColor constructor.
     *
     * @param State $appState
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param string|null $name
     */
    public function __construct(
        State $appState,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        string $name = null
    ) {
        parent::__construct($name);
        $this->appState = $appState;
        $this->read = $filesystem->getDirectoryRead(DirectoryList::PUB);
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('hibrido:color:change')
            ->setDescription('Change the color of all buttons in a store view.')
            ->addArgument('color', InputArgument::REQUIRED, 'Choose the button color in hexadecimal format (ex.: FFFFFF)')
            ->addArgument('store-view-id', InputArgument::REQUIRED, 'Store View ID');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(Area::AREA_FRONTEND);

            $color = $input->getArgument('color');
            $storeViewId = $input->getArgument('store-view-id');

            if (!preg_match('/^[0-9A-Fa-f]{6}$/', $color)) {
                throw new \InvalidArgumentException('Invalid color format. Please provide a valid hex color code.');
            }

            /** @var Store $store */
            $store = $this->storeManager->getStore($storeViewId);

            $cssPath = $this->getCssPath($store);
            var_dump($cssPath);

            $cssContent = file_get_contents($cssPath);
            $newCssContent = preg_replace(
                '/(\.button,\s\.action-primary,\s\.action-secondary\s\{)[^}]+/',
                '${1}background-color: #' . $color . ';',
                $cssContent
            );

            file_put_contents($cssPath, $newCssContent);

            $output->writeln('Button color changed successfully!');

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());

            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * Get the CSS path for the selected store view.
     *
     * @param Store $store
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getCssPath(Store $store)
    {
        $storeCode = $store->getCode();

        switch ($storeCode) {
            case "pt_br":
                $localeCode = "pt_BR";
                break;
            case "en_gb":
                $localeCode = "en_GB";
                break;
            default:
                $localeCode = "en_US";
        }

        $staticPath = $this->read->getAbsolutePath("static/frontend/Magento/luma/{$localeCode}/");

        $stylesCssPath = $staticPath . 'css/styles-l.css';
        if (file_exists($stylesCssPath)) {
            return $stylesCssPath;
        }

        $stylesCssPath = $staticPath . 'css/styles-m.css';
        if (file_exists($stylesCssPath)) {
            return $stylesCssPath;
        }

        throw new \InvalidArgumentException('Unable to locate the CSS file for the given store view.');
    }
}
