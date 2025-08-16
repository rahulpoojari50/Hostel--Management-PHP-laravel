<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'father_mobile' => ['nullable', 'string', 'max:20'],
            'mother_name' => ['required', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'mother_mobile' => ['nullable', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'gender' => ['required', 'in:Male,Female,Other'],
            'dob' => ['required', 'date'],
            'emergency_phone' => ['required', 'string', 'max:20'],
            'religion' => ['nullable', 'string', 'max:255'],
            'caste_category' => ['required', 'in:General,OBC,SC,ST,Other'],
            'caste' => ['nullable', 'string', 'max:255'],
            'admission_quota' => ['required', 'in:General,Management,NRI,Other'],
            'mother_tongue' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'in:Single,Married,Other'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'aadhaar_id' => ['required', 'string', 'max:20'],
            'passport_no' => ['nullable', 'string', 'max:20'],
            'admission_date' => ['required', 'date'],
            // Present Address
            'present_state' => ['required', 'string', 'max:255'],
            'present_city' => ['nullable ', 'string', 'max:255'],
            'present_address' => ['required', 'string', 'max:500'],
            // Permanent Address
            'permanent_state' => ['required', 'string', 'max:255'],
            'permanent_city' => ['required', 'string', 'max:255'],
            'permanent_address' => ['required', 'string', 'max:500'],
            'address' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
