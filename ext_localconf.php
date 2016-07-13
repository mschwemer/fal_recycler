<?php
defined('TYPO3_MODE') or die('Access denied.');

/**
 * Override Local-Filesystem Driver class
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['registeredDrivers']['Local']['class'] = In2code\FalRecycler\Resource\Driver\LocalDriver::class;
