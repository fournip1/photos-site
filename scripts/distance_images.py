# Ce script verifie que des images sont identiques ou pas


import os;
from os import chdir;
import csv;
import numpy;
from numpy import *;
import PIL;
from PIL import Image;


###################################
# definition des parametres       #
###################################

suffixes=[".jpg",".JPG",".jpeg",".JPEG",".png",".gif",".bmp",".webp"];
current_dir=os.getcwd();
csv_output="distances.csv";
ncut = 16;
pcut = 16;

###################################
# recuperer les fichiers          #
###################################

# On parcourt recurisvement le répertoire
selected_files = []
for path, dirs, files in os.walk(current_dir):
    for filename in files:
        base,ext=os.path.splitext(filename); # recuperer l'extension voulue
        if ext in suffixes:
            # recuperer le fichier dans la liste
            selected_files.append([os.path.join(path, filename),filename]); # premier element: chemin complet, second: nom de base

print("Il y a " + str(len(selected_files)) + " fichiers selectionnés.")

            
###################################
# creer une signature par fichier #
###################################

print("Création des signatures.")

''' pour chaque fichier, on calcule:
* son format
* on divise les lignes en ncut lignes et les colonnes en pcut colonnes:
    on obtient un tableau ncut x pcut
    pour chaque cellule, on calcule le niveau de gris moyen
    on obtient une liste de longueur ncut x pcut contenant les niveaux de gris moyens
'''

signatures=[]
for f in range(len(selected_files)):
    gray_values=[]
    I = asarray(PIL.Image.open(selected_files[f][0]).convert('L'))
    n,p = shape(I)
    for i in range(ncut):
        for j in range(pcut):
            gray_values.append(mean(I[i*n//ncut:(i+1)*n//ncut,j*p//pcut:(j+1)*p//pcut]))
    # on ajoute [chemin, nom du fichier], format (n/p), moyennes des gris pour l'ensemble des pixels de l'image restreinte
    signatures.append([selected_files[f],n/p,asarray(gray_values)])


###################################
# calculer les distances          #
###################################

print("Calcul des distances.")

distances=[]
for f in range(len(selected_files)):
    for g in range(f+1,len(selected_files)):
        # calcul d'une distance entre les formes des images
        d_shape=abs(signatures[f][1]-signatures[g][1])
        # calcul d'une distance entre les niveaux de gris de l'image restreinte
        if d_shape<=0.01:
            d_gray=sum(abs(signatures[f][2]-signatures[g][2]))/(ncut*pcut*256)
            if d_gray<=0.1:
                distances.append([signatures[f][0][1],signatures[g][0][1],d_shape,d_gray,max([d_shape,d_gray])])
        

###################################
# ecrire la sortie                #
###################################

f=open(csv_output, mode='w');
writer=csv.writer(f,delimiter=';',quotechar='"');
writer.writerows(distances);
f.close();
