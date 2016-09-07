<?php

if (!defined('BASEPATH')) exit ('No direct script access allowed');

class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('post_model');
        $this->load->model('menu_model');
        $this->load->model('post_category_model');
        $this->load->library('encrypt');
        $this->load->model('opsi_website');
        $this->load->model('permission_model');
    }

    public function index($p="",$a="")
    {
        $usr=$this->session->userdata('logged_in');
        $mod=$this->permission_model->get_data_module('post');
        $per=$this->permission_model->get_data_akses($mod->module_id,$usr['group']);

        if($this->session->userdata('logged_in')) {
            if(!empty($per->is_view)&&($per->is_view=='true')) {
                /* this is for searching */
                $query1=$this->input->post('query1');
                $status=$this->input->post('status');
                $kategori=$this->input->post('kategori');

                $param = array(
                    'query1' => $query1,
                    'status' => $status,
                    'kategori' => $kategori
                );

                $data['query1'] = $query1;
                $data['status'] = $status;
                $data['kategoris'] = $kategori;

                $datas=$this->opsi_website->getdata();

                $data['jumlah']= $this->post_model->jumlah($param);

                // Config page
                $config['base_url'] = base_url().'/post/index';
                $config['total_rows'] = $data['jumlah'];
                $config['per_page'] = 20;

                $dari = $this->uri->segment('3');
                $data['post']=$this->post_model->get_dataall($param,$config['per_page'],$dari);
                $data['kategori'] = $this->post_category_model->get_data();

                $config['full_tag_open'] = '<ul class=pagination>';
                $config['full_tag_close'] = '</ul>';
                $config['first_link'] = false;
                $config['last_link'] = false;
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['prev_link'] = '&laquo';
                $config['prev_tag_open'] = '<li class=prev>';
                $config['prev_tag_close'] = '</li>';
                $config['next_link'] = '&raquo';
                $config['next_tag_open'] = '<li>';
                $config['next_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li>';
                $config['last_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class=active><a href=#>';
                $config['cur_tag_close'] = '</a></li>';
                $config['num_tag_open'] = '<li>';
                $config['num_tag_close'] = '</li>';

                $this->pagination->initialize($config);
                if($p==="x1" || $a==="x1") {
                    $data['pesan'] = 'Gagal Menghapus Data';
                }
                $data['judul']=$datas->website_title;
                $data['company']=$datas->company_name;
                $data['judul_panel']='Pengaturan post';
                $data['tambah']=$per->is_add;
                $data['edit']=$per->is_edit;
                $data['hapus']=$per->is_delete;

                $view = 'templates/backend/post_modul/post_data';
                show($view, $data);
            } else if(empty($per->is_view)) {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('backend_panel/dashboard', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('backend_panel/dashboard', 'refresh');
            }
        } else {
            redirect('backend_panel', 'refresh');
        }
    }

    public function post_add($p="")
    {
        $usr=$this->session->userdata('logged_in');
        $mod=$this->permission_model->get_data_module('post');
        $per=$this->permission_model->get_data_akses($mod->module_id,$usr['group']);

        if($this->session->userdata('logged_in')) {

            $user_data = $this->session->userdata('logged_in');
            //var_dump($user_data);
            $sess=$user_data['user'];

            session_start();
            $_SESSION['ses_kcfinder']=array();
            $_SESSION['ses_kcfinder']['disabled'] = false;
            //$_SESSION['ses_kcfinder']['uploadURL'] = "../content_upload";

            if(!empty($per->is_add)&&($per->is_add=='true')) {
                $datas=$this->opsi_website->getdata();

                $data['kategori'] = $this->post_category_model->get_data();
                $data['judul']=$datas->website_title;
                $data['company']=$datas->company_name;
                $data['judul_panel']='Tambah post';
                if($p=="x1") {
                    $data['pesan'] = 'Gagal Menambah Data';
                }
                $view = 'templates/backend/post_modul/post_add';
                show($view, $data);
            } else if(empty($per->is_add)) {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('post', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('post', 'refresh');
            }
        } else {
            redirect('backend_panel', 'refresh');
        }
    }

    public function post_save()
    {
        if($this->session->userdata('logged_in')) {
            /* PUT YOUR OWN PROCESS HERE */
            
            $judul = $this->input->post('post_title');
            $permalink = $this->opsi_website->removeSpesial($judul);
            //$plink = strtolower($judul);
            //$permalink = str_replace(" ", "-", $plink);
            $konten = $this->input->post('editor');
            $komen_status = $this->input->post('comment_status');
            $tgl = $this->input->post('post_date');
            $status = $this->input->post('post_status');
            $kategori = $this->input->post('post_parent');
            $gambar = $this->input->post('post_image');
            $user_data = $this->session->userdata('logged_in');
            $sess=$user_data['id'];
                        if($this->post_model->ceklink($permalink)) {
                $permalink = $permalink."-".$this->post_model->jumlah();
            }
            if($komen_status =='') {
               $komen_status="off"; 
            }
            $data = array(
                'user' => $sess,
                'post_date' => $tgl,
                'post_date_gmt' => date("Y-m-d h:i:s"),
                'post_content' => $konten,
                'post_title' => $judul,
                'post_status' => $status,
                'comment_status' => $komen_status,
                'post_parent' => $kategori,
                'post_type' => 'post',
                'post_image' => $gambar,
                'permalink' => $permalink
            );
            if($this->post_model->save_data($data)) {
            redirect('post', 'refresh');
            }
            else {
                redirect('post/post_add/x1','refresh');
            }
        } else {
            redirect('backend_panel', 'refresh');
        }
    }

    public function post_edit($id,$p="")
    {
        $usr=$this->session->userdata('logged_in');
        $mod=$this->permission_model->get_data_module('post');
        $per=$this->permission_model->get_data_akses($mod->module_id,$usr['group']);

        if($this->session->userdata('logged_in')) {
            $user_data = $this->session->userdata('logged_in');
            //var_dump($user_data);
            $sess=$user_data['user'];

            session_start();
            $_SESSION['ses_kcfinder']=array();
            $_SESSION['ses_kcfinder']['disabled'] = false;
            //$_SESSION['ses_kcfinder']['uploadURL'] = "../content_upload";

            if(!empty($per->is_edit)&&($per->is_edit=='true')) {
                $datas=$this->opsi_website->getdata();
                $dt=$this->post_model->get_data_edit($id);
                $data['kategori'] = $this->post_category_model->get_data();
                /* DEFINE YOUR OWN DATA HERE */
                $data['id'] = $dt->id_post;
                $data['post_title'] = $dt->post_title;
                $data['post_content'] = $dt->post_content;
                $data['post_date'] = $dt->post_date;
                $data['post_status'] = $dt->post_status;
                $data['post_parent'] = $dt->post_parent;
                $data['post_image'] = $dt->post_image;
                $data['comment_status'] = $dt->comment_status;
                $data['link'] = $dt->permalink;
                if($p=="x1") {
                    $data['pesan'] = 'Gagal Merubah Data';
                }
                $data['judul']=$datas->website_title;
                $data['company']=$datas->company_name;
                $data['judul_panel']='Ubah post';

                $view = 'templates/backend/post_modul/post_edit';
                show($view, $data);
            } else if(empty($per->is_edit)) {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('post', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('post', 'refresh');
            }
        } else {
            redirect('backend_panel', 'refresh');
        }
    }

    public function post_update()
    {
        if($this->session->userdata('logged_in')) {
            /* PUT YOUR OWN PROCESS HERE */
            /* PUT YOUR OWN PROCESS HERE */
            $id = $this->input->post('id_post');
            $judul = $this->input->post('post_title');
            $permalink = $this->opsi_website->removeSpesial($judul);
            //$plink = strtolower($judul);
            //$permalink = str_replace(" ", "-", $plink);
            $konten = $this->input->post('editor');
            $komen_status = $this->input->post('comment_status');
            $tgl = $this->input->post('post_date');
            $status = $this->input->post('post_status');
            $kategori = $this->input->post('post_parent');
            $gambar = $this->input->post('post_image');
            $user_data = $this->session->userdata('logged_in');
            $sess=$user_data['id'];
            $link = "p/".  $this->input->post('link');
            //$lk = $this->post_model->get_data_link($id);
                        if($komen_status =='') {
               $komen_status="off"; 
            }
            if($permalink != $this->input->post('link')){
            if($this->post_model->ceklink($permalink)) {
                $permalink = $permalink."-".$this->post_model->jumlah();
            }             
            }
            $data = array(
                'user' => $sess,
                'post_modified' => $tgl,
                'post_modified_gmt' => date("Y-m-d h:i:s"),
                'post_content' => $konten,
                'post_title' => $judul,
                'post_status' => $status,
                'comment_status' => $komen_status,
                'post_parent' => $kategori,
                'post_type' => 'post',
                'post_image' => $gambar,
                'permalink' => $permalink
            );
            if($this->post_model->update_data($id, $data)) {
                $data_ = array('url'=>"p/".$permalink);
                $this->menu_model->updateUrl($link,$data_);
            redirect('post', 'refresh');
            }
            else {
                redirect('post/post_edit/'.$id.'/x1','refresh');
            }
        } else {
            redirect('backend_panel', 'refresh');
        }
    }

    public function post_delete($id)
    {
        $usr=$this->session->userdata('logged_in');
        $mod=$this->permission_model->get_data_module('post');
        $per=$this->permission_model->get_data_akses($mod->module_id,$usr['group']);

        if($this->session->userdata('logged_in')) {
            if(!empty($per->is_delete)&&($per->is_delete=='true')) {
                /* PUT YOUR OWN PROCESS HERE */
                $data = $this->post_model->get_data_link($id);
                if($this->post_model->hapus($id)) {
                    $this->menu_model->hapuslink($data->permalink);
                    redirect('post', 'refresh');
                    
                }
                else {
                redirect('post/index/x1');
                }
            } else if(empty($per->is_delete)) {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('post', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Maaf, Anda tidak memiliki hak akses!');
                redirect('post', 'refresh');
            }
        } else {
            redirect('backend_panel', 'refresh');
        }
    }


}

/* End of file post.php */
/* Location: ./application/controllers/post.php */
/* Please DO NOT modify this information : */
/* Generated by Codeigniter CRUD Generator 2016-03-24 05:13:11 */

?>