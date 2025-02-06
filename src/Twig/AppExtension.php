<?php
// src/AppBundle/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
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