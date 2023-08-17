<?php

namespace App\Http\Services;

use App\Http\Repositories\IncomeRepository;
use Illuminate\Support\Facades\Hash;

class IncomeService
{
    private $incomeRepository;

    public function __construct(IncomeRepository $repository) {
        $this->incomeRepository = $repository;
    }

    public function getData($id)
    {
        return $this->incomeRepository->get($id);
    }

    public function storeData($id, $data)
    {
        return $this->incomeRepository->store($id, $data);
    }

    public function updateData($id, $data)
    {
        return $this->incomeRepository->update($id, $data);
    }

    public function deleteData($id)
    {
        return $this->incomeRepository->destroy($id);
    }

    public function deleteAllData($user, $password)
    {
        if (Hash::check($password, $user->password)) {
            return $this->incomeRepository->destroyAll($user->id);
        }

        return [
            'data' => [
                'message' => 'Password salah',
            ],
            'status' => 401
        ];
    }
}
