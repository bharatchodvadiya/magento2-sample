<?php
namespace SM\Vendors\Block\Adminhtml\Vendor\Edit;
/**
 * Adminhtml vendor manager edit form block
 *
 * @author Bharat Chodvadiya <bharat.chodvadiya@gmail.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const OBJECT_ID = 'id';
    const EDIT_FORM = 'edit_form';
    const DATA = 'data';
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
         * @var \Magento\Framework\Data\Form $form 
         */
        $form = $this->_formFactory->create(
            [self::DATA => [self::OBJECT_ID => self::EDIT_FORM, self::ACTION => $this->getData(self::ACTION), self::METHOD => self::POST_METHOD, self::ENCTYPE => self::MULTI_FORM_DATA]]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}