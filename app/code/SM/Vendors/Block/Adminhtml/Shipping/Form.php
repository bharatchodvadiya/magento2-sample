<?php
namespace SM\Vendors\Block\Adminhtml\Shipping\Edit;

/**
 * Adminhtml shipping method edit form block
 *
 * @author Bharat Chodvadiya <bharat.chodvadiya@gmail.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const OBJECT_ID = 'id';
    const EDIT_FORM = 'edit_form';
    const DATA = 'data';
    const NAME = 'name';
    const ACTION = 'action';
    const METHOD = 'method';
    const POST_METHOD = 'post';
    const ENCTYPE = 'enctype';
    const MULTI_FORM_DATA = 'multipart/form-data';

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** 
         * @var \Magento\Framework\Data\Form $shippingForm 
         */
        $shippingForm = $this->_formFactory->create(
            [self::DATA => [self::OBJECT_ID => self::EDIT_FORM, self::NAME => self::EDIT_FORM, self::ACTION => $this->getData(self::ACTION), self::METHOD => self::POST_METHOD, self::ENCTYPE => self::MULTI_FORM_DATA]]
        );
        $shippingForm->setUseContainer(true);
        $this->setForm($shippingForm);
        return parent::_prepareForm();
    }
}