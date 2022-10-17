<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Contacto;
use App\Entity\Provincia;
use App\Form\ProvinciaFormType;
use Doctrine\Persistence\ManagerRegistry ;

use Symfony\Component\HttpFoundation\Request;

class ProvinciaController extends AbstractController
{

    //Hacer de esto un array de provincias
    private $provincias = [

        1 => ["nombre" => "Castellon"],

        2 => ["nombre" => "Palencia"],

        5 => ["nombre" => "Albacete"],

        7 => ["nombre" => "Sevilla"],

        9 => ["nombre" => "Murcia"]

    ]; 

    #[Route('/provincia', name: 'app_provincia')]
    public function index(): Response{
        return $this->render('provincia/index.html.twig', [
            'controller_name' => 'ProvinciaController',
        ]);
    }




    //Mostrar una provincia por su id
    /**
    * @Route("/provincia/ficha/{codigo}", name="ficha_provincia")
    */
    public function ficha(ManagerRegistry $doctrine, $codigo): Response{
        $repositorio = $doctrine->getRepository(Provincia::class);
        $provincia = $repositorio->find($codigo);

        return $this->render('provincia/fichaProvincia.html.twig', ['provincia' => $provincia]);

    }



    //Buscar provincia por nombre
    #[Route('/provincia/buscar/{texto}', name: 'buscar_provincia')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        //Se filtran las provincias que existan de los que no
        $repositorio = $doctrine->getRepository(Provincia::class);

        $provincias = $repositorio->findByNombre($texto);


        return $this->render('provincia/listaProvincias.html.twig', ['provincias' => $provincias]);
    }




    //Insertar las provincias del array $provincias
    #[Route('/provincia/insertar', name: 'insertar_provincia')]
    public function insertar(ManagerRegistry $doctrine){
        $entityManager = $doctrine -> getManager();
        foreach($this->provincias as $p){
            $provincia = new Provincia();
            $provincia->setNombre($p["nombre"]);
            $entityManager->persist($provincia);
        }

        try {
            //Con un flush sólo se confirman todas las operaciones
            $entityManager->flush();
            return new Response("Provincias insertadas");
        } catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }




    //Modificar el nombre de la provincia con el id dado
    #[Route('/provincia/update/{id}/{nombre}', name: 'modificar_provincia')]
    public function update(ManagerRegistry $doctrine, $id, $nombre): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Provincia::class);
        $provincia = $repositorio->find($id);
        if(! $provincia){
            return $this->render('provincia/fichaProvincia.html.twig', [ 'provincia'=>null]);
        }

        $provincia->setNombre($nombre);
        try{
            $entityManager->flush();
            return $this->render('provincia/fichaProvincia.html.twig', ['provincia' => $provincia]);
        } catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }




    //Borrar la provincia con el id dado
    #[Route('/provincia/delete/{id}', name: 'eliminar_provincia')]
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Provincia::class);
        $provincia = $repositorio->find($id);
        if(! $provincia){
            return $this->render('provincia/fichaProvincia.html.twig', [ 'provincia'=>null]);
        }

        try{
            $entityManager->remove($provincia);
            $entityManager->flush();
            return new Response("Provincia eliminada");
        } catch (\Exception $e){
            return new Response("Error eliminando objeto.\nCompruebe que ningún contacto pertenezca a esa provincia");
        }
    }



    //Insertar un formulario y guardar la provincia rellenada
    #[Route('/provincia/nueva', name: "nueva_provincia")]
    public function nuevo(ManagerRegistry $doctrine, Request $request){
        $provincia = new Provincia();

        $formulario =  $this->createForm(ProvinciaFormType::class, $provincia);
                    
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $provincia = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($provincia);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_provincia', ["codigo"=>$provincia->getId()]);
        }
        
        return $this->render('provincia/nueva.html.twig', array('formulario' => $formulario->createView()));



    }



    //Enviar un formulario y modificar la provincia del id dado
    /**
     * @Route("/provincia/editar/{codigo}", name="editar_provincia", requirements={"codigo"="\d+"})
     */
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo){
        
        $repositorio = $doctrine->getRepository(Provincia::class);
        $provincia = $repositorio->find($codigo);

        
        $formulario=  $this->createForm(ProvinciaFormType::class, $provincia);
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $provincia = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($provincia);
            $entityManager->flush();
        }

        return $this->render('provincia/editar.html.twig', array('formulario' => $formulario->createView()));
    
    }

}
