<?php

namespace App\Core\ApplicationModels;

use App\Core\Traits\ArraySerializer;
use Illuminate\Http\Exceptions\HttpResponseException;

class JwtToken implements IArraySerializer
{
    use ArraySerializer;
    public string $accessToken = '';
    public string $expiresIn = '';
    public string $userName = '';
    public string $perfilId = '';
    public array $permissoes = [];

    public function validateRole(string $permissao): void
    {
        throw_if(
            condition: !$this->containsRole($permissao),
            exception: new HttpResponseException(response()->json(
                data: [
                    'message' => 'Acesso negado para esse recurso.',
                ],
                status: 403
            ))
        );
    }
    private function containsRole(string $permissao): bool
    {
        foreach ($this->permissoes as $_permissao) {
            if ($_permissao === (string) $permissao) {
                return true;
            }
        }
        return false;
    }
}
