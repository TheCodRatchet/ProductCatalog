<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Collections\CategoriesCollection;
use App\Models\Product;
use App\Redirect;
use App\Repositories\Products\ProductsRepository;
use App\Repositories\Products\MysqlProductsRepository;
use App\View;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private ProductsRepository $productsRepository;
    private CategoriesCollection $categoriesCollection;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
        $this->categoriesCollection = new CategoriesCollection([
            new Category("phone"),
            new Category("components"),
            new Category("laptops"),
            new Category("monitors"),
            new Category("peripherals")]);
    }

    public function index(): View
    {
        $products = $this->productsRepository->getAll($_GET);

        return new View('Products/index.twig', [
            'products' => $products,
            'categories' => $this->categoriesCollection
        ]);
    }

    public function create(): View
    {
        return new View('Products/create.twig', [
            'categories' => $this->categoriesCollection
        ]);
    }

    public function store()
    {
        $product = new Product(Uuid::uuid4(), $_POST['name'], $_POST['category'], $_POST['amount'], Carbon::now(), Carbon::now());

        $this->productsRepository->save($product);

        Redirect::url('/products');
    }

    public function delete(array $vars)
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/products');

        $product = $this->productsRepository->getOne($id);

        if ($product !== null) {
            $this->productsRepository->delete($product);
        }

        Redirect::url('/products');
    }

    public function deleteForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/products');;

        $product = $this->productsRepository->getOne($id);

        if ($product === null) Redirect::url('/products');;

        return new View('Products/delete.twig', [
            'product' => $product
        ]);
    }

    public function edit(array $vars)
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/products');

        $product = $this->productsRepository->getOne($id);

        if ($product !== null) {
            $this->productsRepository->edit($product);
        }

        Redirect::url('/products');
    }

    public function editForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/products');;

        $product = $this->productsRepository->getOne($id);

        if ($product === null) Redirect::url('/products');;

        return new View('Products/edit.twig', [
            'product' => $product,
            'categories' => $this->categoriesCollection
        ]);
    }
}