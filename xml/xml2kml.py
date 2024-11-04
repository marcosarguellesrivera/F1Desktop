import xml.etree.ElementTree as et

def main():
    archivo = input("Introduce el nombre del archivo: ")
    try:
        xml = et.parse(archivo)
    except IOError:
        print(f'El archivo {archivo} no existe')
        exit()
    except et.ParseError:
        print(f'Error parseando el archivo {archivo}')
        exit()
    
    final = input("Nombre del archivo final: ")
    
    try:
        archivoFinal = open(final, 'w')
    except IOError:
        print(f'Error al crear el archivo {final}')
        exit()
    
    prologo(archivoFinal, final)
    escribirCoordenadas(xml.getroot(), archivoFinal)
    epilogo(archivoFinal)

def prologo(archivo, nombreArchivo):
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
    tramos = raiz.find("{http://www.uniovi.es}tramos")

    if(tramos == None): 
        print("No hay tramos")
        return

    for tramo in tramos.findall("{http://www.uniovi.es}tramo"):
        coordenadas = tramo.find("{http://www.uniovi.es}punto_final")
        if(coordenadas == None):
            print("No hay coordenadas")
            return
        longitud = coordenadas.find("{http://www.uniovi.es}longitud").text
        latitud = coordenadas.find("{http://www.uniovi.es}latitud").text
        altura = coordenadas.find("{http://www.uniovi.es}altura").text
        archivo.write(f"{longitud},{latitud},{altura}\n")


def epilogo(archivo):
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

if(__name__ == "__main__"):
    main()