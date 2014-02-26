import pybel
import openbabel as ob
import sys

def check_mol_type(type, string):
    try:
        mol = pybel.readstring(type, string)
        result = 1
    except:
        result = 0

    return result

def convert_to_inchi(type, string):
    try:
        mol = pybel.readstring(type, string)
        result = mol.write("inchi")
    except:
        result = 0

    return result
	
def convert_to_smiles(type, string):
    try:
        mol = pybel.readstring(type, string)
        result = mol.write("smi")
    except:
        result = 0

    return result

def convert_to_mol(type, string):
    try:
        mol = pybel.readstring(type, string)
        mol.make3D()
        result = mol.write("mdl")
    except:
        result = 0

    return result

def convert_to_inchikey(type, string):
    try:
        conv = ob.OBConversion()
        conv.SetInAndOutFormats(type, "inchi")
        conv.SetOptions("K", conv.OUTOPTIONS)

        mol = ob.OBMol()
        conv.ReadString(mol, string)
        result = conv.WriteString(mol)
    except:
        result = 0

    return result

def get_mw(type, string):
    try:
        mol = pybel.readstring(type, string)
        result = mol.molwt
    except:
        result = 0

    return result
	
def get_formula(type, string):
    try:
        mol = pybel.readstring(type, string)
        result = mol.formula
    except:
        result = 0

    return result

if __name__ == "__main__":
    if len(sys.argv) == 3:
        print check_mol_type(sys.argv[1], sys.argv[2])
    elif len(sys.argv) == 4:
        if sys.argv[3] == "-ci" or sys.argv[3] == "-CI":
            print convert_to_inchi(sys.argv[1], sys.argv[2])			
        elif sys.argv[3] == "-w" or sys.argv[3] == "-W":
            print get_mw(sys.argv[1], sys.argv[2])
        elif sys.argv[3] == "-f" or sys.argv[3] == "-F":
            print get_formula(sys.argv[1], sys.argv[2])
        elif sys.argv[3] == "-cs" or sys.argv[3] == "-CS":
            print convert_to_smiles(sys.argv[1], sys.argv[2])
        elif sys.argv[3] == "-ck" or sys.argv[3] == "-CK":
            print convert_to_inchikey(sys.argv[1], sys.argv[2])
        elif sys.argv[3] == "-cm" or sys.argv[3] == "-CM":
            print convert_to_mol(sys.argv[1], sys.argv[2])
        else:
            print "Usage: check_mol.py TYPE STRING OR python check_mol.py TYPE STRING -option"
            print "Option:"
            print "-ci Convert to InChi"
            print "-cs Convert to SMILES"
            print "-ck Convert to InChiKey"
            print "-cm Convert to MOL"
            print "-w Molecular Weight"
            print "-f Molecular formula"
    else:
        print "Usage: check_mol.py TYPE STRING OR python check_mol.py TYPE STRING -option"
        print "Option:"
        print "-ci Convert to InChi"
        print "-cs Convert to SMILES"
        print "-ck Convert to InChiKey"
        print "-cm Convert to MOL"
        print "-w Molecular Weight"
        print "-f Molecular formula"