<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index(string $slug, CategoryRepository $categoryRepository): Response
    {
        // Récuperer la catégorie qui correspond au slug
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('category/index.html.twig', [
            'categorie' => $category,
        ]);
    }

    #[Route('/menu/categories', name: 'menu_categories')]
    public function menuCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/_category.html.twig', [
            'categories' => $categories,
        ]);
    }
}
