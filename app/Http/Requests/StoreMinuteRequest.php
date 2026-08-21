<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMinuteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scope' => ['required', 'string', 'in:ulul_albab,perumnas_2'],
            'group_id' => ['required', 'exists:groups,id'],
            'session_topic' => ['required', 'string', 'max:255'],
            'notulis_name' => ['nullable', 'string', 'max:255'],
            'session_date' => ['nullable', 'date'],
            
            // Problem - Penyebab - Solusi
            'problem' => ['nullable', 'string'],
            'cause' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],

            // Action Plan
            'action_ppg' => ['nullable', 'string'],
            'action_description' => ['nullable', 'string'],
            'action_name' => ['nullable', 'string'],
            'action_participants' => ['nullable', 'string'],
            'action_time' => ['nullable', 'string'],
            'action_budget' => ['nullable', 'string'],

            // Peran 5 Unsur
            'role_keimaman' => ['nullable', 'string'],
            'role_pengurus' => ['nullable', 'string'],
            'role_parents' => ['nullable', 'string'],
            'role_muballigh' => ['nullable', 'string'],
            'role_educator' => ['nullable', 'string'],

            // honeypot anti-spam
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.required' => 'Silakan pilih kelompok/grup FGD terlebih dahulu.',
            'group_id.exists' => 'Grup yang dipilih tidak valid.',
            'session_topic.required' => 'Silakan pilih atau isi Sesi/Topik FGD.',
        ];
    }
}
