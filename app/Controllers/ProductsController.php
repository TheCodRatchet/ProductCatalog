<?php

namespace App\Controllers;

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

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function index(): View
    {
        $products = $this->productsRepository->getAll($_GET);

        return new View('Products/index.twig', [
            'products' => $products
        ]);
    }

    public function create(): View
    {
        return new View('Products/create.twig', []);
    }

    public function store()
    {
        $product = new Product(Uuid::uuid4(), $_POST['name'], $_POST['category'], $_POST['amount'], Carbon::now(), Carbon::now());

        $this->productsRepository->save($product);

        Redirect::url('/');
    }

    public function delete(array $vars)
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/');

        $product = $this->productsRepository->getOne($id);

        if ($product !== null) {
            $this->productsRepository->delete($product);
        }

        Redirect::url('/');
    }

    public function show(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/');;

        $product = $this->productsRepository->getOne($id);

        if ($product === null) Redirect::url('/');;

        return new View('Products/show.twig', [
            'product' => $product
        ]);
    }

    public function edit(array $vars)
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/');

        $product = $this->productsRepository->getOne($id);

        if ($product !== null) {
            $this->productsRepository->edit($product);
        }

        Redirect::url('/');
    }

    public function editForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/');;

        $product = $this->productsRepository->getOne($id);

        if ($product === null) Redirect::url('/');;

        return new View('Products/edit.twig', [
            'product' => $product
        ]);
    }
}