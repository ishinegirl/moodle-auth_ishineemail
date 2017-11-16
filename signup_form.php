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
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');

class ishineemail_login_signup_form extends moodleform implements renderable, templatable{
    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'createuserandpass', get_string('createuserandpass'), '');


        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');
        $mform->addElement('static', 'usernamepolicyinfo', '', get_string('invalidusername'));

        $mform->addElement('passwordunmask', 'password', get_string('password'), 'maxlength="32" size="12"');
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');
        
        if (!empty($CFG->passwordpolicy)){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }

        $mform->addElement('header', 'supplyinfo', get_string('supplyinfo'),'');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');

        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }
        
        //都道府県
        $prefs = \auth_ishineemail\helper::fetch_prefecture_list();
        $mform->addElement('select', 'city', get_string('prefecture','auth_ishineemail'), $prefs,'');
		$mform->addRule('city', get_string('missingcity','auth_ishineemail'), 'required', null, 'client');
        
         //Postcode
        $mform->addElement('text', 'address', get_string('postcode','auth_ishineemail'), 'maxlength="10" size="20"');
        $mform->setType('address', PARAM_INT);
        $mform->addRule('address', get_string('missingpostcode','auth_ishineemail'), 'required', null, 'client');
        $mform->addElement('static', 'numbersonly', '', get_string('nofunnysymbols','auth_ishineemail'));
         $mform->addRule('address',null, 'numeric', null, 'client');
    
        //Telephone number
        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="20"');
        $mform->setType('phone1', core_user::get_property_type('phone1'));
        $mform->addRule('phone1', get_string('missingphone','auth_ishineemail'), 'required', null, 'client');
        $mform->addElement('static', 'numbersonly', '', get_string('nofunnysymbols','auth_ishineemail'));
        $mform->addRule('phone1',null, 'numeric', null, 'client');

/*
        $country = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);
        if( !empty($CFG->country) ){
            $mform->setDefault('country', $CFG->country);
        }else{
            $mform->setDefault('country', '');
        }
*/
        //all our users are Japanese
        $mform->addElement('hidden', 'country', 'JP');
        $mform->setType('country', PARAM_TEXT);
        //we use the URL field to pass to CRM to distinguish between self-enroled users
        //and other users
        $mform->addElement('hidden', 'url', 'ispc-x.jp');
        $mform->setType('url', PARAM_TEXT);
        
        if ($this->signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'), array('https' => $CFG->loginhttps));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        if (!empty($CFG->sitepolicy)) {
            $mform->addElement('header', 'policyagreement', get_string('policyagreement'), '');
            $mform->setExpanded('policyagreement');
            $mform->addElement('static', 'policylink', '', '<a href="'.$CFG->sitepolicy.'" onclick="this.target=\'_blank\'">'.get_String('policyagreementclick').'</a>');
            $mform->addElement('checkbox', 'policyagreed', get_string('policyaccept'));
            $mform->addRule('policyagreed', get_string('policyagree'), 'required', null, 'client');
        }
        // buttons
        $this->add_action_buttons(true, get_string('createaccount'));

        //warning message about email resent
		//$mform->addElement('static', 'cantresend', '<span class="auth_ishineemail_havingtroubleheader">' . get_string('havingtrouble','auth_ishineemail') . '<span>', get_string('cantresend','auth_ishineemail'));
        $mform->addElement('static', 'cantresend', '' , get_string('cantresend','auth_ishineemail'));

    }

    function definition_after_data(){
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    function validation($data, $files) {
        global $CFG, $DB;
        $errors = parent::validation($data, $files);

        $authplugin = get_auth_plugin($CFG->registerauth);

        if ($DB->record_exists('user', array('username'=>$data['username'], 'mnethostid'=>$CFG->mnet_localhost_id))) {
            $errors['username'] = get_string('usernameexists');
        } else {
            //check allowed characters
            if ($data['username'] !== core_text::strtolower($data['username'])) {
                $errors['username'] = get_string('usernamelowercase');
            } else {
                if ($data['username'] !== core_user::clean_field($data['username'], 'username')) {
                    $errors['username'] = get_string('invalidusername');
                }

            }
        }

        //check if user exists in external db
        //TODO: maybe we should check all enabled plugins instead
        if ($authplugin->user_exists($data['username'])) {
            $errors['username'] = get_string('usernameexists');
        }


        if (! validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');

        } else if ($DB->record_exists('user', array('email'=>$data['email']))) {
            $errors['email'] = get_string('emailexists').' <a href="forgot_password.php">'.get_string('newpassword').'?</a>';
        }
        if (empty($data['email2'])) {
            $errors['email2'] = get_string('missingemail');

        } else if ($data['email2'] != $data['email']) {
            $errors['email2'] = get_string('invalidemail');
        }
        if (!isset($errors['email'])) {
            if ($err = email_is_not_allowed($data['email'])) {
                $errors['email'] = $err;
            }

        }

        $errmsg = '';
        if (!check_password_policy($data['password'], $errmsg)) {
            $errors['password'] = $errmsg;
        }

        if ($this->signup_captcha_enabled()) {
            $recaptcha_element = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['recaptcha_challenge_field'])) {
                $challenge_field = $this->_form->_submitValues['recaptcha_challenge_field'];
                $response_field = $this->_form->_submitValues['recaptcha_response_field'];
                if (true !== ($result = $recaptcha_element->verify($challenge_field, $response_field))) {
                    $errors['recaptcha'] = $result;
                }
            } else {
                $errors['recaptcha'] = get_string('missingrecaptchachallengefield');
            }
        }
        // Validate customisable profile fields. (profile_validation expects an object as the parameter with userid set)
        $dataobject = (object)$data;
        $dataobject->id = 0;
        $errors += profile_validation($dataobject, $files);

        return $errors;

    }

    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @return bool
     */
    function signup_captcha_enabled() {
        global $CFG;
        $authplugin = get_auth_plugin($CFG->registerauth);
        return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && $authplugin->is_captcha_enabled();
    }

/*
* Export the data so it can be used as content in a mustache template
*
*/
public function export_for_template(renderer_base $output){
    ob_start();
    $this->display();
    $formhtml = ob_get_contents();
    ob_end_clean();
    $context = ['formhtml'=>$formhtml];
    return $context;
}//end of function

}//end of class