<?php

namespace App\Http\Services;

use App\Http\Repositories\ExpenseRepository;
use Illuminate\Support\Facades\Hash;

class ExpenseService
{
    private $expenseRepository;

    public function __construct(ExpenseRepository $repository) {
        $this->expenseRepository = $repository;
    }

    public function getData($id)
    {
        return $this->expenseRepository->get($id);
    }

    public function storeData($id, $data)
    {
        return $this->expenseRepository->store($id, $data);
    }

    public function updateData($id, $data)
    {
        return $this->expenseRepository->update($id, $data);
    }

    public function deleteData($id)
    {
        return $this->expenseRepository->destroy($id);
    }

    public function deleteAllData($user, $password)
    {
        if (Hash::check($password, $user->password)) {
            return $this->expenseRepository->destroyAll($user->id);
        }

        return [
            'data' => [
                'message' => 'Password salah',
            ],
            'status' => 401
        ];
    }
}
