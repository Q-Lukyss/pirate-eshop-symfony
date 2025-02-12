<?php
// src/AppBundle/Twig/AppExtension.php
namespace App\Twig;

use App\Classe\Cart;
use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private CategoryRepository $categoryRepository;
    private Cart  $cart;

    public function __construct(CategoryRepository $categoryRepository, Cart $cart){
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }

    public function getGlobals(): array
    {
        // retourne toutes les variables à rendre globales dans l'application
        return [
            'categories' => $this->categoryRepository->findAll(),
            'cartQuatity' => $this->cart->getTotalQty(),
            'cartPriceRaw' => $this->cart->getTotalPriceRaw(),
            'cartPriceWt' => $this->cart->getTotalPriceWt(),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($number, $decimals = 2, $decPoint = '.', $thousandsSep = ' ')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        return $price.' Pièce(s) d\'Or';
    }
}