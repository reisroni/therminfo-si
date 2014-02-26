<div id="msg" class="center textCenter"></div>
<div id="a-add-form" class="center">
	<form id="a-insert-form" enctype="multipart/form-data" action="<?php echo site_url('admin/add_data');?>" method="post">
		<div id="a-add-pane-hdr">
			<h2>Insert new Compounds and properties values</h2>
			<p>Note: <span class="req">*</span> required fields</p>
		</div>
		<hr />
		<div id="a-add-pane-group">
			<div class="a-add-pane-1">
				<fieldset>
					<legend>Reference <span class="req">*</span></legend>
					<select id="a-ref-select" class="select" name="ref-id" title="Select a reference from the database"></select>
					<span> or </span>
					<a id="add-ref-bt" href="admin#add-ref" title="Add a new Reference to the database" >Add Reference</a>
				</fieldset>
			</div>
			<div class="a-add-pane-1">
				<fieldset>
					<legend>Compound ID <span class="req">*</span></legend>
					<input type="radio" id="mol-id-1" class="radio" name="mol-id" value="1" title="Standard InChi" /><label for="mol-id-1"> Std. InChi</label><br />
					<input type="radio" id="mol-id-2" class="radio" name="mol-id" value="2" title="CAS Registry Number" /><label for="mol-id-2"> CASRN</label><br />
					<input type="radio" id="mol-id-3" class="radio" name="mol-id" value="3" title="SMILES" /><label for="mol-id-3"> SMILES</label><br />
					<input type="radio" id="mol-id-4" class="radio" name="mol-id" value="4" title="InChi" /><label for="mol-id-4"> InChi</label><br />
				</fieldset>
			</div>
			<div class="a-add-pane-1">
				<fieldset>
					<legend>Properties <span class="req">*</span></legend>
					<select id="a-prop-select" class="select" name="prop-id" title="Select a property from the database"></select><br />
					<input type="checkbox" id="ref-prop-num" class="checkbox" name="prop-num" title="Property that isn't numeric" value="false" /><label for="ref-prop-num"> Not Numeric</label>
				</fieldset>
			</div>
			<div class="a-add-pane-2">
				<fieldset>
					<legend>Values <span class="req">*</span></legend>
					<div class="a-add-subpane textCenter">
						<input type="radio" id="input-type-1" class="radio" name="input_type" value="box" title="Input from text box" /><label for="input-type-1"> Text Box</label>
						<input type="radio" id="input-type-2" class="radio" name="input_type" value="file" title="Input from text file" /><label for="input-type-2"> File</label>
					</div>
					<p class="ref-note">
						Enter the data in the following format [<strong>Compound ID | Property Value | Value uncertainty</strong> (if none, enter n.a.) <strong>| Comments</strong> (if there is any)]
					</p>
					<textarea class="textarea" id="a-mols" name="mols" cols="100" rows="8" title="Place the compounds followed by their values, by line" placeholder="Compound | Value | Uncertainty | OBS"></textarea>
					<button type="button" id="a-mols-file" class="btn white" value="Upload a text file" title="Upload a file">Upload a text file</button>
					<div id="a-file-list"></div>
				</fieldset>
			</div>
		</div>
		<hr />
		<div id="a-add-pane-btn">
			<input type="button" id="validate_btn" class="btTxt" name="validate" value="Validate" title="Validate before submit" />
			<input type="submit" id="insert_btn" class="btTxt" name="submit" value="Insert" title="Insert all data" />
			<input type="reset" id="reset_btn" class="btTxt" value="Clear" title="Clear the form" />
		</div>
	</form>
	<div id="add-ref" title="Add New Reference">
		<form id="a-insert-ref-form" action="<?php echo site_url('admin/add_reference');?>" method="post">
			<fieldset>
				<legend class="desc">Authors <span class="req">*</span></legend>
				<ul>
					<li class="textCenter">
						<p class="ref-note">(Hold CTRL to select multiple)</p>
						<select id="a-author-select" class="select" name="a-ref-author-select" multiple="multiple" size="3" title="Choose the authors">
						</select><br />
						<span> or </span>
						<a id="add-auth-bt" href="admin#add-author" title="Add New Author" >Add Author</a>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend class="desc">Title <span class="req">*</span></legend>
				<ul>
					<li class="textCenter">
						<input type="text" id="a-title-field" class="text" name="a-ref-title" size="35" title="Add the reference title" />
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend class="desc">Other fields</legend>
				<ul>
					<li class="textCenter">
						<span class="req">* </span>
						<select id="a-type-select" class="select" name="a-ref-type" title="Choose the kind of reference">
							<option value="none">Select reference type</option>
							<option value="book">Book</option>
							<option value="paper">Paper</option>
						</select>
					</li>
					<div id="book-type-ref">
						<li>
							<label for="a-book-field">Book:</label>
							<input type="text" id="a-book-field" class="text" name="a-ref-book" title="Book" />
						</li>
						<li>
							<label for="a-editor-field">Editor:</label>
							<input type="text" id="a-editor-field" class="text" name="a-ref-editor" title="Editor" />
						</li>
						<li>
							<label for="a-publisher-field">Publisher:</label>
							<input type="text" id="a-publisher-field" class="text" name="a-ref-publisher" title="Publisher" />
						</li>
					</div>
					<div id="paper-type-ref">
						<li>
							<label for="a-journal-field">Jornal:</label>
							<input type="text" id="a-journal-field" class="text" name="a-ref-journal" title="Journal" />
						</li>
					</div>
					<div id="other-type-ref">
						<li>
							<label for="a-volume-field">Volume:</label>
							<input type="text" id="a-volume-field" class="text" name="a-ref-volume" size="5" title="Volume" />
						</li>
						<li>
							<label for="a-issue-field">Issue:</label>
							<input type="text" id="a-issue-field" class="text" name="a-ref-issue" title="Issue" />
						</li>
						<li>
							<label for="a-year-field">Year: <span class="req">*</span></label>
							<input type="text" id="a-year-field" class="text" name="a-ref-year" size="5" title="Year" />
						</li>
						<li>
							<label for="a-bpage-field">Pages:</label>
							<input type="text" id="a-bpage-field" class="text" name="a-ref-bpage" size="5" title="Beginning page" />
							<span> - </span>
							<input type="text" id="a-epage-field" class="text" name="a-ref-epage" size="5" title="End page" />
						</li>
					</div>
				</ul>
			</fieldset>
		</form>
	</div>
	<div id="add-author" title="Add New Author">
		<form id="a-insert-auth-form" action="<?php echo site_url('admin/add_author');?>" method="post">
			<p class="ref-note textCenter">Add one name at a time</p>
			<label for="a-author-field">Name: <span class="req">*</span></label>
			<input type="text" id="a-author-field" class="text" name="a-ref-author" title="The author name" />
		</form>
	</div>
</div>
<div id="dialog" title="Message"></div>
<script type="text/javascript" src="public/js/vendor/plugins/jquery.ocupload.min.js"></script>
<script type="text/javascript" src="public/js/admin_add.js"></script>