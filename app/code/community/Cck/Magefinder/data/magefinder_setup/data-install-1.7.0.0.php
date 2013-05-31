<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$initConfig = array(
    array(
        'attribute' => 'name',
        'search_attribute' => 'name',
    ),
    array(
        'attribute' => 'sku',
        'search_attribute' => 'sku',
    ),
    array(
        'attribute' => 'short_description',
        'search_attribute' => 'short_description',
    ),
    array(
        'attribute' => 'description',
        'search_attribute' => 'description',
    ),
    array(
        'attribute' => 'price',
        'search_attribute' => 'price',
    ),
);

$installer->setConfigData('magefinder/advanced/mapping', serialize($initConfig));
