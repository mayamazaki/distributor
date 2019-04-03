<nav class="navbar navbar-default">
	<div class="container-fluid">
        <div class="navbar-header">

            <!-- toggle -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#top-nav">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

        </div>


		<div class="collapse navbar-collapse" id="top-nav">

			<ul class="nav navbar-nav">

				<!-- 塾 -->
				<li class="dropdown <?= in_array(strtolower($this->name), array("cramschools")) ? 'active' : '' ?>">
					<a href="<?= $this->Url->build(["controller" => "cramSchools", "action" => "index"], true); ?>" role="button" aria-haspopup="true" aria-expanded="false">塾 </span></a>
				</li>
				<!-- ライセンス -->
				<li class="dropdown <?= in_array(strtolower($this->name), array("licensepartners")) ? 'active' : '' ?>">
					<a href="<?= $this->Url->build(["controller" => "licensePartners", "action" => "index"], true); ?>" role="button" aria-haspopup="true" aria-expanded="false">ライセンス </span></a>
				</li>

			</ul>
		</div><!--/.nav-collapse -->
	</div><!--/.container-fluid -->
</nav>
