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
    
    prologo(archivoFinal)
    escribirAlturas(xml.getroot(), archivoFinal)
    epilogo(archivoFinal)

def prologo(archivo):
    archivo.write('<?xml version="1.0" encoding="UTF-8"?>\n')
    archivo.write(f'<svg xmlns="http://www.w3.org/2000/svg" '
                          f'width="300" height="300" '
                          f'viewBox="0 300 250 500" version="2.0">\n')
    archivo.write('<polyline points=\n"')

def epilogo(archivo):
    archivo.write('style="fill:white;stroke:red;stroke-width:4" />\n')
    archivo.write('</svg>\n')

def escribirAlturas(raiz, archivo):
    tramos = raiz.find("{http://www.uniovi.es}tramos")

    if(tramos == None): 
        print("No hay tramos")
        return
    lista_tramos = tramos.findall("{http://www.uniovi.es}tramo")
    alturas = [650]
    distancias = [10]

    for tramo in lista_tramos:
        coordenadas = tramo.find("{http://www.uniovi.es}punto_final")
        distancia = tramo.find("{http://www.uniovi.es}distancia")
        if(coordenadas == None):
            print("No hay coordenadas")
            return
        if(distancia == None):
            print("No hay distancia")
            return
        alturas.append(float(coordenadas.find("{http://www.uniovi.es}altura").text) / 1.4)
        distancias.append(float(distancia.text) * 25 + 10)

    total = 0
    for i in range(1, len(alturas)):
        total += distancias[i]
        archivo.write(f"{total},{alturas[i]}\n")

    archivo.write(f"{total},{max(alturas)}\n")
    archivo.write(f"{distancias[1]},{max(alturas)}\n")
    archivo.write(f'{distancias[0]},{alturas[1]}"\n')

if(__name__ == "__main__"):
    main()