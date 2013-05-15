<?php

class Cck_Magefinder_Model_System_Config_Backend_Mapping extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            Mage::getSingleton('index/indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
