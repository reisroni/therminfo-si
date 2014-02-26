import oasa
import sys
import time

def generate_imgs(smiles=''):
	try:
		mol = oasa.smiles.text_to_mol(smiles)
		mol.normalize_bond_length(70)
		mol.remove_unimportant_hydrogens()
		c = oasa.cairo_out.cairo_out(color_bonds=True, color_atoms=True, line_width=2)
		c.show_hydrogens_on_hetero = True
		c.font_size = 20
		mols = list(mol.get_disconnected_subgraphs())
		
		path_img = "storage/images/";
		current = time.strftime("%d-%m-%Y_%H%M%S")
		filename = path_img + "mol_" + current
		
		c.mols_to_cairo(mols, filename + ".png", format="png")
		c.mols_to_cairo(mols, filename + ".pdf", format="pdf")
		result = filename
	except:
		result = 0
		
	return result

if __name__ == "__main__":
	if len(sys.argv) > 1:
		print generate_imgs(sys.argv[1])
	else:
		print 0