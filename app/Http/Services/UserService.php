<?php

namespace App\Http\Services;

use App\Http\Repositories\UserRepository;
use Illuminate\Database\QueryException;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $repository)
    {
        $this->userRepository = $repository;
    }

    public function authenticate($creds)
    {
        return $this->userRepository->login($creds);
    }

    public function registerUser($data)
    {
        try {
            return $this->userRepository->register($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return [
                    'data' => [
                        'message' => 'Email sudah terdaftar',
                    ],
                    'status' => 400
                ];
            }
            return [
                'data' => [
                    'message' => 'Terjadi kesalahan',
                ],
                'status' => 400
            ];
        }
    }

    public function updateUser($data)
    {
        try {
            return $this->userRepository->update($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return [
                    'data' => [
                        'message' => 'Email sudah terdaftar',
                    ],
                    'status' => 400
                ];
            }
            return [
                'data' => [
                    'message' => 'Terjadi kesalahan',
                ],
                'status' => 400
            ];
        }
    }

    public function getBalances($user)
    {
        return $this->userRepository->balances($user);
    }
}
