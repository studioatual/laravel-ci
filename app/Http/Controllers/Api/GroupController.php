<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index()
    {
        return Group::all();
    }

    public function store(Request $request)
    {
        $data  = $this->filterData($request->all());

        $validator = $this->validator($data);
        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(400);
        }

        return Group::create($data);
    }

    public function show(Group $group)
    {
        return $group;
    }

    public function update(Request $request, Group $group)
    {
        $data  = $this->filterData($request->all());

        $validator = $this->validator($data, $group);
        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(400);
        }

        $group->update($data);
        return $group;
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(['result' => 'Ok']);
    }

    protected function validator(array $data, Group $group = null)
    {
        return Validator::make($data, $this->getRules($group), $this->getMessages());
    }

    private function getRules(Group $group = null)
    {
        if ($group) {
            $rules = [
                'name' => 'max:50',
                'cnpj' => ['cnpj', Rule::unique('groups')->ignore($group)],
            ];
        } else {
            $rules = [
                'name' => 'required|max:50',
                'cnpj' => 'required|cnpj|unique:groups',
            ];
        }

        return $rules;
    }

    private function getMessages()
    {
        return [
            'required' => 'O campo é obrigatório!',
            'cnpj' => 'CNPJ é inválido!',
            'max' => 'Máximo de :max caracteres!',
            'unique' => ':input já em uso!'
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

        return $data;
    }
}
