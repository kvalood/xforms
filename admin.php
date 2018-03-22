<?php

use CMSFactory\assetManager;
use Components;

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

            // Удаляем email шаблон
            $email_template = $this->db->select('id')->where('name', 'xforms_send_form_' . $this->input->post('id'))->get('mod_email_paterns')->row_array();
            $this->db->where('id', (int) $email_template['id'])->delete('mod_email_paterns');
            $this->db->where('id', (int) $email_template['id'])->delete('mod_email_paterns_i18n');
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
            $this->form_validation->set_rules('desc', 'Описание', 'trim|xss_clean');
            $this->form_validation->set_rules('good', 'Сообщение', 'trim|xss_clean|required');
            $this->form_validation->set_rules('email', 'Email', 'callback_check_emails');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), false, 'r');
            } else {
                // Собираем данные
                $data = [
                         'title'                => $this->input->post('page_title'),
                         'url'                  => $this->input->post('page_url'),
                         'desc'                 => $this->input->post('desc'),
                         'success'              => $this->input->post('good'),
                         'subject'              => $this->input->post('subject'),
                         'email'                => $this->input->post('email'),
                         'captcha'              => $this->input->post('captcha'),
                         'direct_url'           => $this->input->post('direct_url'),
                         'user_message_active'  => $this->input->post('user_message_active'),
                         'action_files'         => $this->input->post('action_files'),
                        ];

                // Создаем / сохраняем
                if (isset($id) AND $this->xforms_model->update_form($id, $data)) {
                    showMessage(lang('Changes has been saved', 'xforms'));
                    $path = '/admin/components/cp/xforms/form/' . $id;
                } else {
                    $id = $this->xforms_model->add_form($data);
                    showMessage('Готово', 'Форма добавлена');
                    $path = '/admin/components/cp/xforms/form/' . $id;

                    // Добавим шаблон "xforms_send" для отправки почты через модуль cmsemail
                    $this->load->dbforge();
                    $email_paterns = [
                        'name'                  => 'xforms_send_form_' . $id,
                        'from'                  => 'Администрация сайта',
                        'from_email'            => '',
                        'type'                  => 'HTML',
                        'patern'                => '',
                        'user_message_active'   => 0,
                        'admin_message_active'  => 0
                    ];
                    $this->db->insert('mod_email_paterns', $email_paterns);
                    $email_patterns_id = $this->db->insert_id();

                    $email_paterns_i18n = [
                        'id'            => $email_patterns_id,
                        'locale'        => 'ru',
                        'theme'         => $data['subject'] ? $data['subject'] : 'ImageCMS - Отправка формы - ' . $id,
                        'user_message'  => 'Здравствуйте. Мы свяжемся с вами в ближайшее время.',
                        'admin_message' => '<p>Пришла заявка.</p><p>$message$</p>',
                        'description'   => 'Отправка формы xforms - ' . $data['title'],
                        'variables'     => 'a:1:{s:9:"$message$";s:46:"Список заполненных полей";}'
                    ];
                    $this->db->insert('mod_email_paterns_i18n', $email_paterns_i18n);

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
                    ->setData('fields', $this->xforms_model->get_form_fields($id, ['visible' => 1, 'type' => 'text']))
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