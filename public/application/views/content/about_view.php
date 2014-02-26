<?php
$data = array(
		'page' => 'about_menu',
		'title' => 'About Us!',
		'css_files' => array('public/css/pages/about.css'),
		'js_files' => array());
		
$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="pageContentTextPanel" class="center bodyText">
		<table class="center about-Table" cellspacing="15">
			<caption><h2 class="orangeText">Research Team</h2></caption>
			<tbody>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/ateixeira.jpg" width="100" height="100" alt="Teixeira" title="Ana Teixeira" /></td>
					<td>
						<strong>Ana L. Teixeira</strong><br /><br />PhD Student - FCUL<br />
						<strong>E-mail: </strong><a href="mailto:ateixeira@lasige.di.fc.ul.pt" title="E-mail">ateixeira@lasige.di.fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://xldb.lasige.di.fc.ul.pt/wiki/Ana_teixeira" title="Webpage">http://xldb.lasige.di.fc.ul.pt/wiki/Ana_teixeira</a>
					</td>
				</tr>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/rreis.jpg" width="100" height="100" alt="Reis" title="Roni Reis" /></td>
					<td>
						<strong>Rony Reis</strong><br /><br />MSc Student - FCUL<br />
						<strong>E-mail: </strong><a href="mailto:reisrony@lasige.di.fc.ul.pt" title="E-mail">reisrony@lasige.di.fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://xldb.di.fc.ul.pt/wiki/Rony_Reis" title="Webpage">http://xldb.di.fc.ul.pt/wiki/Rony_Reis</a>
					</td>
				</tr>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/afalcao.jpg" width="100" height="100" alt="Falcao" title="Andr&eacute; Falc&atilde;o" /></td>
					<td>
						<strong>Andr&eacute; Falc&atilde;o</strong><br /><br />
						<strong>E-mail: </strong><a href="mailto:afalcao@di.fc.ul.pt" title="E-mail">afalcao@di.fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://homepages.di.fc.ul.pt/~afalcao/" title="Webpage">http://homepages.di.fc.ul.pt/~afalcao/</a>
					</td>
				</tr>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/jsimoes.jpg" width="100" height="100" alt="Simoes" title="Jos&eacute; Artur Sim&otilde;es" /></td>
					<td>
						<strong>Jos&eacute; Artur Martinho Sim&otilde;es</strong><br /><br />
						<strong>E-mail: </strong><a href="mailto:jams@fc.ul.pt" title="E-mail">jams@fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://webpages.fc.ul.pt/~jamsimoes/" title="Webpage">http://webpages.fc.ul.pt/~jamsimoes/</a>
					</td>
				</tr>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/jleal.jpg" width="100" height="100" alt="Leal" title="Jo&atilde;o Paulo Leal" /></td>
					<td>
						<strong>Jo&atilde;o Paulo Leal</strong><br /><br />
						<strong>E-mail: </strong><a href="mailto:jpleal@itn.pt" title="E-mail">jpleal@itn.pt</a><br />
						<strong>Webpage: </strong><a href="http://webpages.fc.ul.pt/~jpleal/" title="Webpage">http://webpages.fc.ul.pt/~jpleal/</a>
					</td>
				</tr>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/rsantos.jpg" width="100" height="100" alt="Santos" title="Rui Santos" /></td>
					<td>
						<strong>Rui Centeno Santos</strong><br /><br />
						<strong>E-mail: </strong><a href="mailto:rjsantos@fc.ul.pt" title="E-mail">rjsantos@fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://www.researcherid.com/rid/B-4959-2008" title="Webpage">http://www.researcherid.com/rid/B-4959-2008</a>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="center about-Table" cellspacing="15">
			<caption><h2 class="orangeText">Past Members</h2></caption>
			<tbody>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/msilva.jpg" width="100" height="100" alt="Silva" title="M&aacute;rio Silva" /></td>
					<td>
						<strong>M&aacute;rio J. Silva</strong><br /><br />
						<strong>E-mail: </strong><a href="mailto:mjs@di.fc.ul.pt" title="E-mail">mjs@di.fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://xldb.fc.ul.pt/mjs" title="Webpage">http://xldb.fc.ul.pt/mjs</a>
					</td>
				</tr>
				<tr>
					<td class="textCenter"><img src="public/media/images/team/fcouto.jpg" width="100" height="100" alt="Couto" title="Francisco Couto" /></td>
					<td>
						<strong>Francisco Couto</strong><br /><br />
						<strong>E-mail: </strong><a href="mailto:fcouto@di.fc.ul.pt" title="E-mail">fcouto@di.fc.ul.pt</a><br />
						<strong>Webpage: </strong><a href="http://xldb.di.fc.ul.pt/wiki/Francisco_Couto" title="Webpage">http://xldb.di.fc.ul.pt/wiki/Francisco_Couto</a>
					</td>
				</tr>
			</tbody>
		</table>
        <h2 class="orangeText">How to cite ThermInfo</h2>
        <p>
            - Ana L. Teixeira, Rui C. Santos, Jo&atilde;o P. Leal, Jos&eacute; A. Martinho Sim&otilde;es, Andr&eacute; O Falc&atilde;o, <a href="http://hdl.handle.net/10455/6892" title="Cite ThermInfo" target="_blank"><strong>ThermInfo: Collecting, Retrieving, and Estimating Reliable Thermochemical Data</strong></a>. Technical Report, Department of Informatics, Faculdade de Ci&ecirc;ncias da Universidade de Lisboa (FCUL), Portugal, January 2013:02.
        </p>
		<h2 class="orangeText">Related Publications</h2>
		<p id="publicationsP" class="textLeft">
            - Ana L. Teixeira, Jo&atilde;o P. Leal and Andr&eacute; O Falc&atilde;o, <a href="http://www.jcheminf.com/content/5/1/9" title="Random forests for feature selection in QSPR Models" target="_blank"><strong>Random forests for feature selection in QSPR Models - an application for predicting standard enthalpy of formation of hydrocarbons</strong></a>. <em>Journal of Cheminformatics</em> 2013, 5(1), 9 (<a href="http://dx.doi.org/10.1186/1758-2946-5-9" title="DOI" target="_blank"><strong>doi: 10.1186/1758-2946-5-9</strong></a>).<br><br><br>
            - Ana L. Teixeira, Rui C. Santos, Jo&atilde;o P. Leal, Jos&eacute; A. Martinho Sim&otilde;es, Andr&eacute; O Falc&atilde;o, <a href="http://hdl.handle.net/10455/6892" title="ThermInfo Technical Report" target="_blank"><strong>ThermInfo: Collecting, Retrieving, and Estimating Reliable Thermochemical Data</strong></a>. Technical Report, Department of Informatics, Faculdade de Ci&ecirc;ncias da Universidade de Lisboa (FCUL), Portugal, January 2013:02.<br><br><br>
            - Roni Reis <a href="public/media/docs/rreis_dissertacao_2012.pdf" title="ThermInfo 2.0, R. Reis" target="_blank"><strong>ThermInfo 2.0 - Estrutura&ccedil;&atilde;o e concretiza&ccedil;&atilde;o de um sistema de informa&ccedil;&atilde;o para propriedades qu&iacute;micas (PDF/2.57 MB)</strong></a>. Master Thesis, Faculdade de Ci&ecirc;ncias da Universidade de Lisboa (FCUL), Portugal, December 2012.<br><br><br>
            - Rui C. Santos, Ana L. Teixeira, Jo&atilde;o P. Leal, Andr&eacute; O Falc&atilde;o, Jos&eacute; A. Martinho Sim&otilde;es, <strong>ThermInfo: Collecting, Retrieving, and Estimating Reliable Thermochemical Data</strong>, Proceedings of XXII Encontro Nacional da Sociedade Portuguesa de Quimica 2011, Joao P. Andre and Mario B. e Santos (Eds.), Presented as Poster, Braga, Portugal, June 2011 (ISBN: 978-989-8124-08-1).<br><br><br>
			- R. C. Santos, J. P. Leal, J. A. Martinho Sim&otilde;es, <a href="public/media/docs/rsantos_et_al_2010.pdf" title="ELBA Method, R. Santos et al" target="_blank"><strong>The Extended Laidler Bond Additivity (ELBA) Method for Hydrocarbons Including Substituted Cyclic Compounds (PDF/137 KB)</strong></a>, 2010.<br><br><br>
			- A. L. Teixeira, R. C. Santos, F. M. Couto, <a href="public/media/docs/ateixeira_et_al_2009.pdf" title="ThermInfo, A. Teixeira et al" target="_blank"><strong>ThermInfo: Collecting and Presenting Thermochemical Properties (PDF/690 KB)</strong></a>, Proceedings of INForum. Simp&oacute;sio de Inform&aacute;tica 2009, L. Rodrigues and R. Lopes (Eds.), Faculty of Sciences, University of Lisbon, Portugal, September 2009 (ISBN: 978-972-9348-18-1).<br><br><br>
			- A. L. Teixeira <a href="public/media/docs/ateixeira_dissertacao_2009.pdf" title="ThermInfo, A. Teixeira Master's Thesis" target="_blank"><strong>ThermInfo: Sistema de Informa&ccedil;&atilde;o para Coligir e Apresentar Propriedades Termoqu&iacute;micas (PDF/4.08 MB)</strong></a>. Master Thesis, Faculty of Sciences, University of Lisbon, Portugal, August 2009.<br><br><br>
			- R. C. Santos, J. P. Leal, J. A. Martinho Sim&otilde;es, <a href="public/media/docs/rsantos_et_al_2009.pdf" title="The Laidler Method Revisited. 2. R. Santos et al" target="_blank"><strong>Additivity Methods for Prediction of Thermochemical Properties (PDF/144 KB)</strong></a>. The Laidler Method Revisited. 2. Hydrocarbons Including Substituted Cyclic Compounds, J. Chem. Thermodyn. 2009, 41, 1356-1373 (<a href="http://dx.doi.org/10.1016/j.jct.2009.06.013" title="DOI" target="_blank"><strong>doi: 10.1016/j.jct.2009.06.013</strong></a>).<br><br><br>
			- J. P. Leal, <a href="public/media/docs/jleal_2006.pdf" title="The Laidler Method Revisited. 1. J. Leal" target="_blank"><strong>Additive Methods for Prediction of Thermochemical Properties. The Laidler Method Revisited. 1. Hydrocarbons (PDF/41.1 KB)</strong></a>, J. Phys. Chem. Ref. Data 2006, 35, 55-76 (<a href="http://dx.doi.org/10.1063/1.1996609" title="DOI" target="_blank"><strong>doi: 10.1063/1.1996609</strong></a>).
		</p>
		<table class="center about-Table" cellspacing="15">
			<caption><h2 class="orangeText">Funding Acknowledgements</h2></caption>
			</tbody>
				<tr>
					<td class="textCenter"><a class="img-link" href="http://alfa.fct.mctes.pt/"><img src="public/media/images/fct.gif" width="253" height="87" alt="FCT" title="FCT" /></a></td>
					<td>
						- R.C.S. gratefully acknowledges a post-doctoral grant (SFRH/BPD/26610/2006)<br /><br /><br />
						- A.L.T. gratefully acknowledges a doctoral grant (SFRH/BD/64487/2009)
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>