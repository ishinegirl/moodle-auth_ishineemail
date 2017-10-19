<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'auth_ishineemail', language 'en'.
 *
 * @package   auth_ishineemail
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_ishineemaildescription'] = '<p>iShine Email-based self-registration enables a user to create their own account via a \'Create new account\' button on the login page. The user then receives an email containing a secure link to a page where they can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.</p><p>Note: In addition to enabling the plugin, email-based self-registration must also be selected from the self registration drop-down menu on the \'Manage authentication\' page.</p>';
$string['auth_ishineemailnoemail'] = 'Tried to send you an email but failed!';
$string['auth_ishineemailrecaptcha'] = 'Adds a visual/audio confirmation form element to the sign-up page for email self-registering users. This protects your site against spammers and contributes to a worthwhile cause. See http://www.google.com/recaptcha for more details.';
$string['auth_ishineemailrecaptcha_key'] = 'Enable reCAPTCHA element';
$string['auth_ishineemailsettings'] = 'Settings';
$string['pluginname'] = 'iShine Email-based self-registration';

$string['postcode']='郵便番号';
$string['prefecture']='都道府県';
$string['missingpostcode']='「郵便番号」は必須項目';
$string['missingphone']='「電話」は必須項目';
$string['nofunnysymbols']='ハイフンなしで入力して下さい。';