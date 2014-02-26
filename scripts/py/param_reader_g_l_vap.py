import csv
import pickle


#read the csv file with the parameters
parametersFile = csv.reader(open('scripts/py/params/p_06_12_g_l_vap.csv'), delimiter=';')

param_gas = {}
param_liq = {}
param_vap = {}

#create 2 dictionaries, 1 for the gas phase and another one to the liquid phase
for row in parametersFile:
    param_gas[row[0]] = row[1]
    param_liq[row[0]] = row[2]
    param_vap[row[0]] = row[3]
print param_gas
print param_liq
print param_vap

#pickle the dictionaries

pck_pgas = open('storage/p_gas.pkl', 'wb')
pickle.dump(param_gas, pck_pgas)

pck_pliq = open('storage/p_liq.pkl', 'wb')
pickle.dump(param_liq, pck_pliq)

pck_pvap = open('storage/p_vap.pkl', 'wb')
pickle.dump(param_vap, pck_pvap)

pck_pgas.close()
pck_pliq.close()
pck_pvap.close()
