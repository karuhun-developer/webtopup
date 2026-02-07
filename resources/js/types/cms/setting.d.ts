export interface SettingValue {
    title: string;
    email: string;
    phone: string;
    privacy_policy: string;
    terms: string;
    footer_description: string;
    cs: string;
    template_checkout?: string;
    template_payment_confirmation?: string;
    template_payment_rejected?: string;
    template_order_completed?: string;
    template_order_failed?: string;
    manual_transfer_bank?: string;
    manual_transfer_bank_logo?: string;
    manual_transfer_account_name?: string;
    manual_transfer_account_number?: string;
}

export interface Setting {
    id: number;
    value: SettingValue;
    created_at?: string;
    updated_at?: string;
}
