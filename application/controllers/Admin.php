<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'model_surat_masuk',
            'model_surat_keluar',
            'model_data_petugas',
            'model_jenis_surat',
            'model_disposisi_surat_masuk'
        ));
        
        if(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
        {
            redirect('/', 'refresh');
        }
    }
    
    public function index()
    {
        $this->load->library('table');
        $user = $this->ion_auth->user()->row();
        
        $data['title'] = 'Admin Dashboard';
        $data['nama_lengkap'] = $user->nama_petugas;
        
        $this->db->select('no_surat, tgl_surat, perihal, pengirim, ditujukan, jenis_surat, sifat_surat, username, dibuat_pada');
        $this->db->order_by('dibuat_pada', 'DESC');
        $this->db->limit(10);
        $data['surat_masuk'] = $this->db->get('v_surat_masuk');
        
        $this->db->select('no_surat, tgl_surat, perihal, pengirim, kepada, jenis_surat, sifat_surat, petugas, dibuat_pada');
        $this->db->order_by('dibuat_pada', 'DESC');
        $this->db->limit(10);
        $data['surat_keluar'] = $this->db->get('v_surat_keluar');
        
        $data['jml_surat_masuk'] = $this->db->count_all('surat_masuk');
        $data['jml_surat_keluar'] = $this->db->count_all('surat_keluar');
        $data['jml_petugas'] = $this->db->count_all('v_petugas');
        $data['disposisi_surat_masuk'] = $this->db->count_all('v_disposisi_surat_masuk');
        
        
        $this->load->view('includes/header', $data);
        $this->load->view('pages/dashboard', $data);
    }
    
    public function page($view)
    {
        if (!file_exists(APPPATH.'views/pages/'.$view.'.php'))
        {
            show_404();
        }
        
        $user = $this->ion_auth->user()->row();
        
        $data['title'] = humanize($view);
        $data['username'] = $user->username;
        $data['nama_lengkap'] = $user->nama_petugas;
        
        $this->load->view('includes/header', $data);
        
        switch ($view)
        {
            case 'surat_masuk':
                $data['jenis_surat'] = $this->model_jenis_surat->get_rows();
                $this->load->view('pages/surat_masuk', $data);
                break;
            case 'surat_keluar':
                $data['jenis_surat'] = $this->model_jenis_surat->get_rows();
                $this->load->view('pages/surat_keluar', $data);
                break;
            case 'form_jenis_surat':
                
                $data['id_jenis_surat'] = array(
                    'type' => 'text',
                    'name' => 'input1',
                    'id' => 'inputIdJenisSurat',
                    'class' => 'form-control',
                );
                $data['jenis_surat'] = array(
                    'type' => 'text',
                    'name' => 'input2',
                    'id' => 'inputJenisSurat',
                    'class' => 'form-control',
                );
                
                $this->load->view('pages/form_jenis_surat', $data);
                break;
            case 'data_petugas':
                $this->load->view('pages/data_petugas');
                break;
            case 'disposisi_surat_masuk':
                $this->load->view('pages/disposisi_surat_masuk');
                break;
        }
        
    }

    public function get_surat_masuk()
    {
        $list = $this->model_surat_masuk->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="data-check" value="'.$field->id.'" onclick="enableDeleteBtn()"/>';
            $row[] = $no;
            $row[] = $field->no_surat;
            $row[] = $field->tgl_surat;
            $row[] = $field->perihal;
            $row[] = $field->jenis_surat;
            $row[] = $field->ditujukan;
            $row[] = $field->deskripsi;
            $row[] = $field->username;
            if($field->berkas_surat)
            {
                $row[] = '<a href="'. base_url('uploads/'.$field->berkas_surat).'" target="_blank">'.$field->berkas_surat.'</a>';
            }
            else
            {
                $row[] = '(No file)';
            }
            $row[] = $field->sifat_surat;
            $row[] = $field->status_disposisi;
            $row[] = form_button('ajaxUpdateBtn', 'Disposisi', 'class="btn btn-default" onclick="showModal('.$field->id.')"') .
                    form_button('ajaxUpdateBtn', 'Edit', 'class="btn btn-default" onclick="editSuratMasuk('.$field->id.')"');

            $data[] = $row;
		}

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model_surat_masuk->count_all(),
            "recordsFiltered" => $this->model_surat_masuk->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function input_surat_masuk()
    {
        $this->_validate();
        
        $user = $this->ion_auth->user()->row();
        $data = array(
            'no_surat' => $this->input->post('no_surat'),
            'tgl_surat' => $this->input->post('tgl_surat'),
            'perihal' => $this->input->post('perihal'),
            'id_jenis_surat' => $this->input->post('jenis_surat'),
            'pengirim' => $this->input->post('pengirim'),
            'ditujukan' => $this->input->post('ditujukan'),
            'deskripsi' => $this->input->post('deskripsi'),
            'id_petugas' => $user->id,
            'sifat_surat' => $this->input->post('sifat_surat'),
            'status_disposisi' => $this->input->post('status_disposisi'),
            'dibuat_pada' => time(),
        );
        
        if (!empty($_FILES['berkas_surat']['name'])) {
            $upload = $this->_do_upload();
            $data['berkas_surat'] = $upload;
        }
       
        $insert = $this->model_surat_masuk->save($data);
        echo json_encode(array("status" => TRUE));
    }

    
    public function edit_surat_masuk($id)
    {
        $data = $this->model_surat_masuk->get_by_id($id);
        echo json_encode($data);
    }
    
    public function update_surat_masuk()
    {
        $user = $this->ion_auth->user()->row();
        $data = array(
            'no_surat' => $this->input->post('no_surat'),
            'tgl_surat' => $this->input->post('tgl_surat'),
            'perihal' => $this->input->post('perihal'),
            'id_jenis_surat' => $this->input->post('jenis_surat'),
            'pengirim' => $this->input->post('pengirim'),
            'ditujukan' => $this->input->post('ditujukan'),
            'deskripsi' => $this->input->post('deskripsi'),
            'id_petugas' => $user->id,
            'sifat_surat' => $this->input->post('sifat_surat'),
            'status_disposisi' => $this->input->post('status_disposisi'),
        );
        
        if (!empty($_FILES['berkas_surat']['name'])) {
            $upload = $this->_do_upload();
            $data['berkas_surat'] = $upload;
        }
       
        $this->model_surat_masuk->update(array('id_surat_masuk' => $this->input->post('id_surat_masuk')),$data);
        echo json_encode(array("status" => TRUE));
    }
    
    public function hapus_surat_masuk()
     {
         $list_id = $this->input->post('id');
         foreach ($list_id as $id) {
             $this->model_surat_masuk->delete_by_id($id);
         }
         echo json_encode(array("status" => TRUE));
     }
     
     public function get_surat_keluar()
    {
        $list = $this->model_surat_keluar->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="data-check" value="'.$field->id.'" onclick="enableDeleteBtn()"/>';
            $row[] = $no;
            $row[] = $field->no_surat;
            $row[] = $field->tgl_surat;
            $row[] = $field->perihal;
            $row[] = $field->pengirim;
            $row[] = $field->kepada;
            $row[] = $field->jenis_surat;
            $row[] = $field->sifat_surat;
            $row[] = $field->petugas;
            $row[] = $field->deskripsi;
            $row[] = form_button('ajaxUpdateBtn', 'Edit', 'class="btn btn-default" onclick="editSuratKeluar('.$field->id.')"');

            $data[] = $row;
		}

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model_surat_keluar->count_all(),
            "recordsFiltered" => $this->model_surat_keluar->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function input_surat_keluar()
    {
        
        $user = $this->ion_auth->user()->row();
        $data = array(
            'no_surat' => $this->input->post('no_surat'),
            'tgl_surat' => $this->input->post('tgl_surat'),
            'perihal' => $this->input->post('perihal'),
            'id_jenis_surat' => $this->input->post('jenis_surat'),
            'pengirim' => $this->input->post('pengirim'),
            'kepada' => $this->input->post('ditujukan'),
            'deskripsi' => $this->input->post('deskripsi'),
            'id_petugas' => $user->id,
            'sifat_surat' => $this->input->post('sifat_surat'),
            'dibuat_pada' => time(),
        );

        $this->model_surat_keluar->save($data);
        echo json_encode(array("status" => TRUE));
    }
    
    public function edit_surat_keluar($id)
    {
        $data = $this->model_surat_keluar->get_by_id($id);
        echo json_encode($data);
    }
    
    public function update_surat_keluar()
    {
        $data = array(
            'no_surat' => $this->input->post('no_surat'),
            'tgl_surat' => $this->input->post('tgl_surat'),
            'perihal' => $this->input->post('perihal'),
            'id_jenis_surat' => $this->input->post('jenis_surat'),
            'pengirim' => $this->input->post('pengirim'),
            'kepada' => $this->input->post('ditujukan'),
            'deskripsi' => $this->input->post('deskripsi'),
            'id_petugas' => $this->ion_auth->user()->row()->id,
            'sifat_surat' => $this->input->post('sifat_surat'),
        );
       
        $this->model_surat_keluar->update(array('id_surat_keluar' => $this->input->post('id_surat_keluar')),$data);
        echo json_encode(array("status" => TRUE));
    }
    
    public function hapus_surat_keluar()
     {
         $list_id = $this->input->post('id');
         foreach ($list_id as $id) {
             $this->model_surat_keluar->delete_by_id($id);
         }
         echo json_encode(array("status" => TRUE));
     }
    
    //Method Petugas
    
    public function data_petugas()
    {
        $list = $this->model_data_petugas->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="data-check" value="'.$field->id.'" onclick="enableDeleteBtn()"/>';
            $row[] = $no;
            $row[] = $field->nama_petugas;
            $row[] = $field->username;
            $row[] = $field->jenis_kelamin;
            $row[] = $field->tgl_lahir;
            $row[] = $field->alamat;
            $row[] = $field->email;
            $row[] = $field->telp;
            $row[] = form_button('ajaxUpdateBtn', 'Edit', 'class="btn btn-default" onclick="editPetugas('.$field->id.')"');

            $data[] = $row;
		}

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model_data_petugas->count_all(),
            "recordsFiltered" => $this->model_data_petugas->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function edit_petugas($id)
    {
        $data = $this->model_data_petugas->get_by_id($id);
        echo json_encode($data);
    }
    
    public function tambah_petugas()
    {
        $username = $this->input->post('input1');
        $password = $this->input->post('input2');
        $email = $this->input->post('input3');
        $additional_data = array(
            'nama_petugas' => $this->input->post('nama'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tgl_lahir' => $this->input->post('tgl_lahir'),
            'telp' => $this->input->post('telp'),
            'alamat' => $this->input->post('alamat'),
        );
        $group = array('2');
        $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        echo json_encode(array("status" => TRUE));
    }
    
    
    public function update_petugas()
    {
        $id = $this->input->post('user_id');
        $data = array(
            'username' => $this->input->post('input1'),
            'password' => $this->input->post('input2'),
            'email' => $this->input->post('input3'),
            'nama_petugas' => $this->input->post('nama'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tgl_lahir' => $this->input->post('tgl_lahir'),
            'telp' => $this->input->post('telp'),
            'alamat' => $this->input->post('alamat'),
        );
        
        $this->ion_auth->update($id, $data);
        echo json_encode(array("status" => TRUE));
    }
    
    
    public function hapus_petugas()
     {
         $list_id = $this->input->post('id');
         foreach ($list_id as $id) {
             $this->model_data_petugas->delete_by_id($id);
         }
         echo json_encode(array("status" => TRUE));
     }
    
    //Method Jenis Surat
    
    public function input_jenis_surat()
    {
        $data = array(
                'id_jenis_surat' => $this->input->post('input1'),
                'jenis_surat' => $this->input->post('input2'),
            );
        $insert = $this->model_jenis_surat->save($data);
        echo json_encode(array("status" => TRUE));
    }
  
    public function data_jenis_surat()
    {
        $list = $this->model_jenis_surat->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="data-check" value="'.$field->id_jenis_surat.'" onclick="enableDeleteBtn()"/>';
            $row[] = $no;
            $row[] = $field->id_jenis_surat;
            $row[] = $field->jenis_surat;
            $row[] = form_button('ajaxUpdateBtn', 'Edit', 'class="btn btn-default" onclick="editJenisSurat('.$field->id_jenis_surat.')"');

            $data[] = $row;
		}

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model_jenis_surat->count_all(),
            "recordsFiltered" => $this->model_jenis_surat->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function edit_jenis_surat($id)
    {
        $data = $this->model_jenis_surat->get_by_id($id);
        echo json_encode($data);
    }
    
    public function update_jenis_surat()
    {
        $data = array(
                'id_jenis_surat' => $this->input->post('input1'),
                'jenis_surat' => $this->input->post('input2'),
            );
        $this->model_jenis_surat->update(array('id_jenis_surat' => $this->input->post('input1')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function hapus_jenis_surat()
     {
         $list_id = $this->input->post('id');
         foreach ($list_id as $id) {
             $this->model_jenis_surat->delete_by_id($id);
         }
         echo json_encode(array("status" => TRUE));
     }
     
     
    public function buat_disposisi()
    {
        $this->load->library(array('PDF_1', 'table'));
        $this->load->helper('date');
        $user = $this->ion_auth->user()->row();        
        $id_surat_masuk = $this->input->post('idSurat1');
        $data = array(
            'tgl_disposisi' => mdate('%Y-%m-%d', time()),
            'keterangan' => $this->input->post('ketDisposisi'),
            'id_surat_masuk' => $id_surat_masuk,
            'id_petugas' => $user->id,
            'dibuat_pada' => time()
        );
        
        $update = $this->model_surat_masuk->status_disposisi(array('id_surat_masuk' => $id_surat_masuk), array('status_disposisi' => 'Sudah Disposisi'));
        
        $id = $this->model_disposisi_surat_masuk->save($data);

        $data['today'] = mdate('%Y-%m-%d', time());
        
        $this->db->from('v_disposisi_surat_masuk');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $data['disposisi_surat_masuk'] = $query->row();
        
        $this->load->view('laporan/lembar_disposisi', $data);
    }
    
    public function get_disposisi()
    {
        $list = $this->model_disposisi_surat_masuk->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = '<input type="checkbox" class="data-check" value="'.$field->id.'" onclick="enableDeleteBtn()"/>';
            $row[] = $field->no_surat;
            $row[] = $field->tgl_surat;
            $row[] = $field->tgl_disposisi;
            $row[] = $field->dari;
            $row[] = $field->kepada;
            $row[] = $field->keterangan;
            $row[] = $field->username;
            $row[] = timespan($field->dibuat_pada, time(), 1);
            $row[] = anchor('admin/lembar_disposisi/'.$field->id, 'Print', 'target="_blank" class="btn btn-default"');

            $data[] = $row;
		}

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model_disposisi_surat_masuk->count_all(),
            "recordsFiltered" => $this->model_disposisi_surat_masuk->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
     public function disposisi_surat_masuk($id)
     {
        $data = $this->model_disposisi_surat_masuk->get_by_id($id);
        echo json_encode($data); 
     }
     
     public function hapus_disposisi()
     {
         $list_id = $this->input->post('id');
         foreach ($list_id as $id) {
             $this->model_disposisi_surat_masuk->delete_by_id($id);
         }
         echo json_encode(array("status" => TRUE));
     }
     
     
     private function _do_upload()
     {
        $this->load->helper('string');
         
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = 'jpg|png|pdf|docx';
        $config['max_size'] = 80000; //set max size allowed in Kilobyte
        
        $config['file_name'] = random_string('numeric', 16);

        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('berkas_surat')) { //upload and validate
            $data['inputerror'][] = 'berkas_surat';
            $data['error_string'][] = 'Upload error: ' . $this->upload->display_errors('', ''); //show ajax error
            $data['status'] = FALSE;
            echo json_encode($data);
            exit();
        }

        return $this->upload->data('file_name');
    }



    private function _validate() {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
    
    public function db_backup() {
        $this->load->dbutil();

        $prefs = array(
            'format' => 'zip',
            'filename' => 'db_pengarsipan_surat.sql'
        );

        $backup =& $this->dbutil->backup($prefs);

        $db_name = 'db-pengarsipan-surat-' . date("Y-m-d") . '.zip';
        $save = 'pathtobkfolder/' . $db_name;

        $this->load->helper('file');
        write_file($save, $backup);

        $this->load->helper('download');
        force_download($db_name, $backup);
    }
    
    public function lembar_disposisi($id)
    {
        
        $this->load->library(array('PDF_1', 'table'));
        
        $data['today'] = mdate('%Y-%m-%d', time());
        
        $this->db->from('v_disposisi_surat_masuk');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $data['disposisi_surat_masuk'] = $query->row();
        
        $this->load->view('laporan/lembar_disposisi', $data);
    }


}