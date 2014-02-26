#!/usr/bin/python

import pybel

def read_smiles(fname):
    fil=open(fname, "rt")
    lins=fil.readlines()
    fil.close()
    smis={}
    for lin in lins[1:]:
        lin2=lin.strip()
        smile, pid=lin2.split("\t")
        mol = pybel.readstring("smi", smile)
        fps = mol.calcfp(fptype='FP2')

        bits1={}
        for i in fps.bits: bits1[i]=0
        
        smis[pid]=(pid, smile, bits1)
    return smis

def tanimoto(bits1, bits2):
    if len(bits1) == 0:
        aux = 0
        for b2 in bits2:
            if b2 != 0:
                aux = 1
        if aux == 0:
            return 1
    else:
        a=0
        b=0
        c=0
        over=False
        for b1 in bits1:
            if b1 in bits2: a+=1
            else: b+=1
        c=len(bits2)-a
        if (a+b+c)>0:
            return float(a)/(a+b+c)
        else:
            return 0

def run(smile, threshold, threshold2, db):
    import sys
    mol = pybel.readstring("smi", smile)
    fp1 = mol.calcfp(fptype='FP2')

    res=[]
    for m in db:
        x=tanimoto(fp1.bits, db[m][2])
        if x >= threshold and x <= threshold2: res.append((x, m))
    res.sort()
    res.reverse()
    for x,m in res:
        sys.stdout.write("%05.3f %s\n" % (x, m))
    
def prepare(smi_db):
    # makes a pickled database for all the fingerprints
    import cPickle
    mols=read_smiles(smi_db)
    output = open("storage/dbase.pkl", "wb")
    cPickle.dump(mols, output)
    output.close()

if __name__=="__main__":
    import sys, cPickle
    # process arguments
    if len(sys.argv)==2:
        # with one argument, prepares the database
        prepare(sys.argv[1])
        sys.exit()
    if len(sys.argv)<3:
        sys.exit()
    # with 2 arguments test the molecule against the database
    mol_smi=sys.argv[1]
    
    threshold=float(sys.argv[2])
    threshold2=float(sys.argv[3])
    # unpickles the database
    fil = open('storage/dbase.pkl', 'rb')
    mols = cPickle.load(fil)
    fil.close()
    # run the molecule against the database
    run(mol_smi, threshold, threshold2, mols)