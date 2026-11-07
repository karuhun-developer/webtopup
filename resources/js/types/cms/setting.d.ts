export interface SettingValue {
    supplier_provider?: 'h2h' | 'digiflazz';
    username_provider?: 'h2h' | 'apigames';
    payment_gateway?: 'midtrans' | 'karuhun_qris' | 'paywuz';
    h2h_base_url?: string;
    qris_base_url?: string;
    paywuz_base_url?: string;
    apigames_base_url?: string;
    qris_provider?: string;
    midtrans_is_production?: boolean;
    title: string;
    email: string;
    phone: string;
    logo: string;
    icon: string;
    favicon: string;
    privacy_policy: string;
    terms: string;
    footer_description: string;
    cs: string;
    template_checkout?: string;
    template_payment_confirmation?: string;
    template_payment_rejected?: string;
    template_order_completed?: string;
    template_order_failed?: string;
    template_gift_order_admin_friend_request?: string;
    template_gift_order_user_friend_confirmation?: string;
    template_gift_order_completion?: string;
    manual_transfer_bank?: string;
    manual_transfer_bank_logo?: string;
    manual_transfer_account_name?: string;
    manual_transfer_account_number?: string;
    manual_transfer_type?: 'rekening' | 'qris';
    maintenance_status?: 'active' | 'inactive';
    maintenance_title?: string;
    maintenance_description?: string;
    maintenance_image?: string;
}

export interface Setting {
    id: number;
    value: SettingValue;
    created_at?: string;
    updated_at?: string;
}
