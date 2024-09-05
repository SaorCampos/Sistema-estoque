<?php

namespace App\Http\Requests;

use App\Support\Models\BaseResponse;
use App\Support\Models\Pagination;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseRequest extends FormRequest
{

    abstract public function authorize(): bool;

    abstract public function rules(): array;

    protected $stopOnFirstFailure = false;


    public function getTransactionId(): string
    {
        return $this->get('transactionId') ?? "";
    }

    public function getMsUser(): string
    {
        return $this->get('msUser') ?? '';
    }

    public function getUid(): int
    {
        return $this->get('uid') ?? 0;
    }

    protected function failedValidation(Validator $validator): Response
    {
        $errors = collect($validator->errors()->toArray())
            ->map(fn (array $error): string => count($error) === 0 ? '' : $error[0]);
        $details = [
            'rules' => $this->mappedRules(),
            'error' => $validator->getMessageBag()
        ];
        throw new HttpResponseException(
            BaseResponse::builder()
                ->setData($this->getErrorsArray($errors))
                ->setMessage('Informe os dados corretamente.')
                ->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->setDetails($details)
                ->response()
        );
    }

    private function mappedRules()
    {
        return collect($this->rules())->map(function ($rule) {
            if (gettype($rule) !== 'array') :
                return explode('|', $rule);
            endif;
            foreach ($rule as $i => $subRule) :
                if (gettype($subRule) === 'object') :
                    $rule[$i] = get_class($subRule);
                endif;
            endforeach;
            return $rule;
        });
    }

    private function getErrorsArray(Collection $errors): array
    {
        $errorsArray = [];
        foreach ($errors as $key => $error) :
            $matches = [];
            if (preg_match('/(\w+)\.(\d)+/', $key, $matches)) :
                $newKey = $matches[1] . "[" . $matches[2] . "]";
                $errorsArray[$newKey] = $error;
            else :
                $errorsArray[$key] = $error;
            endif;
        endforeach;
        return $errorsArray;
    }

    public function attributes(): array
    {
        $rulesKeys = array_keys($this->rules());
        $attributes = [];
        foreach ($rulesKeys as $key) :
            $matches = [];
            if (preg_match('/(\w+)\.\*.*/', $key, $matches)) :
                $attributes[$key] = $matches[1] . '[]';
            endif;
        endforeach;
        return $attributes;
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            BaseResponse::builder()
                ->setStatusCode(Response::HTTP_FORBIDDEN)
                ->setMessage("Acesso não autorizado.")
                ->response()
        );
    }

    public function hasPagination(): bool
    {
        return $this->page && $this->perPage;
    }

    public function getPagination(): ?Pagination
    {
        $page = (int)$this->page;
        $perPage = (int)$this->perPage;
        if (!$this->hasPagination()) :
            return null;
        endif;
        if ($page < 0) :
            return null;
        endif;
        if ($perPage < 0) :
            return null;
        endif;
        return Pagination::builder()
            ->setCurrentPage($page)
            ->setPerPage($perPage);
    }
}
