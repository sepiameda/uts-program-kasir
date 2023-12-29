<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index() {
        $data['pesanan'] = $this->session->userdata('pesanan') ?? [];
        $this->load->view('header');
        $this->load->view('pesanan', $data);
        $this->load->view('footer');
    }

    public function tambah_pesanan() {
        $jenis_bakso = $this->input->post('jenis_bakso');
        $jumlah = $this->input->post('jumlah');

        // Validasi input
        if (empty($jenis_bakso) || !is_numeric($jumlah) || $jumlah <= 0) {
            $this->session->set_flashdata('error', 'Input tidak valid. Pastikan Anda mengisi jenis bakso dan jumlah dengan benar.');
        } else {
            // Mendefinisikan harga sesuai jenis bakso
            $harga_per_bakso = $this->get_harga_bakso($jenis_bakso);
    
            if ($harga_per_bakso === false) {
                $this->session->set_flashdata('error', 'Jenis bakso tidak valid.');
            } else {
                $pesanan = array(
                    'jenis_bakso' => $jenis_bakso,
                    'jumlah' => $jumlah,
                    'harga' => $harga_per_bakso * $jumlah
                );
    
                $pesanan_array = $this->session->userdata('pesanan') ?? [];
                $pesanan_array[] = $pesanan;
    
                $this->session->set_userdata('pesanan', $pesanan_array);
    
                $this->session->set_flashdata('success', 'Pesanan berhasil ditambahkan.');
            }
        }
    
        redirect('admin');
    }
    
    private function get_harga_bakso($jenis_bakso) {
        // Definisikan harga untuk setiap jenis bakso
        $harga_bakso = array(
            'bakso_urat' => 12000,
            'bakso_telur' => 15000,
            'bakso_jumbo' => 18000
            // Tambahkan jenis bakso lain sesuai kebutuhan
        );
    
        // Periksa apakah jenis bakso valid
        if (array_key_exists($jenis_bakso, $harga_bakso)) {
            return $harga_bakso[$jenis_bakso];
        } else {
            return false; // Jenis bakso tidak valid
        }
    }
        
    public function hapus_pesanan($index) {
        $pesanan_array = $this->session->userdata('pesanan') ?? [];
        unset($pesanan_array[$index]);
        $this->session->set_userdata('pesanan', array_values($pesanan_array));

        redirect('admin');
    }

    public function clear_pesanan() {
        $this->session->unset_userdata('pesanan');
        redirect('admin');
    }
}
