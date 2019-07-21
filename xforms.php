<?php

use CMSFactory\assetManager;
use cmsemail\email;
use CMSFactory\ModuleSettings;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * @property Xforms_model xforms_model
 */
class Xforms extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->module('core');
        $lang = new MY_Lang();
        $lang->load('xforms');
        $this->load->model('xforms_model');
        $this->load->library('form_validation');

        $this->form_validation->set_message('required', lang('This field is required', 'xforms')); // Поле обязательно для заполнения

        $this->form_validation->set_message('valid_phone', lang('The field must contain the correct phone number. Spaces, hyphens and parentheses are not allowed', 'xforms')); // Поле должно содержать корректный номер телефона. Не допускаются пробелы, дефисы и скобки
        $this->form_validation->set_message('valid_date', lang('The field must contain the correct date', 'xforms')); // Поле должно содержать правильную дату
        $this->form_validation->set_message('valid_time', lang('The field must contain the correct time', 'xforms')); // Поле должно содержать правильное время

        $this->form_validation->set_message('valid_email', lang('Enter a valid email address', 'xforms')); // Введите корректный email адрес
        $this->form_validation->set_message('valid_emails', lang('Enter correct email addresses', 'xforms')); // Введите корректные email адреса
        $this->form_validation->set_message('valid_ip', lang('Enter the correct IP address', 'xforms')); // Введите корректный IP адрес
        $this->form_validation->set_message('valid_url', lang('Еnter a valid URL', 'xforms')); // Введите корректный URL адрес
        $this->form_validation->set_message('numeric', lang('The field must contain only a numeric value', 'xforms')); // Поле должно содержать только числовое значение
        $this->form_validation->set_message('integer', lang('The field must contain an integer', 'xforms')); // Поле дожно содержать целое число
        //$this->form_validation->set_message('min_length', preg_replace('/<!--.*?-->/is', '',  lang('<!--%s--> Must have at least %s characters', 'xforms'))); // В поле <!--%s--> должно быть не менее %s символов
        $this->form_validation->set_message('min_length', lang('<!--%s--> Must have at least %s characters', 'xforms')); // В поле <!--%s--> должно быть не менее %s символов
        $this->form_validation->set_message('max_length', lang('<!--%s--> The field must be no more than %s characters', 'xforms')); // В поле <!--%s--> должно быть не более %s символов

        $this->load->helper(array('form', 'url'));
    }

    public function _deinstall()
    {
        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        $this->load->dbforge();
        $this->dbforge->drop_table('xforms');
        $this->dbforge->drop_table('xforms_field');
    }

    public function _install()
    {
        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        $this->load->dbforge();

        $xforms = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE,
            ],
            'title' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'url' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'desc' => ['type' => 'text'],
            'success' => ['type' => 'text'],
            'captcha' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 1,
            ],
            'direct_url' => [
                'type' => 'int',
                'constraint' => 1,
                'default' => 0,
            ],
            'action_files' => [
                'type' => 'smallint',
                'constraint' => '1',
                'default' => 1
            ],
            'user_message_active' => [
                'type' => 'int',
                'constraint' => '3'
            ]
        ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms);
        $this->dbforge->create_table('xforms', TRUE);

        $xforms_field = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE,
            ],
            'fid' => [
                'type' => 'int',
                'constraint' => 11,
            ],
            'type' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'label' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'error_message' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'value' => ['type' => 'text'],
            'desc' => [
                'type' => 'varchar',
                'constraint' => 400,
            ],
            'position' => [
                'type' => 'int',
                'constraint' => 11,
                'default' => 0,
            ],
            'maxlength' => [
                'type' => 'int',
                'constraint' => 11,
            ],
            'checked' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 0,
            ],
            'disabled' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 0,
            ],
            'visible' => [
                'type' => 'int',
                'constraint' => 1,
                'default' => 1,
            ],
            'require' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 0,
            ],
            'operation' => ['type' => 'text'],
            'validation' => [
                'type' => 'varchar',
                'constraint' => 500,
            ],
            'allowed_types' => [
                'type' => 'varchar',
                'constraint' => 500,
            ]
        ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms_field);
        $this->dbforge->create_table('xforms_field', TRUE);

        $this->db->where('name', 'xforms');
        $this->db->update('components', ['enabled' => '1', 'in_menu' => '1', 'autoload' => '0', 'settings' => serialize(['version' => '3.0.4'])]);
    }

    public function autoload()
    {

    }

    /**
     * captcha check
     * @param string $code
     * @return boolean
     */
    public function captcha_check($code)
    {
        if (!$this->dx_auth->captcha_check($code)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }


    /**
     * Valid Date (europe format)
     */
    public function valid_date($str)
    {
        if (preg_match('/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})/', $str)) {
            $arr = explode('.', $str);
            $dd = $arr[0];
            $mm = $arr[1];
            $yyyy = $arr[2];

            return (checkdate($mm, $dd, $yyyy));
        } else {
            return FALSE;
        }
    }

    /**
     * Validate time
     */
    public function valid_time($str)
    {
        if (preg_match('/([0-9]{1,2})\:([0-9]{1,2})\:([0-9]{1,2})/', $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Validete phone number
     * +7 8 +38 +375 [0-9]
     * междугородных телефонов Российской Федерации (после удаления добавочного номера а так же всех «левых» символов кроме цифр и плюса вначале)
     * с учетом действующего Телефонного плана нумерации, (в том числе, кодов альтернативных операторов дальней связи)
     * дополнительно Украина с привычным вбиванием для Украины через 038
     * дополнительно Белоруссия
     * Отбрасывает номера вида:
     * +70000000000
     * +77777777777
     * +78901234567
     * 88888888888
     * Дополнительно ПРОСТО ПРОВЕРКА РОССИЯ,УКРАИНА, БЕЛКА - без заморочек, просто межгород и 8
     * ^(8|\+7|\+038|\+38|\+375)\d{9,10}$
     */
    public function valid_phone($number)
    {
        return (bool)preg_match('/^(?:8(?:(?:21|22|23|24|51|52|53|54|55)|(?:15\d\d))?|\+7|\+375|\+38|\+038)?(?:(?:3[04589]|4[012789]|8[^89\D]|9\d)\d)?\d{7}$/', $number);
    }


    public function index()
    {
        $this->core->error_404();
    }

    /**
     * @param string $url
     * @return string
     */
    public function show($url = '')
    {
        $is_ajax = $this->input->is_ajax_request() ? 1 : 0;
        $form = $this->xforms_model->get_form($url);

        if (!$is_ajax) {
            if (!$form['id'] OR !$form['direct_url']) {
                $this->core->error_404();
            } else {
                $this->core->set_meta_tags($form['title']);
            }
        }

        $fields = $this->xforms_model->get_form_fields($form['id'], ['visible' => 1]);

        // captcha
        if ($form['captcha'] == 1) {
            $this->dx_auth->captcha();
            $form['captcha_image'] = $this->dx_auth->get_captcha_image();
        }

        $notify = []; // Для вывода уведомлений, ошибок и др.

        // Если нажали отправить форму, то перебираем все входящие значения
        if ($this->input->post()) {

            $msg_email = []; // Текст для почты
            $user_email = ''; // Email клиента, для отправки ему письма
            $attach_email = []; // Файлы, прикрепляемые к письму


            $post_data = $this->input->post();

            foreach ($fields as $field) {

                $key_post = 'f' . $field['id'];
                $require = ($field['require'] == 1) ? 'required|' : '';
                $data_msg = '';

                // Делаем валидацию полей + подготоваливаем данные для отправки в письме
                switch ($field['type']) {

                    case 'text':
                    case 'tel':
                    case 'email':
                    case 'textarea':
                        $this->form_validation->set_rules($key_post, $field['label'], 'trim|xss_clean|' . $require . $field['validation']);
                        $data_msg = $post_data[$key_post];

                        // Найдем поле с email'ом отправителя
                        if ($form['user_message_active'] AND $field['id'] == $form['user_message_active']) {
                            $user_email = $post_data[$key_post];
                        }

                        break;

                    case 'radio':
                    case 'select':
                        $this->form_validation->set_rules($key_post, $field['label'], 'trim|max_length[3]|integer|' . $require . $field['validation']);
                        $checked = explode("\n", $field['value']);
                        $data_msg = $checked[$post_data[$key_post]];
                        break;

                    case 'checkbox':

                        if ($require) {
                            $this->form_validation->set_rules($key_post, $field['label'], $require);
                        }
                        $checked = explode("\n", $field['value']);
                        foreach ($post_data[$key_post] as $key => $val) {
                            $data_msg .= $checked[$val] . '<br/>';
                        }
                        break;

                    case 'file':
                        if ($require) {
                            $this->form_validation->set_rules($key_post, $field['label'], $require);
                        }
                        $files = [];

                        if (!empty($post_data[$key_post])) {
                            foreach ($post_data[$key_post] as $key => $val) {
                                foreach ($val as $k => $v) {
                                    $files[$k][$key] = $v;
                                }
                            }
                            foreach ($files as $key => $file) {
                                if ($form['action_files'] == 1 OR $form['action_files'] == 3) {
                                    // Вставляем ссылки файлы в текст письма
                                    $data_msg .= '<a href="' . site_url('xforms/download/' . $file['url']) . '">' . $file['name'] . '</a> - ';
                                    $data_msg .= '<a href="' . site_url('xforms/deleteFile/' . $file['url']) . '" title="удалить" style="color:red;">×</a><br/>';
                                }

                                // Добавляем для вложения в письмо
                                $attach_email[] = $file['url'];
                            }
                        }
                        break;

                    case 'hidden':
                        $this->form_validation->set_rules($key_post, $field['label'], 'trim|xss_clean');
                        $data_msg = $post_data[$key_post];
                        break;

                }

                if ($field['type'] != 'file' OR ($field['type'] == 'file' AND $form['action_files'] != 2)) {
                    $msg_email[$field['id']]['field'] = $field;
                }

                if (!empty($data_msg)) {
                    $msg_email[$field['id']]['data'] = $data_msg;
                }

            }

            if ($form['captcha'] == 1) {
                $this->form_validation->set_rules('captcha', lang('Code protection', 'xforms'), 'callback_captcha_check');
            }

            if (!$this->form_validation->run($this) == FALSE) {

                /*
                 * Отправялем email
                 * т.к. стандарный модуль cmsemail не позволяет прикреплять больше 1 файла и отправлять на несколько email'ов админам, см. = https://github.com/imagecms/ImageCMS/issues/103
                 * Загружаем его настройки для отправки почты через стандартные средства
                 */
                $geo = unserialize(file_get_contents('http://ip-api.com/php/'.$_SERVER['REMOTE_ADDR']));

                $message = assetManager::create()
                    ->setData('data', $msg_email)
                    ->setData('geo', $geo)
                    ->fetchTemplate('email');

                $this->load->library('email');
                $this->email->clear();

                $locale = MY_Controller::defaultLocale();
                $pattern_name = 'xforms_send_form_' . $form['id'];
                $default_settings = ModuleSettings::ofModule('cmsemail')->get($locale ?: null);
                $pattern_settings = email::getInstance()->cmsemail_model->getPaternSettings($pattern_name);

                // Заменяемые переменные для cmsemail
                $replaceData = ['message' => $message];

                // Утсановим настройки для email
                if ($pattern_settings) {
                    foreach ($pattern_settings as $key => $value) {
                        if (!$value) {
                            if ($default_settings[$key]) {
                                $pattern_settings[$key] = $default_settings[$key];
                            }
                        }
                    }
                }

                $default_settings['type'] = strtolower($pattern_settings['type']);
                $pattern_settings['protocol'] = strtolower($default_settings['protocol']);
                if (strtolower($pattern_settings['protocol']) == strtolower('SMTP')) {
                    $pattern_settings['smtp_port'] = $default_settings['port'];
                    $pattern_settings['smtp_host'] = $default_settings['smtp_host'];
                    $pattern_settings['smtp_user'] = $default_settings['smtp_user'];
                    $pattern_settings['smtp_pass'] = $default_settings['smtp_pass'];
                    $pattern_settings['smtp_crypto'] = $default_settings['encryption'];
                }
                $pattern_settings['mailtype'] = strtolower($pattern_settings['type']);
                $pattern_settings['mailpath'] = $default_settings['mailpath'];

                $this->email->initialize($pattern_settings);

                /**
                 * Отправляем письмо клиенту
                 */
                if ($form['user_message_active'] AND $pattern_settings['user_message_active']) {

                    $this->email->from($pattern_settings['from_email'], $pattern_settings['from']);
                    $this->email->to($user_email);
                    $this->email->subject($pattern_settings['theme']);
                    $this->email->message(email::getInstance()->replaceVariables($pattern_settings['user_message'], $replaceData));

                    $this->email->send();
                    $this->email->clear(TRUE);
                }

                /**
                 * Отправляем письма админам
                 */
                $form['email'] = array_diff(explode(',', str_replace(' ', '', $pattern_settings['admin_email'])), ['']);
                foreach ($form['email'] as $item) {
                    $item = trim($item);

                    if ($this->form_validation->valid_email($item)) {

                        $this->email->from($pattern_settings['from_email'], $pattern_settings['from']);
                        $this->email->to($item);
                        $this->email->subject($pattern_settings['theme']);
                        $this->email->message(email::getInstance()->replaceVariables($pattern_settings['admin_message'], $replaceData));
                        $this->email->set_newline("\r\n");
                        // Добавляем вложения
                        if ($attach_email AND ($form['action_files'] == 2 OR $form['action_files'] == 3)) {
                            foreach ($attach_email as $file) {
                                if (file_exists(FCPATH . 'uploads/xforms/' . $file)) {
                                    $this->email->attach(FCPATH . 'uploads/xforms/' . $file);
                                }
                            }
                        }

                        $this->email->send();

                        // отдаем в console.log(notify.console) информацию об отправке. Только Админу
                        if ($this->dx_auth->is_admin()) {
                            $notify['console']['debug'][] = $this->email->print_debugger();
                        }

                        $this->email->clear(TRUE);
                    }
                }

                // Удаляем вложения
                if ($attach_email AND $form['action_files'] == 2) {
                    foreach ($attach_email as $file) {
                        $this->deleteFile($file);
                    }
                }

                $notify['success'] = $form['success'];

            } else {

                // Кастомные ошибки для полей.
                $field_errors = [];
                foreach ($this->form_validation->getErrorsArray() as $field_id => $error) {
                    $f_id = preg_replace('/[^0-9]/', '', $field_id);
                    $result = array_search($f_id, array_column($fields, 'id'));

                    if ($fields[$result]['error_message']) {
                        $field_errors[$field_id] = $fields[$result]['error_message'];
                    } else {
                        $field_errors[$field_id] = $error;
                    }
                }

                $notify['errors'] = $field_errors;
                $notify['fields'] = $fields;
                $notify['group_errors'] = validation_errors();
                $notify['captcha_image'] = $form['captcha_image'];
            }
        }

        if ($is_ajax) {
            return json_encode($notify);
        } else {

            // Если есть поля "файл" в форме. Что бы не загружать лишние скрипты...
            $field_file_exists = array_filter($fields, function ($lines) {
                return ($lines['type'] == 'file'); //Поиск по первому значению
            });


            // Отдаем в tpl
            $xform_fetch = assetManager::create();

            $xform_fetch
                ->setData('form', $form)
                ->setData('fields', $fields)
                ->registerScript('xforms')
                ->registerStyle('xforms');

            if ($field_file_exists) {
                $xform_fetch
                    ->registerScript('jquery.ui.widget')
                    ->registerScript('jquery.iframe-transport')
                    ->registerScript('jquery.fileupload')
                    ->registerScript('xforms_files');
            }

            if ($form['direct_url']) {
                $xform_fetch
                    ->render('../templates/wrapper');
            } else {
                $xform_fetch
                    ->render('../templates/show_form');
            }
        }
    }

    /**
     * Upload file
     */
    public function upload($field_id)
    {
        // Найдем форму.
        if (!$field = $this->xforms_model->get_field($field_id))
            return false;

        $form = $this->xforms_model->get_form(intval($field['fid']));

        // расширния файлов доступные к загрузке
        if ($field['allowed_types'])
            $config['allowed_types'] = $field['allowed_types'];
        else
            $config['allowed_types'] = 'jpg|png|rar|zip|doc|docx|psd|pdf';

        // путь загрузки
        $config['upload_path'] = './uploads/xforms/'; // . $form['url'] . '/';
        $upload_path_url = '/uploads/xforms/'; // . $form['url'] . '/';

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0700, true);
        }

        $config['encrypt_name'] = TRUE;
        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        // fixed multiple file upload. Fucking codeigninter...
        $files = $_FILES;
        $cpt = count($_FILES['f' . $field_id]['name']);
        for ($i = 0; $i < $cpt; $i++) {
            $_FILES['userfile']['name'] = $files['f' . $field_id]['name'][$i];
            $_FILES['userfile']['type'] = $files['f' . $field_id]['type'][$i];
            $_FILES['userfile']['tmp_name'] = $files['f' . $field_id]['tmp_name'][$i];
            $_FILES['userfile']['error'] = $files['f' . $field_id]['error'][$i];
            $_FILES['userfile']['size'] = $files['f' . $field_id]['size'][$i];

            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                echo json_encode(['error' => $this->upload->display_errors()]);
            } else {
                $info = $this->upload->data();

                $file = new StdClass;
                $file->name = $_FILES['userfile']['name'];
                $file->url = $info['file_name'];
                $file->size = $info['size'];
                $file->extension = $info['extension'];
                $file->full_url = $upload_path_url . $info['file_name'];
                $file->deleteUrl = base_url() . 'xforms/deleteFile/' . $info['file_name'];
                $file->deleteType = 'DELETE';
                $file->error = null;

                echo json_encode($file);
            }
        }
    }


    /**
     * Download file
     */
    public function download($file)
    {
        $this->load->helper('download');

        $data = file_get_contents("./uploads/xforms/$file");

        if (empty($data)) {
            $this->core->error_404();
        } else {
            force_download($file, $data);
        }
    }


    /**
     * remove file
     * @param $file
     * TODO: Доделать проверку безопасности и вывод ошибок, если файл отсутствует.
     */
    public function deleteFile($file)
    {
        $success = unlink(FCPATH . 'uploads/xforms/' . $file);
        //info to see if it is doing what it is supposed to
        $info = new StdClass;
        $info->sucess = $success;
        $info->path = base_url() . 'uploads/' . $file;
        $info->file = is_file(FCPATH . 'uploads/' . $file);

        $is_ajax = $this->input->is_ajax_request() ? 1 : 0;

        if ($is_ajax) {
            //I don't think it matters if this is set but good for error checking in the console/firebug
            echo json_encode(array($info));
        } else {
            echo "File has been deleted.";
        }
    }

}