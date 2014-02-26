import sys
import openbabel
import pybel
import pickle

def numatoms(mol):
    '''Count the number of atoms in a molecule; param: molecule; returns the number of atoms ''' 
    natoms = mol.NumAtoms()
    return natoms

def numbonds(mol):
    '''Count the number of bonds in a molecule; param: molecule; returns the number of bonds '''
    nbonds = mol.NumBonds()
    return nbonds

def only_C(mol):
    isCarbon = True
    natoms = numatoms(mol)
    for i in range(0, natoms):
	atom = mol.GetAtom(i+1)
        if (not atom.IsCarbon()) and (not atom.IsHydrogen()):
           isCarbon = False

    return isCarbon
    
def countsingleb(mol):
    '''Count the number of single bonds in a molecule; param: molecule; returns the number of single bonds '''
    cont_singleb = 0
    nbonds = numbonds(mol)
    for i in range(0, nbonds):
        bond = mol.GetBond(i)
        if bond.IsSingle():
            cont_singleb = cont_singleb + 1

    return cont_singleb

def countdoubleb(mol):
    '''Count the number of double bonds in a molecule; param: molecule; returns the number of double bonds '''
    cont_doubleb = 0
    nbonds = numbonds(mol)
    for i in range(0, nbonds):
        bond = mol.GetBond(i)
        if bond.IsDouble():
            cont_doubleb = cont_doubleb + 1

    return cont_doubleb

def counttripleb(mol):
    '''Count the number of triple bonds in a molecule; param: molecule; returns the number of triple bonds '''
    cont_tripleb = 0
    nbonds = numbonds(mol)
    for i in range(0, nbonds):
        bond = mol.GetBond(i)
        if bond.IsTriple():
            cont_tripleb = cont_tripleb + 1

    return cont_tripleb

def countringb(mol):
    '''Count the number of ring bonds in a molecule; param: molecule; returns the number of ring bonds '''
    cont_ringb = 0
    nbonds = numbonds(mol)
    for i in range(0, nbonds):
        bond = mol.GetBond(i)
        if bond.IsInRing():
            cont_ringb = cont_ringb + 1

    return cont_ringb

def countaromaticb(mol):
    '''Count the number of aromatic bonds in a molecule; param: molecule; returns the number of aromatic bonds '''
    cont_arob = 0
    nbonds = numbonds(mol)
    for i in range(0, nbonds):
        bond = mol.GetBond(i)
        if bond.IsAromatic():
            cont_arob = cont_arob + 1

    return cont_arob

def bondinfo(mol):
    natoms = numatoms(mol)
    nbonds = numbonds(mol)
    if nbonds > 0:
	#list of bond information (position 0 - 1st atom of the bond,
	#position 1 - 2nd atom of the bond, position 2 - bond order)
	bond_info = []

	for i in range(0, nbonds):
		bond = mol.GetBond(i)
		atom1 = bond.GetBeginAtomIdx()
		atom2 = bond.GetEndAtomIdx()
		bond_info.append([atom1, atom2, bond.GetBondOrder()])

	return bond_info

def atommultiplicity(mol):
    natoms = numatoms(mol)
    nbonds = numbonds(mol)

    if nbonds > 0:
    #list of atoms multiplicity (position 0 - multiplicity of the atom od the bond, position
    #1 - max bond order and position 2 - min bond order)
	atom_multiplicity = [[0]*3 for i in range(natoms)]

	for i in range(0, nbonds):
		bond = mol.GetBond(i)
		atom1 = bond.GetBeginAtomIdx()
		atom2 = bond.GetEndAtomIdx()
		atom_multiplicity [atom1 - 1][0] += 1
		atom_multiplicity [atom2 - 1][0] += 1

	for i in range(0, natoms):
		atom = mol.GetAtom(i+1)
		if atom.HasBondOfOrder(3):
			max = 3
		elif atom.HasBondOfOrder(2):
			max = 2
		elif atom.HasBondOfOrder(1):
			max = 1
		atom_multiplicity [i][1] = max

		if atom.HasBondOfOrder(1):
			min = 1
		elif atom.HasBondOfOrder(2):
			min = 2
		elif atom.HasBondOfOrder(3):
			min = 3
		atom_multiplicity [i][2] = min

	return atom_multiplicity                                    

def addparam (p, params, i):
    if p in params:
        params[p] += i
    else:
        params[p] = i
    return params

def countRings(mol):
    numRings = 0
    for ring in mol.GetSSSR():
        numRings += 1
    return numRings

def methane(param):
    p = 'C0H'
    params = addparam(p, param, 4)
    return params

### Functions to single bonds #####
def alkanes_C(atom1_mult, atom2_mult, params):
    if atom1_mult <= atom2_mult:
        p = 'C'+str(atom1_mult)+'C'+str(atom2_mult)
    else:
        p = 'C'+str(atom2_mult)+'C'+str(atom1_mult)
	
    params = addparam(p, params, 1)
    return params

def single_double(atom1_max, atom1_mult, atom2_mult, params):
    if atom1_max == 2:
        p = 'C'+str(atom2_mult)+'D'+str(atom1_mult)
    else:
        p = 'C'+str(atom1_mult)+'D'+str(atom2_mult)
	
    params = addparam(p, params, 1)
    return params

def single_triple(atom1_max, atom1_mult, atom2_mult, params):
    if atom1_max == 3:
        p = 'C'+str(atom2_mult)+'T'+str(atom1_mult)
    else:
        p = 'C'+str(atom1_mult)+'T'+str(atom2_mult)
	
    params = addparam(p, params, 1)
    return params

def dienes_polienes(atom1_mult, atom2_mult, params):
    # C-C DIENES E POLYENES (C=C-C=C)
    if atom1_mult <= atom2_mult:
        p = 'Cd'+str(atom1_mult)+'Cd'+str(atom2_mult)
    else:
        p = 'Cd'+str(atom2_mult)+'Cd'+str(atom1_mult)
	
    params = addparam(p, params, 1)
    return params

def diynes(atom1_mult, atom2_mult, params):
    # Diynes (C#C-C#C)
    p = 'Ct'+str(atom1_mult)+'Ct'+str(atom2_mult)
    params = addparam(p, params, 1)
    return params

def alkyenes(atom1_mult, atom2_mult, atom1_max, params):
    # alkyne-enes (C=C-C#C)
    if atom1_max == 2:
        p = 'Cd'+str(atom1_mult)+'Ct'+str(atom2_mult)
    else:
        p = 'Cd'+str(atom2_mult)+'Ct'+str(atom1_mult)
	
    params = addparam(p, params, 1)
    return params

def non_bond_alkanes_special(mol, natoms, nbonds, bond_info, atom_multiplicity, params):
    v = 0
    key = 'Z15'
    if (not key in params):
        for i in range(0, natoms):
            atom_mult = atom_multiplicity[i][0]
            at0 = i+1
            mult_2 = 0
            mult_3 = 0
            mult_4 = 0
            if (atom_mult == 4 and not mol.GetAtom(at0).IsInRing() and v == 0):
                v = 1
                for y in range(0, natoms):
                    atmx =  mol.GetAtom(y+1)
                    if (mol.GetAtom(at0).IsConnected(atmx) and not at0 == y+1):
                        atomx_mult = atom_multiplicity[y][0]
                        if (atomx_mult == 2):
                            mult_2 = mult_2 + 1
                        elif (atomx_mult == 3):
                            mult_3 = mult_3 + 1
                        elif (atomx_mult == 4):
                            mult_4 = mult_4 + 1
				
                if (mult_2 == 3 and mult_3 == 1):
                    p = "Z15"
                    x = 1
                    params = addparam(p, params, x)
                elif (mult_2 == 3 and mult_4 == 1):
                    p = "Z15"
                    x = 2
                    params = addparam(p, params, x)
                elif (mult_3 == 0 and mult_4 == 0):
                    for z in range(0, natoms):
                        atom_mult = atom_multiplicity[z][0]
                        at1 = z+1
                        if (atom_mult == 4 and not mol.GetAtom(at1).IsInRing() and not at1 == at0):
                            p = "Z15"
                            x = 2
                            params = addparam(p, params, x)

    return params
 
def non_bond_alkanes(mol, natoms, nbonds, bond_info, atom_multiplicity, params):
    central_atom = -1
    c_atom = []
    for i in range(0, natoms):
        # at0(mult = 4) - at1(mult >= 2) - at2(mult >= 3)
        atom_mult = atom_multiplicity[i][0]
        at0 = i+1
        
        if (atom_mult == 4 and not mol.GetAtom(at0).IsInRing()):
            for x in range(0, nbonds):
                atom1 = bond_info[x][0]
                atom2 = bond_info[x][1]
                bond_order = bond_info[x][2]
                atom1_mult = atom_multiplicity[atom1-1][0]
                atom2_mult = atom_multiplicity[atom2-1][0]
                at1 = 0
                
                if (at0 == atom1 and atom2_mult >= 2 and atom2 != central_atom and bond_order == 1):
                    at1 = atom2
                    central_atom = at1
                elif (at0 == atom2 and atom1_mult >= 2 and atom1 != central_atom and bond_order == 1):
                    at1 = atom1
                    central_atom = at1

                if (central_atom != -1):
                    if (central_atom not in c_atom):
                        c_atom.append(central_atom)
                        
                        if (at1 != 0 and not mol.GetAtom(at1).IsInRing()):
                            common_c_atom = 0
                            for y in range (0, nbonds):
                                a1 = bond_info[y][0]
                                a2 = bond_info[y][1]
                                b_ord = bond_info[y][2]
                                at1_m = atom_multiplicity[a1-1][0]
                                at2_m = atom_multiplicity[a2-1][0]
                                
                                if (at1 == a1 and a2 != at0 and at2_m == 3 and not mol.GetAtom(a2).IsInRing() and b_ord == 1):
                                    common_c_atom += 1
                                    p = 'Z15'
                                    x = 1
                                    if common_c_atom >= 2:
                                        x = x-1
                                    params = addparam(p, params, x)
                                elif (at1 == a2 and a1 != at0 and at1_m == 3 and not mol.GetAtom(a1).IsInRing() and b_ord == 1):
                                    common_c_atom += 1
                                    p = 'Z15'
                                    x = 1
                                    if (common_c_atom >= 2):
                                        x = x-1
                                    params = addparam(p, params, x)
                                elif (at1 == a1 and a2 != at0 and at2_m == 4 and not mol.GetAtom(a2).IsInRing() and b_ord == 1):
                                    common_c_atom += 1
                                    p = 'Z15'
                                    x = 2
                                    if (common_c_atom >= 2):
                                        x = x-1
                                    params = addparam(p, params, x)
                                elif (at1 == a2 and a1 != at0 and at1_m == 4 and not mol.GetAtom(a1).IsInRing() and b_ord == 1):
                                    common_c_atom += 1
                                    p = 'Z15'
                                    x = 2
                                    if (common_c_atom >= 2):
                                        x = x-1
                                    params = addparam(p, params, x)

    return params
##### End of Functions to single bonds #####

##### Functions to double bonds ######
def alkenes_C(atom1_mult, atom2_mult, params):
    if (atom1_mult <= atom2_mult):
        p = 'D'+str(atom1_mult)+'D'+str(atom2_mult)
    else:
        p = 'D'+str(atom2_mult)+'D'+str(atom1_mult)

    params = addparam(p, params, 1)
    return params

def two_doubles(atom1_mult, atom2_mult, atom1_min, atom2_min, params):
    if (atom1_min == 2 and atom1_mult != 1 and atom2_min == 2 and atom2_mult != 1):
        p = 'DdDd'
    elif (atom1_min == 2 and atom1_mult != 1 and (atom2_min == 1 or atom2_mult == 1)):
        p = 'D'+str(atom2_mult)+'Dd'
    elif ((atom1_min == 1 or atom1_mult == 1) and atom2_min == 2 and atom2_mult != 1):
        p = 'D'+str(atom1_mult)+'Dd'
	
    params = addparam(p, params, 1)
    return params

def nonbonded_alkenes(natoms, atom1, atom2, at1, at2, mol, atom_multiplicity, params):
    a1up = 0
    a1down = 0
    a2up = 0
    a2down = 0
    
    if atom_multiplicity [atom1-1][1] == 2 and atom_multiplicity [atom1-1][2] == 1 and atom_multiplicity [atom2-1][1] == 2 and atom_multiplicity [atom2-1][2] == 1:
        for x in range (0, natoms):
            if x != atom1-1 and x != atom2-1 and not mol.GetAtom(x+1).IsInRing():
                bond_a1 = at1.GetBond(mol.GetAtom(x+1))
                bond_a2 = at2.GetBond(mol.GetAtom(x+1))
                if bond_a1:
                    if bond_a1.IsUp():
                        a1up = atom_multiplicity [x][0]
                    elif bond_a1.IsDown():
                        a1down = atom_multiplicity [x][0]
                    else:
                        if a1up == 0 and a1down == 0:
                            a1down = atom_multiplicity [x][0]
                        elif a1down != 0 and a1up == 0:
                            a1up = atom_multiplicity [x][0]

                if bond_a2:
                    if bond_a2.IsUp():
                        a2up = atom_multiplicity [x][0]
                    elif bond_a2.IsDown():
                        a2down = atom_multiplicity [x][0]
                    else:
                        if a2up == 0 and a2down == 0:
                            a2down = atom_multiplicity [x][0]
                        elif a2down != 0 and a2up == 0:
                            a2up = atom_multiplicity [x][0]

        # print a1up, a1down, a2up, a2down
        if a1up != 0 and a2up != 0:
            if a1up in [1, 2, 3] and a2up in [1, 2, 3]:
                p = 'Z11'
                params = addparam(p, params, 1)
                
            if a1up == 4 and a2up in [1, 2, 3]:
                p = 'Z14'
                params = addparam(p, params, 1)
                
            if a1up in [1, 2, 3] and a2up == 4:
                p = 'Z14'
                params = addparam(p, params, 1)
                
            if a1up == 4 and a2up == 4:
                p = 'Z44'
                params = addparam(p, params, 1)                           
        
        if a1down != 0 and a2down != 0:
            if a1down in [1, 2, 3] and a2down in [1, 2, 3]:
                p = 'Z11'
                params = addparam(p, params, 1)                            
                
            if a1down == 4 and a2down in [1, 2, 3]:
                p = 'Z14'
                params = addparam(p, params, 1)
                
            if a1down in [1, 2, 3] and a2down == 4:
                p = 'Z14'
                params = addparam(p, params, 1)
                
            if a1down == 4 and a2down == 4:
                p = 'Z44'
                params = addparam(p, params, 1)                           
    
    return params

def non_bond_alkenes_z15(central_atom, params):
    for c in central_atom:
        if central_atom[c] == 2:
            p = 'Z15'
            params = addparam(p, params, 2)                                    

    return params
##### End of Functions to double bonds ####

###### Fucntions to triple bonds #######
def alkines_C(atom1_mult, atom2_mult, params):
    if atom1_mult <= atom2_mult:
        p = 'T'+str(atom1_mult)+'T'+str(atom2_mult)
    else:
        p = 'T'+str(atom2_mult)+'T'+str(atom1_mult)
	
    params = addparam(p, params, 1)
    return params
#### End of functions to triple bonds ####

def alka_e_i_nes_C_params(mol, nbonds, atom_multiplicity, bond_info, natoms, params):
    central_atom = {}
    for i in range(0, nbonds):
        bond_order = bond_info[i][2]
        atom1 = bond_info[i][0]
        atom2 = bond_info[i][1]
        atom1_max = atom_multiplicity [atom1-1][1]
        atom2_max = atom_multiplicity[atom2-1][1]
        atom1_mult = atom_multiplicity [atom1-1][0]
        atom2_mult = atom_multiplicity[atom2-1][0]
        atom1_min = atom_multiplicity [atom1-1][2]
        atom2_min = atom_multiplicity[atom2-1][2]
        bond = mol.GetBond(i)
        at1 = mol.GetAtom(atom1)
        at2 = mol.GetAtom(atom2)

        if not bond.IsAromatic() and not at1.IsAromatic() and not at2.IsAromatic():
            if bond_order == 1:
                # C-C DIENES E POLYENES (C=C-C=C) - both C have max bond order = 2
                if atom1_max == 2 and atom2_max == 2:
                    if atom1_mult == 3 and atom2_mult == 2 and not bond.IsInRing():
                        if atom1 in central_atom:
                            central_atom[atom1] += 1
                        else:
                            central_atom[atom1] = 1
                    elif atom2_mult == 3 and atom1_mult == 2 and not bond.IsInRing():
                        if atom2 in central_atom:
                            central_atom[atom2] += 1
                        else:
                            central_atom[atom2] = 1

                    params = dienes_polienes(atom1_mult, atom2_mult, params)
                # Diynes (C#C-C#C)
                elif atom1_max == 3 and atom2_max == 3:
                    params = diynes(atom1_mult, atom2_mult, params)    
                # Alkyne-enes (C=C-C#C)                            
                elif (atom1_max == 2 and atom2_max == 3) or (atom1_max == 3 and atom2_max == 2):
                    params = alkyenes(atom1_mult, atom2_mult, atom1_max, params)
                # C-C=
                else:
                    if atom1_max == 2 or atom2_max == 2:
                        params = single_double(atom1_max, atom1_mult, atom2_mult, params)
                    # C-C#
                    elif atom1_max == 3 or atom2_max == 3:
                        params = single_triple(atom1_max, atom1_mult, atom2_mult, params)
                    # Alkane C-C        
                    else:
                        params = alkanes_C(atom1_mult, atom2_mult, params)
			##################################################
            elif bond_order == 2:
                # C=C=C
                if ((atom2_min == 2 and atom2_mult != 1) or ((atom1_min == 1 and atom1_mult == 1) or (atom1_min == 1 and atom1_mult == 1)) or ((atom1_min == 2 and atom1_mult != 1) or (atom2_min == 1 and atom2_mult == 1))): 
                    params = two_doubles(atom1_mult, atom2_mult, atom1_min, atom2_min, params)
                # C=C    
                else:
                    params = alkenes_C(atom1_mult, atom2_mult, params)
				
                # Parameters Z11, Z14 and Z44
                if atom1_mult in [2, 3] and atom2_mult in [2, 3]:
                    params = nonbonded_alkenes(natoms, atom1, atom2, at1, at2, mol, atom_multiplicity, params)
			###################################################
            # Triple bonds (C#C)
            elif bond_order == 3:
                params = alkines_C(atom1_mult, atom2_mult, params)
    
    if central_atom != {}:
        # z15 for alkenes
        params = non_bond_alkenes_z15(central_atom, params)
        
    return params

def alkanes_H(atom_m, params):
    if atom_m == 1:
        p = 'C1H'
        params = addparam(p, params, 3)
    elif atom_m == 2:
        p = 'C2H'
        params = addparam(p, params, 2)
    elif atom_m == 3:
        p = 'C3H'
        params = addparam(p, params, 1)

    return params

def alkenes_H(atom_m, atom_min, params):
    if atom_m == 1:
        p = 'D1H'
        params = addparam(p, params, 2)
    elif atom_m == 2 and atom_min != 2:
        p = 'D2H'
        params = addparam(p, params, 1)

    return params

def alkines_H (params):
    p = 'T1H'
    params = addparam(p, params, 1)
    return params

def alka_e_i_nes_H_params(mol, natoms, atom_multiplicity, params):
    for i in range(0, natoms):
        atom_max = atom_multiplicity [i][1]
        atom_mult = atom_multiplicity [i][0]
        atom_min = atom_multiplicity [i][2]
        atom = mol.GetAtom(i+1)

        if not atom.IsAromatic():
            if atom_max == 1:
                params = alkanes_H(atom_mult, params)
            elif atom_max == 2:
                params = alkenes_H(atom_mult, atom_min, params)                
            elif atom_max == 3 and atom_mult == 1:
                params = alkines_H (params)
	
    return params

def hasAromaticRing(numRings, mol):
    aromatic = 0
    for i in range(0, numRings):
        ring = mol.GetSSSR()[i]
        if ring.IsAromatic():
            aromatic = 1

    return aromatic

def byphenyls(atom1_mult, atom2_mult, params):
    p = 'Ca'+str(atom1_mult)+'Ca'+str(atom2_mult)
    params = addparam(p, params, 1)
    return params

def benzene_bondsC(atom1_mult, atom2_mult, params):
    if atom1_mult <= atom2_mult:
        p = 'A'+str(atom1_mult)+'A'+str(atom2_mult)
    else:
        p = 'A'+str(atom2_mult)+'A'+str(atom1_mult)

    params = addparam(p, params, 1)
    return params

def alkyl_nonalkyl_benzeneC(at1, at2, a1, a2, atom1_max, atom2_max, atom1_mult, atom2_mult, params):
    # print "a1: ", a1, "a2: ", a2
    if a1 != a2 and at1.IsAromatic() and atom2_max == 1:
        p = 'C'+str(atom2_mult)+'A'+str(atom1_mult)
        params = addparam(p, params, 1)
    elif a2 != a1 and at2.IsAromatic() and atom1_max == 1:
        p = 'C'+str(atom1_mult)+'A'+str(atom2_mult)
        params = addparam(p, params, 1)
    elif a1 != a2 and at1.IsAromatic() and atom2_max == 2:
        p = 'D'+str(atom2_mult)+'A'+str(atom1_mult)
        params = addparam(p, params, 1)
    elif a2 != a1 and at2.IsAromatic() and atom1_max == 2:
        p = 'D'+str(atom1_mult)+'A'+str(atom2_mult)
        params = addparam(p, params, 1)
    elif a1 != a2 and at1.IsAromatic() and atom2_max == 3:
        p = 'T'+str(atom2_mult)+'A'+str(atom1_mult)
        params = addparam(p, params, 1)
    elif a2 != a1 and at2.IsAromatic() and atom1_max == 3:
        p = 'T'+str(atom1_mult)+'A'+str(atom2_mult)
        params = addparam(p, params, 1)
	
    return params

def benzene_bonds_H(natoms, atom_multiplicity, mol, params):
    for i in range(0, natoms):
        atom_max = atom_multiplicity [i][1]
        atom_mult = atom_multiplicity [i][0]
        atom_min = atom_multiplicity [i][2]
        atom = mol.GetAtom(i+1)

        if atom.IsAromatic():
            if atom_mult == 2:
               p = 'A'+str(atom_mult)+'H'
               params = addparam(p, params, 1)
	
    return params

def non_bonded_biphenyls(a1, a2, mol, nbonds, atom_multiplicity, bond_info, params):
    for x in range(0, nbonds):
        atom1 = bond_info[x][0]
        atom2 = bond_info[x][1]
        bond_order = bond_info[x][2]
        atom1_mult = atom_multiplicity[atom1-1][0]
        atom2_mult = atom_multiplicity[atom2-1][0]
        bond = mol.GetBond(x)

        if a1 == atom1 and atom2_mult > 2 and bond.IsAromatic():
            p = 'ZA1A'
            params = addparam(p, params, 1)
        elif a1 == atom2 and atom1_mult > 2 and bond.IsAromatic():
            p = 'ZA1A'
            params = addparam(p, params, 1)
            
        if a2 == atom1 and atom2_mult > 2 and bond.IsAromatic():
            p = 'ZA1A'
            params = addparam(p, params, 1)
        elif a2 == atom2 and atom1_mult > 2 and bond.IsAromatic():
            p = 'ZA1A'
            params = addparam(p, params, 1)    
	
    return params

def non_bonded_benzenes(a1, a2, atom1, atom2, atom1_mult, atom2_mult, non_bonded):
    at_ring = 0
    if a1 != 0 and a2 == 0 and atom2_mult <= 3:
        # a1 esta no anel e a2 e' o grupo substituinte
        p_idx = '1'
        at_ring = atom1
    elif a2 != 0 and a1 == 0 and atom1_mult <= 3:
        # a2 esta no anel e a1 e' o grupo substituinte
        p_idx = '1'
        at_ring = atom2
    elif a1 != 0 and a2 == 0 and atom2_mult == 4:
        # a1 esta no anel e a2 e' o grupo substituinte
        p_idx = '4'
        at_ring = atom1
    elif a2 != 0 and a1 == 0 and atom1_mult == 4:       
        # a2 esta no anel e a1 e' o grupo substituinte
        p_idx = '4'
        at_ring = atom1
        
    # dicionario com at_rig key e o index como valor
    if at_ring != 0:
        non_bonded[at_ring] = p_idx

    return non_bonded

def adj_nonbonded_benzenes(bond_info, nbonds, non_bonded, adj_nonbonded):
    for i in range(0, nbonds):
        atom1 = bond_info[i][0]
        atom2 = bond_info[i][1]
        if atom1 in non_bonded and atom2 in non_bonded:
            if atom1 in adj_nonbonded:
                adj_nonbonded[atom1] = int(adj_nonbonded[atom1]) + int(1)
            else:
                adj_nonbonded[atom1] = int(1)

            if atom2 in adj_nonbonded:
                adj_nonbonded[atom2] = int(adj_nonbonded[atom2]) + int(1)
            else:
                adj_nonbonded[atom2] = int(1)
	
    return adj_nonbonded

def benzene_nonbonded_p(mol, bond_info, nbonds, non_bonded, adj_nonbonded, params):
    if adj_nonbonded:
        for i in range(0, nbonds):
            atom1 = bond_info[i][0]
            atom2 = bond_info[i][1]
            if atom1 in non_bonded and atom2 in non_bonded and mol.GetAtom(atom1).IsInRing() and mol.GetAtom(atom2).IsInRing():
                if non_bonded[atom1] == non_bonded[atom2]:
                    if adj_nonbonded[atom1] == 1 and adj_nonbonded[atom2] == 1:
                        p = 'ZA'+str(non_bonded[atom1])+str(non_bonded[atom2])
                    elif adj_nonbonded[atom1] == 2 and adj_nonbonded[atom2] == 2:
                        p = 'ZA'+str(non_bonded[atom1])+'\''+str(non_bonded[atom2])+'\'' 
                    elif adj_nonbonded[atom1] == 2 and adj_nonbonded[atom2] == 1:
                        p = 'ZA'+str(non_bonded[atom1])+'\''+str(non_bonded[atom2])
                    elif adj_nonbonded[atom1] == 1 and adj_nonbonded[atom2] == 2:
                        p = 'ZA'+str(non_bonded[atom2])+'\''+str(non_bonded[atom1])
                elif non_bonded[atom1] < non_bonded[atom2]:
                    if adj_nonbonded[atom1] == 1 and adj_nonbonded[atom2] == 1:
                        p = 'ZA'+str(non_bonded[atom1])+str(non_bonded[atom2])

                    if adj_nonbonded[atom1] == 1 and adj_nonbonded[atom2] == 2:
                        p = 'ZA'+str(non_bonded[atom2])+'\''+str(non_bonded[atom1])                    

                    if adj_nonbonded[atom1] == 2 and adj_nonbonded[atom2] == 1:
                        p = 'ZA'+str(non_bonded[atom1])+'\''+str(non_bonded[atom2])

                    if adj_nonbonded[atom1] == 2 and adj_nonbonded[atom2] == 2:
                        p = 'ZA'+str(non_bonded[atom1])+'\''+str(non_bonded[atom2])+'\''
                elif non_bonded[atom1] > non_bonded[atom2]:
                    if adj_nonbonded[atom1] == 1 and adj_nonbonded[atom2] == 1:
                        p = 'ZA'+str(non_bonded[atom2])+str(non_bonded[atom1])

                    if adj_nonbonded[atom1] == 1 and adj_nonbonded[atom2] == 2:
                        p = 'ZA'+str(non_bonded[atom2])+'\''+str(non_bonded[atom1])                    

                    if adj_nonbonded[atom1] == 2 and adj_nonbonded[atom2] == 1:
                        p = 'ZA'+str(non_bonded[atom1])+'\''+str(non_bonded[atom2])

                    if adj_nonbonded[atom1] == 2 and adj_nonbonded[atom2] == 2:
                        p = 'ZA'+str(non_bonded[atom2])+'\''+str(non_bonded[atom1])+'\''
				
                params = addparam(p, params, 1)
       
    return params

def near_atomaticring(near_aromatic, params):
    for a in near_aromatic:
        if near_aromatic[a] == 2:
            p = 'ZAA'
            params = addparam(p, params, 1)                

        if near_aromatic[a] == 3:
            p = 'ZAA'
            params = addparam(p, params, 2)

        if near_aromatic[a] == 4:
            p = 'ZAA'
            params = addparam(p, params, 4)  

    return params

def aromatic_strain(numRings, mol, natoms, nbonds, atom_multiplicity, bond_info, params):
    aromatic = hasAromaticRing(numRings, mol)
    non_bonded = {}
    adj_nonbonded = {}
    near_aromatic = {}
	
    for x in range(0, natoms):                              
        near_aromatic[x] = 0
    
    if aromatic == 1:
        for i in range(0, nbonds):
            bond_order = bond_info[i][2]
            atom1 = bond_info[i][0]
            atom2 = bond_info[i][1]
            atom1_max = atom_multiplicity [atom1-1][1]
            atom2_max = atom_multiplicity[atom2-1][1]
            atom1_mult = atom_multiplicity [atom1-1][0]
            atom2_mult = atom_multiplicity[atom2-1][0]
            atom1_min = atom_multiplicity [atom1-1][2]
            atom2_min = atom_multiplicity[atom2-1][2]
            bond = mol.GetBond(i)
            at1 = mol.GetAtom(atom1)
            at2 = mol.GetAtom(atom2)
            a1 = 0
            a2 = 0
            
            for i in range(0, numRings):
                ring = mol.GetSSSR()[i]
                if ring.IsMember(bond):
                    ringSize = ring.Size()
                if ring.IsMember(at1):
                    a1 = i+1
                if ring.IsMember(at2):
                    a2 = i+1
			
            if numRings >= 2 and (atom1_mult >= 2 or atom2_mult >= 2) and ((at1.IsAromatic() and not at2.IsAromatic()) or (at2.IsAromatic() and not at1.IsAromatic())):
                if at1.IsAromatic():
                    near_aromatic[atom2 - 1] += 1
                elif at2.IsAromatic():
                    near_aromatic[atom1 - 1] += 1
			
            if bond_order == 1 and not a1 == a2 and not a1 == 0 and not a2 == 0 and at1.IsAromatic() and at2.IsAromatic():
                params = byphenyls(atom1_mult, atom2_mult, params)
                params = non_bonded_biphenyls(atom1, atom2, mol, nbonds, atom_multiplicity, bond_info, params)

            if bond.IsAromatic():
                params = benzene_bondsC(atom1_mult, atom2_mult, params)
            else:
                params = alkyl_nonalkyl_benzeneC(at1, at2, a1, a2, atom1_max, atom2_max, atom1_mult, atom2_mult, params)
                non_bonded = non_bonded_benzenes(a1, a2, atom1, atom2, atom1_mult, atom2_mult, non_bonded)

        adj_nonbonded = adj_nonbonded_benzenes(bond_info, nbonds, non_bonded, adj_nonbonded)
        params = benzene_nonbonded_p(mol, bond_info, nbonds, non_bonded, adj_nonbonded, params)
        params = benzene_bonds_H(natoms, atom_multiplicity, mol, params)
        params = near_atomaticring(near_aromatic, params)

    return params

def hasRing(numRings):
    ring = 0
    if numRings > 0:
        ring = 1

    return ring

def return_Ring(numRings, mol, atom):
    for i in range(0, numRings):
        ring = mol.GetSSSR()[i]
        if ring.IsMember(atom):
            ringSize = ring.Size()
            ring_atom = ring
            ring_num = i
	
    return ringSize, ring_atom, ring_num

def double_toRing(numRings, at1, at2, mol, atom1_mult, atom2_mult, params):
    for i in range(0, numRings):
        ring = mol.GetSSSR()[i]
        if ring.IsMember(at1) and not ring.IsMember(at2):
            ringSize = ring.Size()
            if ringSize <= 6:
                p = 'ZS'+str(ringSize)+'D3'
                params = addparam(p, params, 1)
			
            if atom2_mult == 2:
                p = 'Z1cy'
                params = addparam(p, params, 1)
            elif atom2_mult == 3:
                p = 'Z1cy'
                params = addparam(p, params, 2) 
        elif not ring.IsMember(at1) and ring.IsMember(at2):
            ringSize = ring.Size()
            if ringSize <= 6:
                p = 'ZS'+str(ringSize)+'D3'
                params = addparam(p, params, 1)

            if atom1_mult == 2:
                p = 'Z1cy'
                params = addparam(p, params, 1)
            elif atom1_mult == 3:
                p = 'Z1cy'
                params = addparam(p, params, 2)
	
    return params

def hasDouble_toRing(numRings, at1, at2, mol):
    hasDouble_toRing = False
    for i in range(0, numRings):
        ring = mol.GetSSSR()[i]
        if (ring.IsMember(at1) and not ring.IsMember(at2)) or (not ring.IsMember(at1) and ring.IsMember(at2)):
            hasDouble_toRing = True
	
    return hasDouble_toRing

def simpleC_inRing(ringSize, atom_mult, params):
    p = 'ZS'+str(ringSize)+'C'+str(atom_mult)
    params = addparam(p, params, 1)
    return params

def doubleC_inRing(numRings, nbonds, mol, ringSize, bond_info, a, atom_mult, atom_max, atom_min, params):
    # atomo entre 2 duplas C=C=C
    if ringSize == 10 and atom_max == 2 and atom_min == 2 and atom_mult == 2:
        p = 'ZD'+str(ringSize)+'Cd'
        params = addparam(p, params, 1)
    else:
        hasDouble_Ring = False
        for i in range(0, nbonds):
            bond_order = bond_info[i][2]
            atom1 = bond_info[i][0]
            atom2 = bond_info[i][1]
            at1 = mol.GetAtom(atom1)
            at2 = mol.GetAtom(atom2)
            
            if (a+1 == atom1 or a+1 == atom2) and bond_order == 2:
                hasDouble_Ring = hasDouble_toRing(numRings, at1, at2, mol)
		
        if not hasDouble_Ring:   
                p = 'ZD'+str(ringSize)+'D'+str(atom_mult)
                params = addparam(p, params, 1) 
	
    return params

def methyl_inRing(nbonds, ring, atom_mult, a, atom_multiplicity, bond_info, ringSize, params):
    ring_methyl = {}
    
    for b in range(0, nbonds):
        bond_order = bond_info[b][2]
        atom1 = bond_info[b][0]
        atom2 = bond_info[b][1]

        if atom_mult == 3:
            if (a+1 == atom1 and bond_order == 1 and not ring.IsInRing(atom2) and atom_multiplicity[atom2-1][0] == 1) or (a+1 == atom2 and bond_order == 1 and not ring.IsInRing(atom1) and atom_multiplicity[atom1-1][0] == 1):
                p = 'ZS'+str(ringSize)+'C3m'
                params = addparam(p, params, 1)
            elif (a+1 == atom1 and bond_order == 1 and not ring.IsInRing(atom2) and atom_multiplicity[atom2-1][0] > 1) or (a+1 == atom2 and bond_order == 1 and not ring.IsInRing(atom1) and atom_multiplicity[atom1-1][0] > 1):
                p = 'ZS'+str(ringSize)+'C3'
                params = addparam(p, params, 1)
        elif atom_mult == 4:
            if (a+1 == atom1 and bond_order == 1 and not ring.IsInRing(atom2) and atom_multiplicity[atom2-1][0] == 1) or (a+1 == atom2 and bond_order == 1 and not ring.IsInRing(atom1) and atom_multiplicity[atom1-1][0] == 1):
                if a+1 in ring_methyl:
                    ring_methyl[a+1] += 1
                else:
                    ring_methyl[a+1] = 1
            elif (a+1 == atom1 and bond_order == 1 and not ring.IsInRing(atom2) and atom_multiplicity[atom2-1][0] > 1) or (a+1 == atom2 and bond_order == 1 and not ring.IsInRing(atom1) and atom_multiplicity[atom1-1][0] > 1):
                ring_methyl[a+1] = 0
	
    for m in ring_methyl:
        if ring_methyl[m] == 1:
            p = 'ZS'+str(ringSize)+'C4m'
            params = addparam(p, params, 1)
        elif ring_methyl[m] == 2:
            p = 'ZS'+str(ringSize)+'C4mm'
            params = addparam(p, params, 1)
        elif ring_methyl[m] == 0:
            p = 'ZS'+str(ringSize)+'C4'
            params = addparam(p, params, 1)        
	
    return params

def isopropenyl_inRing(nbonds, mol, atom_mult, a, atom_multiplicity, bond_info):
    hasPropenyl = False
    
    for b in range(0, nbonds):
        bond_order = bond_info[b][2]
        atom1 = bond_info[b][0]
        atom2 = bond_info[b][1]
        at1 = mol.GetAtom(atom1)
        at2 = mol.GetAtom(atom2)

        if atom_mult > 2:
            if (a+1 == atom1 and bond_order == 1 and not at2.IsInRing() and atom_multiplicity[atom2-1][0] == 3 and atom_multiplicity[atom2-1][1] == 2) or (a+1 == atom2 and bond_order == 1 and not at1.IsInRing() and atom_multiplicity[atom1-1][0] == 3 and atom_multiplicity[atom1-1][1] == 2):
                hasPropenyl = True
	
    return hasPropenyl

def molfile(molf):
    obc = openbabel.OBConversion()
    obc.SetInFormat("mol")
    mymol = openbabel.OBMol()
    obc.ReadFile(mymol, '/storage/'+molf)
    return mymol

def uniq(list):
    # element order preserved
    set1 = []
    set2 = []
    list2 = []
    for l in list:
        list2.append(l.GetIndex)
	
    i = 0
    for l in list2:
        if l not in set1:
            set1.append(l)
            set2.append(list[i])
        i = i + 1

    return set2

def cis12_inRing(molf, params):
    mymol = molfile(molf)
    numRings = countRings(mymol)
    natoms = mymol.NumAtoms()
    nbonds = mymol.NumBonds()
	
    for r in range(0, numRings):
        w = []
        h = []
        n = []
        ring = mymol.GetSSSR()[r]
        ringSize = ring.Size()
        if not ring.IsAromatic() and ringSize in [3, 4, 5]:
            for b in range (0, nbonds):
                bond = mymol.GetBond(b)
                if bond.IsSingle():
                    if bond.IsWedge():
                        at1 = bond.GetBeginAtom()
                        at2 = bond.GetEndAtom()
                        if ring.IsMember(at1) and not at2.IsInRing():
                            w.append(at1)
                        elif ring.IsMember(at2) and not at1.IsInRing():
                            w.append(at2)

                    if bond.IsHash():
                        at1 = bond.GetBeginAtom()
                        at2 = bond.GetEndAtom()
                        if ring.IsMember(at1) and not at2.IsInRing():
                            h.append(at1)
                        elif ring.IsMember(at2) and not at1.IsInRing():
                            h.append(at2)

                    if not bond.IsHash() and not bond.IsWedge():
                        at1 = bond.GetBeginAtom()
                        at2 = bond.GetEndAtom()
                        if at1.IsCarbon() and at2.IsCarbon():
                            if ring.IsMember(at1) and not at2.IsInRing() and at1 not in n:
                                n.append(at1)
                            if ring.IsMember(at2) and not at1.IsInRing() and at2 not in n:
                                n.append(at2)
        
        i = 0
        for x in range(i, len(w)):
            i = i + 1
            for y in range(i, len(w)):
                if w[x].GetBond(w[y]):
                    p = 'Z'+str(ringSize)+'c12'
                    params = addparam(p, params, 1)
        
        i = 0
        for x in range(i, len(h)):
            i = i + 1
            for y in range(i, len(h)):
                if h[x].GetBond(h[y]):
                    p = 'Z'+str(ringSize)+'c12'
                    params = addparam(p, params, 1)
        
        i = 0
        for x in range(i, len(n)-(len(w)+len(h))):
            i = i + 1
            for y in range(i, len(n)-(len(w)+len(h))):
                if n[x].GetBond(n[y]):
                    p = 'Z'+str(ringSize)+'c12'
                    params = addparam(p, params, 1)
	
    return params

def trans13_inRing(molf, params):
    mymol = molfile(molf)
    numRings = countRings(mymol)
    natoms = mymol.NumAtoms()
    nbonds = mymol.NumBonds()
	
    for r in range(0, numRings):
        w = []
        h = []
        ring = mymol.GetSSSR()[r]
        ringSize = ring.Size()
		
        if not ring.IsAromatic() and ringSize == 5:
            for b in range (0, nbonds):
                bond = mymol.GetBond(b)
                if bond.IsWedge():
                    at1 = bond.GetBeginAtom()
                    at2 = bond.GetEndAtom()
                    if ring.IsMember(at1):
                        w.append(at1)
                    elif ring.IsMember(at2):
                        w.append(at2)
                if bond.IsHash():
                    at1 = bond.GetBeginAtom()
                    at2 = bond.GetEndAtom()
                    if ring.IsMember(at1):
                        h.append(at1)
                    elif ring.IsMember(at2):
                        h.append(at2)
		
        for x in range(0, len(w)):
            for y in range(0, len(h)):
                if w[x].IsOneThree(h[y]):
                    p = 'Z'+str(ringSize)+'t13'
                    params = addparam(p, params, 1)
	
    return params

def trans13_cis12_cis14_inRing(molf, params, atom_multiplicity):
    mymol = molfile(molf)
    numRings = countRings(mymol)
    natoms = mymol.NumAtoms()
    nbonds = mymol.NumBonds()
    w = []
    h=[]
	
    for r in range(0, numRings):
        w = []
        h = []
        ring = mymol.GetSSSR()[r]
        ringSize = ring.Size()
		
        if not ring.IsAromatic() and ringSize == 6:
            for a in range (0, len(atom_multiplicity)):
                atom_mult = atom_multiplicity[a][0]
                at = atom_multiplicity[a-1][1]
                at1 = mymol.GetAtom(at)
                if ring.IsMember(at1):
                    if atom_mult == 4:
                        p = 'Z'+str(ringSize)+'ax'
                        params = addparam(p, params, 1)
			
            for b in range (0, nbonds):
                bond = mymol.GetBond(b)
                if bond.IsWedge():
                    at1 = bond.GetBeginAtom()
                    at2 = bond.GetEndAtom()
                    if ring.IsMember(at1):
                        w.append(at1)
                    elif ring.IsMember(at2):
                        w.append(at2)
				
                if bond.IsHash():
                    at1 = bond.GetBeginAtom()
                    at2 = bond.GetEndAtom()
                    if ring.IsMember(at1):
                        h.append(at1)
                    elif ring.IsMember(at2):
                        h.append(at2)
    # funcao onefour nao esta correcta, temos de nos salvaguardar, para que o parametro n seja adicionado 2 vezes
    v = 0
    # trans 1,3
    for x in range(0, len(w)):
        for y in range(0, len(h)):
            if w[x].IsOneThree(h[y]):
                p = 'Z'+str(ringSize)+'ax'
                params = addparam(p, params, 1)
    # cis 1,2
    i = 0
    for x in range(i, len(w)):
        i = i + 1
        for y in range(i, len(w)):
            if w[x].GetBond(w[y]):
                p = 'Z'+str(ringSize)+'ax'
                params = addparam(p, params, 1)
                v = 1
	
    i = 0
    for x in range(i, len(h)):
        i = i + 1
        for y in range(i, len(h)):
            if h[x].GetBond(h[y]):
                p = 'Z'+str(ringSize)+'ax'
                params = addparam(p, params, 1)
                v = 1
    # cis 1,4
    i = 0
    for x in range(i, len(w)):
        i = i + 1
        for y in range(i, len(w)):
            if w[x].IsOneFour(w[y]) and not w[x].IsOneThree(w[y]) and v == 0:
                p = 'Z'+str(ringSize)+'ax'
                params = addparam(p, params, 1)
	
    i = 0
    for x in range(i, len(h)):
        i = i + 1
        for y in range(i, len(h)):
            if h[x].IsOneFour(h[y]) and v == 0:
                p = 'Z'+str(ringSize)+'ax'
                params = addparam(p, params, 1)
	
    return params   

def onetwosubs_inRing(molf, nbonds, ring, ring_atom, atom_mult, a, ringSize, isomers, ring_iso, params):
    mymol = molf(molf)
    bond_info = bonfinfo(mymol)
	
    for b in range(0, nbonds):
        bond_order = bond_info[b][2]
        atom1 = bond_info[b][0]
        atom2 = bond_info[b][1]

        if (a+1 == atom1 and bond_order == 1 and not ring.IsInRing(atom2)) or (a+1 == atom2 and bond_order == 1 and not ring.IsInRing(atom1)):
            atom = mol.GetAtom(a+1)
            ring_iso[a+1] = ring_atom
            if atom.IsChiral():
                bond = mol.GetBond(b)
                if bond.IsWedge(): 
                    isomers[a+1] = 1
                elif atom.IsHash():
                    isomers[a+1] = 2
	
    return isomers, ring_iso

def r56hasSubs(hasConj, hasSubs, hasDouble, ringSize_num, ringHasPropenyl, params):
    # print "hasSusbs: ", hasSubs, "hasDouble: ", hasDouble, "ring size: ", ringSize_num
    if len(hasConj.keys()) > 0 and  len(hasDouble.keys()) > 0 and len(ringSize_num.keys()) > 0 and len(ringHasPropenyl.keys()) > 0:
        for x in hasSubs:
            if hasSubs[x] == 0 and hasDouble[x] != 0 and ringSize_num[x] == 5:
                p = 'Z5'
                params = addparam(p, params, 1)
            elif hasSubs[x] <= 1 and hasDouble[x] == 1 and ringSize_num[x] == 6:
                p = 'Z6'
                params = addparam(p, params, 2)
            elif hasSubs[x] == 2 and hasDouble[x] == 1 and ringSize_num[x] == 6:
                p = 'Z6'
                params = addparam(p, params, 4)
            elif hasDouble[x] == 2 and hasSubs[x] == 0 and hasConj[x] and ringSize_num[x] == 6:
                p = 'Z6'
                params = addparam(p, params, 8)
            elif hasDouble[x] == 2 and (hasSubs[x] == 1 or hasSubs[x] == 2) and hasConj[x] and ringSize_num[x] == 6:
                p = 'Z6'
                params = addparam(p, params, 5)
			
            if hasSubs[x] > 0 and ringHasPropenyl[x] and (hasDouble[x] == 1 or hasDouble[x] == 2) and ringSize_num[x] == 6:
                p = 'Z6s'
                params = addparam(p, params, 1)
	
    return params

def countdouble_inring(hasDouble, nbonds, mol, numRings, bond_info):
    for x in range(0, numRings):                              
        hasDouble[x] = 0
            
    for b in range(0, nbonds):
        bond_order = bond_info[b][2]
        atom1 = bond_info[b][0]
        atom2 = bond_info[b][1]
        if bond_order == 2:
            at1 = mol.GetAtom(atom1)
            at2 = mol.GetAtom(atom2)
            for i in range(0, numRings):
                ring = mol.GetSSSR()[i]
                if ring.IsMember(at1) and ring.IsMember(at1) and not at1.IsAromatic() and not at2.IsAromatic():
                    ring_num = i
                    hasDouble[i] += 1
	
    return hasDouble

def pos_double_inring(ring_num, ringSize, mol, nbonds, bond_info):
    positions = {}
    for x in range(0, ringSize):
        positions[x] = 0
        
    ring = mol.GetSSSR()[ring_num]
    i = -1
    for b in range(0, nbonds):
        bond_order = bond_info[b][2]
        atom1 = bond_info[b][0]
        atom2 = bond_info[b][1]
        at1 = mol.GetAtom(atom1)
        at2 = mol.GetAtom(atom2)
        if ring.IsMember(at1) and ring.IsMember(at1):
            i += 1
            if bond_order == 2:
                positions[i] = 1
	
    return positions

def hasConjBonds_inring(hasConj, nbonds, mol, atom_multiplicity, numRings, bond_info):
    for x in range(0, numRings):                              
        hasConj[x] = False
	
    for b in range(0, nbonds):
        bond_order = bond_info[b][2]
        atom1 = bond_info[b][0]
        atom2 = bond_info[b][1]
        at1 = mol.GetAtom(atom1)
        at2 = mol.GetAtom(atom2)
        bond = mol.GetBond(b)
        atom1_max = atom_multiplicity[atom1-1][1]
        atom2_max = atom_multiplicity[atom2-1][1]

        for i in range(0, numRings):
            ring = mol.GetSSSR()[i]
            if ring.IsMember(at1) and ring.IsMember(at2) and not at1.IsAromatic() and not at2.IsAromatic() and bond_order == 1 and atom1_max == 2 and atom2_max == 2:
                ring_num = i
                hasConj[i] = True
	
    return hasConj

def conformational_double_inring(numRings, mol, nbonds, bond_info, hasDouble, params):
    positions = {}
    for nr in range(0, numRings):
        ring = mol.GetSSSR()[nr]
        ringSize = ring.Size()
        a = 0
        b = 0
        c = 0
        d = 0
        aux_a = 0
    
        if ringSize in [7, 8, 10] and hasDouble[nr] >= 2 and hasDouble[nr] <= 4:
            positions = pos_double_inring(nr, ringSize, mol, nbonds, bond_info)
            for p in positions:
                if positions[p] == 1:
                    if a == 0:
                        a = 1
                        aux_a = p 
                    elif a!= 0 and b == 0:
                        b = p-aux_a + 1
                    elif a != 0 and b != 0 and c == 0:
                        c = p-aux_a + 1
                    elif a != 0 and b != 0 and c != 0 and d == 0:
                        d = p-aux_a + 1
            
            if ringSize == 7 and hasDouble[nr] == 2 and a == 1 and b == 3:
                p = 'Z7u13'
                params = addparam(p, params, 1)
            elif ringSize == 7 and hasDouble[nr] == 3 and a == 1 and b == 3 and c == 5:
                p = 'Z7u135'
                params = addparam(p, params, 1)
            elif ringSize == 8 and hasDouble[nr] == 3 and a == 1 and b == 3 and c == 6:
                p = 'Z8u136'
                params = addparam(p, params, 1)
            elif ringSize == 8 and hasDouble[nr] == 4 and a == 1 and b == 3 and c == 5 and d == 7:
                p = 'Z8u1357'
                params = addparam(p, params, 1)
            elif ringSize == 10 and hasDouble[nr] == 4 and a == 1 and b == 2 and c == 6 and d == 7:
                p = 'Z10u1267'
                params = addparam(p, params, 1)
	
    return params

# rings having double bonds in E conformation
def ebonds_ring(ebonds, ringSize, hasDouble, positions, params):
    if ringSize == 8 and hasDouble == 1 and ebonds == "1":
        p = 'Z8uE'
        params = addparam(p, params, 1)
        
    if ringSize == 8 and ebonds == "2" and hasDouble == 2:
        p = 'Z8uEE'
        params = addparam(p, params, 1)
	
    if ringSize == 8 and ebonds == "1" and hasDouble == 2:
        p = 'Z8uZE'
        params = addparam(p, params, 1)
	
    if ringSize == 12 and ebonds == "3" and hasDouble == 3:
        a = 0
        b = 0
        c = 0
        aux_a = 0
		
        for p in positions:
            if positions[p] == 1:
                if a == 0:
                    a = 1
                    aux_a = p
                elif a!= 0 and b == 0:
                    b = p-aux_a + 1
                elif a != 0 and b != 0 and c == 0:
                    c = p-aux_a + 1
        
        if a == 1 and b == 5 and c == 9:                                                                                                                                                
            p = 'Z12u159E'
            params = addparam(p, params, 1)
	
    return params

def zbonds_ring(ringSize, hasDouble, params):
    if ringSize == 8 and hasDouble == 2:
        p = 'Z8uZZ'
        params = addparam(p, params, 1)

    return params

def z11z14_double_ring(ebonds, ringSize, hasDouble, params, nbonds, natoms, bond_info, atom_multiplicity, mol):
    ebonds_done = 0
    key = 'Z11'
    key2 = 'Z14'
	
    if (not key in params and not key2 in params):
        if ringSize in [8, 12] and hasDouble >= int(ebonds) and int(ebonds) > 0:
            for b in range(0, nbonds):
                bond_order = bond_info[b][2]
                atom1 = bond_info[b][0]
                atom2 = bond_info[b][1]
                at1 = mol.GetAtom(atom1)
                at2 = mol.GetAtom(atom2)
                atom1_mult = atom_multiplicity[atom1-1][0]
                atom2_mult = atom_multiplicity[atom2-1][0]
            
                if (bond_order == 2 and int(ebonds) > ebonds_done and at1.IsInRing() and at2.IsInRing() and (atom1_mult == 3 or atom2_mult == 3)):
                    for a in range(0, natoms):
                        atx = mol.GetAtom(a+1)
                        atx_mul = 0
						
                        if (atom1_mult == 3 and int(ebonds) > ebonds_done):
                            if (atx.IsConnected(at1) and not atx.IsInRing()):
                                atx_mul = atom_multiplicity[a][0]
						
                        if (atom2_mult == 3 and int(ebonds) > ebonds_done):
                            if (atx.IsConnected(at2) and not atx_mul == 4 and not atx.IsInRing()):
                                atx_mul = atom_multiplicity[a][0]
                        
                        if (not atx_mul == 4 and not atx_mul == 0 and int(ebonds) > ebonds_done):
                            p = 'Z11'
                            params = addparam(p, params, 1)
                            ebonds_done = ebonds_done + 1
                        elif (atx_mul == 4 and not atx_mul == 0 and int(ebonds) > ebonds_done):
                            p = 'Z14'
                            params = addparam(p, params, 1)
                            ebonds_done = ebonds_done + 1
	
    return params

# cycloalkanes and cycloalkenes
def rings_strain(ebonds, numRings, mol, natoms, nbonds, atom_multiplicity, bond_info, params, molf):
    ebonds = ebonds.strip()
     
    if hasRing(numRings) == 1:
        isomers = {}
        ring_iso = {}
        hasDouble = {}
        ringSize_num = {}
        params = cis12_inRing(molf, params)
        params = trans13_inRing(molf, params)
        params = trans13_cis12_cis14_inRing(molf, params, atom_multiplicity)
        hasDouble = countdouble_inring(hasDouble, nbonds, mol, numRings, bond_info)
		
        for b in range(0, nbonds):
            bond_order = bond_info[b][2]
            atom1 = bond_info[b][0]
            atom2 = bond_info[b][1]
            at1 = mol.GetAtom(atom1)
            at2 = mol.GetAtom(atom2)
            atom1_mult = atom_multiplicity[atom1-1][0]
            atom2_mult = atom_multiplicity[atom2-1][0]

            if not at1.IsAromatic() and not at2.IsAromatic() and bond_order == 2:                                            
                # ligacao dupla para fora do anel
                params = double_toRing(numRings, at1, at2, mol, atom1_mult, atom2_mult, params)
		
        hasSubs = {}
        ringHasPropenyl = {}
        hasConj = {}
       
        for d in range(0, numRings):                              
            hasSubs[d] = 0
            ringHasPropenyl[d] = False
            ring = mol.GetSSSR()[d]
            ringSize = ring.Size()
                        
            if ebonds != "0" and ringSize in [8, 12]:
                positions = pos_double_inring(d, ringSize, mol, nbonds, bond_info) 
                params = ebonds_ring(ebonds, ringSize, hasDouble[d], positions, params)
                params = z11z14_double_ring(ebonds, ringSize, hasDouble, params, nbonds, natoms, bond_info, atom_multiplicity, mol)
            elif ebonds == "0" and ringSize == 8:
                params = zbonds_ring(ringSize, hasDouble[d], params)
        
        position = 0
        for a in range(0, natoms): 
            atom = mol.GetAtom(a+1)
            if atom.IsInRing() and not atom.IsAromatic():
                atom_max = atom_multiplicity [a][1]
                atom_mult = atom_multiplicity[a][0]
                atom_min = atom_multiplicity [a][2]
                ringSize, ring_atom, ring_num = return_Ring(numRings, mol, atom)
                ringSize_num[ring_num] = ringSize

                if atom.HasDoubleBond():  
                    params = doubleC_inRing(numRings, nbonds, mol, ringSize, bond_info, a, atom_mult, atom_max, atom_min, params)
                elif ringSize == 3 and (atom_mult == 3 or atom_mult == 4):
                    # print "metil", a
                    params = methyl_inRing(nbonds, ring_atom, atom_mult, a, atom_multiplicity, bond_info, ringSize, params)
                    # print params
                elif atom_max == 1:
                    params = simpleC_inRing(ringSize, atom_mult, params)
        
                #if ringSize in [3,4,5]  and atom_mult >= 3:
                    #if (ringSize == 3 or ringSize == 4 or ringSize == 5) and atom_mult >= 3:
                    #print "not done"
                    #isomers, ring_iso = onetwosubs_inRing(nbonds, ring, ring_atom, atom_mult, a, bond_info, ringSize, isomers, ring_iso, params)
				
                if (ringSize == 5 or ringSize == 6) and atom_mult > 2:
                    hasSubs[ring_num] += 1
                    hasPropenyl = isopropenyl_inRing(nbonds, mol, atom_mult, a, atom_multiplicity, bond_info)

                    if hasPropenyl:
                        ringHasPropenyl[ring_num] = True
				
                if position == 0 and atom_mult == 4:
                    position = 1
                elif position > 0:
                    position = position + 1
				
                if ringSize == 10 and atom_mult == 4 and position in [2, 3, 5, 7, 10]:
                    p = 'Z10int'
                    params = addparam(p, params, 1)
        
        hasConj = hasConjBonds_inring(hasConj, nbonds, mol, atom_multiplicity, numRings, bond_info)
        params = r56hasSubs(hasConj, hasSubs, hasDouble, ringSize_num, ringHasPropenyl, params)
        params = conformational_double_inring(numRings, mol, nbonds, bond_info, hasDouble, params)

        #for r in range(0, numRings):
			#iso_temp = {}
			#for atom_r in ring_iso:
				#if r == ring_iso[atom_r]:
					#iso_temp[atom_r] = isomers[atom_r]

            #for a_r in iso_temp:
				#print "allo"
                #combinacoes entre os atomos, estao ligados? sao cis?
	
    return params

def testageometria(mol):
    natoms = numatoms(mol)
    nbonds = numbonds(mol)
	
    for i in range(0, natoms):
        atom = mol.GetAtom(i+1)
        # print i
        if atom.IsAxial():
            print "atomo axial"

        if atom.IsChiral():
            print "atomo chiral"

        if atom.IsClockwise():
            print "atomo clockwise"

        if atom.IsAntiClockwise():
            print "atomo anti cloclwise"

        if atom.IsPositiveStereo():
            print "atomo positive stereo"

        if atom.IsNegativeStereo():
            print "atomo negative stereo"

        if atom.IsWedge():
            print "bond is wedge"
		
        if atom.IsHash():
            print "bond is hash"
		
        print "--------"

    for b in range(0, nbonds):     
        bond = mol.GetBond(b)
        print b
        if bond.IsUp():
            print "bond is up"
        if bond.IsDown():
            print "bond is down"
        if bond.IsWedge():
            print "bond is wedge"
        if bond.IsHash():
            print "bond is hash"
        print bond.GetFlags()
            
        print "--------"

##################### TESTING ###################
def test():
    ent_pkl = open('ch_ent.pkl', 'rb')
    ent = pickle.load(ent_pkl)

    gas = 0
    liq = 0
    predicted={}
    myfile = open('teste.txt', 'w')
    myfile.write ('SMILES ---- RGAS ---- PredictedGAS -----TRUE?---- Rliq ---- predictedLIQ ----TRUE? \n')
    
    for smi, gas in ent.iteritems():
        gas = ent[smi][0]
        liq = ent[smi][1]
        
        if (smi != ''):
            predicted = run(smi)
            predicted['gas'] = float(predicted['gas'])
            if gas != "-":   
                gas = float(gas)
			
            predicted['liq'] = float(predicted['liq'])
            if liq != "-":
                liq = float(liq)
			
            if gas == "-":
                g = "not available"
            elif abs(gas-predicted['gas'])<0.1:
                g = 'TRUE'
            else:
                g= 'FALSE'

            if liq == "-":
                l = "not available"
            elif abs(liq-predicted['liq'])<0.1:
                l = 'TRUE'
            else:
                l= 'FALSE'
			
            toFile = smi+' ---- '+str(gas)+'----'+str(predicted['gas'])+' ---- '+str(g)+' ---- '+str(liq)+'----'+str(l)+'---- '+str(predicted['liq'])+'\n'
            #toFile="%s----%6.2f----%f----%f----%f----%f----%f\n" %( smi, gas, predicted['gas'], g, liq, l, predicted['lq'])             
            myfile.write (toFile) 

#smiles = sys.argv[1] # Receber o SMILES da linha de comandos
def run(smiles, ebonds, molf):
    obConversion = openbabel.OBConversion()
    obConversion.SetInAndOutFormats("smi", "mdl")
    mol = openbabel.OBMol()
    obConversion.ReadString(mol, smiles)
    ######
    #testageometria(mol)
    mol.DeleteHydrogens()

    if mol.Empty():
        print "There aren't any carbon atoms in this molecule."
    elif not only_C(mol):
        #print "The method is only available for hydrocarbons"
        print "Error1";
    else:
        #print "Compound Name: ", compoundName(smiles)
        print "xpto"
        #print "Atoms: ", numatoms(mol)
        #print "Bonds: ", numbonds(mol)
        #print "Single Bonds: ", countsingleb(mol)
        #print "Double Bonds: ", countdoubleb(mol)
        #print "Triple Bonds: ", counttripleb(mol)
        #print "Ring Bonds: ", countringb(mol)
        #print "Aromatic Bonds: ", countaromaticb(mol)
        #print "Bond info (atom 1, atom 2, bond order): ", bondinfo(mol)
        #print "Atom multiplicity (multiplicity, max order, min order): ", atommultiplicity(mol)
		
        # Variables
        natoms = numatoms(mol)
        nbonds = numbonds(mol)
        sing_bonds = countsingleb(mol)
        double_bonds = countdoubleb(mol)
        triple_bonds = counttripleb(mol)
        ring_bonds = countringb(mol)
        aromatic_bonds = countaromaticb(mol)
        bond_info = bondinfo(mol)
        atom_mult = atommultiplicity(mol)
        params = {}
        num_rings = countRings(mol)
        valid = 0
    
        if nbonds == 0:
            methane(params)
        # Alkanes    
        else:
            #alkanes(atom_mult, bond_info, nbonds, natoms, params)
            alka_e_i_nes_H_params(mol, natoms, atom_mult, params)
            alka_e_i_nes_C_params(mol, nbonds, atom_mult, bond_info, natoms, params)
            non_bond_alkanes(mol, natoms, nbonds, bond_info, atom_mult, params)
            non_bond_alkanes_special(mol, natoms, nbonds, bond_info, atom_mult, params)

            if ring_bonds > 0:
                rings_strain(ebonds, num_rings, mol, natoms, nbonds, atom_mult, bond_info, params, molf)

            if aromatic_bonds > 0:
                aromatic_strain(num_rings, mol, natoms, nbonds, atom_mult, bond_info, params)
        if len(params) == 0:
            print "Error2"
            #print "SMILES is not valid"
        else:
            ###### Enthalpies prediction
            # Read the pickle files containing the parameters for gas and liquid phase
            gas_pkl = open('storage/p_gas.pkl', 'rb')
            param_gas = pickle.load(gas_pkl)
            liq_pkl = open('storage/p_liq.pkl', 'rb')
            param_liq = pickle.load(liq_pkl)
            vap_pkl = open('storage/p_vap.pkl', 'rb')
            param_vap = pickle.load(vap_pkl)

            gas_zerop = []
            liq_zerop = []
            vap_zerop = []
            gas_ent = 0
            liq_ent = 0
            vap_ent = 0

            for p in params:
                if p in param_gas:
                    gas_ent += float(param_gas[p])*params[p]
                    if float(param_gas[p]) == 0:
                        gas_zerop.append(p)
                else:
                    valid = 1
				
                if p in param_liq:   
                    liq_ent += float(param_liq[p])*params[p]
                    if float(param_liq[p]) == 0:
                        liq_zerop.append(p)
                else:
                    valid = 1

                if p in param_vap:
                    vap_ent += float(param_vap[p])*params[p]
                    if float(param_vap[p]) == 0:
                        vap_zerop.append(p)
                else:
                    valid = 1

            if valid == 0:        
                #print "Gas enthalpy = ", gas_ent, "kJ/mol"
                print gas_ent
                #print "Liquid enthalpy = ", liq_ent, "kJ/mol"
                print  liq_ent
                print vap_ent
                #print "Parameters: ", params
                print  params
                print len(gas_zerop)
                print len(liq_zerop)
                print len(vap_zerop)
                print gas_zerop
                print liq_zerop
                print vap_zerop
				
                return {'gas': gas_ent, 'liq': liq_ent, 'vap': vap_ent}
            else:
                print "Error2"
                #print "SMILES is not valid"

if __name__=="__main__":
    run(sys.argv[1], sys.argv[2], sys.argv[3])