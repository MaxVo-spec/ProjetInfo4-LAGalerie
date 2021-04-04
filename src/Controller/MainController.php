<?php

namespace App\Controller;

use App\Article\CommentaireType;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class MainController
 * @package App\Controller
 */

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = $request->get("page", 1);
        $limit = $request->get("limit", 10);
        $total = $this->getDoctrine()->getRepository(Article::class)->count([]);
        $articles = $this->getDoctrine()->getRepository(Article::class)->getPaginatedArticles(
            $page,
            $limit
        );

        $pages = ceil($total / $limit);
        return $this->render("index.html.twig", [
            "articles" => $articles,
            "pages" => $pages,
            "page" => $page,
            "limit" => $limit
        ]);
    }

    /**
     * @Route ("/article-{id}", name="readOne")
     * @param Article $article
     * @param Request $request
     * @return Response
     */

    public function readOne(Article $article, Request $request): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setArticle($article);
        $form = $this->createForm(CommentaireType::class, $commentaire)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($commentaire);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("readOne", ["id" => $article->getId()]);
        }

        return $this->render("readOne.html.twig", [
            "article" => $article,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route ("/publish", name="create")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param string $uploadsAbsoluteDir
     * @param string $uploadsRelativeDir
     * @return Response
     */
    public function create(
        Request $request,
        SluggerInterface $slugger,
        string $uploadsAbsoluteDir,
        string $uploadsRelativeDir
    ): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            "validation_groups" => ["Default, create"]
        ])->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get("img")->getData();

            $filename = sprintf(
                "%s_%s.%s",
                $slugger->slug($file->getClientOriginalName()),
                uniqid(),
                $file->getClientOriginalExtension()
            );

            $file->move($uploadsAbsoluteDir, $filename);

            $article->setImg($uploadsRelativeDir . "/" . $filename);

            $this->getDoctrine()->getManager()->persist($article);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("readOne", ["id" => $article->getId()]);
        }

        return $this->render("create.html.twig", [
            "form" => $form->createView()
        ]);
    }


    /**
     * @Route ("/update-{id}", name="update")
     * @param Request $request
     * @param Article $article
     * @param SluggerInterface $slugger
     * @param string $uploadsAbsoluteDir
     * @param string $uploadsRelativeDir
     * @return Response
     */

    public function update(
        Request $request,
        Article $article,
        SluggerInterface $slugger,
        string $uploadsAbsoluteDir,
        string $uploadsRelativeDir
    ): Response
    {
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get("img")->getData();

            if ($file !== null) {
                $filename = sprintf(
                    "%s_%s.%s",
                    $slugger->slug($file->getClientOriginalName()),
                    uniqid(),
                    $file->getClientOriginalExtension()
                );

                $file->move($uploadsAbsoluteDir, $filename);

                $article->setImg($uploadsRelativeDir . "/" . $filename);
            }


            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("readOne", ["id" => $article->getId()]);
        }

        return $this->render("update.html.twig", [
            "form" => $form->createView()
        ]);
    }


    /**
     * @Route("delete/{id}", name="delete")
     * @param Article $article
     * @return Response
     */
    public function delete(Article $article): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }
}