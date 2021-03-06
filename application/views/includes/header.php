<?php
defined('BASEPATH') OR exit('No direct script access allowed');
echo doctype('html5') . '<html><head>';
echo '<meta charset="utf-8"/>';
echo meta('vewport', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no');
echo '<title>' . $title . '</title>';
echo link_tag('assets/bootstrap/css/bootstrap.min.css');
echo link_tag('assets/font-awesome/css/font-awesome.min.css');
echo link_tag('assets/Ionicons/css/ionicons.min.css');
echo link_tag('assets/datatables/css/dataTables.bootstrap.min.css');
echo link_tag('assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css');
echo link_tag('assets/adminlte/css/AdminLTE.min.css');
echo link_tag('assets/adminlte/css/skins/skin-blue-light.min.css')
?>

</head>
<body class="hold-transition skin-blue-light sidebar-collapse sidebar-mini">
<div class="wrapper">

  <header class="main-header">

    <!-- Logo -->
    <a href="<?php echo site_url('admin') ?>" class="logo">
        <span class="logo-mini">
            <i class="fa fa-home"></i>
        </span>
      <span class="logo-lg">Pengarsipan-<b>Surat</b></span>
    </a>
    



    <nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- User Account Menu -->
          <li class="dropdown">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><?php echo $nama_lengkap; ?></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="<?php echo site_url('logout') ?>">
                        <i class="fa fa-sign-out"></i>Keluar
                    </a>
                </li>
            </ul>
          </li>

        </ul>
      </div>
    </nav>
  </header>
    
