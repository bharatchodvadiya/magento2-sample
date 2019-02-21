<?php
namespace SM\Vendors\Block\Adminhtml\Banner\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractRenderer
{
    private $_storeManager;

    /**
     * @param \Magento\Backend\Block\Context $contextData
     * @param StoreManagerInterface $storeManager
     * @param array $imageData
     */
    public function __construct(
        \Magento\Backend\Block\Context $contextData,
        StoreManagerInterface $storeManager,
        array $imageData = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($contextData, $imageData);
    }

    /**
     * Renders grid column
     *
     * @param Object $rowData
     * @return string
     */
    public function render(DataObject $rowData)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $imageUrl = $mediaDirectory.$this->_getValue($rowData);
        $popupLink = "popWin('$imageUrl','image','width=800,height=600,resizable=yes,scrollbars=yes')";
        return '<a href="javascript:;" onclick="'.$popupLink.'"><img src="'.$imageUrl.'" style="border: 2px solid #CCCCCC;" width="80" height="60"/></a>';
    }
}