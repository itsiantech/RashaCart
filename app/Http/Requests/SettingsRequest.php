<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'app_name' => ['nullable', 'string', 'max:255'],
            'app_description' => ['nullable', 'string'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'items_per_page' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'app_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'theme' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:255'],
            'google_map_location' => ['nullable', 'string'],

            // ✅ Dynamic city-wise delivery charges (dc_inside / dc_outside are
            // derived from the first two rows and no longer edited directly)
            'delivery_cities' => ['nullable', 'array'],
            'delivery_cities.*.city' => ['nullable', 'string', 'max:60'],
            'delivery_cities.*.charge' => ['nullable', 'numeric', 'min:0', 'max:100000'],

            // ✅ Contact settings validation
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email'],
            'contact_address' => ['nullable', 'string'],
            'business_hours' => ['nullable', 'string', 'max:100'],
            'support_email' => ['nullable', 'email'],

            // ✅ Social-media validation
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'tiktok' => ['nullable', 'url'],

            'attribute_system' => ['nullable', 'boolean'],

            'automation_menu' => ['nullable', 'boolean'],

            'seller_menu' => ['nullable', 'boolean'],

            'seo_menu' => ['nullable', 'boolean'],

            // ✅ OTP validation
            'sms_notification' => ['nullable', 'boolean'],
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_url' => 'nullable|string|max:255',
            'sms_sender_id' => 'nullable|string|max:255',

            'email_notification' => ['nullable', 'boolean'],

            'selected_courier' => ['nullable', 'string', 'in:quirior,steedfast,available'],
            'steedfast_api_base_url' => ['nullable', 'url', 'max:255'],
            'steedfast_api_key' => ['nullable', 'string', 'max:255'],
            'steedfast_api_secret' => ['nullable', 'string', 'max:255'],

            'pathao_client_id' => 'nullable|string',
            'pathao_client_secret' => 'nullable|string',
            'pathao_username' => 'nullable|email',
            'pathao_password' => 'nullable|string',
            'pathao_store_id' => 'nullable|string',

            'GOOGLE_CLIENT_SECRET' => ['nullable', 'string', 'max:255'],
            'GOOGLE_CLIENT_ID' => ['nullable', 'string', 'max:255'],

            // ✅ Customer Login/Register settings
            'customer_login_enabled' => ['nullable', 'boolean'],
            'customer_login_identifier' => ['nullable', 'string', 'in:email,phone,both'],
            'customer_google_enabled' => ['nullable', 'boolean'],

            // ✅ Purchase actions
            'add_to_cart_enabled' => ['nullable', 'boolean'],
            'buy_now_enabled' => ['nullable', 'boolean'],
            'cart_drawer_enabled' => ['nullable', 'boolean'],
            'home_category_sidebar' => ['nullable', 'boolean'],
            'add_to_cart_label' => ['nullable', 'string', 'max:40'],
            'buy_now_label' => ['nullable', 'string', 'max:40'],
            'purchase_disabled_text' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages()
    {
        return [
            'app_name.string' => 'Application name must be a valid string.',
            'app_description.string' => 'Application description must be a valid string.',
            'items_per_page.numeric' => 'Items per page must be a numeric value.',

            'api_base_url.url' => 'The API Base URL must be a valid URL.',
            'api_key.string' => 'The API Key must be a valid string.',
            'api_secret.string' => 'The API Secret must be a valid string.',
            'api_available_queries.string' => 'Available Queries must be a valid string.',
        ];
    }
}
