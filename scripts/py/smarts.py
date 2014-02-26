import pybel
from operator import itemgetter

def read_smiles(smart):
    fname= "storage/therminfo_db.smi"
    fil=open(fname, "rt")
    lins=fil.readlines()
    fil.close()
    smis={}
    try:
        smarts = pybel.Smarts(smart)
        for lin in lins[1:]:
            lin2=lin.strip()
            smile, pid=lin2.split("\t")
            mol = pybel.readstring("smi", smile)
        
            atom_indexes = smarts.findall(mol)
            if len(atom_indexes) > 0:
                smis[pid] = len(atom_indexes)
        smis = sorted(smis.items(), key=itemgetter(1), reverse=True)
            
        return smis
    except IOError:
		return "Invalid SMARTS pattern"

def run():
    smarts = "CC=O"
    smis = read_smiles(smarts)
    print smis

if __name__=="__main__":
    import sys
    if len(sys.argv)==2:
        print read_smiles(sys.argv[1])
		#print len(read_smiles(sys.argv[1]))
        sys.exit