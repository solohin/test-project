<?php

namespace Solohin\ToptalExam\Controllers;

use Solohin\ToptalExam\ErrorTypes;
use Solohin\ToptalExam\Security\UserRoles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Solohin\ToptalExam\Services\NotesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class NotesController
{
    private $notesService;

    public function __construct(NotesService $service)
    {
        $this->notesService = $service;
    }

    public function getList()
    {
        throw new \Exception('Method not implemented yet');
    }

    public function getOne($id)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function add(Request $request)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function remove($id)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function update($id, Request $request)
    {
        throw new \Exception('Method not implemented yet');
    }

    public function delete($id)
    {
        throw new \Exception('Method not implemented yet');
    }
}
