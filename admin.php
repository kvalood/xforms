<?php

use CMSFactory\assetManager;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


/**
 * @property Xforms_model xforms_model
 */
class Admin extends BaseAdminController
{

    public function __construct() {

        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('xforms');
        $this->load->model('xforms_model');
        $this->load->library('form_validation');
    }

    /**
     * Изменение статуса, видим или не видим, для поля формы
     * @param int $field_id
     */
    public function change_visible($field_id) {

        $field = $this->xforms_model->get_field($field_id);

        if ($field['visible'] == 1) {
            $field['visible'] = 0;
        } elseif ($field['visible'] == 0) {
            $field['visible'] = 1;
        }

        $this->xforms_model->update_field($field['id'], $field);
        showMessage(lang('Status change success', 'xforms'));
    }

    /**
     * @param string $data
     * @return bool
     */
    public function check_emails($data) {

        return $this->form_validation->valid_emails($data);
    }

    /**
     * Удаление полей формы
     */
    public function delete_fields() {

        $this->xforms_model->delete_fields($this->input->post('id'));
    }

    /**
     * Удаление формы
     */
    public function delete_form() {

        if (count($this->input->post()) > 0) {
            $this->db->where('id', (int) $this->input->post('id'))->delete('xforms');
            $this->db->where('fid', (int) $this->input->post('id'))->delete('xforms_field');
        }
    }

    /**
     * Работа с полями формы
     * @param null|int $fid
     * @param null|string $field
     */
    public function field($fid = null, $field = null) {

        if ($this->input->post('type')) {
            $this->form_validation->set_rules('value', 'Значение', 'trim|xss_clean');
            $this->form_validation->set_rules('desc', 'Описание', 'trim|xss_clean|max_length[255]');
            $this->form_validation->set_rules('validation', 'Валидация', 'trim|xss_clean');
            $this->form_validation->set_rules('operation', 'Операции', 'trim');
            $this->form_validation->set_rules('position', 'Позиция', 'trim|xss_clean|numeric');
            $this->form_validation->set_rules('maxlength', 'Максимум символов', 'trim|xss_clean|numeric');
            $this->form_validation->set_rules('allowed_types', 'Типы файлов', 'trim|xss_clean|max_length[500]');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), false, 'r');
            } else {
                $data = [
                         'fid'           => $fid,
                         'type'          => $this->input->post('type'),
                         'label'         => $this->input->post('name'),
                         'value'         => $this->input->post('value'),
                         'desc'          => $this->input->post('desc'),
                         'operation'     => $this->input->post('operation'),
                         'position'      => $this->input->post('position'),
                         'maxlength'     => $this->input->post('maxlength'),
                         'checked'       => $this->input->post('check'),
                         'disabled'      => $this->input->post('disable'),
                         'require'       => $this->input->post('required'),
                         'validation'    => $this->input->post('validation'),
                         'visible'       => $this->input->post('visible'),
                         'allowed_types' => $this->input->post('allowed_types'),
                        ];

                if (!$field) {
                    $field_id = $this->xforms_model->add_field($data);

                    if (!$data['position']) {
                        $data['position'] = $field_id;
                        $this->xforms_model->update_field($field_id, $data);
                    }

                    showMessage(lang('Field created', 'xforms'));
                    $path = '/admin/components/cp/xforms/field/' . $fid . '/' . $field_id;
                } else {

                    $this->xforms_model->update_field((int) $field, $data);
                    showMessage(lang('Changes has been saved', 'xforms'));
                    $path = '/admin/components/cp/xforms/field/' . $fid . '/' . $field;
                }

                if ($this->input->post('action') == 'exit') {
                    $path = '/admin/components/cp/xforms/fields/' . $fid;
                }

                pjax($path);
            }
        } else {
            if ($field) {
                assetManager::create()
                    ->setData('field', $this->xforms_model->get_field((int) $field))
                    ->setData('fid', $fid)
                    ->renderAdmin('field');
            } else {
                assetManager::create()->setData('fid', $fid)->renderAdmin('field');
            }
        }
    }

    /**
     * EDIT and ADD field for form
     * @param int $id
     */
    public function fields($id) {

        assetManager::create()
            ->setData('fields', $this->xforms_model->get_form_fields($id))
            ->setData('form_name', $this->xforms_model->get_form_name($id))
            ->setData('form_id', $id)
            ->renderAdmin('fields');

    }

    /**
     * CREATE and EDIT form
     * создание / редактирование формы
     * @param null|int $id
     */
    public function form($id = null) {

        // Сохраняем
        if ($this->input->post()) {

            $this->form_validation->set_rules('page_title', 'Заголовок', 'trim|xss_clean|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('page_url', 'URL формы', 'alpha_dash|least_one_symbol');
            $this->form_validation->set_rules('subject', 'Тема', 'trim|xss_clean|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('desc', 'Описание', 'trim|xss_clean|max_length[255]');
            $this->form_validation->set_rules('good', 'Сообщение', 'trim|xss_clean|max_length[255]|required');
            $this->form_validation->set_rules('email', 'Email', 'callback_check_emails');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), false, 'r');
            } else {
                // Собираем данные
                $data = [
                         'title'      => $this->input->post('page_title'),
                         'url'        => $this->input->post('page_url'),
                         'desc'       => $this->input->post('desc'),
                         'success'    => $this->input->post('good'),
                         'subject'    => $this->input->post('subject'),
                         'email'      => $this->input->post('email'),
                         'captcha'    => $this->input->post('captcha'),
                         'direct_url' => $this->input->post('direct_url'),
                        ];

                // Создаем / сохраняем
                if (isset($id) AND $this->xforms_model->update_form($id, $data)) {
                    showMessage(lang('Changes has been saved', 'xforms'));
                    $path = '/admin/components/cp/xforms/form/' . $id;
                } else {
                    $id = $this->xforms_model->add_form($data);
                    showMessage('Готово', 'Форма добавлена');
                    $path = '/admin/components/cp/xforms/form/' . $id;
                }

                if ($this->input->post('action') == 'close') {
                    $path = '/admin/components/cp/xforms';
                }

                pjax($path);

            }
        } else {
            // Show form
            if (isset($id)) {
                assetManager::create()
                    ->setData('form', $this->xforms_model->get_form($id))
                    ->renderAdmin('form');
            } else {
                assetManager::create()->renderAdmin('form');
            }
        }
    }

    /**
     * Show list forms in admin panel
     */
    public function index() {

        assetManager::create()->setData('forms', $this->xforms_model->get_forms())->renderAdmin('forms');
    }

    /**
     * Обновление модуля с 2.0 версии до 2.3
     */
    public function update_2_3() {

        //Добавим колонку с расшерениями файлов
        $xforms_field = [
                'allowed_types' => [
                    'type'       => 'varchar',
                    'constraint' => 500,
                ]
            ];

        $this->load->dbforge();
        $this->dbforge->add_column('xforms_field', $xforms_field);

        // удаляем старые поля из xforms_messages
        $this->dbforge->drop_column('xforms_messages', 'author');
        $this->dbforge->drop_column('xforms_messages', 'file');
        $this->dbforge->drop_column('xforms_messages', 'msg');
        $this->dbforge->drop_column('xforms_messages', 'date');

        //Добавим Новые колонки в xforms_messages
        $xforms_messages = [
                'fid'       => [
                    'type'       => 'int',
                    'constraint' => 11,
                ],
                'message'   => ['type' => 'text'],
                'status' => [
                    'type'         => 'smallint',
                    'constraint'   => 1
                ]
            ];
        $this->dbforge->add_column('xforms_messages', $xforms_messages);

    }

    /**
     * Работа с сообщениями для формы
     */
    public function messages() {

        assetManager::create()
            ->setData('message', $this->xforms_model->get_messages())
            ->renderAdmin('message');
    }

    /**
     * Sort fields in the form
     */
    public function update_positions() {

        $positions = $this->input->post('positions');

        foreach ($positions as $key => $value) {
            $this->db->where('id', (int) $value)->set('position', $key)->update('xforms_field');
        }

        showMessage('Позиция обновлена');
    }
}