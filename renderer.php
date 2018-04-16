<?php


class auth_digicode_renderer extends plugin_renderer_base {

    public function login_form($session) {
        global $CFG;

        $config = get_config('auth_digicode');

        $template = new StdClass;
        $template->username = get_string('username');
        $template->digicode = get_string('digicode', 'auth_digicode');
        $template->loginstr = get_string('login', 'auth_digicode');
        $template->profilestr = get_string('profiling', 'auth_digicode');
        $template->otherusersstr = get_string('otherusers', 'auth_digicode');
        $template->loginurl = new moodle_url('/login/index.php');
        $template->debug = $CFG->debug == DEBUG_DEVELOPER;
        $template->isrunning = $session->is_running;

        $digits = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        shuffle($digits);

        $i = 0;
        foreach ($digits as $digit) {
            $key = 'digit'.$i;
            $template->$key = $digit;
            $i++;
        }

        $template->name = $session->name;

        if (!empty($session->instructions)) {
            $template->instructions = format_text($session->instructions, $session->instructionsformat);
        }

        $secs = $session->sessiontime - time();

        $template->chrono = '
            <div id="chrono-display"></div>
        ';

        echo $this->output->render_from_template('auth_digicode/login', $template);

    }

}