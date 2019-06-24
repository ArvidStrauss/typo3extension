<?php
namespace AppsTeam\HtwddStudicockpit\Controller;

/***
 *
 * This file is part of the "HTWDD-Studicockpit" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Apps-Team <apps@htw-dresden.de>, HTW Dresden
 *
 ***/

/**
 * ScUtilityController
 */
class StudicockpitController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * scUtilityRepository
     * 
     * @var \AppsTeam\HtwddStudicockpit\Domain\Repository\StudicockpitRepository
     * @inject
     */
    protected $studicockpitRepository = null;

    protected function initializeAction()
    {
        $this->studicockpitRepository->setDatabase($this->settings['db_database']);
        $this->studicockpitRepository->setServer($this->settings['db_server']);
        $this->studicockpitRepository->setUsername($this->settings['db_username']);
        $this->studicockpitRepository->setPassword($this->settings['db_password']);

        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Page\\PageRenderer');

        $pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/FontAwesome/css/all.min.css');
        $pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/jQueryUI/jquery-ui.min.css');
        $pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Css/tx_htwdd_studicockpit.css');

        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/jQuery/jquery.min.js');
        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/jQueryUI/jquery-ui.min.js');
        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/jQueryValidation/jquery.validate.min.js');
        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/jQueryValidation/localization/messages_de.min.js');
        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/salvattore/salvattore.min.js');
        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Libraries/jQueryCollapser/jquery.collapser.min.js');
        $pageRenderer->addJsFooterFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("htwdd_studicockpit") . 'Resources/Public/Js/tx_htwdd_studicockpit.js');
    }

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $arguments = $this->request->getArguments();

        $usergroups = explode(',', $GLOBALS['TSFE']->fe_user->user['usergroup']);
        $usergroupConfig = array($this->settings['admin_usergroup'], $this->settings['usergroup1']);

        $usergroupIntersect = array_intersect($usergroups, $usergroupConfig);

        if(!empty($usergroupIntersect))
        {
            if(isset($arguments['message']))
                $this->addFlashMessage($arguments['message']);
            
            $query = "SELECT o.*, a.text_long FROM htwd_sc_offers_new o JOIN htwd_sc_administration_groups a ON o.administration_group_id = a.administration_group_id WHERE a.administration_group_id IN (". implode(',',$usergroupIntersect) . ")";

            $result = $this->studicockpitRepository->getRecords($query);

            foreach ($result as &$entry) 
            {
                $query = "SELECT * FROM htwd_sc_offers_categories_map_new cm JOIN htwd_sc_offers_categories_new c ON cm.offers_category_id=c.offers_category_id WHERE offer_id = ".$entry['OFFER_ID'];
                $categories = $this->studicockpitRepository->getRecords($query);
                $entry['categories'] = $categories;

                $query = "SELECT f.dtxt FROM htwd_sc_offers_faculties_map fm JOIN k_fb f ON fm.faculty_id = f.fb WHERE offer_id = ".$entry['OFFER_ID'];
                $faculties = $this->studicockpitRepository->getRecords($query);
                $entry['faculties'] = $faculties;

                $query = "SELECT dm.semester_from,dm.semester_to,d.dtxt FROM htwd_sc_offers_degrees_map dm JOIN k_abint d ON dm.degree_id = d.abint WHERE offer_id = ".$entry['OFFER_ID'];
                $degrees = $this->studicockpitRepository->getRecords($query);
                $entry['degrees'] = $degrees;
            }
            unset($entry);

            $this->view->assign('result',$result);

        }else
        {
            $this->redirect('error',null,null,array('message' => 'Sie haben keine Berechtigung. Bitte wenden Sie sich an Ihren Typo3 Beauftragten'));
        }
    }

    /**
     * action detail
     * 
     * @return void
     */
    public function detailAction()
    {
        /* will be provided by Projekt Motor once the StudiCockpit is finished */
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $query = "SELECT dtxt FROM k_fb WHERE sbearb is not null";
        $faculties = $this->studicockpitRepository->getRecords($query);

        $query = "SELECT dtxt FROM k_stg WHERE ";

        $query = "SELECT * FROM htwd_sc_offers_categories_new WHERE offers_category_id != '40'";
        $categories = $this->studicockpitRepository->getRecords($query);

        $this->view->assign('faculties',$faculties);
        $this->view->assign('categories',$categories);
    }

    /**
     * action create
     * 
     * @return void
     */
    public function createAction()
    {        
        $arguments = $this->request->getArguments();

        $query = 
        "INSERT INTO htwd_sc_offers_new 
        (
            title, administration_group_id, ad_text,    
            online_course, personal_conversation, workshop, tutorium,
            fee_required, credits_achievable, external_offer,
            visible_from, visible_to,
            link_to_enrolement, link_to_contact_person, link_to_offer, contact_person,
            last_change_date, last_change_user
        ) 
        VALUES 
        (
            '" . $arguments['title'] . "', 
            " . $GLOBALS['TSFE']->fe_user->user['usergroup'] . ", 
            '" . $arguments['adtext'] . "',
            '". ((empty($arguments['online'])) ? "f" : "t") . "', 
            '". ((empty($arguments['personal'])) ? "f" : "t") . "', 
            '". ((empty($arguments['workshop'])) ? "f" : "t") . "', 
            '". ((empty($arguments['tutorium'])) ? "f" : "t") . "',
            '". ((empty($arguments['fees'])) ? "f" : "t") . "', 
            '". ((empty($arguments['credits'])) ? "f" : "t") . "', 
            '". ((empty($arguments['external'])) ? "f" : "t") . "',
            ". ((empty($arguments['visible_from'])) ? "NULL" : "'" . date("d.m.Y", strtotime($arguments['visible_from'])) ."'") .", 
            '". ((empty($arguments['visible_to'])) ? "01.01.2099" : date("d.m.Y", strtotime($arguments['visible_to']))) . "',
            ". ((empty($arguments['link_to_enrolement'])) ? "NULL" : "'" . $arguments['link_to_enrolement'] . "'") . ", 
            ". ((empty($arguments['link_to_contact_person'])) ? "NULL" : "'" . $arguments['link_to_contact_person'] . "'") . ",
            ". ((empty($arguments['link_to_offer'])) ? "NULL" : "'" . $arguments['link_to_offer'] . "'") . ", 
            NULL,
            '".date("d.m.Y")."',
            '".$GLOBALS['TSFE']->fe_user->user['name']."'
        )";

        $resultArray = $this->studicockpitRepository->insertMainRecords($query,'offer_id');

        if($resultArray['status'])
        {
            #insert dependencies into degrees_map
            foreach ($arguments as $key => $value)
            {
                if(substr($key,0,6)=="degree")
                {
                    switch ($value) {
                        case 'bachelor':
                            $degree = 84;
                            break;
                        case 'diplom':
                            $degree = 51;
                            break;
                        case 'master':
                            $degree = 90;
                            break;    
                        default:
                            $degree = -1;
                            break;
                    }

                    if($degree > 0)
                    {
                        $semester = explode(",", $arguments['semester_'.$value]);
                        $query = "INSERT INTO htwd_sc_offers_degrees_map VALUES(".$resultArray['id'].",'".$degree."',".$semester[0].",".$semester[1].")";
                        $this->studicockpitRepository->executeManipulationQuery($query);  
                    }
                }
            }

            #Insert dependencies into categories_map_new
            foreach ($arguments as $key => $value) {
                if(substr($key,0,9)=="category_")
                {
                    if(!empty($value))
                    {
                        $query = "INSERT INTO htwd_sc_offers_categories_map_new VALUES(".$resultArray['id'].",".$value.")";
                        $this->studicockpitRepository->executeManipulationQuery($query);
                    }
                }
            }

            #Insert dependencies into faculties_map
            foreach ($arguments as $key => $value) {
                if(substr($key,0,8)=="faculty_")
                {
                    if(!empty($value))
                    {
                        $query = "INSERT INTO htwd_sc_offers_faculties_map(offer_id, faculty_id) VALUES(".$resultArray['id'].", '0".$value."')";
                        $this->studicockpitRepository->executeManipulationQuery($query);
                    }
                }
            }

            #TODO Insert dependencies into courses_map

            $this->redirect('list',null,null,array('message' => 'Angebot erfolgreich erstellt.'));

        }
        else
        {
            $this->redirect('new',null,null,array('message' => $resultArray['message']));
        }
    }

    /**
     * action edit
     * 
     * @return void
     */
    public function editAction()
    {
        $arguments = $this->request->getArguments();
        $offer_id = $arguments['offer-id'];
        $query = "SELECT o.*, a.text_long FROM htwd_sc_offers_new o JOIN htwd_sc_administration_groups a ON o.administration_group_id = a.administration_group_id WHERE o.offer_id =".$offer_id;

        $result = $this->studicockpitRepository->getRecords($query);

        foreach ($result as &$entry) 
        {
            #get which categories where selected
            $query = "SELECT * FROM htwd_sc_offers_categories_map_new cm JOIN htwd_sc_offers_categories_new c ON cm.offers_category_id=c.offers_category_id WHERE offer_id = ".$entry['OFFER_ID'];
            $categories = $this->studicockpitRepository->getRecords($query);
            if(is_array($categories))
            {
                foreach ($categories as $category)
                {
                    $entry['categories'][$category['OFFERS_CATEGORY_ID']] = $category;
                }
            }


            #get faculty-selections
            $query = "SELECT f.dtxt, fm.faculty_id FROM htwd_sc_offers_faculties_map fm JOIN k_fb f ON fm.faculty_id = f.fb WHERE offer_id = ".$entry['OFFER_ID'];
            $faculties = $this->studicockpitRepository->getRecords($query);
            if(is_array($faculties))
            {
                foreach ($faculties as $faculty)
                {
                    $entry['faculties'][$faculty['FACULTY_ID']] = $faculty;
                }
            }


            #get degrees-selection and which semester
            $query = "SELECT dm.degree_id, dm.semester_from,dm.semester_to,d.dtxt FROM htwd_sc_offers_degrees_map dm JOIN k_abint d ON dm.degree_id = d.abint WHERE offer_id = ".$entry['OFFER_ID'];
            $degrees = $this->studicockpitRepository->getRecords($query);
            if(is_array($degrees))
            {
                foreach ($degrees as $degree) 
                {
                    $entry['degrees'][$degree['DEGREE_ID']] = $degree;
                } 
            }      
        }
        unset($entry);

        #sending the result array to the view
        $this->view->assign('result',$result[0]);


        $query = "SELECT dtxt FROM k_fb WHERE sbearb is not null";
        $faculties = $this->studicockpitRepository->getRecords($query);

        $query = "SELECT dtxt FROM k_stg WHERE ";

        $query = "SELECT * FROM htwd_sc_offers_categories_new WHERE offers_category_id != '40'";
        $categories = $this->studicockpitRepository->getRecords($query);

        $this->view->assign('faculties',$faculties);
        $this->view->assign('categories',$categories);
    }

    /**
     * action update
     * 
     * @return void
     */
    public function updateAction()
    {
         $arguments = $this->request->getArguments();
         $offer_id = $arguments['offer_id'];

        #delete all entries in map tables
        $query = 
        "DELETE FROM htwd_sc_offers_categories_map_new WHERE offer_id = ".$offer_id.
        ";DELETE FROM htwd_sc_offers_degrees_map WHERE offer_id = ".$offer_id.
        ";DELETE FROM htwd_sc_offers_courses_map WHERE offer_id = ".$offer_id.
        ";DELETE FROM htwd_sc_offers_faculties_map WHERE offer_id = ".$offer_id.";";

        $this->studicockpitRepository->executeManipulationQuery($query);

        #update ALL Attributes
        $query =
        "update htwd_sc_offers_new 
        set title = '" . $arguments['title'] . "',
        ad_text = '" . $arguments['adtext'] . "',
        online_course = '" . ((empty($arguments['online'])) ? "f" : "t") . "',
        personal_conversation = '". ((empty($arguments['personal'])) ? "f" : "t") . "', 
        workshop = '". ((empty($arguments['workshop'])) ? "f" : "t") . "', 
        tutorium = '". ((empty($arguments['tutorium'])) ? "f" : "t") . "',
        fee_required = '". ((empty($arguments['fees'])) ? "f" : "t") . "', 
        credits_achievable = '". ((empty($arguments['credits'])) ? "f" : "t") . "', 
        external_offer = '". ((empty($arguments['external'])) ? "f" : "t") . "',
        visible_from = ". ((empty($arguments['visible_from'])) ? "NULL" : "'" . date("d.m.Y", strtotime($arguments['visible_from'])) ."'") .", 
        visible_to = '". ((empty($arguments['visible_to'])) ? "01.01.2099" : date("d.m.Y", strtotime($arguments['visible_to']))) . "',
        link_to_enrolement = ". ((empty($arguments['link_to_enrolement'])) ? "NULL" : "'" . $arguments['link_to_enrolement'] . "'") . ", 
        link_to_contact_person = ". ((empty($arguments['link_to_contact_person'])) ? "NULL" : "'" . $arguments['link_to_contact_person'] . "'") . ",
        link_to_offer = ". ((empty($arguments['link_to_offer'])) ? "NULL" : "'" . $arguments['link_to_offer'] . "'") . ",
        last_change_date = '".date("d.m.Y")."',
        last_change_user = '".$GLOBALS['TSFE']->fe_user->user['name']."'
        where offer_id = " . $offer_id;

        $this->studicockpitRepository->executeManipulationQuery($query);

        #insert all Map referencial records AGAIN
        foreach ($arguments as $key => $value)
        {
            if(substr($key,0,6)=="degree")
            {
                switch ($value) {
                    case 'bachelor':
                        $degree = 84;
                        break;
                    case 'diplom':
                        $degree = 51;
                        break;
                    case 'master':
                        $degree = 90;
                        break;    
                    default:
                        $degree = -1;
                        break;
                }

                if($degree > 0)
                {
                    $semester = explode(",", $arguments['semester_'.$value]);
                    $query = "INSERT INTO htwd_sc_offers_degrees_map VALUES(".$offer_id.",'".$degree."',".$semester[0].",".$semester[1].")";
                    $this->studicockpitRepository->executeManipulationQuery($query);  
                }
            }
        }


        #Insert dependencies into categories_map_new
        foreach ($arguments as $key => $value) {
            if(substr($key,0,9)=="category_")
            {
                if(!empty($value))
                {
                    $query = "INSERT INTO htwd_sc_offers_categories_map_new VALUES(".$offer_id.",".$value.")";
                    $this->studicockpitRepository->executeManipulationQuery($query);
                }
            }
        }


        #Insert dependencies into faculties_map
        foreach ($arguments as $key => $value) {
            if(substr($key,0,8)=="faculty_")
            {
                if(!empty($value))
                {
                    $query = "INSERT INTO htwd_sc_offers_faculties_map(offer_id, faculty_id) VALUES(".$offer_id.", '0".$value."')";
                    $this->studicockpitRepository->executeManipulationQuery($query);
                }
            }
        }

        $this->redirect('list',null,null,array('message' => 'Angebot erfolgreich geändert.'));
    }

    /**
     * action delete
     * 
     * @return void
     */
    public function deleteAction()
    {
         $arguments = $this->request->getArguments();
         $offer_id = $arguments['offer-id'];
         
         $this->view->assign('offer-id',$offer_id);
         $this->view->assign('title',$arguments['title']);

         if($arguments['confirmed']=='true')
         {
            $query = 
            "DELETE FROM htwd_sc_offers_categories_map_new WHERE offer_id =".$offer_id. 
            ";DELETE FROM htwd_sc_offers_degrees_map WHERE offer_id = ".$offer_id.
            ";DELETE FROM htwd_sc_offers_courses_map WHERE offer_id = ".$offer_id.   
            ";DELETE FROM htwd_sc_offers_faculties_map WHERE offer_id = ".$offer_id.
            ";DELETE FROM htwd_sc_offers_new WHERE offer_id = ".$offer_id.";";

            $this->studicockpitRepository->executeManipulationQuery($query);

            $this->redirect('list',null,null,array('message' => 'Angebot '.$arguments['title'].' gelöscht.'));
         }
    }

    /**
     * action hide
     * 
     * @return void
     */
    public function hideAction()
    {
        /* not sure if needed */
    }

    public function errorAction()
    {
        $arguments = $this->request->getArguments();
        if(isset($arguments['message'])) {
            $this->addFlashMessage($arguments['message']);
        }
    }
}
