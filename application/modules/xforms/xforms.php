<?php

use CMSFactory\assetManager;

(defined('BASEPATH')) OR exit('No direct script access allowed');

class Xforms extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->module('core');
        $this->load->model('xforms_model');
        $this->load->library('form_validation');

        $this->load->library('email');
    }

    public function index() {
        $this->core->error_404();
    }


    public function show($url = '')
    {
        $is_widget = ($this->input->post('cms_widget_form')) ? 1 : 0;

		$form = $this->xforms_model->get_form($url);

        if(!$is_widget) {
            if (!$form OR !$form['direct_url'])
                $this->core->error_404();
            else
                $this->core->set_meta_tags($form['title']);
        }

        $fields = $this->xforms_model->get_form_fields($form['id'], ['visible' => 1]);

        // captcha
        if($form['captcha'] == 1) {
            $this->dx_auth->captcha();
            $form['captcha_image'] = $this->dx_auth->get_captcha_image();
        }

        $notify = []; // Для вывода уведомлений, ошибок и др.

        // Если нажали отправить форму, то перебираем все входящие значения
        if ($this->input->post()) {

            $msg = []; // Текст на почту

            $post_data = $this->input->post();

            foreach ($fields as $field) {
                $key_post = 'f'.$field['id'];

                if(isset($post_data[$key_post])) {
                    $require = ($field['require'] == 1) ? 'required|' : '';

                    if($field['type'] == 'radio') {
                        $this->form_validation->set_rules($key_post, $field['label'], 'trim|max_length[3]|integer|' . $require . $field['validation']);
                        $radio = explode("\n", $field['value']);
                        $msg[] = ['field' => $field, 'data' => $radio[$post_data[$key_post]]];
                    } else {
                        $this->form_validation->set_rules($key_post, $field['label'], 'trim|xss_clean|' . $require . $field['validation']);
                        $msg[] = ['field' => $field, 'data' => $post_data[$key_post]];
                    }
                } elseif($field['type'] == 'separator') {
                    $msg[] = ['field' => $field];
                }
            }

            if ($form['captcha'] == 1) {
                $this->form_validation->set_rules('captcha', lang("Code protection"), 'callback_captcha_check');
            }

            if (!$this->form_validation->run($this) == FALSE)
            {
                // добавляем сообщение в БД.


                // Отправялем email
                $message = assetManager::create()->setData('data', $msg)->fetchTemplate('email');
                $form['email'] = array_diff(explode(',', $form['email']), ['']);
                foreach($form['email'] as $item)
                {
                    $item = trim($item);
                    if($this->form_validation->valid_email($item)) {
                        $this->email->initialize(array('mailtype' => 'html'));
                        $this->email->from($form['email'][0]);
                        $this->email->subject($form['subject']);
                        $this->email->message($message);
                        $this->email->to($item);
                        $this->email->send();
                    }
                }

                $notify['success'] = $form['success'];
            }
            else
            {
                $notify['errors'] = $this->form_validation->getErrorsArray();
                $notify['group_errors'] = validation_errors();
                $notify['captcha_image'] = $form['captcha_image'];
            }
        }

        if($is_widget) {
            return json_encode($notify);
        } else {

            assetManager::create()
                ->setData('form', $form)
                ->setData('fields', $fields)
                ->setData('notify', $notify)
                ->render('xforms');
        }
    }

    public function autoload() {

    }

    public function _install() {

        if ($this->dx_auth->is_admin() == FALSE)
            exit;

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
            'desc' => [
                'type' => 'text',
            ],
            'success' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'subject' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'captcha' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 1
            ],
            'direct_url' => [
                'type' => 'int',
                'constraint' => 1,
                'default' => 0
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
            'value' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'desc' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'position' => [
                'type' => 'int',
                'constraint' => 11,
                'default' => 0
            ],
            'maxlength' => [
                'type' => 'int',
                'constraint' => 11,
            ],
            'checked' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 0
            ],
            'disabled' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 0
            ],
            'visible' => [
                'type' => 'int',
                'constraint' => 1,
                'default' => 1
            ],
            'require' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 0
            ],
            'operation' => [
                'type' => 'text',
            ],
            'validation' => [
                'type' => 'varchar',
                'constraint' => 500
            ]
        ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms_field);
        $this->dbforge->create_table('xforms_field', TRUE);
		
		$xforms_messages = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE,
            ],
            'author' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
			'file' => [
                'type' => 'text',
            ],
            'msg' => [
                'type' => 'text',
            ],
            'date' => [
				'type' => 'INT',
				'constraint' => 32,
            ]
        ];

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($xforms_messages);
        $this->dbforge->create_table('xforms_messages', TRUE);

        $this->db->where('name', 'xforms');
        $this->db->update('components', ['enabled' => '1', 'in_menu' => '1', 'autoload' => '1']);
    }

    public function _deinstall() {

        if ($this->dx_auth->is_admin() == FALSE)
            exit;

        $this->load->dbforge();
        $this->dbforge->drop_table('xforms');
        $this->dbforge->drop_table('xforms_field');
		$this->dbforge->drop_table('xforms_messages');
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
   

}