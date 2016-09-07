<?php

if (!defined('BASEPATH')) exit ('No direct script access allowed');

class Testimoni_model extends CI_Model
{
    public $table = 'kb_testimonial';

    function __construct()
    {
        parent::__construct();
    }

    function get_data()
    {
        $this->db->select('*');
        $this->db->from('kb_testimonial');

        $query = $this->db->get();
        return $query->result();
    }

    function get_dataall($param,$sampai,$dari)
    {
        $this->db->select('*');
        if($param['query1']!='') {
            $this->db->like('name',$param['query1']);
        }

        $query = $this->db->get('kb_testimonial',$sampai,$dari);
        return $query->result();
    }

    function jumlah(){
        return $this->db->get('kb_testimonial')->num_rows();
    }

    function get_data_edit($id)
    {
        $this->db->select('*');
        $this->db->from('kb_testimonial');
        $this->db->where('id_testimonial', $id);

        $query = $this->db->get();
        $result = $query->row();
        return $result;
    }

    function save_data($data)
    {
        $query = $this->db->insert('kb_testimonial', $data);
        return $query;
    }

    function update_data($id,$data)
    {
        $this->db->where('id_testimonial', $id);
        return $this->db->update('kb_testimonial', $data);
    }

    function hapus($id)
    {
        $this->db->where('id_testimonial', $id);
        return $this->db->delete('kb_testimonial');
    }

}

/* End of file testimoni_model.php */
/* Location: ./application/models/testimoni_model.php */
/* Please DO NOT modify this information : */
/* Generated by Codeigniter CRUD Generator 2016-04-13 11:59:47 */
?>