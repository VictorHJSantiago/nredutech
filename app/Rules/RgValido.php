<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RgValido implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove caracteres não numéricos
        $rg = preg_replace('/[^0-9]/', '', $value);

        // Verifica se o RG tem um formato básico (não pode estar vazio ou ter todos os dígitos iguais)
        if (empty($rg) || strlen($rg) < 7 || strlen($rg) > 9 || preg_match('/(\d)\1{7,}/', $rg)) {
            $fail('O campo :attribute não é um RG válido.');
        }
    }
}