<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$initConfig = array(
    array(
        'attribute' => 'name',
        'search_attribute' => 'name',
        'weight' => '',
    ),
    array(
        'attribute' => 'short_description',
        'search_attribute' => 'short_description',
        'weight' => '',
    ),
    array(
        'attribute' => 'description',
        'search_attribute' => 'description',
        'weight' => '',
    ),
    array(
        'attribute' => 'price',
        'search_attribute' => 'price',
        'weight' => '',
    ),
);

$installer->setConfigData('magefinder/advanced/mapping', serialize($initConfig));
