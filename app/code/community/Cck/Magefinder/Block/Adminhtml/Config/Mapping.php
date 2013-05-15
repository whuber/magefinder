<?php
class Cck_Magefinder_Block_Adminhtml_Config_Mapping 
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('attribute', array(
            'label' => Mage::helper('adminhtml')->__('Attribute'),
            'style' => 'width:120px',
        ));
        $this->addColumn('search_attribute', array(
            'label' => Mage::helper('adminhtml')->__('Search Attribute'),
            'style' => 'width:120px',
        ));
        $this->addColumn('weight', array(
            'label' => Mage::helper('adminhtml')->__('Weight'),
            'style' => 'width:60px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Mapping');
        parent::__construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column    = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($columnName == 'attribute') {
            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
            $productAttributeCollection->addIsSearchableFilter();

            $html = '<select class="select" name="' . $inputName . '">';
            $html .= '<option value="">'
                    . $this->__('-- Please Select --')
                    .'</option>';
            foreach ($productAttributeCollection->getItems() as $attribute) {
                $html .= '<option value="'.$attribute->getAttributeCode().'" #{option_'.$attribute->getAttributeCode().'}>'
                    .addslashes($attribute->getFrontendLabel()).' ('.$attribute->getAttributeCode().', ' . $attribute->getFrontendInput() . ')'
                    .'</option>';
            }
            $html .= '</select>';
            return $html;

        } elseif ($columnName == 'search_attribute') {
            $searchAttributes = Mage::getConfig()->getNode('global/magefinder/attributes');
            $html = '<select class="select" name="' . $inputName . '">';
            $html .= '<option value="">'
                    . $this->__('-- Please Select --')
                    .'</option>';
            foreach ($searchAttributes->children() as $name => $title) {
                $html .= '<option value="'.$name.'" #{option_search_'.$name.'}>' . (string)$title . '</option>';
            }
            $html .= '</select>';
            return $html;

        } else {
            return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
                ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
                (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
                (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
        }
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row['option_'.$row['attribute']] = 'selected';
        $row['option_search_'.$row['search_attribute']] = 'selected';
    }
}