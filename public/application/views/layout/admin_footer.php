    </section>
    <footer id="section-footer" class="bg-dark place-bottom-left">
        <div class="container bg-dark">
            <div class="grid fluid">
                <div class="row">
                    <div class="span11">
                        <p class="fg-white"><b><?php echo date('l, F d, Y');?></b></p>
                    </div>
                    <div class="span1">
						<a href="<?php echo uri_string();?>#" title="Link para cima"><i class="icon-arrow-up-5 round"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <p class="container bg-dark fg-white">
            <small>Copyright (c) <?php echo date('Y');?>, ThermInfo &nbsp;&middot;&nbsp; LaSIGE - XLDB</small>
        </p>
    </footer>
    </div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="public/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="public/js/vendor/plugins/jquery.ui.widget.min.js"></script>
    <script src="public/js/vendor/metro.min.js"></script>
    <?php foreach($js_files as $script): ?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
	<?php endforeach; ?>
</body>
</html>