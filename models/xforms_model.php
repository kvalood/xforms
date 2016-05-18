<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Xforms_model extends CI_Model
{

    /**
     * Add field for form
     * @param array $data
     */
    public function add_field($data = []) {

        $this->db->insert('xforms_field', $data);
        return $this->db->insert_id();
    }

    /**
     * Add form
     * @param array $data
     */
    public function add_form($data = []) {

        $this->db->insert('xforms', $data);
        return $this->db->insert_id();
    }

    /**
     * @param array $ids
     */
    public function delete_fields($ids = []) {

        foreach ($ids as $id) {
            $this->db->where('id', $id)->delete('xforms_field');
        }
    }

    /***
     * @param int $id
     */

    public function get_field($id) {

        return $this->db->where('id', $id)->get('xforms_field')->row_array();
    }

    /***
     * @param $id
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

    /***
     * @param int $id
     * @param array $param
     * @return
     */

    public function get_form_fields($id, $param = []) {

        if (isset($param['visible'])) {
            $this->db->where('visible', $param['visible']);
        }

        return $this->db->where('fid', $id)->order_by('position', 'asc')->get('xforms_field')->result_array();

    }

    /***
     * @param $id - form
     * @return string - title form
     */

    /**
     * @param integer $id
     */
    public function get_form_name($id) {

        $q = $this->db->select('title')->where('id', $id)->get('xforms')->row_array();
        return $q['title'];
    }

    /***
     * @return mixed
     * list forms
     */

    public function get_forms() {

        return $this->db->get('xforms')->result_array();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get_label($name) {

        $label = $this->db->select('label')->where('name', $name)->get('xforms_field')->row_array();
        return $label['label'];
    }

    /**
     * @return mixed
     */
    public function get_messages() {

        return $this->db->get('xforms_messages')->result_array();
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

}