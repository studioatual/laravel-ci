<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function index()
    {
        return Person::paginate();
    }

    public function store(Request $request)
    {
        $data = $this->filterData($request->all());

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(400);
        }

        return Person::create($data);
    }

    protected function validator(array $data, Person $person = null)
    {
        return Validator::make($data, $this->getRules($person), $this->getMessages());
    }

    private function getRules(Person $person = null)
    {
        if ($person) {
            $rules = [
                'name' => 'max:100',
                'cpf_cnpj' => ['cnpj'],
            ];
        } else {
            $rules = [
                'group_id' => 'required',
                'name' => 'required|max:100',
                'cpf_cnpj' => 'required|cpf_cnpj',
            ];
        }

        return $rules;
    }

    private function getMessages()
    {
        return [
            'required' => 'O campo é obrigatório!',
            'cpf_cnpj' => 'CPF/CNPJ é inválido!',
            'max' => 'Máximo de :max caracteres!',
        ];
    }

    private function filterData($values)
    {
        $data = [];

        if (!$values) {
            return $data;
        }

        foreach ($values as $key => $value) {
            if (is_string($value) && trim(strip_tags($value))) {
                $data[$key] = trim(strip_tags($value));
            }

            if (is_array($value) && !empty($value)) {
                $data[$key] = $this->filterData($value);
            }

            if (is_int($value) || is_float($value)) {
                $data[$key] = $value;
            }
        }

        if (isset($data['cnpj'])) {
            $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);
        }

        if (isset($data['cpf_cnpj'])) {
            $data['cpf_cnpj'] = preg_replace('/\D/', '', $data['cpf_cnpj']);
        }

        if (isset($data['rg_ie'])) {
            $data['rg_ie'] = preg_replace('/\D/', '', $data['rg_ie']);
        }

        return $data;
    }
}
