  <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
  </div>
  <!-- end page container -->

<!-- ================== BEGIN BASE JS ================== -->
  <script src="../assets/plugins/jquery-1.8.2/jquery-1.8.2.min.js"></script>
  <script src="../assets/plugins/jquery-ui-1.10.4/ui/minified/jquery-ui.min.js"></script>
  
  <!--[if lt IE 9]>
    <script src="../assets/crossbrowserjs/html5shiv.js"></script>
    <script src="../assets/crossbrowserjs/respond.min.js"></script>
    <script src="../assets/crossbrowserjs/excanvas.min.js"></script>
  <![endif]-->
  <script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>  
  <!-- ================== END BASE JS ================== -->
  
  

<script>
	<?
		foreach ($admin_config[visible_pannel] as $key => $value) {
			if ($value) echo "$('#" .$value. "').show();";
		}
	?>
</script>