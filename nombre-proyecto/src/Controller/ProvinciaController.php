<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProvinciaController extends AbstractController
{

    //Hacer de esto un array de provincias
    private $provincias = [

        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],

        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],

        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],

        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],

        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]

    ]; 

    #[Route('/provincia', name: 'app_provincia')]
    public function index(): Response
    {
        return $this->render('provincia/index.html.twig', [
            'controller_name' => 'ProvinciaController',
        ]);
    }


    //Mostrar una provincia por su id
    /**
    * @Route("/provincia/ficha/{codigo}", name="ficha_contacto")
    */
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

        return $this->render('contacto/fichaContacto.html.twig', ['contacto' => $contacto]);

    }

    //Buscar provincia por nombre
    #[Route('/provincia/buscar/{texto}', name: 'buscar_provincia')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        //Se filtran los usuarios que existan de los que no
        $repositorio = $doctrine->getRepository(Contacto::class);

        /* @warning */
        $contactos = $repositorio->findByNombre('Ma');


        return $this->render('contacto/listaContactos.html.twig', ['contactos' => $contactos]);
    }

    //Insertar las provincias del array $provicnias
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

    //Modificar el nombre de la provincia con el id dado
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

    //Borrar la provincia con el id dado
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


    //Insertar un formulario y guardar la provincia rellenada
    #[Route('/contacto/nuevo', name: "nuevo_contacto")]
    public function nuevo(ManagerRegistry $doctrine, Request $request){
        $contacto = new Contacto();

        $formulario=  $this->createForm(ContactoType::class, $contacto);
                    
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $contacto = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contacto);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_contacto', ["codigo"=>$contacto->getId()]);
        }
        
        return $this->render('contacto/nuevo.html.twig', array('formulario' => $formulario->createView()));



    }


    //Enviar un formulario y odificar la provincia dcel id dado
    /**
     * @Route("/contacto/editar/{codigo}", name="editar_contacto", requirements={"codigo"="\d+"})
     */
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo){
        
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

        
        $formulario=  $this->createForm(ContactoType::class, $contacto);
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $contacto = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contacto);
            $entityManager->flush();
        }

        return $this->render('contacto/editar.html.twig', array('formulario' => $formulario->createView()));
    
    }

}
