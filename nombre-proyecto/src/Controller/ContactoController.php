<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Contacto;
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
    public function buscar(ManagerRegistry $doctrine, $texto): Response
    {
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
}
