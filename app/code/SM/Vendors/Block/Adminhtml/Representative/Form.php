<?php
namespace SM\Vendors\Block\Adminhtml\Representative\Edit;

/**
 * Adminhtml representative edit form block
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

    /**
     * Prepare representative form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** 
         * @var \Magento\Framework\Data\Form $representativeForm 
         */
        $representativeForm = $this->_formFactory->create(
            [self::DATA => [self::OBJECT_ID => self::EDIT_FORM, self::NAME => self::EDIT_FORM, self::ACTION => $this->getData(self::ACTION), self::METHOD => self::POST_METHOD]]
        );
        $representativeForm->setUseContainer(true);
        $this->setForm($representativeForm);
        return parent::_prepareForm();
    }
}