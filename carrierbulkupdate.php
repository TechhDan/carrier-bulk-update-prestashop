<?php
/**
* 2017 TechDesign
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@techdesign.io so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade TechDesign to newer
* versions in the future. If you wish to customize TechDesign for your
* needs please refer to http://techdesign.io for more information.
*
*  @author    TechDesign <admin@techdesign.io>
*  @copyright 2017 TechDesign
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of TechDesign
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class CarrierBulkUpdate extends Module
{
    protected $html = '';

    public function __construct()
    {
        $this->name = 'carrierbulkupdate';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->author = 'TechDesign';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '6959cd7a0e783663ba29a63567a33668';
        parent::__construct();
        $this->displayName = $this->l('Carrier Bulk Update');
        $this->description = $this->l('Automatically assign new carriers to all products');
    }
    
    public function install()
    {
        Configuration::updateValue('CBU_METHOD_CATEGORIES', true);
        return parent::install();
    }
    
    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->context->controller->addJS($this->_path . 'views/js/backoffice_tabs.js');
        if (Tools::isSubmit('submitUpdate')) {
            $this->submitPost();
            $this->html .= $this->_errors ?
                $this->displayError(implode($this->_errors, '<br />')) :
                $this->displayConfirmation($this->l('Settings Updated!'));
        }
        $this->html .= $this->displayInfosPage();
        $this->html .= $this->renderForm();
        return $this->html;
    }

    private function displayInfosPage()
    {
        $logo = Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/views/img/carrierbulkupdate_logo.png';
        $this->context->smarty->assign('carrierbulkupdate_logo', $logo);
        return $this->display(__FILE__, 'infos.tpl');
    }

    protected function renderForm()
    {
        $fields_form = array();
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $carriers = Carrier::getCarriers($this->context->language->id);

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use Categories'),
                    'name' => 'CBU_METHOD_CATEGORIES',
                    'desc' => $this->l('Use category method to assign new carriers'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products IDs'),
                    'name' => 'CBU_PRODUCT_IDS',
                    'desc' => $this->l('Add Product ID separated by comma')
                ),
                array(
                    'type' => 'categories',
                    'label' => $this->l('Categories'),
                    'name' => 'CBU_ASSIGN_CATEGORIES',
                    'desc' => $this->l('Select the category to which assign new category'),
                    'tree' => array(
                        'root_category' => 1,
                        'id' => 'id_category',
                        'name' => 'name_category',
                        'selected_categories' => array(),
                    )
                ),
                array(
                    'type' => 'select',
                    'multiple' => true ,
                    'label' => $this->l('Carrier'),
                    'name' => 'CBU_ASSIGN_CARRIER[]',
                    'desc' => 'Select the carrier to add',
                    'options' => array(
                        'query' => $carriers,
                        'id' => 'id_reference',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Assign carrier'),
                    'name' => 'CBU_METHOD_ADD',
                    'desc' => $this->l('Select "No" to remove previously assigned carrier'),
                    'default' => 1,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Assign'),
                'class' => 'btn btn-default pull-right'
            ),
        );

        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        // Title and toolbar
        $helper->title = null;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submitUpdate';

        // Load current value
        $helper->fields_value['CBU_METHOD_CATEGORIES'] = Configuration::get('CBU_METHOD_CATEGORIES');
        $helper->fields_value['CBU_METHOD_ADD'] = true;
        $helper->fields_value['CBU_PRODUCT_IDS'] = null;
        $helper->fields_value['CBU_ASSIGN_CARRIER[]'] = array();

        return $helper->generateForm($fields_form);
    }

    private function submitPost()
    {
        Configuration::updateValue('CBU_METHOD_CATEGORIES', Tools::getValue('CBU_METHOD_CATEGORIES'));

        if (!Tools::getValue('CBU_ASSIGN_CARRIER')) {
            $this->_errors[] = $this->l('You must select a carrier.');
            return;
        }

        // Categories method
        if (Tools::getValue('CBU_METHOD_CATEGORIES')) {
            $categoryId = Tools::getValue('CBU_ASSIGN_CATEGORIES');
            if (!$categoryId) {
                $this->_errors[] = $this->l('You must select a category.');
                return;
            }
            $category = new Category((int) $categoryId);
            $products = $category->getProductsWs();
            if (!$products) {
                $this->_errors[] = Tools::displayError('No product found in the chosen category');
                return;
            }
            $productIds = array();
            foreach ($products as $idpr) {
                $productIds[] = $idpr['id'];
            }
        }

        // Product IDs array method
        if (!Tools::getValue('CBU_METHOD_CATEGORIES')) {
            $productIds = Tools::getValue('CBU_PRODUCT_IDS');
            if (!preg_match("/^[0-9,]+$/", $productIds)) {
                $this->_errors[] = $this->l('Invalid Product ID entry. Remove any spaces.');
                return;
            }
            $productIds = explode(',', $productIds);
        }

        if (isset($productIds)) {
            foreach (Tools::getValue('CBU_ASSIGN_CARRIER') as $assignCarrierId) {
                $this->assignCarrier($productIds, (int) $assignCarrierId);
            }
        }
    }

    private function assignCarrier($productIds, $assignCarrierId)
    {
        $query_length = 0;
        Db::getInstance(_PS_USE_SQL_SLAVE_)->query('START TRANSACTION;');
        foreach ($productIds as $productId) {
            if (Tools::getValue('CBU_METHOD_ADD')) {
                $sql =
                    'INSERT IGNORE INTO '._DB_PREFIX_.'product_carrier (id_product, id_carrier_reference, id_shop)'.
                    ' VALUES ('.(int) $productId.',' . $assignCarrierId .','.(int) $this->context->shop->id.');';
            } else {
                $sql =
                    'DELETE FROM '._DB_PREFIX_.'product_carrier'.
                    ' WHERE id_product = '.(int) $productId.' AND id_carrier_reference = ' . $assignCarrierId .
                    ' AND id_shop = '.(int) $this->context->shop->id;
            }
            $query_length++;
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);
            if (!$res) {
                $this->_errors[] =
                    Tools::displayError("Unable to assign/unassign carrier to Product ID {$productId}. Error: ").
                    Db::getInstance()->getMsgError();
                break;
            }
            if ($query_length == 500) {
                Db::getInstance()->query('COMMIT');
                $query_length = 0;
            }
        }
        if (!Db::getInstance()->query('COMMIT')) {
            $this->_errors[] = Tools::displayError('Error: ').Db::getInstance()->getMsgError();
        }
    }
}
