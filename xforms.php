<?php

use CMSFactory\assetManager;
use cmsemail\email;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * @property Xforms_model xforms_model
 */
class Xforms extends MY_Controller
{

    public function __construct() {

        parent::__construct();
        $this->load->module('core');
        $lang = new MY_Lang();
        $lang->load('xforms');
        $this->load->model('xforms_model');
        $this->load->library('form_validation');

        $this->load->library('email');

        $this->load->helper(array('form', 'url'));
    }

    public function _deinstall() {

        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        $this->load->dbforge();
        $this->dbforge->drop_table('xforms');
        $this->dbforge->drop_table('xforms_field');
        $this->dbforge->drop_table('xforms_messages');
    }

    public function _install() {

        if ($this->dx_auth->is_admin() == FALSE) {
            exit;
        }

        $this->load->dbforge();

        $xforms = [
                   'id'         => [
                                    'type'           => 'INT',
                                    'constraint'     => 11,
                                    'auto_increment' => TRUE,
                                   ],
                   'title'      => [
                                    'type'       => 'varchar',
                                    'constraint' => 255,
                                   ],
                   'url'        => [
                                    'type'       => 'varchar',
                                    'constraint' => 255,
                                   ],
                   'desc'       => ['type' => 'text'],
                   'success'    => [
                                    'type'       => 'varchar',
                                    'constraint' => 255,
                                   ],
                   'subject'    => [
                                    'type'       => 'varchar',
                                    'constraint' => 255,
                                   ],
                   'email'      => [
                                    'type'       => 'varchar',
                                    'constraint' => 255,
                                   ],
                   'captcha'    => [
                                    'type'       => 'int',
                                    'constraint' => 2,
                                    'default'    => 1,
                                   ],
                   'direct_url' => [
                                    'type'       => 'int',
                                    'constraint' => 1,
                                    'default'    => 0,
                                   ],
                  ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms);
        $this->dbforge->create_table('xforms', TRUE);

        $xforms_field = [
                         'id'         => [
                                          'type'           => 'INT',
                                          'constraint'     => 11,
                                          'auto_increment' => TRUE,
                                         ],
                         'fid'        => [
                                          'type'       => 'int',
                                          'constraint' => 11,
                                         ],
                         'type'       => [
                                          'type'       => 'varchar',
                                          'constraint' => 255,
                                         ],
                         'label'      => [
                                          'type'       => 'varchar',
                                          'constraint' => 255,
                                         ],
                         'value'      => ['type'       => 'text'],
                         'desc'       => [
                                          'type'       => 'varchar',
                                          'constraint' => 255,
                                         ],
                         'position'   => [
                                          'type'       => 'int',
                                          'constraint' => 11,
                                          'default'    => 0,
                                         ],
                         'maxlength'  => [
                                          'type'       => 'int',
                                          'constraint' => 11,
                                         ],
                         'checked'    => [
                                          'type'       => 'int',
                                          'constraint' => 2,
                                          'default'    => 0,
                                         ],
                         'disabled'   => [
                                          'type'       => 'int',
                                          'constraint' => 2,
                                          'default'    => 0,
                                         ],
                         'visible'    => [
                                          'type'       => 'int',
                                          'constraint' => 1,
                                          'default'    => 1,
                                         ],
                         'require'    => [
                                          'type'       => 'int',
                                          'constraint' => 2,
                                          'default'    => 0,
                                         ],
                         'operation'  => ['type' => 'text'],
                         'validation' => [
                                          'type'       => 'varchar',
                                          'constraint' => 500,
                                         ],
                         'allowed_types' => [
                                          'type'       => 'varchar',
                                          'constraint' => 500,
                                         ]
                        ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms_field);
        $this->dbforge->create_table('xforms_field', TRUE);

        $xforms_messages = [
                            'id'     => [
                                         'type'           => 'INT',
                                         'constraint'     => 11,
                                         'auto_increment' => TRUE,
                                        ],
                            'fid'    => [
                                         'type'       => 'int',
                                         'constraint' => 11,
                                        ],
                            'message'=> ['type' => 'text'],
                            'status' => [
                                         'type'         => 'smallint',
                                         'constraint'   => 1
                                        ]
                           ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms_messages);
        $this->dbforge->create_table('xforms_messages', TRUE);

        $this->db->where('name', 'xforms');
        $this->db->update('components', ['enabled' => '1', 'in_menu' => '1', 'autoload' => '1']);
    }

    public function autoload() {

    }

    /**
     * captcha check
     * @param string $code
     * @return boolean
     */
    public function captcha_check($code) {

        if (!$this->dx_auth->captcha_check($code)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function index() {

        $this->core->error_404();
    }

    /**
     * @param string $url
     * @return string
     */
    public function show($url = '') {

        $is_widget  = ($this->input->post('is_widget')) ? 1 : 0; // TODO: Remove
        $is_ajax    = $this->input->is_ajax_request() ? 1 : 0;
        $form       = $this->xforms_model->get_form($url);

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

            $msg        = []; // Текст в админку
            $msg_email  = []; // Текст для почты

            $post_data = $this->input->post();

            foreach ($fields as $field) {

                $key_post = 'f' . $field['id'];
                $require = ($field['require'] == 1) ? 'required|' : '';
                $data_msg = '';

                // Делаем валидацию полей + подготоваливаем данные для отправки в письме
                if ($field['type'] == 'radio' OR $field['type'] == 'select') {
                    $this->form_validation->set_rules($key_post, $field['label'], 'trim|max_length[3]|integer|' . $require . $field['validation']);
                    $checked = explode("\n", $field['value']);
                    $data_msg = $checked[$post_data[$key_post]];
                } elseif($field['type'] == 'checkbox') {

                    if($require)
                        $this->form_validation->set_rules($key_post, $field['label'], $require);

                    $checked = explode("\n", $field['value']);

                    foreach ($checked as $key => $val) {
                        if(isset($post_data[$key_post][$key]))
                            $data_msg .= $val . '<br/>';
                    }
                }
                elseif($field['type'] == 'file') {

                    if($require)
                        $this->form_validation->set_rules($key_post, $field['label'], $require);

                    $files = [];

                    if(!empty($post_data[$key_post])) {
                        foreach ($post_data[$key_post] as $key => $val) {
                            foreach($val as $k => $v) {
                                $files[$k][$key] = $v;
                            }
                        }
                        foreach($files as $file) {
                            $data_msg .= '<a href="' . site_url('xforms/download/' . $file['url']) . '">' . $file['name'] . '</a> - ';
                            $data_msg .= '<a href="' . site_url('xforms/deleteFile/' . $file['url']) . '" title="удалить" style="color:red;">×</a><br/>';
                        }
                    }
                } elseif ($field['type'] == 'text' OR $field['type'] == 'textarea') {
                    $this->form_validation->set_rules($key_post, $field['label'], 'trim|xss_clean|' . $require . $field['validation']);
                    $data_msg = $post_data[$key_post];
                }

                $msg_email[$field['id']]['field'] = $field;

                if(!empty($data_msg))
                    $msg_email[$field['id']]['data'] = $data_msg;
            }

            if ($form['captcha'] == 1) {
                $this->form_validation->set_rules('captcha', lang('Code protection', 'xforms'), 'callback_captcha_check');
            }

            if (!$this->form_validation->run($this) == FALSE) {
                // добавляем сообщение в БД.
                // TODO: Добавить сообщение в админку.

                // Отправялем email
                $message = assetManager::create()->setData('data', $msg_email)->fetchTemplate('email');
                $form['email'] = array_diff(explode(',', str_replace(' ', '', $form['email'])), ['']);
                foreach ($form['email'] as $item) {
                    $item = trim($item);

                    if ($this->form_validation->valid_email($item)) {

                        // email::getInstance()->sendEmail($this->input->post('email'), 'feedback', $feedback_variables);

                        $this->email->initialize(['mailtype' => 'html']);

                        if($this->email->protocol != 'smtp') {
                            $this->email->from($form['email'][0]);
                        }

                        $this->email->subject($form['subject']);
                        $this->email->message($message);
                        $this->email->to($item);
                        $this->email->send();

                        //$notify['console']['debug'][] = $this->email->print_debugger(); // отдаем в console.log(notify.console) информацию об отправке.
                        $this->email->clear();
                    }
                }

                $notify['success'] = $form['success'];
            } else {
                $notify['errors'] = $this->form_validation->getErrorsArray();
                $notify['group_errors'] = validation_errors();
                $notify['captcha_image'] = $form['captcha_image'];
            }
        }

        if ($is_ajax) {
            return json_encode($notify);
        } else {

            // Если есть поля "файл" в форме. Что бы не загружать лишние скрипты...
            $result = array_filter($fields, function($lines){
                return ($lines['type'] == 'file'); //Поиск по первому значению
            });

            if($result) {
                assetManager::create()
                    ->setData('form', $form)
                    ->setData('fields', $fields)
                    ->setData('notify', $notify)
                    ->registerScript('jquery.ui.widget')
                    ->registerScript('jquery.iframe-transport')
                    ->registerScript('jquery.fileupload')
                    ->registerScript('xforms')
                    ->registerScript('xforms_files')
                    ->registerStyle('xforms')
                    ->render('../templates/show_form');
            } else {
                assetManager::create()
                    ->setData('form', $form)
                    ->setData('fields', $fields)
                    ->setData('notify', $notify)
                    ->registerScript('xforms')
                    ->registerStyle('xforms')
                    ->render('../templates/show_form');
            }
        }
    }

    /**
     * Upload file
     */
    public function upload($field_id) {

        // Найдем форму.
        if(!$field = $this->xforms_model->get_field($field_id))
            return false;

        $form = $this->xforms_model->get_form(intval($field['fid']));

        // расширния файлов доступные к загрузке
        if($field['allowed_types'])
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
        $cpt = count($_FILES['f'.$field_id]['name']);
        for($i=0; $i<$cpt; $i++) {
            $_FILES['userfile']['name']     = $files['f'.$field_id]['name'][$i];
            $_FILES['userfile']['type']     = $files['f'.$field_id]['type'][$i];
            $_FILES['userfile']['tmp_name'] = $files['f'.$field_id]['tmp_name'][$i];
            $_FILES['userfile']['error']    = $files['f'.$field_id]['error'][$i];
            $_FILES['userfile']['size']     = $files['f'.$field_id]['size'][$i];

            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                echo json_encode(array('error' => $this->upload->display_errors()));
            } else {
                $info = $this->upload->data();

                $file             = new StdClass;
                $file->name       = $_FILES['userfile']['name'];
                $file->url        = $info['file_name'];
                $file->size       = $info['size'];
                $file->extension  = $info['extension'];
                $file->full_url   = $upload_path_url . $info['file_name'];
                $file->deleteUrl  = base_url() . 'xforms/deleteFile/' . $info['file_name'];
                $file->deleteType = 'DELETE';
                $file->error      = null;

                echo json_encode($file);
            }
        }
    }


    /**
     * Download file
     */
    public function download($file) {
        $this->load->helper('download');

        $data = file_get_contents("./uploads/xforms/$file");

        if(empty($data)) {
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
    public function deleteFile($file) {
        $success = unlink(FCPATH . 'uploads/xforms/' . $file);
        //info to see if it is doing what it is supposed to
        $info = new StdClass;
        $info->sucess = $success;
        $info->path = base_url() . 'uploads/' . $file;
        $info->file = is_file(FCPATH . 'uploads/' . $file);

        if (IS_AJAX) {
            //I don't think it matters if this is set but good for error checking in the console/firebug
            echo json_encode(array($info));
        } else {
            //here you will need to decide what you want to show for a successful delete
            $file_data['delete_data'] = $file;
            $this->load->view('admin/delete_success', $file_data);
        }
    }

}