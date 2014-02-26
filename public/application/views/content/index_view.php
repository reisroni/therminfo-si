<?php
$data = array(
		'page' => 'home_menu',
		'title' => 'Welcome to ThermInfo!',
		'css_files' => array(),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="pageContentTextPanel" class="center bodyText">
		<h2 class="panelTitle">Collecting, Retrieving, and Estimating Reliable Thermochemical Data</h2>
		<p><strong>ThermInfo</strong> is a cheminformatics system designed and built with two main objectives in mind: collecting and retrieving critically evaluated thermochemical values, and estimating new data.</p>
		<p>In its present version, by using chemically intelligent software, <strong>ThermInfo</strong> allows to retrieve the value of a thermochemical property, such as a gas-phase standard enthalpy of formation, by inputting, for example, the molecular structure or the name of a compound. The same inputs can also be used to estimate data (this feature is presently restricted to non-polycyclic hydrocarbons).</p>
		<p>Future versions of <strong>ThermInfo</strong> will cover a wide range of (long-lived and transient) organic, inorganic, and organometallic molecules in the gas- and in condensed-phases. A variety of empirical methods, selected on the basis of their reliability to predict data, will be included. New estimation procedures, based on structure-energetics relationships and machine learning methods will be searched for.</p>
		<p><strong>ThermInfo</strong> involves a partnership between the <a href="http://molenergetics.fc.ul.pt/" title="Molecular Energetics Group Page"><strong>Molecular Energetics Group</strong></a> of <a href="http://cqb.fc.ul.pt/" title="Centro de Qu&iacute;mica e Bioqu&iacute;mica Page"><strong>CQB (Centro de Qu&iacute;mica e Bioqu&iacute;mica)</strong></a> and <a href="http://lasige.di.fc.ul.pt/" title="Large-Scale Informatics Systems Laboratory Page"><strong>LaSIGE (Large-Scale Informatics Systems Laboratory)</strong></a>. The chemistry team has considerable expertise on a variety of experimental thermochemical techniques, on assessing thermochemical data, and on the development of prediction methods. The informatics team has extensive experience in web systems development, in particular biomolecular databases.</p>
		<p>We hope that <strong>ThermInfo</strong> will become a valuable resource for chemists and chemical engineers.</p>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>