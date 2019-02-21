<?php
namespace SM\Vendors\Block\Adminhtml\Banner;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    const OBJECT_ID = 'id';
    const BLOCK_GROUP = 'SM_Vendors';
    const ADMIN_BANNER = 'adminhtml_banner';
    const SAVE_BANNER = 'Save Banner';
    const DELETE_BANNER = 'Delete Banner';
    const SAVE_CONTINUE_EDIT = 'Save and Continue Edit';
    const LABEL = 'label';
    const SAVE_BUTTON = 'save';
    const DELETE_BUTTON = 'delete';
    const SAVE_CONTINUE = 'saveandcontinue';
    const VENDORS_BANNER = 'vendors_banner';
    const EDIT_BANNER = "Edit Banner '%1'";
    const ADD_BANNER = 'Add Banner';
    const BUTTON_CLASS = 'class';
    const DATA_ATTRIBUTE = 'data_attribute';
    const MAGE_INIT = 'mage-init';
    const BUTTON = 'button';
    const EVENT = 'event';
    const SAVE_AND_CONTINUE = 'saveAndContinueEdit';
    const TARGET = 'target';
    const EDIT_FORM = '#edit_form';
    const VENDORS_SAVE = 'vendors/*/save';
    const CURRENT = '_current';
    const BACK = 'back';
    const EDIT = 'edit';
    const ACTIVE_TAB = 'active_tab';
    const TAB_ID = '{{tab_id}}';

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $contextData
     * @param \Magento\Framework\Registry $registryData
     * @param array $gridData
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $contextData,
        \Magento\Framework\Registry $registryData,
        array $gridData = []
    ) {
        $this->_coreRegistry = $registryData;
        parent::__construct($contextData, $gridData);
    }

	/**
     * Initialize banners
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = self::OBJECT_ID;
        $this->_blockGroup = self::BLOCK_GROUP;
        $this->_controller = self::ADMIN_BANNER;
        parent::_construct();
        $this->buttonList->update(self::SAVE_BUTTON, self::LABEL, __(self::SAVE_BANNER));
        $this->buttonList->add(
            self::SAVE_CONTINUE,
            [
                self::LABEL => __(self::SAVE_CONTINUE_EDIT),
                self::BUTTON_CLASS => self::SAVE_BUTTON,
                self::DATA_ATTRIBUTE => [
                    self::MAGE_INIT => [
                        self::BUTTON => [self::EVENT => self::SAVE_AND_CONTINUE, self::TARGET => self::EDIT_FORM],
                    ],
                ]
            ],
            -100
        );
        $this->buttonList->update(self::DELETE_BUTTON, self::LABEL, __(self::DELETE_BANNER));
	}

    /**
     * Retrieve text for header element depending on loaded banner
     * 
     * @return string
     */
    public function getHeaderText()
    {
        $bannerRegistry = $this->_coreRegistry->registry(self::VENDORS_BANNER);
        if ($bannerRegistry->getId()) {
            $bannerTitle = $this->escapeHtml($bannerRegistry->getTitle());
            return __(self::EDIT_BANNER, $bannerTitle);
        } else {
            return __(self::ADD_BANNER);
        }
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(self::VENDORS_SAVE, [self::CURRENT => true, self::BACK => self::EDIT, self::ACTIVE_TAB => self::TAB_ID]);
    }
}