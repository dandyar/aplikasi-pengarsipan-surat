<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  
  <aside class="main-sidebar">

    <section class="sidebar">

      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU UTAMA</li>
        <li class="active">
            <a href="<?php echo site_url('admin'); ?>">
              <i class="fa fa-home"></i>
              <span>Dashboard</span>
          </a>
        </li>
        <li class="header">DAFTAR DATA</li>
        <li>
            <a href="<?php echo site_url('admin/page/surat_masuk'); ?>">
              <i class="fa fa-inbox"></i>
              <span>Surat Masuk</span>
          </a>
        </li>
        <li>
          <a href="<?php echo site_url('admin/page/surat_keluar'); ?>">
              <i class="fa fa-mail-forward"></i>
              <span>Surat Keluar</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo site_url('admin/page/disposisi_surat_masuk'); ?>">
              <i class="fa fa-file"></i>
              <span>Disposisi Surat Masuk</span>
          </a>
        </li>
        
        <li>
            <a href="<?php echo site_url('admin/page/form_jenis_surat'); ?>">
              <i class="fa fa-tag"></i>
              <span>Jenis Surat</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo site_url('admin/page/data_petugas'); ?>">
              <i class="fa fa-users"></i>
              <span>Petugas TU</span>
          </a>
        </li>
        
        
        <li class="header">LAPORAN DATA</li>
        <li>
          <a href="<?php echo site_url('laporan/view/surat_masuk'); ?>">
              <i class="fa fa-print"></i>
              <span>Surat Masuk</span>
          </a>
        </li>
        <li>
          <a href="<?php echo site_url('laporan/view/surat_keluar'); ?>">
              <i class="fa fa-print"></i>
              <span>Surat Keluar</span>
          </a>
        </li>
        <li>
          <a href="<?php echo site_url('laporan/view/disposisi_surat_masuk'); ?>">
              <i class="fa fa-print"></i>
              <span>Disposisi Surat Masuk</span>
          </a>
        </li>
        <li class="header">PENGATURAN</li>
        <li>
          <a href="<?php echo site_url('admin/db_backup'); ?>">
              <i class="fa fa-database"></i>
              <span>Database Backup</span>
          </a>
        </li>
        
      </ul>
      
    </section>
    
  </aside>


  <div class="content-wrapper">
    <section class="content-header">
        <h1>
        Dashboard
        <small>Aplikasi Pengarsipan Surat</small>
      </h1>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-android-mail"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Surat Masuk</span>
                  <span class="info-box-number"><?php echo $jml_surat_masuk ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa ion-android-share"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Surat Keluar</span>
                  <span class="info-box-number"><?php echo $jml_surat_keluar ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-android-document"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Disposisi Surat Masuk</span>
                  <span class="info-box-number"><?php echo $disposisi_surat_masuk ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-android-contacts"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Petugas</span>
                  <span class="info-box-number"><?php echo $jml_petugas ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        
        <div class="row">
            <section class="col-lg-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs pull-right">
                      <li class="active"><a href="#surat-masuk" data-toggle="tab" aria-expanded="true">Surat Masuk</a></li>
                      <li class=""><a href="#surat-keluar" data-toggle="tab" aria-expanded="false">Sura Keluar</a></li>
                      <li class="pull-left header"><i class="fa fa-desktop"></i> Data Surat Terbaru</li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="surat-masuk">
                            <table id="table1" class="table">
                                <thead>
                                    <tr>
                                        
                                        <th>No. Surat</th>
                                        <th>Tanggal Surat</th>
                                        <th>Perihal</th>
                                        <th>Pengirim</th>
                                        <th>Kepada</th>
                                        <th>Jenis Surat</th>
                                        <th>Sifat</th>
                                        <th>Petugas</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($surat_masuk->result() as $row): ?>
                                    <tr>
                                        
                                        <td><?php echo $row->no_surat ?></td>
                                        <td><?php echo $row->tgl_surat ?></td>
                                        <td><?php echo $row->perihal ?></td>
                                        <td><?php echo $row->pengirim ?></td>
                                        <td><?php echo $row->ditujukan ?></td>
                                        <td><?php echo $row->jenis_surat ?></td>
                                        <td><?php echo $row->sifat_surat ?></td>
                                        <td><?php echo $row->username ?></td>
                                        <td><?php echo timespan($row->dibuat_pada, time(), 2) ?> ago</td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="surat-keluar">
                            <table id="table1" class="table">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>No. Surat</th>
                                        <th>Tanggal Surat</th>
                                        <th>Perihal</th>
                                        <th>Pengirim</th>
                                        <th>Kepada</th>
                                        <th>Jenis Surat</th>
                                        <th>Sifat</th>
                                        <th>Petugas</th>
                                        <th>timespan()</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($surat_keluar->result() as $row): ?>
                                    <tr>
                                        <td><span class="label label-danger">Baru</span></td>
                                        <td><?php echo $row->no_surat ?></td>
                                        <td><?php echo $row->tgl_surat ?></td>
                                        <td><?php echo $row->perihal ?></td>
                                        <td><?php echo $row->pengirim ?></td>
                                        <td><?php echo $row->kepada ?></td>
                                        <td><?php echo $row->jenis_surat ?></td>
                                        <td><?php echo $row->sifat_surat ?></td>
                                        <td><?php echo $row->petugas ?></td>
                                        <td><?php echo timespan($row->dibuat_pada, time(), 2) ?> ago</td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
    </section>
    
  </div>

</div>


<script src="<?php echo base_url('assets/js/jquery-3.2.1.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/adminlte/js/adminlte.min.js'); ?>"></script>



</body>
</html>

