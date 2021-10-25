<?php

namespace App\Controllers;

use App\Container;
use App\Models\Category;
use App\Models\Collections\CategoriesCollection;
use App\Models\Product;
use App\Redirect;
use App\Repositories\Products\ProductsRepository;
use App\Repositories\Tags\TagsRepository;
use App\View;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private ProductsRepository $productsRepository;
    private CategoriesCollection $categoriesCollection;
    private TagsRepository $tagsRepository;

    public function __construct(Container $container)
    {
        $this->productsRepository = $container->container[ProductsRepository::class];
        $this->categoriesCollection = new CategoriesCollection([
            new Category("phone"),
            new Category("components"),
            new Category("laptops"),
            new Category("peripherals")]);
        $this->tagsRepository = $container->container[TagsRepository::class];
    }

    public function index(): View
    {
        $products = $this->productsRepository->getAll($_GET);
        $tags = $this->tagsRepository->getAll();

        return new View('Products/index.twig', [
            'products' => $products,
            'categories' => $this->categoriesCollection,
            'tags' => $tags
        ]);
    }

    public function create(): View
    {
        $tags = $this->tagsRepository->getAll();

        return new View('Products/create.twig', [
            'categories' => $this->categoriesCollection,
            'tags' => $tags
        ]);
    }

    public function store()
    {
        $product = new Product(Uuid::uuid4(), $_POST['name'], $_POST['category'], $_POST['amount'], Carbon::now(), Carbon::now());

        $this->productsRepository->save($product, $_POST['tag']);

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
            $this->productsRepository->edit($product, $_POST['tag'], $_POST['name'], $_POST['category'], $_POST['amount']);
        }

        Redirect::url('/products');
    }

    public function editForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/products');;

        $product = $this->productsRepository->getOne($id);
        $tags = $this->tagsRepository->getAll();

        if ($product === null) Redirect::url('/products');;

        return new View('Products/edit.twig', [
            'product' => $product,
            'categories' => $this->categoriesCollection,
            'tags' => $tags
        ]);
    }
}