<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom du Produit'),
            SlugField::new('slug')->setLabel('Url')->setTargetFieldName('name'),
            TextEditorField::new('description')->setLabel('Description'),
            ImageField::new('illustration')->setLabel('Image')
                ->setHelp('Image')
                ->setUploadDir('public\upload')
                ->setBasePath('upload'),
            NumberField::new('price')->setLabel('Prix'),
            ChoiceField::new('tva')->setChoices([
                '5,5%' => '5.5',
                '10%' => '10',
                '20%' => '20',
            ]),
            AssociationField::new('category')
        ];
    }

}
