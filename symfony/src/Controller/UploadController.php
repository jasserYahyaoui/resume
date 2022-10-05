<?php

namespace App\Controller;

use App\Form\FileUploadType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadController extends AbstractController
{
    /**
     * @Route("/", name="index_upload", methods={"GET"})
     */
    public function index(){

        return $this->render('app/index.html.twig');
    }

    /**
     * @Route("/api/upload", name="api_upload", methods={"POST"})
     *
     * @param Request $request
     * @param FileUploader $file_uploader
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function uploadApi(Request $request, FileUploader $file_uploader): RedirectResponse
    {
        $file = $request->files->get('resume');
        if ($file) {
            $file_name = $file_uploader->upload($file);
            if (null !== $file_name) {
                $directory = $file_uploader->getTargetDirectory();
                $full_path = $directory . '/' . $file_name;
            }
            $this->addFlash('success', $file->getClientOriginalName() . ' Resume uploaded with success !');
        } else {
            $this->addFlash('warning', $file->getClientOriginalName() . ' Resume failed to be uploaded !');
        }

        return $this->redirectToRoute('index_upload');
    }

    /**
     * @Route("/upload-pdf", name="uploadPdf")
     */
    public function uploadPdf(Request $request, FileUploader $file_uploader, SluggerInterface $slugger)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['resume']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);

                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                    // Do what you want with the full path file...
                    // Why not read the content or parse it !!!

                    $this->addFlash('success', 'Resume uploaded with success !');
                } else {

                    $this->addFlash('warning', 'Resume failed to be uploaded !');
                    // Oups, an error occured !!!
                }
            }
        }

        return $this->render('app/test-upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
