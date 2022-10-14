<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Contacto;
use App\Entity\Provincia;
use Doctrine\Persistence\ManagerRegistry ;


class ContactoController extends AbstractController

{

    private $contactos = [

        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],

        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],

        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],

        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],

        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]

    ]; 

    /**
    * @Route("/contacto/ficha/{codigo}", name="ficha_contacto")
    */
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

        return $this->render('contacto/fichaContacto.html.twig', ['contacto' => $contacto]);

    }

    #[Route('/contacto/buscar/{texto}', name: 'buscar_contacto')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        //Se filtran los usuarios que existan de los que no
        $repositorio = $doctrine->getRepository(Contacto::class);

        /* @warning */
        $contactos = $repositorio->findByNombre('Ma');


        return $this->render('contacto/listaContactos.html.twig', ['contactos' => $contactos]);
    }

    #[Route('/contacto/insertar', name: 'insertar_contacto')]
    public function insertar(ManagerRegistry $doctrine){
        $entityManager = $doctrine -> getManager();
        foreach($this->contactos as $c){
            $contacto = new Contacto();
            $contacto->setNombre($c["nombre"]);
            $contacto->setTelefono($c["telefono"]);
            $contacto->setEmail($c["email"]);
            $entityManager->persist($contacto);


        }

        try {
            //Con un flush sólo se confirman todas las operaciones
            $entityManager->flush();
            return new Response("Contactos insertados");
        } catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }


    #[Route('/contacto/update/{id}/{nombre}', name: 'modificar_contacto')]
    public function update(ManagerRegistry $doctrine, $id, $nombre): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($id);
        if(! $contacto){
            return $this->render('ficha_contacto.html.twig', [ 'contacto'=>null]);
        }

        $contacto->setNombre($nombre);
        try{
            $entityManager->flush();
            return $this->render('ficha_contacto.html.twig', ['contacto' => $contacto]);
        } catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }

    #[Route('/contacto/delete/{id}', name: 'eliminar_contacto')]
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($id);
        if(! $contacto){
            return $this->render('ficha_contacto.html.twig', [ 'contacto'=>null]);
        }

        try{
            $entityManager->remove($contacto);
            $entityManager->flush();
            return new Response("Contanto eliminado");
        } catch (\Exception $e){
            return new Response("Error eliminando objeto");
        }
    }


    #[Route('/contacto/insertarConProvincia', name: 'insertar_con_provincia_contacto')]
    public function insertarConProvincia(ManagerRegistry $doctrine){
        $entityManager = $doctrine -> getManager();
        $provincia = new Provincia();

        $provincia->setNombre("Alicante");
        $contacto = new Contacto();

        $contacto->setNombre("inserción de prueba con provincia");
        $contacto->setTelefono("3453453455");
        $contacto->setEmail("insercion.de.prueba.provicnia@contacto.es");
        $contacto->setProvincia($provincia);
        
        $entityManager->persist($provincia);
        $entityManager->persist($contacto);

        

        try {
            //Con un flush sólo se confirman todas las operaciones
            $entityManager->flush();
            return $this->render("contacto/fichaContacto.html.twig", ['contacto' => $contacto]);
        } catch (\Exception $e){
            return new Response("Error insertando objetos:\n$e");
        }
    }

    #[Route('/contacto/insertarSinProvincia', name: 'insertar_sin_provincia_contacto')]
    public function insertarSinProvincia(ManagerRegistry $doctrine){
        $entityManager = $doctrine -> getManager();
        $repositorio = $doctrine->getRepository(Provincia::class);

        $provincia = $repositorio->findOneBy(["nombre"=>"Alicante"]);
        $contacto = new Contacto();

        $contacto->setNombre("inserción de prueba con provincia");
        $contacto->setTelefono("95234234234255");
        $contacto->setEmail("insercion.de.prueba.provicnia@contacto.es");
        $contacto->setProvincia($provincia);
        
        $entityManager->persist($provincia);
        $entityManager->persist($contacto);

        

        try {
            //Con un flush sólo se confirman todas las operaciones
            $entityManager->flush();
            return $this->render("contacto/fichaContacto.html.twig", ['contacto' => $contacto]);
        } catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }

}
