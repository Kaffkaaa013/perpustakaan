<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('Kelola data buku') ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('kategori') && ! $this->filled('kategori_id')) {
            $this->merge(['kategori_id' => $this->kategori]);
        }

        if ($this->has('tahun') && ! $this->filled('tahun_terbit')) {
            $this->merge(['tahun_terbit' => $this->tahun]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|min:3|max:255',
            'penulis' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|digits:4|min:1900',
        ];
    }

    /**
     * Get custom attribute names in Indonesian.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kategori_id' => 'kategori',
            'judul' => 'judul buku',
            'penulis' => 'penulis buku',
            'tahun_terbit' => 'tahun terbit',
        ];
    }

    /**
     * Get custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid atau tidak ditemukan.',
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.string' => 'Judul buku harus berupa teks.',
            'judul.max' => 'Judul buku tidak boleh lebih dari :max karakter.',
            'judul.min' => 'Judul buku harus lebih dari :min karakter.',
            'penulis.required' => 'Penulis buku wajib diisi.',
            'penulis.string' => 'Penulis buku harus berupa teks.',
            'penulis.max' => 'Penulis buku tidak boleh lebih dari :max karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.min' => 'Tahun terbit harus diatas 1900.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit harus berupa 4 digit angka.',
        ];
    }
}
