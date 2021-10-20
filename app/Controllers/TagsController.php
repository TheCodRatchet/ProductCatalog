<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Redirect;
use App\Repositories\Tags\TagsRepository;
use App\Repositories\Tags\MysqlTagsRepository;
use App\View;
use Ramsey\Uuid\Uuid;

class TagsController
{
    private TagsRepository $tagsRepository;

    public function __construct()
    {
        $this->tagsRepository = new MysqlTagsRepository();
    }

    public function index(): View
    {
        $tags = $this->tagsRepository->getAll($_GET);

        return new View('Tags/index.twig', [
            'tags' => $tags,
        ]);
    }

    public function create(): View
    {
        return new View('Tags/create.twig', []);
    }

    public function store()
    {
        $tag = new Tag(Uuid::uuid4(), $_POST['name']);

        $this->tagsRepository->save($tag);

        Redirect::url('/tags');
    }

    public function delete(array $vars)
    {
        var_dump("i was here");
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/tags');

        $tag = $this->tagsRepository->getOne($id);

        if ($tag !== null) {
            $this->tagsRepository->delete($tag);
        }

        Redirect::url('/tags');
    }

    public function deleteForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/tags');;

        $tag = $this->tagsRepository->getOne($id);

        if ($tag === null) Redirect::url('/tags');;

        return new View('Tags/delete.twig', [
            'tag' => $tag
        ]);
    }

    public function edit(array $vars)
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/tags');

        $tag = $this->tagsRepository->getOne($id);

        if ($tag !== null) {
            $this->tagsRepository->edit($tag);
        }

        Redirect::url('/tags');
    }

    public function editForm(array $vars): View
    {
        $id = $vars['id'] ?? null;
        if ($id == null) Redirect::url('/tags');;

        $tag = $this->tagsRepository->getOne($id);

        if ($tag === null) Redirect::url('/tags');;

        return new View('Tags/edit.twig', [
            'tag' => $tag,
        ]);
    }
}