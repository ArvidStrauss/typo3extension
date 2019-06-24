<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['htwddstudicockpit_studicockpitplugin'] = 'pi_flexform';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['htwddstudicockpit_studicockpitplugin'] = 'recursive,select_key,pages';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'htwddstudicockpit_studicockpitplugin',
    'FILE:EXT:htwdd_studicockpit/Configuration/FlexForms/flexform_studicockpitplugin.xml'
);


