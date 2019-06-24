<?php
defined('TYPO3_MODE') || die('Access denied.');


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AppsTeam.HtwddStudicockpit',
    'StudicockpitPlugin', 
    [
        'Studicockpit' => 'list, detail, new, create, edit, update, delete, error' 
    ],
    [
        'Studicockpit' => 'list, detail, new, create, edit, update, delete, error'
    ]
);
