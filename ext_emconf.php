<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'HTWDD Studicockpit', /*name in typo3 extension manager */
    'description' => 'Plugin zur Anzeige und Verwaltung von Angeboten für das Studicockpit', /*mouseover in backend*/
    'category' => 'plugin',
    'author' => 'Arvid Strauss',
    'author_email' => 'arvid.strauss@htw-dresden.de',
    'state' => 'alpha', /*später ÄNDERN */
    'uploadfolder' => 0, /*im root folder upload folder erstellen z.B. um Bilder hochzuladen - hier nicht benötigt*/
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99', /*wichtig für öffentliche Bereitstellung der Ext., rote INformation an Benutzer, dass die Ext nicht funktioniert, HTW Homepage ist aktuell v6.2.0, wird aber im Sommer 2019 auf v9.x.x umgestellt*/
        ],
        'conflicts' => [], /* mit welchen anderen extensions ein Konflikt bestehen könnte*/
        'suggests' => [], /*empfehlung für komplementär extensions*/
    ],
];
