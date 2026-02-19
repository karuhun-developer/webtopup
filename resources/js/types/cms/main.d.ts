import { PPOBBrandDataItem } from '@/types/cms/ppob';
import { UserDataItem } from './management';
import { PPOBProductDataItem } from './ppob.d';

export interface PaymentDataItem {
    driver: string;
    payable_type: string;
    payable_id: number;
    order_id: string;
    transaction_id: string;
    payment_type: string;
    account_number: string;
    account_code: string;
    channel: string;
    expired_at: string;
    paid_at: string;
    amount: number;
    image?: string;
    created_at: string;
    updated_at: string;
}

export interface OrderNotificationDataItem {
    id: number;
    order_id: number;
    provider: string;
    title: string;
    content: string;
    error: string | null;
    created_at: string;
    updated_at: string;
}

export interface OrderDataItem {
    id: number;
    user_id: number;
    p_p_o_b_brand_id: number;
    p_p_o_b_product_id: number;
    reference: string;
    ref_number: string;
    name: string;
    email: string;
    phone: string;
    amount: number;
    fee: number;
    discount_amount: number;
    total_amount: number;
    payment_status: number;
    topup_status: number;
    submited: any;
    created_at: string;
    updated_at: string;
    user?: UserDataItem;
    product?: PPOBProductDataItem;
    brand?: PPOBBrandDataItem;
    payment?: PaymentDataItem;
    notifications?: OrderNotificationDataItem[];
}
