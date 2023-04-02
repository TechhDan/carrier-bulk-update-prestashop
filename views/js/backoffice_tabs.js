/**
* 2017 WebDevOverture
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@webdevoverture.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade WebDevOverture to newer
* versions in the future. If you wish to customize WebDevOverture for your
* needs please refer to http://www.webdevoverture.com for more information.
*
*  @author    WebDevOverture <contact@webdevoverture.com>
*  @copyright 2017 WebDevOverture
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of WebDevOverture
*
*/
$(document).ready(function() {
    $("#CBU_METHOD_CATEGORIES_on").click(function(){
        $("#fieldset_0 > div.form-wrapper > div:nth-child(3)").show();
        $("#fieldset_0 > div.form-wrapper > div:nth-child(2)").hide();
    });

    $("#CBU_METHOD_CATEGORIES_off").click(function(){
        $("#fieldset_0 > div.form-wrapper > div:nth-child(2)").show();
        $("#fieldset_0 > div.form-wrapper > div:nth-child(3)").hide();
    });

    if ($('#CBU_METHOD_CATEGORIES_on:checked').val()) {
        $("#fieldset_0 > div.form-wrapper > div:nth-child(3)").show();
        $("#fieldset_0 > div.form-wrapper > div:nth-child(2)").hide();
    } else {
        $("#fieldset_0 > div.form-wrapper > div:nth-child(3)").hide();
        $("#fieldset_0 > div.form-wrapper > div:nth-child(2)").show();
    };
});
