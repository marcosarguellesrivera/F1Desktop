import xml.etree.ElementTree as et

get_tramos = ".//{http://www.uniovi.es}circuito//{http://www.uniovi.es}tramos"
get_puntos = "{http://www.uniovi.es}punto"


def main(archivo):
    try:
        xml = et.parse(archivo)
    except IOError:
        print(f'El archivo {archivo} no existe')
    except et.ParseError:
        print(f'Error parseando el archivo {archivo}')
    
    final = input("Nombre del archivo final: ")
    
    try:
        archivoFinal = open(final, 'w')
    except IOError:
        print(f'Error al crear el archivo {final}')
    
    principioKml(archivoFinal, final)
    escribirCoordenadas(archivo.getroot(), archivoFinal)
    finalKml(archivoFinal)

def principioKml(archivo, nombreArchivo):
    archivo.write('<?xml version="1.0" encoding="UTF-8"?>\n')
    archivo.write('<kml xmlns="http://www.opengis.net/kml/2.2">\n')
    archivo.write("<Document>\n")
    archivo.write("<Placemark>\n")
    archivo.write("<name>" + nombreArchivo + "</name>\n")
    archivo.write("<LineString>\n")
    archivo.write("<extrude>1</extrude>\n")
    archivo.write("<tessellate>1</tessellate>\n")
    archivo.write("<coordinates>\n")

def escribirCoordenadas(raiz, archivo):
    puntos = raiz.find(get_tramos).findAll(get_puntos)
    for punto in puntos:
        str = ""


def finalKml(archivo):
    archivo.write("</coordinates>\n")
    archivo.write("<altitudeMode>relativeToGround</altitudeMode>\n")
    archivo.write("</LineString>\n")
    archivo.write("<Style> id='lineaRoja'>\n")
    archivo.write("<LineStyle>\n")
    archivo.write("<color>#ff0000ff</color>\n")
    archivo.write("<width>5</width>\n")
    archivo.write("</LineStyle>\n")
    archivo.write("</Style>\n")
    archivo.write("</Placemark>\n")
    archivo.write("</Document>\n")
    archivo.write("</kml>\n")