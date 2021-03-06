<?php

/**
 *
 * Copyright © 2015 TemplateMonster. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace TemplateMonster\FilmSlider\Block\Adminhtml\Slider\Edit\Tab\Slides;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class SliderItemGrid extends Extended
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \TemplateMonster\FilmSlider\Model\SliderItem
     */
    protected $_sliderItemFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \TemplateMonster\FilmSlider\Model\SliderItem $sliderItemFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_sliderItemFactory = $sliderItemFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('slider_items');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    protected function getSlider()
    {
        return $this->_coreRegistry->registry(\TemplateMonster\FilmSlider\Api\Data\SliderInterface::REGISTRY_NAME);
    }

    protected function _prepareCollection()
    {
        $sliderModel = $this->_coreRegistry->registry('film_slider');

        $collection = $this->_sliderItemFactory->getCollection();
        $collection->addFieldToSelect('*');
        if ($sliderModel) {
            $collection->addFieldToFilter('parent_id', $sliderModel->getId());
        }
        $this->setCollection($collection);

        return parent::_prepareCollection(); // TODO: Change the autogenerated stub
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title',
            [
                'header' => __('Title'),
                'sortable' => true,
                'index' => 'title',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]);

        $this->addColumn('image',
            [
                'header' => __('Image'),
                'sortable' => false,
                'filter' => false,
                'index' => 'image',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => '\TemplateMonster\FilmSlider\Block\Adminhtml\Slider\Edit\Tab\Slides\Column\Image'
            ]);
        $this->addColumn('action',
            [
                'header' => __('Action'),
                'width' => 15,
                'sortable' => false,
                'filter' => false,
                'type' => 'action',
                'renderer' => '\TemplateMonster\FilmSlider\Block\Adminhtml\Slider\Edit\Tab\Slides\Column\Edit'
            ]);
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        $sliderModel = $this->_coreRegistry->registry('film_slider');
        return $this->getUrl('filmslider/*/grid', ['slider_id'=>$sliderModel->getId(), '_current' => true]);
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $sliderModel = $this->_coreRegistry->registry('film_slider');

        if (!$sliderModel || !$sliderModel->getId()) {
            return $html;
        }

        $url = $this->getUrl('filmslider/slideritem/new', ['parent_id'=>$sliderModel->getId()]);
        return $html.$this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'parent_id',
                'label' => __('Add Slider Item'),
                'type' => 'button',
                'title' => __('Add Slider Item'),
                'class' => 'slider-item-grid',
                'onclick' => "setLocation('$url')",
            ]
        )->toHtml();
    }
}
