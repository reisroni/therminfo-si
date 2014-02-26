import openbabel
import pybel
import sys

def run(molf):
	obconversion = openbabel.OBConversion()
	obconversion.SetInAndOutFormats("mol", "can")
	obmol = openbabel.OBMol()
	mymol = obconversion.ReadFile(obmol, molf)
	can = obconversion.WriteString(obmol)
	print can

if __name__=="__main__":
	run(sys.argv[1])