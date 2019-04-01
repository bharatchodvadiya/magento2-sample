<?php
namespace SM\Vendors\Block\Adminhtml\Page\Edit\Tab;

/**
 * Vendor page edit form content tab
 */
class Content extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const TAB_LABEL = 'Content';
    const VENDORS_PAGE = 'vendors_page';
    const PAGE_MAIN = 'page_';
    const CONTENT_FIELDSET = 'content_fieldset';
    const LEGEND = 'legend';
    const FORM_CLASS = 'class';
    const FIELDSET_WIDE = 'fieldset-wide';
    const TAB_ID = 'tab_id';
    const VENDORS_SAVE = 'SM_Vendors::save';
    const RENDERER_BLOCK = 'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element';
    const RENDERER_TEMPLATE = 'Magento_Cms::page/edit/form/renderer/content.phtml';
    const FORM_EVENT = 'adminhtml_page_edit_tab_content_prepare_form';
    const FORM = 'form';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_wysiwygConfig;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Framework\Registry $registryData
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $formData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $formData = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($contextData, $registryData, $formFactory, $formData);
    }

    /**
     * Prepare page form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $pageModel = $this->_coreRegistry->registry(self::VENDORS_PAGE);
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction(self::VENDORS_SAVE)) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $pageForm = $this->_formFactory->create();
        $pageForm->setHtmlIdPrefix(self::PAGE_MAIN);
        $fieldsetData = $pageForm->addFieldset(self::CONTENT_FIELDSET, [self::LEGEND => __(self::TAB_LABEL), self::FORM_CLASS => self::FIELDSET_WIDE]);
        $wysiwygConfig = $this->_wysiwygConfig->getConfig([self::TAB_ID => $this->getTabId()]);
        $contentField = $fieldsetData->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'style' => 'height:36em;',
                'required' => true,
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
        // Setting custom renderer for content field to remove label column
        $contentRenderer = $this->getLayout()->createBlock(self::RENDERER_BLOCK)
        ->setTemplate(self::RENDERER_TEMPLATE);
        $contentField->setRenderer($contentRenderer);
        $this->_eventManager->dispatch(self::FORM_EVENT, [self::FORM => $pageForm]);
        $pageForm->setValues($pageModel->getData());
        $this->setForm($pageForm);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __(self::TAB_LABEL);
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __(self::TAB_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}