<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Xforms_model extends CI_Model
{

    /**
     * @return array
     */
    public function get_settings() {
        $this->db->where('name', 'xforms');
        $query = $this->db->get('components')->row_array();
        return unserialize($query['settings']);
    }

    /**
     * @param array $data
     */
    public function save_settings($data) {
        $this->db->where('name', 'xforms');
        $this->db->update('components', ['settings' => serialize($data)]);
    }


    /**
     * Add form
     * @param array $data
     * @return int
     */
    public function add_form($data = []) {

        $this->db->insert('xforms', $data);
        return $this->db->insert_id();
    }

    /**
     * @param int $id
     * @return array form
     */
    public function get_form($id) {

        $this->db->limit(1);

        if (is_int($id)) {
            return $this->db->get_where('xforms', ['id' => $id])->row_array();
        } else {
            return $this->db->get_where('xforms', ['url' => $id])->row_array();
        }
    }

    /**
     * Update form
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_form($id, $data = []) {

        $this->db->where('id', $id);
        $this->db->update('xforms', $data);

        return TRUE;
    }

    /**
     * @param int $id = form id
     * @param array $param [visible, type]
     * @return array
     */
    public function get_form_fields($id, $param = []) {

        if (isset($param['visible'])) {
            $this->db->where('visible', $param['visible']);
        }

        if (isset($param['type'])) {
            $this->db->where('type', $param['type']);
        }

        return $this->db->where('fid', $id)->order_by('position', 'asc')->get('xforms_field')->result_array();
    }

    /**
     * @param integer $id
     * @return int
     */
    public function get_form_name($id) {

        $q = $this->db->select('title')->where('id', $id)->get('xforms')->row_array();
        return $q['title'];
    }

    /**
     * list forms
     * @return mixed
     */
    public function get_forms() {

        return $this->db->get('xforms')->result_array();
    }

    /**
     * Add field for form
     * @param array $data
     * @return int
     */
    public function add_field($data = []) {

        $this->db->insert('xforms_field', $data);
        return $this->db->insert_id();
    }

    /**
     * @param int $id
     * @return array
     */
    public function get_field($id) {

        return $this->db->where('id', $id)->get('xforms_field')->row_array();
    }

    /**
     * @param int $id
     * @param array $data
     * @return integer
     */
    public function update_field($id, $data) {

        $this->db->where('id', $id);
        $this->db->update('xforms_field', $data);
        return $id;
    }

    /**
     * @param array $ids
     */
    public function delete_fields($ids = []) {

        foreach ($ids as $id) {
            $this->db->where('id', $id)->delete('xforms_field');
        }
    }

    /**
     * @return mixed
     */
    public function get_messages() {

        $this->db->select('xforms_messages.*, xforms.title');
        $this->db->from('xforms_messages');
        $this->db->join('xforms', 'xforms_messages.fid = xforms.id');
        $this->db->order_by("xforms_messages.created", "desc");
        $result = $this->db->get()->result_array();

        return $result;
        //return $this->db->get()->result_array();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function add_message($data = []) {

        $this->db->insert('xforms_messages', $data);
        return $this->db->insert_id();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get_message($id) {

        $this->db->select('xforms_messages.*, xforms.title, xforms.action_files');
        $this->db->from('xforms_messages');
        $this->db->join('xforms', 'xforms_messages.fid = xforms.id');
        $this->db->where('xforms_messages.id', $id);

        return $this->db->get()->row_array();
    }

    /**
     * @param $id
     * @param $data
     */
    public function update_message($id, $data) {

        $this->db->where('id', $id);
        $this->db->update('xforms_messages', $data);
        return $id;
    }

}