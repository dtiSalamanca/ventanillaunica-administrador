<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $passwordPrueba = Hash::make('password');

        $usuarios = array_map(function (array $usuario) use ($passwordPrueba) {
            $usuario['password'] = $passwordPrueba;

            return $usuario;
        }, [
            0 => [
                'id' => 8,
                'name' => 'Cristhian Jair Rangel Parra',
                'email' => 'cthjxir@gmail.com',
                'email_verified_at' => '2026-06-30 20:39:49',
                'password' => '$2y$12$NQl7KDf3U2HnKCyjduHK.OsxZL0j1FKSOSfOHC7VuMuVES7njMJ.C',
                'remember_token' => null,
                'created_at' => '2026-06-30 20:39:17',
                'updated_at' => '2026-07-03 16:53:00',
            ],
            1 => [
                'id' => 9,
                'name' => 'Prueba Pendiente 1',
                'email' => 'prueba.pendiente.1@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$I1xJbfoRq.uQf.3SsXPo5eyQ.sSg03bdnxkfGurPrCpRLwCFrvZ.2',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:53',
                'updated_at' => '2026-07-01 15:06:53',
            ],
            2 => [
                'id' => 10,
                'name' => 'Prueba Pendiente 2',
                'email' => 'prueba.pendiente.2@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$4hB3ZiBc2DKlddcTr.zcyuLAoMNBM0mNBEM.GSblNMN5d31VmYK96',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:54',
                'updated_at' => '2026-07-01 15:06:54',
            ],
            3 => [
                'id' => 11,
                'name' => 'Prueba Pendiente 3',
                'email' => 'prueba.pendiente.3@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$711aYOtiCIcc9IoF6XJBB.Afr0OXUJ3lzJPlEDoLedCfGCEg/z1CW',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:54',
                'updated_at' => '2026-07-01 15:06:54',
            ],
            4 => [
                'id' => 12,
                'name' => 'Prueba Pendiente 4',
                'email' => 'prueba.pendiente.4@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$wEuvqz9oEsaiQJjj/KjU.OzL5rleod7L5aC6RpFNOlyeFLab9nfRi',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:54',
                'updated_at' => '2026-07-01 15:06:54',
            ],
            5 => [
                'id' => 13,
                'name' => 'Prueba Pendiente 5',
                'email' => 'prueba.pendiente.5@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$SY.R9gUbKc1gwowZbppi6.Z6oGuFzNaciH8LiQgq1.JH70SZfjcbK',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:54',
                'updated_at' => '2026-07-01 15:06:54',
            ],
            6 => [
                'id' => 14,
                'name' => 'Prueba Pendiente 6',
                'email' => 'prueba.pendiente.6@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$kUmqyu/BxKnRq.UmGMMFfuosbIoDzcXCjnXVxv4y.HFUQ4xdvLeyG',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:55',
                'updated_at' => '2026-07-01 15:06:55',
            ],
            7 => [
                'id' => 15,
                'name' => 'Prueba Pendiente 7',
                'email' => 'prueba.pendiente.7@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$mDarfjkNqp4zFMDrK7C.Iex7VqO4In32nFu/CtpOWAfE9SQf0w1ny',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:55',
                'updated_at' => '2026-07-01 15:06:55',
            ],
            8 => [
                'id' => 16,
                'name' => 'Prueba Pendiente 8',
                'email' => 'prueba.pendiente.8@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$TGEHh7eIBysr95Q50.cB7OEuAqL/GMvftM4NpXp.usGWLhmihY0vK',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:55',
                'updated_at' => '2026-07-01 15:06:55',
            ],
            9 => [
                'id' => 17,
                'name' => 'Prueba Revisado 1',
                'email' => 'prueba.revisado.1@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$Jn4t5nyL4j7.b2RQsKDvjeUZc0r0qc24pPvAjgljLPP9ALCtnP0Pq',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:55',
                'updated_at' => '2026-07-01 15:06:55',
            ],
            10 => [
                'id' => 18,
                'name' => 'Prueba Revisado 2',
                'email' => 'prueba.revisado.2@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$X/S3fzDMtZfVEywGgmTbJOKP.S/t9EBev.kZ.v7xjkojjXZU5Gqcy',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:55',
                'updated_at' => '2026-07-01 15:06:55',
            ],
            11 => [
                'id' => 19,
                'name' => 'Prueba Revisado 3',
                'email' => 'prueba.revisado.3@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$epbzjzybLJ6VhZDMIZIAKupS1gbtHX4S.qm0OhD6wmgPQXP3GcHgW',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:56',
                'updated_at' => '2026-07-01 15:06:56',
            ],
            12 => [
                'id' => 20,
                'name' => 'Prueba Revisado 4',
                'email' => 'prueba.revisado.4@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$vCsXg9voLt50qWik5yP9Re7/mw02sgy0JP5pnR4MG04LjUPnHbPlm',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:56',
                'updated_at' => '2026-07-01 15:06:56',
            ],
            13 => [
                'id' => 21,
                'name' => 'Prueba Revisado 5',
                'email' => 'prueba.revisado.5@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$Mp08sq9S/UTAIRNboAv3ieoJY.eGDkIy306C5uogaVnw0hq2QEtwm',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:56',
                'updated_at' => '2026-07-01 15:06:56',
            ],
            14 => [
                'id' => 22,
                'name' => 'Prueba Revisado 6',
                'email' => 'prueba.revisado.6@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$OUTOMtzPHBZevDhmYrsBtOW5/9ZQ09ycPYhTVfnSAKDbEF3D6Pzzy',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:56',
                'updated_at' => '2026-07-01 15:06:56',
            ],
            15 => [
                'id' => 23,
                'name' => 'Prueba Revisado 7',
                'email' => 'prueba.revisado.7@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$54SQpQmIG7c6gqaVjS.WaugsB2j4Jtm6ZStZLSPfUmLazdriqYIf.',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:57',
                'updated_at' => '2026-07-01 15:06:57',
            ],
            16 => [
                'id' => 24,
                'name' => 'Prueba Revisado 8',
                'email' => 'prueba.revisado.8@example.test',
                'email_verified_at' => null,
                'password' => '$2y$12$dYjds5GfwVk2pHia0BfFouzK8OfuhGHZ46gz6PUCo2P517dof7jNK',
                'remember_token' => null,
                'created_at' => '2026-07-01 15:06:57',
                'updated_at' => '2026-07-01 15:06:57',
            ],
        ]);

        DB::table('users')->insert($usuarios);
    }
}
