<div class="page-header">
<h1>
	<?php echo $judul_panel;?>
</h1>
</div><!-- /.page-header -->
	<!-- PAGE CONTENT BEGINS -->
				<?php
					if(isset($pesan)){
						echo '<div class="alert alert-info">'.$pesan.'</div>';
					};
				?>
<div class="row">
<div class="col-xs-12">

	<form class="form-horizontal" role="form" action="<?php echo base_url()?>label_produk/label_produk_update" method="post" enctype="multipart/form-data">
            <div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">id label</label>

			<div class="col-sm-9">
                            <input type="hidden" name="id_label" value="<?php echo $id; ?>"/>
                            <input type="text" id="form-field-1-1" name="id_labeli" placeholder="id_label" class="col-xs-10 col-sm-5" value="<?php echo $id; ?>" disabled="disabled" />
			</div>
		</div><div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">nama label</label>

			<div class="col-sm-9">
                            <input type="text" id="form-field-1-1" name="nama_label" placeholder="nama_label" class="col-xs-10 col-sm-5" value="<?php echo $nama_label; ?>" required />
			</div>
		</div> <?php /*
            <div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1">created</label>

			<div class="col-sm-9">
				<input type="text" id="form-field-1-1" name="created" placeholder="created" class="col-xs-10 col-sm-5" required />
			</div>
		</div> */ ?>
            <div class="clearfix form-actions">
			<div class="col-md-offset-3 col-md-9">
				<button class="btn btn-info" type="submit">
					<i class="ace-icon fa fa-check bigger-110"></i>
					Simpan
				</button>

				&nbsp; &nbsp; &nbsp;
				<a class="btn" href="<?php echo base_url('label_produk'); ?>">
					<i class="ace-icon fa fa-undo bigger-110"></i>
					Batal
				</a>
			</div>
		</div>
	</form>
</div><!-- /.col -->
</div>